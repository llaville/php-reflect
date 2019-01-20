<?php

declare(strict_types=1);

/**
 * Node processor to check pre-condition before traversing AST.
 *
 * @category PHP
 * @package  PHP_Reflect
 * @author   Laurent Laville <pear@laurent-laville.org>
 * @license  https://opensource.org/licenses/BSD-3-Clause The 3-Clause BSD License
 * @link     http://php5.laurent-laville.org/reflect/
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
 * @link     http://php5.laurent-laville.org/reflect/
 * @since    Class available since Release 3.0.0-alpha3
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
    public function push(callable $callback);

    /**
     * Gets list of processors that will check pre-conditions.
     *
     * @return callable[]
     */
    public function getProcessors();

    /**
     * Traverses an array of nodes.
     *
     * @param Node[] $nodes Array of nodes
     *
     * @return array list of pre-conditions found
     */
    public function traverse(array $nodes);
}
