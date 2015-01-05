<?php

namespace Bartlett\Reflect\Output;

use Bartlett\Reflect\Console\Formatter\OutputFormatter;

use Symfony\Component\Console\Output\OutputInterface;

/**
 * Cache results
 *
 */
class Cache extends OutputFormatter
{
    /**
     *
     *
     * @param OutputInterface $output
     * @param array           $response
     */
    public function clear(OutputInterface $output, $response)
    {
        $output->writeln(
            sprintf(
                '<info>%d cache entries cleared</info>',
                $response
            )
        );
    }
}
