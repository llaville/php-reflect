<?php

declare(strict_types=1);

namespace Bartlett\Reflect\Application\Analyser;

use Bartlett\Reflect\Application\Collection\ReflectionCollection;

use PhpParser\Node;

/**
 * This analyzer collects information about
 * classes, interfaces, traits, to produce report like PHP Reflection API
 *
 * PHP version 7
 *
 * @category PHP
 * @package  bartlett/php-reflect
 * @author   Laurent Laville <pear@laurent-laville.org>
 * @license  https://opensource.org/licenses/BSD-3-Clause The 3-Clause BSD License
 * @link     http://php5.laurent-laville.org/reflect/
 */
class ReflectionAnalyser extends AbstractAnalyser
{
    public function __construct()
    {
        $this->metrics = new ReflectionCollection();
    }

    public function enterNode(Node $node)
    {
        parent::enterNode($node);

        if ($node instanceof Node\Stmt\Class_
            || $node instanceof Node\Stmt\Interface_
            || $node instanceof Node\Stmt\Trait_
        ) {
            foreach ($node->stmts as $stmt) {
                if ($stmt instanceof Node\Stmt\Property) {
                    $stmt->setAttribute(
                        'implicitlyPublic',
                        $this->isImplicitlyPublicProperty($this->tokens, $stmt)
                    );

                } elseif ($stmt instanceof Node\Stmt\ClassMethod) {
                    $stmt->setAttribute(
                        'implicitlyPublic',
                        $this->isImplicitlyPublicFunction($this->tokens, $stmt)
                    );
                    // remove class methods statements
                    unset($stmt->stmts);
                }
            }
            $node->setAttribute('fileName', $this->file);
            $this->metrics->add($node);
        } elseif ($node instanceof Node\Stmt\Function_
            || $node instanceof Node\Expr\Closure
        ) {
            if (is_array($node->stmts)) {
                foreach ($node->stmts as $stmt) {
                    // remove statements
                    unset($node->stmts);
                }
            }
            $node->setAttribute('fileName', $this->file);
            $this->metrics->add($node);
        } elseif ($node instanceof Node\Stmt\Const_) {
            $node->setAttribute('fileName', $this->file);
            $this->metrics->add($node);
        }
    }
}
