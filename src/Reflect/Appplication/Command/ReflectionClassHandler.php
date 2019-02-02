<?php

declare(strict_types=1);

namespace Bartlett\Reflect\Application\Command;

use Bartlett\Reflect\Model\ClassModel;

/**
 * PHP version 7
 *
 * @category PHP
 * @package  bartlett/php-reflect
 * @author   Laurent Laville <pear@laurent-laville.org>
 * @license  https://opensource.org/licenses/BSD-3-Clause The 3-Clause BSD License
 * @link     http://php5.laurent-laville.org/reflect/
 */
class ReflectionClassHandler implements CommandHandlerInterface
{
    public function __invoke(ReflectionClassCommand $command): ClassModel
    {
        $analyserCommand = new AnalyserRunCommand(
            $command->source,
            ['reflection']
        );

        $analyserRun = new AnalyserRunHandler();

        $metrics = $analyserRun($analyserCommand);

        $class = $command->class;

        $collect = $metrics['Bartlett\Reflect\Analyser\ReflectionAnalyser']->filter(
            function ($element) use ($class) {
                return $element instanceof ClassModel
                    && $element->getName() === $class;
            }
        );

        if (count($collect) === 0) {
            throw new \Exception(
                sprintf('Class "%s" not found.', $class)
            );
        }
        return $collect->first();
    }
}
