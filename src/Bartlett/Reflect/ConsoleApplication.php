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
use Symfony\Component\Console\Helper\TableHelper;

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
        $version = sprintf(
            '<info>%s</info> version <comment>%s</comment>',
            $this->getName(),
            '@' . 'package_version@' == $this->getVersion() ? 'DEV' : $this->getVersion()
        );

        if ('@' . 'git_commit@' !== '@git_commit@') {
            $version .= sprintf(' build <comment>%s</comment>', '@git_commit@');
        }
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

        return $definition;
    }

    public function doRun(InputInterface $input, OutputInterface $output)
    {
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
        $path = trim(getenv('REFLECT')) ? : './reflect.json';

        if (!file_exists($path)) {
            throw new \Exception(
                'Configuration file "' . realpath($path) . '" does not exists.'
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
