<?php

namespace Bartlett\Reflect\Output;

use Bartlett\Reflect\Environment;
use Bartlett\Reflect\Console\Formatter\OutputFormatter;

use Symfony\Component\Console\Output\OutputInterface;

/**
 * Config results
 *
 */
class Config extends OutputFormatter
{
    /**
     *
     *
     * @param OutputInterface $output
     * @param array           $response
     */
    public function validate(OutputInterface $output, $response)
    {
        $output->writeln(
            sprintf(
                '<info>"%s" config file is valid</info>',
                Environment::getJsonConfigFilename()
            )
        );
    }
}
