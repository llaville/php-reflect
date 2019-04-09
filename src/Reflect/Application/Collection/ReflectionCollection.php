<?php

declare(strict_types=1);

namespace Bartlett\Reflect\Application\Collection;

use Bartlett\Reflect\Application\Model\ClassModel;
use Bartlett\Reflect\Application\Model\FunctionModel;
use Bartlett\Reflect\Application\Model\ConstantModel;

use PhpParser\Node;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * Reflection collection that collect models for the reflection analyser.
 *
 * PHP version 7
 *
 * @category PHP
 * @package  bartlett/php-reflect
 * @author   Laurent Laville <pear@laurent-laville.org>
 * @license  https://opensource.org/licenses/BSD-3-Clause The 3-Clause BSD License
 * @link     http://php5.laurent-laville.org/reflect/
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
