<?php

declare(strict_types=1);

namespace Bartlett\Reflect\Application\PhpParser;

use PhpParser\Node;

/**
 * Node processor to check pre-condition before traversing AST.
 * Should be used in each Node visitor into beforeTraverse().
 *
 * PHP version 7
 *
 * @category PHP
 * @package  bartlett/php-reflect
 * @author   Laurent Laville <pear@laurent-laville.org>
 * @license  https://opensource.org/licenses/BSD-3-Clause The 3-Clause BSD License
 * @link     http://php5.laurent-laville.org/reflect/
 */
interface NodeProcessor
{
    /**
     * Pushes a callback on to the stack of node processors
     *
     * @param callback $callback
     *
     * @return void
     */
    public function push(callable $callback): void;

    /**
     * Gets list of processors that will check pre-conditions.
     *
     * @return callable[]
     */
    public function getProcessors(): array;

    /**
     * Traverses an array of nodes.
     *
     * @param Node[] $nodes Array of nodes
     *
     * @return array list of pre-conditions found
     */
    public function traverse(array $nodes): array;
}
