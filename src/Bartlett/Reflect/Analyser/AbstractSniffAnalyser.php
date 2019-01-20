<?php

declare(strict_types=1);

/**
 * Base class to all analysers accessible through the AnalyserPlugin
 * that used sniff.
 *
 * @category PHP
 * @package  PHP_Reflect
 * @author   Laurent Laville <pear@laurent-laville.org>
 * @license  http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version  GIT: $Id$
 * @link     http://php5.laurent-laville.org/reflect/
 */

namespace Bartlett\Reflect\Analyser;

use Bartlett\Reflect;
use Bartlett\Reflect\Visitor\VisitorInterface;

use PhpParser\Node;

/**
 * Base code for all analysers that used sniffs.
 *
 * @category PHP
 * @package  PHP_Reflect
 * @author   Laurent Laville <pear@laurent-laville.org>
 * @license  http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version  Release: @package_version@
 * @link     http://php5.laurent-laville.org/reflect/
 * @since    Class available since Release 4.0.0
 */
abstract class AbstractSniffAnalyser extends AbstractAnalyser implements VisitorInterface
{
    protected $sniffs;

    protected $currentObject;
    protected $currentMethod;
    protected $currentFunction;
    protected $currentClosure;

    public function setMetrics(array $values)
    {
        $this->metrics = array_merge($this->metrics, $values);
    }

    public function inContext($id)
    {
        if (strcasecmp($id, 'object') === 0) {
            return (null !== $this->currentObject);

        } elseif (strcasecmp($id, 'method') === 0) {
            return (null !== $this->currentMethod);

        } elseif (strcasecmp($id, 'function') === 0) {
            return (null !== $this->currentFunction);

        } elseif (strcasecmp($id, 'closure') === 0) {
            return (null !== $this->currentClosure);
        }
        return false;
    }

    public function setUpBeforeVisitor()
    {
        $this->subject->dispatch(
            Reflect\Events::SNIFF,
            array(
                'method' => get_class($this) . '::' . __FUNCTION__,
                'node'   => null,
            )
        );

        foreach ($this->sniffs as $sniff) {
            $sniff->setVisitor($this);
            $sniff->setUpBeforeSniff();
        }
    }

    public function tearDownAfterVisitor()
    {
        $this->subject->dispatch(
            Reflect\Events::SNIFF,
            array(
                'method' => get_class($this) . '::' . __FUNCTION__,
                'node'   => null,
            )
        );

        foreach ($this->sniffs as $sniff) {
            $sniff->tearDownAfterSniff();
        }
    }

    public function beforeTraverse(array $nodes)
    {
        parent::beforeTraverse($nodes);

        foreach ($this->sniffs as $sniff) {
            $sniff->enterSniff();
        }
    }

    public function enterNode(Node $node)
    {
        parent::enterNode($node);

        // store current context
        if ($node instanceof Node\Stmt\ClassLike) {
            $this->currentObject = $node;

        } elseif ($node instanceof Node\Stmt\ClassMethod) {
            $this->currentMethod = $node;

        } elseif ($node instanceof Node\Stmt\Function_) {
            $this->currentFunction = $node;

        } elseif ($node instanceof Node\Expr\Closure) {
            $this->currentClosure = $node;
        }

        foreach ($this->sniffs as $sniff) {
            $sniff->enterNode($node);
        }
    }

    public function leaveNode(Node $node)
    {
        parent::leaveNode($node);

        foreach ($this->sniffs as $sniff) {
            $sniff->leaveNode($node);
        }

        // clear current context
        if ($node instanceof Node\Stmt\ClassLike) {
            $this->currentObject = null;

        } elseif ($node instanceof Node\Stmt\ClassMethod) {
            $this->currentMethod = null;

        } elseif ($node instanceof Node\Stmt\Function_) {
            $this->currentFunction = null;

        } elseif ($node instanceof Node\Expr\Closure) {
            $this->currentClosure = null;
        }
    }

    public function afterTraverse(array $nodes)
    {
        parent::afterTraverse($nodes);

        foreach ($this->sniffs as $sniff) {
            $sniff->leaveSniff();
        }
    }
}
