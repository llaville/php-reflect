<?php
/**
 * Default console output class for Diagnose Api.
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
use Symfony\Component\Console\Formatter\OutputFormatterStyle;

/**
 * Diagnose results default render on console
 *
 * @category PHP
 * @package  PHP_Reflect
 * @author   Laurent Laville <pear@laurent-laville.org>
 * @license  http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version  Release: @package_version@
 * @link     http://php5.laurent-laville.org/reflect/
 * @since    Class available since Release 3.0.0-RC1
 */
class Diagnose extends OutputFormatter
{
    /**
     * Diagnose run results
     *
     * @param OutputInterface $output   Console Output concrete instance
     * @param array           $response Render config:validate command
     */
    public function run(OutputInterface $output, $response)
    {
        $output->writeln('<comment>Diagnostics:</comment>');

        $withError = false;

        foreach ($response as $key => $values) {
            list ($flag, $message) = each($values);

            if ($flag == 'OK') {
                $flag = '<diagpass>   OK    </diagpass>';
            } elseif ($flag == 'WARN') {
                $flag = '<warning> WARNING </warning>';
            } else {
                $flag = '<error>   KO    </error>';
                $withError = true;
            }
            $output->writeln($flag . ' ' . $message);
        }

        $summary = sprintf('(%d diagnostics checks)', count($response));

        if ($withError) {
            $message = str_pad('KO ' . $summary, 80);
            $message = '<error>' . $message . '</error>';
        } else {
            $message = str_pad('OK ' . $summary, 80);
            $message = '<diagpass>' . $message . '</diagpass>';
        }
        $output->writeln(['', $message], true);
    }
}
