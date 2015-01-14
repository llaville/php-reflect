<?php

namespace Bartlett\Reflect\Api\V3;

use Bartlett\Reflect\Model;

class Reflection extends Common
{
    const FORMAT_TEXT = 'text';

    public function __call($name, $args)
    {
        if ('invoke' == $name) {
        } elseif ('class' == $name) {
            list($argument, $source, $alias, $return) = $args;
            return $this->class_($argument, $source, $alias, $return);

        } elseif ('function' == $name) {
            list($argument, $source, $alias, $return) = $args;
            return $this->function_($argument, $source, $alias, $return);
        }
    }

    public function __invoke($arg)
    {
    }

    public function class_($argument, $source, $alias = null, $return = false)
    {
        $api = new Analyser();
        $api->setEventDispatcher($this->eventDispatcher);
        $metrics = $api->run($source, array('reflection'), $alias);

        $collect = $metrics['ReflectionAnalyser']->filter(
            function($element) use ($argument) {
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

    public function function_($argument, $source, $alias = null, $return = false)
    {
        $api = new Analyser();
        $api->setEventDispatcher($this->eventDispatcher);
        $metrics = $api->run($source, array('reflection'), $alias);

        $collect = $metrics['ReflectionAnalyser']->filter(
            function($element) use ($argument) {
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
