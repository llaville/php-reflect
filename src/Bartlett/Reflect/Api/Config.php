<?php
/**
 * Validates structure of the JSON configuration file.
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

namespace Bartlett\Reflect\Api;

/**
 * Validates structure of the JSON configuration file.
 *
 * @category PHP
 * @package  PHP_Reflect
 * @author   Laurent Laville <pear@laurent-laville.org>
 * @license  http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version  Release: @package_version@
 * @link     http://php5.laurent-laville.org/reflect/
 * @since    Class available since Release 3.0.0-alpha1
 */
class Config extends BaseApi
{
    /**
     * Validates a JSON configuration file.
     *
     * @param string $file Path to {json} file
     *
     * @return array|string Array when valid json data found, String when an error occured
     */
    public function validate($file)
    {
        return $this->request('config/validate', 'GET', array($file));
    }
}
