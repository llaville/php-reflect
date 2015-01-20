<?php
/**
 * Application Environment.
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

namespace Bartlett\Reflect;

/**
* Application Environment.
*
* @category PHP
* @package PHP_Reflect
* @author Laurent Laville <pear@laurent-laville.org>
* @license http://www.opensource.org/licenses/bsd-license.php BSD License
* @version Release: @package_version@
* @link http://php5.laurent-laville.org/reflect/
* @since Class available since Release 2.6.0
*/
class Environment
{
    /**
     * Search a json file on a list of scan directory pointed by
     * the BARTLETT_SCAN_DIR env var.
     * Config filename is identify by the BARTLETTRC env var.
     *
     * @return string|boolean FALSE if not found, otherwise its location
     */
    public static function getJsonConfigFilename()
    {
        $scanDir = getenv('BARTLETT_SCAN_DIR');
        if ($scanDir) {
            $dirs = explode(PATH_SEPARATOR, $scanDir);

            foreach ($dirs as $scanDir) {
                $filename = $scanDir . DIRECTORY_SEPARATOR . getenv('BARTLETTRC');
                if (file_exists($filename) && is_file($filename)) {
                    return realpath($filename);
                }
            }
        }
        return false;
    }

    /**
     * Defines the scan directories where to search for a json config file.
     *
     * @return void
     * @see    getJsonConfigFilename()
     */
    public static function setScanDir()
    {
        if (!getenv("BARTLETT_SCAN_DIR")) {
            $home = defined('PHP_WINDOWS_VERSION_BUILD') ? 'USERPROFILE' : 'HOME';
            $dirs = array(
                realpath('.'),
                getenv($home) . DIRECTORY_SEPARATOR . '.config',
                DIRECTORY_SEPARATOR . 'etc',
            );
            putenv("BARTLETT_SCAN_DIR=" . implode(PATH_SEPARATOR, $dirs));
        }
    }
}
