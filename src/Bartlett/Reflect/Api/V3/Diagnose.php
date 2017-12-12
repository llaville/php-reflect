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
    const PHP_MIN         = '5.5.0';
    const PHP_RECOMMANDED = '5.6.0';

    /**
     * Diagnoses the system to identify common errors.
     *
     * @return array
     */
    public function run()
    {
        $response = array();

        if (version_compare(PHP_VERSION, self::PHP_MIN, '<')) {
            $response['php_version'] = false;
        } else {
            $response['php_version'] = PHP_VERSION;
        }

        $response['php_ini'] = php_ini_loaded_file();

        $extensions = array(
            'date',
            'json',
            'pcre',
            'phar',
            'reflection',
            'spl',
            'tokenizer',
        );

        foreach ($extensions as $extension) {
            $response[$extension . '_loaded'] = extension_loaded($extension);
        }

        if (extension_loaded('xdebug')) {
            $response['xdebug_loaded']          = true;
            $response['xdebug_profiler_enable'] = ini_get('xdebug.profiler_enable');
        }

        return $response;
    }
}
