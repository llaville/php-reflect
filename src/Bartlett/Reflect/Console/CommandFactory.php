<?php

namespace Bartlett\Reflect\Console;

use Bartlett\Reflect\Client;
use Bartlett\Reflect\Events;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Helper\FormatterHelper;

use Symfony\Component\EventDispatcher\GenericEvent;

use phpDocumentor\Reflection\DocBlock;

/**
 * Command factory
 *
 */
class CommandFactory
{
    private $application;
    private $cmdExceptions;

    /**
     * Creates an instance of the factory that build all Api Methods.
     *
     * @param Application $app           The Console Application
     * @param array       $cmdExceptions (optional) Commands specific rules to apply
     */
    public function __construct(Application $app, array $cmdExceptions = null)
    {
        $this->application   = $app;
        $this->cmdExceptions = $cmdExceptions ? : array();
    }

    /**
     * Generates Commands from all Api Methods or a predefined set.
     *
     * @param array $classes (optional) Api classes to lookup for commands
     *
     * @return Command[]
     */
    public function generateCommands(array $classes = null)
    {
        if (!isset($classes)) {
            $classes = array();
        }
        $path = dirname(__DIR__) . '/Api';

        if (\Phar::running(false)) {
            $iterator = new \Phar($path);
        } else {
            $iterator = new \DirectoryIterator($path);
        }

        foreach ($iterator as $file) {
            if (fnmatch('*.php', $file->getPathName())) {
                $classes[] = Application::API_NAMESPACE . basename($file, '.php');
            }
        }

        $commands = array();

        foreach ($classes as $class) {
            $api = new \ReflectionClass($class);
            if ($api->isAbstract()) {
                // skip abtract classes
                continue;
            }

            foreach ($api->getMethods(\ReflectionMethod::IS_PUBLIC) as $method) {
                if (strstr($method->getName(), '__')) {
                    // skip magics
                    continue;
                }
                $commands[] = $this->generateCommand(strtolower($api->getShortName()), $method);
            }
        }
        return $commands;
    }

    /**
     * Creates a Command based on an Api Method
     *
     * @param string            $namespace Command namespace
     * @param \ReflectionMethod $method
     *
     * @return Command
     */
    private function generateCommand($namespace, \ReflectionMethod $method)
    {
        $docBlock = new DocBlock($method->getDocComment());

        $methodShortName = $method->getShortName();

        $command = new Command($namespace . ':' . $this->dash($methodShortName));

        $cmdExceptions = array();

        if (isset($this->cmdExceptions[$namespace])) {
            if (isset($this->cmdExceptions[$namespace][$methodShortName])) {
                $cmdExceptions = $this->cmdExceptions[$namespace][$methodShortName];
            }
        }

        $params   = $docBlock->getTagsByName('param');
        $aliases  = $docBlock->getTagsByName('alias');
        $disabled = $docBlock->getTagsByName('disabled');

        if (!empty($disabled)) {
            // restrict some public Api methods
            $command->disable();
        } else {
            if (!empty($aliases)) {
                $names = array();
                foreach ($aliases as $aliasTag) {
                    $names[] = $namespace . ':' . $aliasTag->getContent();
                }
                $name = array_shift($names);
                // rename command with the first alias
                $command->setName($name);
                // others @alias are really some command aliases
                $command->setAliases($names);
            }

            $command->setDefinition(
                $this->buildDefinition($namespace, $method, $params, $cmdExceptions)
            );
            $command->setDescription($docBlock->getShortDescription());
            $command->setCode($this->createCode($namespace, $method));
        }
        return $command;
    }

    /**
     * Builds the Input Definition based upon Api Method Parameters
     *
     * @param string            $namespace     Command namespace
     * @param \ReflectionMethod $method        Api Method
     * @param array             $params        Command arguments and options
     * @param array             $cmdExceptions Command specific values
     *
     * @return InputDefinition
     */
    private function buildDefinition($namespace, \ReflectionMethod $method, $params, $cmdExceptions)
    {
        $definition = new InputDefinition();

        foreach ($method->getParameters() as $pos => $parameter) {
            $name        = $parameter->getName();
            $description = null;
            if (isset($params[$pos])) {
                if (ltrim($params[$pos]->getVariableName(), '$') == $name) {
                    $description = $params[$pos]->getDescription();
                    // replace tokens if available
                    if (isset($cmdExceptions[$name]['replaceTokens'])) {
                        $description = strtr(
                            $description,
                            $cmdExceptions[$name]['replaceTokens']
                        );
                    }
                }
            }

            if ($parameter->isOptional()) {
                $shortcut = null;
                $default  = $parameter->isDefaultValueAvailable()
                    ? $parameter->getDefaultValue() : null;
                // option
                if ($default === null) {
                    $mode = InputOption::VALUE_NONE;
                } else {
                    $mode = InputOption::VALUE_OPTIONAL;
                }
                if (isset($params[$pos])
                    && strcasecmp($params[$pos]->getType(), 'array') === 0
                ) {
                    $mode = InputOption::VALUE_IS_ARRAY | $mode;
                }
                $definition->addOption(
                    new InputOption($name, $shortcut, $mode, $description, $default)
                );
            } else {
                if (isset($cmdExceptions[$name]['default'])) {
                    $default = $cmdExceptions[$name]['default'];
                    $mode    = InputArgument::OPTIONAL;
                } else {
                    $default = null;
                    $mode    = InputArgument::REQUIRED;
                }
                if (isset($params[$pos])
                    && strcasecmp($params[$pos]->getType(), 'array') === 0
                ) {
                    $mode = InputArgument::IS_ARRAY | $mode;
                }
                // argument
                $definition->addArgument(
                    new InputArgument($name, $mode, $description, $default)
                );
            }
        }

        return $definition;
    }

    /**
     * Creates the Command execution code
     *
     * @param string            $namespace Command namespace
     * @param \ReflectionMethod $method
     *
     * @return \Closure
     */
    private function createCode($namespace, \ReflectionMethod $method)
    {
        $app = $this->application;

        return function (InputInterface $input, OutputInterface $output) use ($namespace, $method, $app) {
            $methodName = $method->getName();

            $client = new Client($app->getClient());
            $api    = $client->api(strtolower($namespace));

            if (true === $input->hasParameterOption('--no-plugins')) {
                // tells to Api, do not use any plugins
                $api->activatePlugins(false);
            }
            if (true === $input->hasParameterOption('--progress')) {
                $formats = array(
                    'very_verbose' => ' %current%/%max% %percent:3s%% %elapsed:6s% %message%',
                    'very_verbose_nomax' => ' %current% %elapsed:6s% %message%',

                    'debug' => ' %current%/%max% %percent:3s%% %elapsed:6s% %memory:6s% %message%',
                    'debug_nomax' => ' %current% %elapsed:6s% %memory:6s% %message%',
                );
                foreach ($formats as $name => $format) {
                    ProgressBar::setFormatDefinition($name, $format);
                }
                $progress = new ProgressBar($output);
                $progress->setMessage('');

                $api->getEventDispatcher()->addListener(
                    Events::PROGRESS,
                    function (GenericEvent $event) use ($progress) {
                        if ($progress instanceof ProgressBar) {
                            $progress->setMessage(
                                sprintf(
                                    'File %s in progress...',
                                    $event['file']->getRelativePathname()
                                )
                            );
                            $progress->advance();
                        }
                    }
                );
            }

            $args = array();

            foreach ($method->getParameters() as $parameter) {
                if ($parameter->isOptional()) {
                    // option
                    $args[$parameter->getName()] = $input->getOption($parameter->getName());
                } else {
                    // argument
                    $args[$parameter->getName()] = $input->getArgument($parameter->getName());
                }
            }

            if (isset($progress) && $progress instanceof ProgressBar) {
                $progress->start();
            }

            // calls the Api method
            try {
                $response = call_user_func_array(array($api, $methodName), $args);

            } catch (\Exception $e) {
                $response = $e;
            }

            if (isset($progress) && $progress instanceof ProgressBar) {
                $progress->finish();
                $progress->clear();
            }
            $output->writeln('');

            // prints response returned
            $classParts      = explode('\\', get_class($api));
            $outputFormatter = Application::OUTPUT_NAMESPACE . array_pop($classParts);

            // handle all Api exceptions when occured
            if ($response instanceof \Exception) {
                if (substr_count($response->getMessage(), "\n") > 0) {
                    // message on multiple lines
                    $fmt = new FormatterHelper;
                    $output->writeln(
                        $fmt->formatBlock(
                            explode("\n", $response->getMessage()),
                            'error'
                        )
                    );
                } else {
                    // message on single line
                    $output->writeln('<error>' . $response->getMessage() . '</error>');
                }
                return;
            }

            if (!method_exists($outputFormatter, $methodName)
                || !is_callable(array($outputFormatter, $methodName))
                || $output->isDebug()
            ) {
                $output->writeln('<debug>Raw response</debug>');
                $output->writeln(print_r($response, true), OutputInterface::OUTPUT_RAW);
                return;
            }

            $result = new $outputFormatter();

            if ($input->hasParameterOption('--format')) {
                $transformMethod = sprintf('transformTo%s', ucfirst($input->getOption('format')));
                if (method_exists($result, $transformMethod)) {
                    $methodName = $transformMethod;
                } else {
                    $output->writeln(
                        '<error>Could not render result in this format (not implemented).</error>'
                    );
                    return;
                }
            }
            $result->$methodName($output, $response);
        };
    }

    /**
     * Dashifies a camelCase string
     *
     * @param string $name Name of an Api method
     *
     * @return string
     */
    private function dash($name)
    {
        return strtolower(
            preg_replace(
                array('/([A-Z]+)([A-Z][a-z])/', '/([a-z\d])([A-Z])/'),
                array('\\1-\\2', '\\1-\\2'),
                strtr($name, '-', '.')
            )
        );
    }
}
