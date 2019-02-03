<?php

declare(strict_types=1);

namespace Bartlett\Reflect\Application\PhpParser;

use PhpParser\Node;

/**
 * An abstract Node processor to check pre-condition before traversing AST.
 *
 * PHP version 7
 *
 * @category PHP
 * @package  bartlett/php-reflect
 * @author   Laurent Laville <pear@laurent-laville.org>
 * @license  https://opensource.org/licenses/BSD-3-Clause The 3-Clause BSD License
 * @link     http://php5.laurent-laville.org/reflect/
 */
class NodeProcessorAbstract implements NodeProcessor
{
    protected $processors    = [];
    protected $preConditions = [];

    /**
     * {@inheritdoc}
     */
    public function push(callable $callback): void
    {
        array_push($this->processors, $callback);
    }

    /**
     * {@inheritdoc}
     */
    public function getProcessors(): array
    {
        return $this->processors;
    }

    /**
     * {@inheritdoc}
     */
    public function traverse(array $nodes): array
    {
        $this->traverseArray($nodes);
        return $this->preConditions;
    }

    protected function traverseArray(array $nodes): Node
    {
        foreach ($nodes as &$node) {
            if (is_array($node)) {
                $node = $this->traverseArray($node);
            } elseif ($node instanceof Node) {
                $node = $this->traverseNode($node);
            }
        }
    }

    protected function traverseNode(Node $node): Node
    {
        $node = clone $node;

        foreach ($node->getSubNodeNames() as $name) {
            $subNode =& $node->$name;

            if (is_array($subNode)) {
                $this->traverseArray($subNode);
            } elseif ($subNode instanceof Node) {
                $this->traverseNode($subNode);
            }
        }

        foreach ($this->processors as $callback) {
            $result = call_user_func($callback, $node);
            if (is_array($result)) {
                array_push($this->preConditions, $result);
            }
        }
    }
}
