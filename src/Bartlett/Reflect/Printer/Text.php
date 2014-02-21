<?php
/**
 * Text Printer for the Console Application.
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

namespace Bartlett\Reflect\Printer;

use Symfony\Component\Console\Output\OutputInterface;

/**
 * Prints pre-defined formatted text to current output interface.
 *
 * @category PHP
 * @package  PHP_Reflect
 * @author   Laurent Laville <pear@laurent-laville.org>
 * @license  http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version  Release: @package_version@
 * @link     http://php5.laurent-laville.org/reflect/
 * @since    Interface available since Release 2.0.0RC3
 */
class Text
{
    /**
     * Writes a message to the output.
     *
     * @param object $output Instance of OutputInterface
     * @param array  $lines  Pre-defined formatted text and results to print.
     *
     * @return void
     */
    public function write(OutputInterface $output, array $lines)
    {
        foreach ($lines as $ident => $contents) {
            list ($format, $args) = $contents;
            $output->writeln(vsprintf($format, $args));
        }
    }
}
