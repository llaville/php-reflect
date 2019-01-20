<?php

declare(strict_types=1);

/**
 * Default console output class for Diagram Api.
 *
 * @category PHP
 * @package  PHP_Reflect
 * @author   Laurent Laville <pear@laurent-laville.org>
 * @license  https://opensource.org/licenses/BSD-3-Clause The 3-Clause BSD License
 * @link     http://php5.laurent-laville.org/reflect/
 */

namespace Bartlett\Reflect\Output;

use Bartlett\Reflect\Console\Formatter\OutputFormatter;

use Symfony\Component\Console\Output\OutputInterface;

/**
 * Diagram results, default render on console.
 *
 * @category PHP
 * @package  PHP_Reflect
 * @author   Laurent Laville <pear@laurent-laville.org>
 * @license  https://opensource.org/licenses/BSD-3-Clause The 3-Clause BSD License
 * @link     http://php5.laurent-laville.org/reflect/
 * @since    Class available since Release 3.0.0-beta3
 */
class Diagram extends OutputFormatter
{
    /**
     * Namespace(s) Diagram results.
     *
     * @param OutputInterface $output   Console Output concrete instance
     * @param array           $response Results of diagram processor
     *
     * @return void
     */
    public function package(OutputInterface $output, $response)
    {
        $output->writeln($response);
    }

    /**
     * Class Diagram results
     *
     * @param OutputInterface $output   Console Output concrete instance
     * @param array           $response Results of diagram processor
     *
     * @return void
     */
    public function class_(OutputInterface $output, $response)
    {
        $output->writeln($response);
    }
}
