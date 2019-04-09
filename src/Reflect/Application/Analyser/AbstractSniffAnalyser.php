<?php

declare(strict_types=1);

namespace Bartlett\Reflect\Application\Analyser;

use Bartlett\Reflect\Application\Events;
use Bartlett\Reflect\Application\Visitor\VisitorInterface;

use PhpParser\Node;

/**
 * Base class to all analysers accessible through the AnalyserPlugin
 * that used sniff.
 *
 * PHP version 7
 *
 * @category PHP
 * @package  bartlett/php-reflect
 * @author   Laurent Laville <pear@laurent-laville.org>
 * @license  https://opensource.org/licenses/BSD-3-Clause The 3-Clause BSD License
 * @link     http://php5.laurent-laville.org/reflect/
 */
abstract class AbstractSniffAnalyser extends AbstractAnalyser implements VisitorInterface
{
    protected $sniffs;
    protected $currentObject;
    protected $currentMethod;
    protected $currentFunction;
    protected $currentClosure;

    public function setMetrics(array $values): void
    {
        $this->metrics = array_merge($this->metrics, $values);
    }

    public function inContext($id): bool
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

    public function setUpBeforeVisitor(): void
    {
        $this->subject->dispatch(
            Events::SNIFF,
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

    public function tearDownAfterVisitor(): void
    {
        $this->subject->dispatch(
            Events::SNIFF,
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
