<?php declare(strict_types=1);

/**
 * Common interface to all analysers accessible through the AnalyserManager.
 *
 * @category PHP
 * @package  PHP_Reflect
 * @author   Laurent Laville <pear@laurent-laville.org>
 * @license  http://www.opensource.org/licenses/bsd-license.php  BSD License
 */

namespace Bartlett\Reflect\Analyser;

use Bartlett\Reflect;

/**
 * Interface that all analysers must implement.
 *
 * @category PHP
 * @package  PHP_Reflect
 * @author   Laurent Laville <pear@laurent-laville.org>
 * @license  http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @since    Class available since Release 2.0.0RC2
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
