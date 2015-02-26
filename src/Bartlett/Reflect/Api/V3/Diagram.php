<?php
/**
 * Diagram generator API
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
 * Diagram generator API
 *
 * @category PHP
 * @package  PHP_Reflect
 * @author   Laurent Laville <pear@laurent-laville.org>
 * @license  http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version  Release: @package_version@
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
     * @param string $source   Path to the data source or its alias.
     * @param mixed  $alias    If set, the source refers to its alias.
     * @param string $engine   Graphical syntax.
     *
     * @return mixed
     */
    public function package($source, $alias = null, $engine = 'plantuml')
    {
        $api = new Analyser();
        $api->setEventDispatcher($this->eventDispatcher);
        $metrics = $api->run($source, array('reflection'), $alias, false);

        return $this->getDiagram(
            $engine,
            $metrics['Bartlett\Reflect\Analyser\ReflectionAnalyser']
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
        $api = new Analyser();
        $api->setEventDispatcher($this->eventDispatcher);
        $metrics = $api->run($source, array('reflection'), $alias, false);

        $collect = $metrics['Bartlett\Reflect\Analyser\ReflectionAnalyser']->filter(
            function($element) use ($argument) {
                return $element instanceof Model\ClassModel
                    && $element->getName() === $argument;
            }
        );

        if (count($collect) === 0) {
            throw new \InvalidArgumentException(
                sprintf('Class "%s" not found.', $argument)
            );
        }
        return $this->getDiagram(
            $engine,
            $metrics['Bartlett\Reflect\Analyser\ReflectionAnalyser'],
            $collect->first()
        );
    }

    protected function getDiagram($engine, $metrics, $class = null)
    {
        $processors = array(
            'plantuml' => __NAMESPACE__ . '\\Diagram\\PlantUmlProcessor',
        );
        if (!array_key_exists($engine, $processors)) {
            throw new \InvalidArgumentException(
                sprintf('Graphical processor "%s" is unknown.', $engine)
            );
        }
        $processor = new $processors[$engine];
        return $processor->render($metrics, $class);
    }
}
