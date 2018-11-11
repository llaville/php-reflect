<?php
/**
 * Default console output class for Diagnose Api.
 *
 * @category PHP
 * @package  PHP_Reflect
 * @author   Laurent Laville <pear@laurent-laville.org>
 * @license  https://opensource.org/licenses/BSD-3-Clause The 3-Clause BSD License
 * @link     http://php5.laurent-laville.org/reflect/
 */

namespace Bartlett\Reflect\Output;

use Bartlett\Reflect\Console\Formatter\OutputFormatter;
use Bartlett\Reflect\Api\V3\Diagnose as ApiDiagnose;

use Symfony\Component\Console\Output\OutputInterface;

/**
 * Diagnose results default render on console
 *
 * @category PHP
 * @package  PHP_Reflect
 * @author   Laurent Laville <pear@laurent-laville.org>
 * @license  https://opensource.org/licenses/BSD-3-Clause The 3-Clause BSD License
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

        foreach ($response as $key => $value) {
            if (strcasecmp('php_version', $key) == 0) {
                $output->writeln('Checking php settings:');

                $output->writeln(
                    sprintf(
                        '- Requires PHP ' . ApiDiagnose::PHP_MIN . ' or better %s',
                        is_bool($value) ? '<error>FAIL</error>' : '<info>OK</info>'
                    )
                );
                if ($output->isVeryVerbose() && is_bool($value)) {
                    $this->writeComment(
                        $output,
                        'Upgrading to PHP ' .
                        ApiDiagnose::PHP_RECOMMANDED . ' or higher is recommended.'
                    );
                }
            }

            if (strcasecmp('php_ini', $key) == 0) {
                $output->writeln(
                    sprintf(
                        '- php.ini file loaded <info>%s</info>',
                        is_bool($value) ? 'NONE' : $value
                    )
                );
            }

            if (preg_match('/^(.*)_loaded$/', $key, $matches)) {
                // checks extensions loaded
                if (strcasecmp('xdebug', $matches[1]) == 0) {
                    // Xdebug is special case (performance issue)
                    $output->writeln(
                        sprintf(
                            '- Xdebug extension loaded %s',
                            $value ? '<warning>YES</warning>' : '<info>NO</info>'
                        )
                    );
                    if ($output->isVeryVerbose()) {
                        $this->writeComment(
                            $output,
                            'You are encouraged to unload xdebug extension' .
                            ' to speed up execution.'
                        );
                    }
                    if ($output->isVeryVerbose() && $response['xdebug_profiler_enable']) {
                        $this->writeComment(
                            $output,
                            'The xdebug.profiler_enable setting is enabled,' .
                            ' this can slow down execution a lot.'
                        );
                    }
                } else {
                    $output->writeln(
                        sprintf(
                            '- %s extension loaded %s',
                            $matches[1],
                            $value ? '<info>YES</info>' : '<error>FAIL</error>'
                        )
                    );
                }
            }
        }
    }

    protected function writeComment($output, $comment)
    {
        $output->writeln(
            sprintf('  : <comment>%s</comment>', $comment)
        );
    }
}
