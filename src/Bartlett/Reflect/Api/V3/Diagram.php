<?php

declare(strict_types=1);

/**
 * Diagram generator API
 *
 * PHP version 5
 *
 * @category PHP
 * @package  PHP_Reflect
 * @author   Laurent Laville <pear@laurent-laville.org>
 * @license  https://opensource.org/licenses/BSD-3-Clause The 3-Clause BSD License
 * @link     http://php5.laurent-laville.org/reflect/
 */

namespace Bartlett\Reflect\Api\V3;

use Bartlett\Reflect\Model;

/**
 * Diagram generator API
 *
 * @category PHP
 * @package  PHP_Reflect
 * @author   Laurent Laville <pear@laurent-laville.org>
 * @license  https://opensource.org/licenses/BSD-3-Clause The 3-Clause BSD License
 * @link     http://php5.laurent-laville.org/reflect/
 * @since    Class available since Release 3.0.0-beta3
 */
class Diagram extends Common
{
    public function __call($name, $args)
    {
        if ('class' == $name) {
            list($argument, $source, $alias, $return) = $args;
            return $this->class_($argument, $source, $alias, $return);
        }
    }

    /**
     * Generates diagram about namespaces in a data source.
     *
     * @param string $argument Name of the namespace to inspect.
     * @param string $source   Path to the data source or its alias.
     * @param mixed  $alias    If set, the source refers to its alias.
     * @param string $engine   Graphical syntax.
     *
     * @return mixed
     */
    public function package($argument, $source, $alias = null, $engine = 'plantuml')
    {
        return $this->getDiagram(
            $engine,
            $source,
            __FUNCTION__,
            $argument
        );
    }

    /**
     * Generates diagram about a user class present in a data source.
     *
     * @param string $argument Name of the class to inspect.
     * @param string $source   Path to the data source or its alias.
     * @param mixed  $alias    If set, the source refers to its alias.
     * @param string $engine   Graphical syntax.
     *
     * @return mixed
     */
    public function class_($argument, $source, $alias = null, $engine = 'plantuml')
    {
        return $this->getDiagram(
            $engine,
            $source,
            __FUNCTION__,
            $argument
        );
    }

    protected function getDiagram($engine, $source, $function, $argument = '')
    {
        $processors = array(
            'plantuml' => '\\Bartlett\\UmlWriter\\Processor\\PlantUMLProcessor',
            'graphviz' => '\\Bartlett\\UmlWriter\\Processor\\GraphvizProcessor',
        );
        if (!array_key_exists($engine, $processors)) {
            throw new \InvalidArgumentException(
                sprintf('Graphical processor "%s" is unknown.', $engine)
            );
        }
        if (!class_exists($processors[$engine])) {
            throw new \InvalidArgumentException(
                'You should install Bartlett\UmlWriter'
            );
        }
        $reflector = new \Bartlett\UmlWriter\Reflector\Reflect($source);
        $processor = new $processors[$engine]($reflector);

        if (strpos($function, 'class') === 0) {
            $graphStmt = $processor->renderClass($argument);
        } else {
            if (empty($argument)) {
                $graphStmt = $processor->render();
            } else {
                $graphStmt = $processor->renderNamespace($argument);
            }
        }
        return $graphStmt;
    }
}
