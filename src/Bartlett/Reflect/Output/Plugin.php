<?php

namespace Bartlett\Reflect\Output;

use Bartlett\Reflect\Console\Formatter\OutputFormatter;

use Symfony\Component\Console\Output\OutputInterface;

/**
 * Plugin results
 *
 */
class Plugin extends OutputFormatter
{
    /**
     *
     *
     * @param OutputInterface $output
     * @param array           $response
     */
    public function dir(OutputInterface $output, $response)
    {
        if (empty($response)) {
            $output->writeln('<info>No plugin installed</info>');
        } else {
            $headers = array('Plugin Class', 'Events Subscribed');
            $this->tableHelper($output, $headers, $response);
        }
    }
}
