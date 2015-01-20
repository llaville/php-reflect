<?php
/**
 * Default console output class for Plugin Api.
 *
 * @category PHP
 * @package  PHP_Reflect
 * @author   Laurent Laville <pear@laurent-laville.org>
 * @license  http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version  GIT: $Id$
 * @link     http://php5.laurent-laville.org/reflect/
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
 * @license  http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version  Release: @package_version@
 * @link     http://php5.laurent-laville.org/reflect/
 * @since    Class available since Release 3.0.0-alpha1
 */
class Plugin extends OutputFormatter
{
    /**
     * Plugin list results
     *
     * @param OutputInterface $output   Console Output concrete instance
     * @param array           $response Available plugins list
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
