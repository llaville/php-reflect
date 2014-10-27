<?php

namespace Bartlett\Reflect;

/**
 * Application Environment.
 *
 * @category PHP
 * @package  PHP_Reflect
 * @author   Laurent Laville <pear@laurent-laville.org>
 * @license  http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version  Release: @package_version@
 * @link     http://php5.laurent-laville.org/reflect/
 * @since    Class available since Release 2.6.0
 */
abstract class AbstractEnvironment
{
    /**
     * Gets the contents of a JSON configuration file.
     *
     * @return array
     * @throws \Exception if configuration file does not exists or is invalid
     */
    public function getJsonConfigFile()
    {
        $path = realpath(getenv(static::ENV));

        if (!is_file($path)) {
            throw new \Exception(
                'Configuration file "' . $path . '" does not exists.'
            );
        }
        $json = file_get_contents($path);
        $var  = json_decode($json, true);

        if (null === $var || !is_array($var)) {
            throw new \Exception(
                'Configuration file "' . $path . '" has an invalid JSON format.'
            );
        }
        return $var;
    }
}
