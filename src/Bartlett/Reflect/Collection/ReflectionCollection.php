<?php

namespace Bartlett\Reflect\Collection;

use Bartlett\Reflect\Model\ClassModel;
use Bartlett\Reflect\Model\FunctionModel;
use Bartlett\Reflect\Model\ConstantModel;

use PhpParser\Node;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * Reflection collection that collect models for the reflection analyser.
 */
class ReflectionCollection extends ArrayCollection
{

    /**
     * {@inheritDoc}
     */
    public function add($node)
    {
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
}
