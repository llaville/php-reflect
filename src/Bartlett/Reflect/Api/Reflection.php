<?php
/**
 * Reflection API
 *
 * PHP version 7
 *
 * @category PHP
 * @package  PHP_Reflect
 * @author   Laurent Laville <pear@laurent-laville.org>
 * @license  https://opensource.org/licenses/BSD-3-Clause The 3-Clause BSD License
 */

namespace Bartlett\Reflect\Api;

/**
 * Reflection API
 *
 * @category PHP
 * @package  PHP_Reflect
 * @author   Laurent Laville <pear@laurent-laville.org>
 * @license  https://opensource.org/licenses/BSD-3-Clause The 3-Clause BSD License
 * @since    Class available since Release 3.0.0-alpha1
 */
class Reflection extends BaseApi
{
    public function __call($name, $args)
    {
        if ('class' == $name) {
            return call_user_func_array(array($this, 'class_'), $args);
        } elseif ('function' == $name) {
            return call_user_func_array(array($this, 'function_'), $args);
        }
    }

    /**
     * Reports information about a user class present in a data source.
     *
     * @param string $argument Name of the class to reflect.
     * @param string $source   Path to the data source or its alias.
     * @param mixed  $alias    If set, the source refers to its alias.
     * @param string $format   To ouput results in other formats.
     *
     * @return mixed
     * @alias  class
     */
    public function class_($argument, $source, $alias = null, $format = 'txt')
    {
        return $this->request('reflection/class', 'POST', array($argument, $source, $alias, $format));
    }

    /**
     * Reports information about a user function present in a data source.
     *
     * @param string $argument Name of the function to reflect.
     * @param string $source   Path to the data source or its alias.
     * @param mixed  $alias    If set, the source refers to its alias.
     * @param string $format   To ouput results in other formats.
     *
     * @return mixed
     * @alias  function
     */
    public function function_($argument, $source, $alias = null, $format = 'txt')
    {
        return $this->request('reflection/function', 'POST', array($argument, $source, $alias, $format));
    }
}
