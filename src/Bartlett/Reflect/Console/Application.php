<?php
/**
 * The Reflect CLI version.
 *
 * PHP version 5
 *
 * @category PHP
 * @package  PHP_Reflect
 * @author   Laurent Laville <pear@laurent-laville.org>
 * @license  http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version  GIT: $Id$
 * @link     http://php5.laurent-laville.org/reflect/
 */

namespace Bartlett\Reflect\Console;

use Bartlett\Reflect\Environment;

use Symfony\Component\Console\Application as BaseApplication;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Output\StreamOutput;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;
use Symfony\Component\Console\ConsoleEvents;
use Symfony\Component\Console\Event\ConsoleCommandEvent;
use Symfony\Component\Console\Event\ConsoleTerminateEvent;

use Symfony\Component\Stopwatch\Stopwatch;

use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Console Application.
 *
 * @category PHP
 * @package  PHP_Reflect
 * @author   Laurent Laville <pear@laurent-laville.org>
 * @license  http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version  Release: @package_version@
 * @link     http://php5.laurent-laville.org/reflect/
 * @since    Class available since Release 3.0.0-alpha1
 */
class Application extends BaseApplication
{
    const API_NAMESPACE    = 'Bartlett\Reflect\Api\\';
    const OUTPUT_NAMESPACE = 'Bartlett\Reflect\Output\\';

    private $release;
    private $container;
    private $eventDispatcher;
    private $stopwatch;

    public function __construct($appName, $appVersion)
    {
        // disable Garbage Collector
        gc_disable();

        $jsonFile = Environment::getJsonConfigFilename();
        if ($jsonFile === false) {
            $jsonFile =  './' . getenv("BARTLETTRC");
        }

        parent::__construct($appName, '@package_version@');
        $this->release = $appVersion;

        $defaultAnalyser = (strcasecmp($appName, 'phpcompatinfo') === 0)
            ? 'compatibility' : 'structure';

        $exceptions = array(
            'analyser' => array(
                'run' => array(
                    'analysers' => array(
                        'default' => array($defaultAnalyser),
                    ),
                ),
            ),
            'config' => array(
                'validate' => array(
                    'file' => array(
                        'default' => $jsonFile,
                        'replaceTokens' => array('{json}' => getenv("BARTLETTRC"))
                    ),
                ),
            ),
        );

        $classes = array();
        if (strcasecmp($appName, 'phpcompatinfo') === 0) {
            $classes[] = 'Bartlett\CompatInfo\Api\Reference';
        }

        $factory = new CommandFactory($this, $exceptions);
        $this->addCommands($factory->generateCommands($classes));
    }

    public function setDispatcher(EventDispatcherInterface $dispatcher)
    {
        $this->stopwatch = new Stopwatch();

        $dispatcher->addListener(ConsoleEvents::COMMAND, function (ConsoleCommandEvent $event) {
            // Just before executing any command
            $this->stopwatch->start($event->getCommand()->getName());
        });

        $dispatcher->addListener(ConsoleEvents::TERMINATE, function (ConsoleTerminateEvent $event) {
            // Just after executing any command
            $command = $event->getCommand();

            $consoleEvent = $this->stopwatch->stop($command->getName());

            $input  = $event->getInput();
            $output = $event->getOutput();

            if (false === $input->hasParameterOption('--profile')) {
                return;
            }

            $time   = $consoleEvent->getDuration();
            $memory = $consoleEvent->getMemory();

            $text = sprintf(
                '%s<comment>Time: %s, Memory: %4.2fMb</comment>',
                PHP_EOL,
                $this->toTimeString($time),
                $memory / (1024 * 1024)
            );
            $output->writeln($text);
        });

        $this->eventDispatcher = $dispatcher;
        parent::setDispatcher($dispatcher);
    }

    public function getDispatcher()
    {
        if (!$this->eventDispatcher) {
            $this->setDispatcher(new EventDispatcher());
        }
        return $this->eventDispatcher;
    }

    public function getLogger()
    {
        return $this->getContainer()->get($this->getName() . '.logger');
    }

    public function getClient()
    {
        return $this->getContainer()->get($this->getName() . '.client');
    }

    public function getVersion()
    {
        $version = parent::getVersion();

        if ('@' . 'package_version@' == $version) {
            $version = new \SebastianBergmann\Version(
                $this->release,
                dirname(dirname(dirname(dirname(__DIR__))))
            );
            $version = $version->getVersion();
        }
        return $version;
    }

    public function getLongVersion()
    {
        $version = sprintf(
            '<info>%s</info> version <comment>%s</comment>',
            $this->getName(),
            $this->getVersion()
        );
        return $version;
    }

    /**
     * {@inheritDoc}
     */
    public function run(InputInterface $input = null, OutputInterface $output = null)
    {
        if (null === $input) {
            $input = new ArgvInput();
        }

        if (null === $output) {
            $target = $input->getParameterOption('--output');
            if ($target === false) {
                $output = new ConsoleOutput();
            } else {
                $url  = parse_url($target);
                $mode = 'w';

                if (isset($url['scheme']) && $url['scheme'] == 'ftp') {
                    $options = array($url['scheme'] => array('overwrite' => true));
                    $context = stream_context_create($options);
                    $output  = new StreamOutput(fopen($target, $mode, false, $context), null, false);
                } else {
                    $output  = new StreamOutput(fopen($target, $mode), null, false);
                }
            }
            $output->getFormatter()->setStyle('warning', new OutputFormatterStyle('black', 'yellow'));
            $output->getFormatter()->setStyle('debug', new OutputFormatterStyle('black', 'cyan'));
            $output->getFormatter()->setStyle('php', new OutputFormatterStyle('white', 'magenta'));
            $output->getFormatter()->setStyle('ext', new OutputFormatterStyle('white', 'blue'));
        }

        $this->getDispatcher();

        parent::run($input, $output);
    }

    public function doRun(InputInterface $input, OutputInterface $output)
    {
        if (\Phar::running()
            && true === $input->hasParameterOption('--manifest')
        ) {
            $manifest = 'phar://' . strtolower($this->getName()) . '.phar/manifest.txt';

            if (file_exists($manifest)) {
                $out = file_get_contents($manifest);
                $exitCode = 0;
            } else {
                $fmt = $this->getHelperSet()->get('formatter');
                $out = $fmt->formatBlock('No manifest defined', 'error');
                $exitCode = 1;
            }
            $output->writeln($out);
            return $exitCode;
        }

        $exitCode = parent::doRun($input, $output);

        return $exitCode;
    }

    protected function getDefaultInputDefinition()
    {
        $definition = parent::getDefaultInputDefinition();
        $definition->addOption(
            new InputOption(
                '--no-plugins',
                null,
                InputOption::VALUE_NONE,
                'Disables all plugins.'
            )
        );
        $definition->addOption(
            new InputOption(
                '--profile',
                null,
                InputOption::VALUE_NONE,
                'Display timing and memory usage information.'
            )
        );
        $definition->addOption(
            new InputOption(
                '--progress',
                null,
                InputOption::VALUE_NONE,
                'Show progress bar.'
            )
        );
        $definition->addOption(
            new InputOption(
                '--output',
                null,
                InputOption::VALUE_REQUIRED,
                'Write results to file or URL.'
            )
        );
        if (\Phar::running()) {
            $definition->addOption(
                new InputOption(
                    '--manifest',
                    null,
                    InputOption::VALUE_NONE,
                    'Show which versions of dependencies are bundled.'
                )
            );
        }
        return $definition;
    }

    /**
     * Formats the elapsed time as a string.
     *
     * This code has been copied and adapted from phpunit/php-timer
     *
     * @param int $time The period duration (in milliseconds)
     *
     * @return string
     */
    protected function toTimeString($time)
    {
        $times = array(
            'hour'   => 3600000,
            'minute' => 60000,
            'second' => 1000
        );

        $ms = $time;

        foreach ($times as $unit => $value) {
            if ($ms >= $value) {
                $time = floor($ms / $value * 100.0) / 100.0;
                return $time . ' ' . ($time == 1 ? $unit : $unit . 's');
            }
        }
        return $ms . ' ms';
    }

    private function createContainer()
    {
        $container = new ContainerBuilder();

        // client for interacting with the API
        $container->register(
            $this->getName() . '.client',
            'Bartlett\Reflect\Client\LocalClient'
        );

        // PSR-3 compatible logger
        $container->register(
            $this->getName() . '.logger',
            'Bartlett\Reflect\Plugin\Log\DefaultLogger'
        );

        return $container;
    }

    private function getContainer()
    {
        if ($this->container === null) {
            $this->container = $this->createContainer();
        }

        return $this->container;
    }
}
