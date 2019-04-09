<?php

declare(strict_types=1);

namespace Bartlett\Reflect\Application\Command;

use Bartlett\Reflect\Model\FunctionModel;

/**
 * PHP version 7
 *
 * @category PHP
 * @package  bartlett/php-reflect
 * @author   Laurent Laville <pear@laurent-laville.org>
 * @license  https://opensource.org/licenses/BSD-3-Clause The 3-Clause BSD License
 * @link     http://php5.laurent-laville.org/reflect/
 */
class ReflectionFunctionHandler implements CommandHandlerInterface
{
    public function __invoke(ReflectionFunctionCommand $command): FunctionModel
    {
        $analyserCommand = new AnalyserRunCommand(
            $command->source,
            ['reflection']
        );

        $analyserRun = new AnalyserRunHandler();

        $metrics = $analyserRun($analyserCommand);

        $function = $command->function;

        $collect = $metrics['Bartlett\Reflect\Analyser\ReflectionAnalyser']->filter(
            function ($element) use ($function) {
                return $element instanceof FunctionModel
                    && $element->getName() === $function;
            }
        );

        if (count($collect) === 0) {
            throw new \Exception(
                sprintf('Function "%s" not found.', $function)
            );
        }
        return $collect->first();
    }
}
