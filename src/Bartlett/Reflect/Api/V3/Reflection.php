<?php
/**
 * Reflection API
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

use Bartlett\Reflect\Model;

/**
 * Reflection API
 *
 * @category PHP
 * @package  PHP_Reflect
 * @author   Laurent Laville <pear@laurent-laville.org>
 * @license  http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version  Release: @package_version@
 * @link     http://php5.laurent-laville.org/reflect/
 * @since    Class available since Release 3.0.0-alpha1
 */
class Reflection extends Common
{
    public function __call($name, $args)
    {
        if ('class' == $name) {
            list($argument, $source, $alias, $return) = $args;
            return $this->class_($argument, $source, $alias, $return);

        } elseif ('function' == $name) {
            list($argument, $source, $alias, $return) = $args;
            return $this->function_($argument, $source, $alias, $return);
        }
    }

    /**
     * Reports information about a user class present in a data source.
     *
     * @param string $argument Name of the class to reflect.
     * @param string $source   Path to the data source or its alias.
     * @param mixed  $alias    If set, the source refers to its alias.
     * @param mixed  $return   Format of result to return. False when raw text.
     *
     * @return mixed
     */
    public function class_($argument, $source, $alias = null, $return = false)
    {
        $api = new Analyser();
        $api->setEventDispatcher($this->eventDispatcher);
        $metrics = $api->run($source, array('reflection'), $alias, false);

        $collect = $metrics['Bartlett\Reflect\Analyser\ReflectionAnalyser']->filter(
            function ($element) use ($argument) {
                return $element instanceof Model\ClassModel
                    && $element->getName() === $argument;
            }
        );

        if (count($collect) === 0) {
            throw new \Exception(
                sprintf('Class "%s" not found.', $argument)
            );
        }
        return $collect->first();
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
     */
    public function function_($argument, $source, $alias = null, $return = false)
    {
        $api = new Analyser();
        $api->setEventDispatcher($this->eventDispatcher);
        $metrics = $api->run($source, array('reflection'), $alias, false);

        $collect = $metrics['Bartlett\Reflect\Analyser\ReflectionAnalyser']->filter(
            function ($element) use ($argument) {
                return $element instanceof Model\FunctionModel
                    && $element->getName() === $argument;
            }
        );

        if (count($collect) === 0) {
            throw new \Exception(
                sprintf('Function "%s" not found.', $argument)
            );
        }
        return $collect->first();
    }
}
