<?php

namespace Bartlett\Reflect\Output;

use Bartlett\Reflect\Console\Formatter\OutputFormatter;

use Symfony\Component\Console\Output\OutputInterface;

/**
 * Reflection results
 *
 */
class Reflection extends OutputFormatter
{
    /**
     *
     *
     * @param OutputInterface $output
     * @param array           $response
     *
     * @return void
     */
    public function class_(OutputInterface $output, $response)
    {
        $output->writeln((string) $response);
    }

    /**
     *
     *
     * @param OutputInterface $output
     * @param array           $response
     *
     * @return void
     */
    public function function_(OutputInterface $output, $response)
    {
        $output->writeln((string) $response);
    }
}
