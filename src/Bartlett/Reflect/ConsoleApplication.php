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

namespace Bartlett\Reflect;

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use Bartlett\Reflect\Command\ProviderListCommand;
use Bartlett\Reflect\Command\ProviderShowCommand;
use Bartlett\Reflect\Command\ProviderDisplayCommand;
use Bartlett\Reflect\Command\PluginListCommand;
use Bartlett\Reflect\Command\ValidateCommand;

/**
 * Console Application.
 *
 * @category PHP
 * @package  PHP_Reflect
 * @author   Laurent Laville <pear@laurent-laville.org>
 * @license  http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version  Release: @package_version@
 * @link     http://php5.laurent-laville.org/reflect/
 * @since    Class available since Release 2.0.0RC2
 */
class ConsoleApplication extends Application
{
    const VERSION = '@package_version@';

    public function __construct()
    {
        parent::__construct('phpReflect', self::VERSION);
    }

    public function getLongVersion()
    {
        $version = $this->getVersion();

        if ('@' . 'package_version@' == $version) {
            $version = new \SebastianBergmann\Version('2.5.0', dirname(dirname(dirname(__DIR__))));
            $version = $version->getVersion();
        }

        $version = sprintf(
            '<info>%s</info> version <comment>%s</comment>',
            $this->getName(),
            $version
        );
        return $version;
    }

    protected function getDefaultInputDefinition()
    {
        $definition = parent::getDefaultInputDefinition();
        $definition->addOption(
            new InputOption(
                '--profile',
                null,
                InputOption::VALUE_NONE,
                'Display timing and memory usage information.'
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
     * Initializes the default commands that should always be available.
     *
     * @return array An array of default Command instances
     */
    protected function getDefaultCommands()
    {
        $commands   = parent::getDefaultCommands();
        $commands[] = new ValidateCommand;
        $commands[] = new PluginListCommand;
        $commands[] = new ProviderListCommand;
        $commands[] = new ProviderShowCommand;
        $commands[] = new ProviderDisplayCommand;

        try {
            $var = $this->getJsonConfigFile();
        } catch (\Exception $e) {
            // stop here if json config file is missing or invalid
        }

        if (isset($var['plugins'])) {
            // checks for additional commands

            if (is_array($var['plugins'])) {
                $plugins = $var['plugins'];
            } else {
                $plugins = array($var['plugins']);
            }

            foreach ($plugins as $plugin) {
                if (isset($plugin['class']) && is_string($plugin['class'])) {
                    // try to load the plugin
                    if (class_exists($plugin['class'])
                        && method_exists($plugin['class'], 'getCommands')
                    ) {
                        $cmds = $plugin['class']::getCommands();
                        while (!empty($cmds)) {
                            // add each command provided by the plugin
                            $commands[] = array_shift($cmds);
                        }
                    }
                }
            }
        }

        return $commands;
    }

    public function doRun(InputInterface $input, OutputInterface $output)
    {
        if (\Phar::running()
            && true === $input->hasParameterOption('--manifest')
        ) {
            $manifest = 'phar://phpreflect.phar/manifest.txt';

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

        if (true === $input->hasParameterOption('--profile')) {

            if (true === class_exists('\\PHP_Timer')) {
                $text = sprintf(
                    '%s<comment>%s</comment>',
                    PHP_EOL,
                    \PHP_Timer::resourceUsage()
                );
                $output->writeln($text);
            }
        }
        return $exitCode;
    }

    /**
     * Gets the json contents of REFLECT configuration file
     *
     * @return array
     * @throws \Exception if configuration file does not exists or is invalid
     */
    public function getJsonConfigFile()
    {
        $path = realpath(getenv('REFLECT'));

        if (!is_file($path)) {
            throw new \Exception(
                'Configuration file "' . $path . '" does not exists.'
            );
        }
        $json = file_get_contents($path);
        $var  = json_decode($json, true);

        if (null === $var) {
            throw new \Exception(
                'The json configuration file has an invalid format.'
            );
        }
        return $var;
    }
}
