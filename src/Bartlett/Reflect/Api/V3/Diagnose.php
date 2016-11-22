<?php
/**
 * Diagnoses the system to identify common errors.
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

namespace Bartlett\Reflect\Api\V3;

use ZendDiagnostics\Check;
use ZendDiagnostics\Runner\Runner;
use ZendDiagnostics\Result\FailureInterface;

/**
 * Diagnoses the system to identify common errors.
 *
 * @category PHP
 * @package  PHP_Reflect
 * @author   Laurent Laville <pear@laurent-laville.org>
 * @license  http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version  Release: @package_version@
 * @link     http://php5.laurent-laville.org/reflect/
 * @since    Class available since Release 3.0.0-RC1
 */
class Diagnose extends Common
{
    const PHP_MIN         = '5.4.0';
    const PHP_RECOMMANDED = '5.6.0';

    /**
     * Diagnoses the system to identify common errors.
     *
     * @return array
     */
    public function run()
    {
        $extensions = array(
            'date',
            'json',
            'pcre',
            'phar',
            'reflection',
            'spl',
            'tokenizer',
        );

        $runner = new Runner();

        // Add checks
        $checkPhpversion = new Check\PhpVersion(self::PHP_MIN);
        $checkPhpIni     = new Check\IniFile(php_ini_loaded_file());
        $checkExtensions = new Check\ExtensionLoaded($extensions);
        $checkXdebugProf = new Check\PhpFlag('xdebug.profiler_enable', false);
        $runner->addCheck($checkPhpversion);
        $runner->addCheck($checkPhpIni);
        $runner->addCheck($checkExtensions);
        $runner->addCheck($checkXdebugProf);

        // Run all checks
        $results = $runner->run();

        // Prepare formatted responses
        $response = array();

        if ($results[$checkPhpversion] instanceof FailureInterface) {
            $flag = 'KO';
            $response['php_version'] = [$flag => $results[$checkPhpversion]->getMessage()];
        } else {
            $flag = 'OK';
            $response['php_version'] = [$flag => 'PHP version at least ' . self::PHP_MIN . ': ' .
                $results[$checkPhpversion]->getMessage()
            ];
        }

        if ($results[$checkPhpIni] instanceof FailureInterface) {
            $flag = 'KO';
            $response['php_ini'] = [$flag => $results[$checkPhpIni]->getMessage()];
        } else {
            $flag = 'OK';
            $response['php_ini'] = [$flag => sprintf('php.ini file loaded is valid: %s', php_ini_loaded_file())];
        }

        if ($results[$checkExtensions] instanceof FailureInterface) {
            $flag = 'KO';
        } else {
            $flag = 'OK';
        }
        $response['extensions'] = [$flag => $results[$checkExtensions]->getMessage()];

        if (extension_loaded('xdebug')) {
            $flag = 'WARN';
            $response['xdebug_loaded'] = [$flag => 'You are encouraged to unload xdebug extension' .
                ' to speed up execution.'
            ];
            if ($results[$checkXdebugProf] instanceof FailureInterface) {
                $response['xdebug_profiler_enable'] = [$flag => 'The xdebug.profiler_enable setting is enabled,' .
                    ' this can slow down execution a lot.'
                ];
            } else {
                $flag = 'OK';
                $response['xdebug_profiler_enable'] = [$flag => $results[$checkXdebugProf]->getMessage()];
            }
        }

        return $response;
    }
}
