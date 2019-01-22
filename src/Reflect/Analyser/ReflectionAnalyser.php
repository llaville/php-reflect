<?php

declare(strict_types=1);

/**
 * The Reflect Reflection Analyser.
 *
 * @category PHP
 * @package  PHP_Reflect
 * @author   Laurent Laville <pear@laurent-laville.org>
 * @license  http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version  GIT: $Id$
 * @link     http://php5.laurent-laville.org/reflect/
 */

namespace Bartlett\Reflect\Analyser;

use Bartlett\Reflect\Collection\ReflectionCollection;

use PhpParser\Node;

/**
 * This analyzer collects information about
 * classes, interfaces, traits, to produce report like PHP Reflection API
 *
 * @category PHP
 * @package  PHP_Reflect
 * @author   Laurent Laville <pear@laurent-laville.org>
 * @license  http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version  Release: @package_version@
 * @link     http://php5.laurent-laville.org/reflect/
 * @since    Class available since Release 3.0.0-alpha1
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
