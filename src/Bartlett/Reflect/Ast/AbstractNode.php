<?php
/**
 * AbstractNode represents any node of the AST.
 *
 * PHP version 5
 *
 * @category PHP
 * @package  PHP_Reflect
 * @author   Laurent Laville <pear@laurent-laville.org>
 * @license  http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version  GIT: $Id$
 * @link     http://php5.laurent-laville.org/reflect/
 */

namespace Bartlett\Reflect\Ast;

use Bartlett\Reflect\Visitor\VisitorInterface;

/**
 * The AbstractNode class represents any node of the AST (Abstract Syntax Tree).
 *
 * @category PHP
 * @package  PHP_Reflect
 * @author   Laurent Laville <pear@laurent-laville.org>
 * @license  http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version  Release: @package_version@
 * @link     http://php5.laurent-laville.org/reflect/
 * @since    Class available since Release 2.0.0RC2
 */
abstract class AbstractNode
{
    protected $type;
    protected $struct;

    /**
     * Parsed child nodes of this node.
     *
     * @var array
     */
    protected $nodes;

    /**
     * The parent node of this node or null when this node is the root
     * of a node tree.
     *
     * @var AstNode
     */
    protected $parent;

    /**
     * Node class constructor
     */
    public function __construct($type, $attributes = null)
    {
        $this->type   = $type;
        $this->struct = $attributes;
        $this->nodes  = array();
    }

    /**
     * Gets the name of this node.
     *
     * @return mixed
     */
    public function getName()
    {
        return ($this->struct['name'] ? : null);
    }

    /**
     * Gets all attributes of this node.
     *
     * @return array
     */
    public function getAttributes()
    {
        return $this->struct;
    }

    /**
     * Checks if a node has a property.
     *
     * @param string $name Name of the property
     *
     * @return bool
     */
    public function hasAttribute($name)
    {
        return array_key_exists($name, $this->struct);
    }

    /**
     * Gets the value of a property of this node.
     *
     * @param string $name Name of the property
     *
     * @return mixed
     */
    public function getAttribute($name)
    {
        if ($this->hasAttribute($name)) {
            $value = $this->struct[$name];
        } else {
            $value = null;
        }
        return $value;
    }

    /**
     * Sets the value of a property (that must be declared at build-time)
     * of this node.
     *
     * @return void
     */
    public function setAttribute($name, $value)
    {
        if ($this->hasAttribute($name)) {
            $this->struct[$name] = $value;
        }
    }

    /**
     * Gets the type of the node.
     *
     * @return string Type of the node
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Returns the parent node of this node or null when this node is
     * the root of a node tree.
     *
     * @return AST Node
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * Sets the parent node of this node.
     *
     * @param object $node The parent node of this node.
     *
     * @return void
     */
    public function setParent($node)
    {
        $this->parent = $node;
    }

    /**
     * Adds a new child to the actual node.
     *
     * @param object $node AST Node
     *
     * @return void
     */
    public function addChild($node)
    {
        $this->nodes[] = $node;
        $node->setParent($this);
    }

    /**
     * Returns the node instance for the given index or throws an exception.
     *
     * @param integer $index Index of the requested node.
     *
     * @return object AstNode
     * @throws \OutOfBoundsException When no node exists at the given index.
     */
    public function getChild($index)
    {
        if (isset($this->nodes[$index])) {
            return $this->nodes[$index];
        }
        throw new \OutOfBoundsException(
            sprintf(
                'No node found at index %d in node of type: %s',
                $index,
                get_class($this)
            )
        );
    }

    /**
     * This method returns all direct children of the actual node.
     *
     * @return \ArrayIterator
     */
    public function getChildren()
    {
        return new \ArrayIterator($this->nodes);
    }

    /**
     * Count number of children of the actual node.
     *
     * @return int
     */
    public function count()
    {
        return count($this->nodes);
    }

    /**
     * Find all nodes that correspond to the $targetType.
     *
     * @param string $targetType Family of AST node to search for
     * @param string $nodeType   Category of the AST family
     * @param array  &$results   All nodes that correspond to search criterias
     *
     * @return void
     */
    public function findChildren($targetType, $nodeType, array &$results)
    {
        foreach ($this->nodes as $node) {
            if ($node instanceof $targetType
                && $node->getType() == $nodeType
            ) {
                $results[] = $node;
            }
            $node->findChildren($targetType, $nodeType, $results);
        }
    }

    /**
     * Implement Visitor Design Pattern.
     *
     * @param VisitorInterface $visitor Concrete visitor
     *
     * @return void
     */
    public function accept(VisitorInterface $visitor)
    {
        $modelClass = explode('\\', get_class($this));
        $method     = 'visit' . array_pop($modelClass);

        if (method_exists($visitor, $method)) {
            // visit the method and exit
            $visitor->{$method}($this);
            return;
        }

        // if not visit operations is defined, call a default algorithm
        $visitor->defaultVisit($this);
    }
}
