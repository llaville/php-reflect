<?php
/**
 *
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
 *
 *
 * @category PHP
 * @package  PHP_Reflect
 * @author   Laurent Laville <pear@laurent-laville.org>
 * @license  http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version  Release: @package_version@
 * @link     http://php5.laurent-laville.org/reflect/
 * @since    Class available since Release 3.0.0-alpha1
 */
class Reflection extends BaseApi
{
    /**
     * Reports information about a user class present in a data source.
     *
     * @param string $argument Name of the class to reflect.
     * @param string $source   Path to the data source or its alias.
     * @param mixed  $alias    If set, the source refers to its alias.
     * @param mixed  $return   Format of result to return. False when raw text.
     *
     * @return mixed
     * @alias  class
     */
    public function class_($argument, $source, $alias = null, $return = false)
    {
        return $this->request('reflection/class', 'POST', array($argument, $source, $alias, $return));
    }

    /**
     * Reports information about a user function present in a data source.
     *
     * @param string $argument Name of the function to reflect.
     * @param string $source   Path to the data source or its alias.
     * @param mixed  $alias    If set, the source refers to its alias.
     * @param mixed  $return   Format of result to return. False when raw text.
     *
     * @return mixed
     * @alias  function
     */
    public function function_($argument, $source, $alias = null, $return = false)
    {
        return $this->request('reflection/function', 'POST', array($argument, $source, $alias, $return));
    }
}
