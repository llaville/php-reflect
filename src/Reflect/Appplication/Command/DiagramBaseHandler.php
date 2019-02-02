<?php

namespace Bartlett\Reflect\Appplication\Command;

use Bartlett\UmlWriter\Processor\GraphvizProcessor;
use Bartlett\UmlWriter\Processor\PlantUMLProcessor;

/**
 * PHP version 7
 *
 * @category PHP
 * @package  bartlett/php-reflect
 * @author   Laurent Laville <pear@laurent-laville.org>
 * @license  https://opensource.org/licenses/BSD-3-Clause The 3-Clause BSD License
 * @link     http://php5.laurent-laville.org/reflect/
 */
abstract class DiagramBaseHandler
{
    protected function getDiagram(string $engine, string $source, string $function, string $argument = '')
    {
        $processors = [
            'plantuml' => PlantUMLProcessor::class,
            'graphviz' => GraphvizProcessor::class,
        ];
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
