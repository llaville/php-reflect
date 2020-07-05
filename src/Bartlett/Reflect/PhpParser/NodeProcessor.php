<?php declare(strict_types=1);

/**
 * Node processor to check pre-condition before traversing AST.
 *
 * @category PHP
 * @package  PHP_Reflect
 * @author   Laurent Laville <pear@laurent-laville.org>
 * @license  https://opensource.org/licenses/BSD-3-Clause The 3-Clause BSD License
 */

namespace Bartlett\Reflect\PhpParser;

/**
 * Node processor to check pre-condition before traversing AST.
 * Should be used in each Node visitor into beforeTraverse().
 *
 * @category PHP
 * @package  PHP_Reflect
 * @author   Laurent Laville <pear@laurent-laville.org>
 * @license  https://opensource.org/licenses/BSD-3-Clause The 3-Clause BSD License
 * @since    Class available since Release 3.0.0-alpha3
 */
interface NodeProcessor
{
    /**
     * Pushes a callback on to the stack of node processors
     *
     * @param callable $callback
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
