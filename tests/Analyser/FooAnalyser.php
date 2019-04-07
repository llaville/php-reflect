<?php

declare(strict_types=1);

namespace Bartlett\Tests\Reflect\Analyser;

use Bartlett\Reflect\Application\Analyser\AbstractAnalyser;

class FooAnalyser extends AbstractAnalyser
{
    public function setTokens(array $tokens) : void
    {
    }

    public function setCurrentFile(string $path) : void
    {
    }

    public function getMetrics() : array
    {
    }
}
