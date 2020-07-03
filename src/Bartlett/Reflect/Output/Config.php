<?php declare(strict_types=1);

/**
 * Default console output class for Config Api.
 *
 * @category PHP
 * @package  PHP_Reflect
 * @author   Laurent Laville <pear@laurent-laville.org>
 * @license  https://opensource.org/licenses/BSD-3-Clause The 3-Clause BSD License
 */

namespace Bartlett\Reflect\Output;

use Bartlett\Reflect\Environment;
use Bartlett\Reflect\Console\Formatter\OutputFormatter;

use Symfony\Component\Console\Output\OutputInterface;

/**
 * Config results default render on console
 *
 * @category PHP
 * @package  PHP_Reflect
 * @author   Laurent Laville <pear@laurent-laville.org>
 * @license  https://opensource.org/licenses/BSD-3-Clause The 3-Clause BSD License
 * @since    Class available since Release 3.0.0-alpha1
 */
class Config extends OutputFormatter
{
    /**
     * Config validate results
     *
     * @param OutputInterface $output   Console Output concrete instance
     * @param array           $response Render config:validate command
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
