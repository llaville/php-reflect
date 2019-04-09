<?php

declare(strict_types=1);

namespace Bartlett\Reflect\Application\Command;

use Bartlett\Reflect\Application\Command\AnalyserBaseHandler;

/**
 * PHP version 7
 *
 * @category PHP
 * @package  bartlett/php-reflect
 * @author   Laurent Laville <pear@laurent-laville.org>
 * @license  https://opensource.org/licenses/BSD-3-Clause The 3-Clause BSD License
 * @link     http://php5.laurent-laville.org/reflect/
 */
class AnalyserListHandler extends AnalyserBaseHandler implements CommandHandlerInterface
{
    public function __invoke(AnalyserListCommand $command): array
    {
        $am = $this->registerAnalysers();
        return $am->toArray();
    }
}
