<?php declare(strict_types=1);

/**
 * Default console output class for Plugin Api.
 *
 * @category PHP
 * @package  PHP_Reflect
 * @author   Laurent Laville <pear@laurent-laville.org>
 * @license  https://opensource.org/licenses/BSD-3-Clause The 3-Clause BSD License
 */

namespace Bartlett\Reflect\Output;

use Bartlett\Reflect\Console\Formatter\OutputFormatter;

use Symfony\Component\Console\Output\OutputInterface;

/**
 * Plugin results default render on console
 *
 * @category PHP
 * @package  PHP_Reflect
 * @author   Laurent Laville <pear@laurent-laville.org>
 * @license  https://opensource.org/licenses/BSD-3-Clause The 3-Clause BSD License
 * @since    Class available since Release 3.0.0-alpha1
 */
class Plugin extends OutputFormatter
{
    /**
     * Plugin list results
     *
     * @param OutputInterface $output   Console Output concrete instance
     * @param array           $response Available plugins list
     *
     * @return void
     */
    public function dir(OutputInterface $output, array $response): void
    {
        if (empty($response)) {
            $output->writeln('<info>No plugin installed</info>');
        } else {
            $headers = array('Plugin Class', 'Events Subscribed');
            $rows    = array();

            foreach ($response as $pluginClass => $events) {
                $first  = true;
                foreach ($events as $event) {
                    if (!$first) {
                        $rows[] = array('', $event);
                    } else {
                        $rows[] = array($pluginClass, $event);
                        $first  = false;
                    }
                }
            }
            $this->tableHelper($output, $headers, $rows);
        }
    }
}
