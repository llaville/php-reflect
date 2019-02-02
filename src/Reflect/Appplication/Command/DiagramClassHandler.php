<?php

declare(strict_types=1);

namespace Bartlett\Reflect\Application\Command;

use Bartlett\Reflect\Appplication\Command\DiagramBaseHandler;

/**
 * PHP version 7
 *
 * @category PHP
 * @package  bartlett/php-reflect
 * @author   Laurent Laville <pear@laurent-laville.org>
 * @license  https://opensource.org/licenses/BSD-3-Clause The 3-Clause BSD License
 * @link     http://php5.laurent-laville.org/reflect/
 */
class DiagramClassHandler extends DiagramBaseHandler implements CommandHandlerInterface
{
    public function __invoke(DiagramClassCommand $command): string
    {
        return $this->getDiagram(
            $command->engine,
            $command->source,
            'class_',
            $command->class
        );
    }
}
