<?php

declare(strict_types=1);

namespace Bartlett\Reflect\Application\Analyser;

use Bartlett\Reflect;

/**
 * Common interface to all analysers accessible through the AnalyserManager.
 *
 * PHP version 7
 *
 * @category PHP
 * @package  bartlett/php-reflect
 * @author   Laurent Laville <pear@laurent-laville.org>
 * @license  https://opensource.org/licenses/BSD-3-Clause The 3-Clause BSD License
 * @link     http://php5.laurent-laville.org/reflect/
 */
interface AnalyserInterface
{
    public function getSubject(): Reflect;

    public function getCurrentFile(): string;

    public function getTokens(): array;

    public function setSubject(Reflect $reflect): void;

    public function setTokens(array $tokens): void;

    public function setCurrentFile(string $path): void;

    public function getMetrics(): array;

    public function getName(): string;

    public function getNamespace(): string;

    public function getShortName(): string;
}
