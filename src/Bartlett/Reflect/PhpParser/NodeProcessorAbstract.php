<?php
/**
 * An abstract Node processor to check pre-condition before traversing AST.
 *
 * @category PHP
 * @package  PHP_Reflect
 * @author   Laurent Laville <pear@laurent-laville.org>
 * @license  http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version  GIT: $Id$
 * @link     http://php5.laurent-laville.org/reflect/
 */

namespace Bartlett\Reflect\PhpParser;

use PhpParser\Node;

/**
 * An abstract Node processor to check pre-condition before traversing AST.
 *
 * @category PHP
 * @package  PHP_Reflect
 * @author   Laurent Laville <pear@laurent-laville.org>
 * @license  http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version  Release: @package_version@
 * @link     http://php5.laurent-laville.org/reflect/
 * @since    Class available since Release 3.0.0-alpha3
 */
class NodeProcessorAbstract implements NodeProcessor
{
    protected $processors    = array();
    protected $preConditions = array();

    /**
     * {@inheritdoc}
     */
    public function push(callable $callback)
    {
        array_push($this->processors, $callback);
    }

    /**
     * {@inheritdoc}
     */
    public function getProcessors()
    {
        return $this->processors;
    }

    /**
     * {@inheritdoc}
     */
    public function traverse(array $nodes)
    {
        $this->traverseArray($nodes);
        return $this->preConditions;
    }

    protected function traverseArray(array $nodes)
    {
        foreach ($nodes as &$node) {
            if (is_array($node)) {
                $node = $this->traverseArray($node);
            } elseif ($node instanceof Node) {
                $node = $this->traverseNode($node);
            }
        }
    }

    protected function traverseNode(Node $node)
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
