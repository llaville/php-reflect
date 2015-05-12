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
    const PHP_MIN         = '5.3.2';
    const PHP_RECOMMANDED = '5.3.4';

    /**
     * Diagnose run results
     *
     * @param OutputInterface $output   Console Output concrete instance
     * @param array           $response Render config:validate command
     */
    public function run(OutputInterface $output, $response)
    {
        foreach ($response as $key => $value) {
            if (strcasecmp('php_version', $key) == 0) {
                $output->writeln('Checking php settings:');

                $output->writeln(
                    sprintf(
                        '- Requires PHP ' . self::PHP_MIN . ' or better %s',
                        is_bool($value) ? '<error>FAIL</error>' : '<info>OK</info>'
                    )
                );
                if ($output->isVeryVerbose() && is_bool($value)) {
                    $this->writeComment(
                        $output,
                        'Upgrading to PHP ' .
                        self::PHP_RECOMMANDED . ' or higher is recommended.'
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
