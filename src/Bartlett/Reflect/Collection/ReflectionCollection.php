<?php

namespace Bartlett\Reflect\Collection;

use Bartlett\Reflect\Model\ClassModel;
use Bartlett\Reflect\Model\FunctionModel;
use Bartlett\Reflect\Model\ConstantModel;

use PhpParser\Node;

use Doctrine\Common\Collections\ArrayCollection;

use PDO;
use Closure;

/**
 * Reflection collection that collect models for the reflection analyser.
 */
class ReflectionCollection extends ArrayCollection
{
    private $dbal;
    private $references;

    public function __construct(array $nodes = array(), PDO $pdo = null)
    {
        $this->references = new ReferenceCollection(array(), $pdo);
        $this->dbal       = $pdo;
        parent::__construct($nodes);
    }

    /**
     * {@inheritDoc}
     */
    public function add($node)
    {
        $versions = $node->getAttribute('compatinfo');
        if ($versions === null) {
            $groups = array(
                'Stmt_Interface' => 'interfaces',
                'Stmt_Class'     => 'classes',
                'Stmt_Trait'     => 'traits',
                'Stmt_Function'  => 'functions',
                'Expr_Closure'   => 'functions',
                'Stmt_Const'     => 'constants',
            );
            // find reference info
            $versions = $this->references->find(
                $groups[$node->getType()],
                (string) $node->namespacedName
            );
            // cache to speed-up later uses
            $node->setAttribute('compatinfo', $versions);
        }

        if ($node instanceof Node\Stmt\Class_
            || $node instanceof Node\Stmt\Interface_
            || $node instanceof Node\Stmt\Trait_
        ) {
            $model = new ClassModel($node);

        } elseif ($node instanceof Node\Stmt\Function_
            || $node instanceof Node\Expr\Closure
        ) {
            $model = new FunctionModel($node);

        } elseif ($node instanceof Node\Stmt\Const_) {
            $model = new ConstantModel($node);
        }
        parent::add($model);
    }

    /**
     * {@inheritDoc}
     */
    public function filter(Closure $p)
    {
        return new static(array_filter($this->toArray(), $p), $this->dbal);
    }
}
