<?php

namespace Bartlett\Reflect;

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\TableHelper;

class ConsoleApplication extends Application
{
    const VERSION = '2.0.0RC2';

    public function __construct()
    {
        parent::__construct('phpReflect', self::VERSION);
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
     */
    public function getJsonConfigFile()
    {
        $path = trim(getenv('REFLECT')) ? : './reflect.json';
        $json = file_get_contents($path);
        $var  = json_decode($json, true);
        return $var;
    }
}
