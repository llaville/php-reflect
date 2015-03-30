<?php
/*
 *  Temporary NameResolver to fix https://github.com/nikic/PHP-Parser/issues/188
 *  not yet included in a public release
 *  Today (2015-03-30), latest stable version is nikic/php-parser 1.2.1
 */

namespace Bartlett\Reflect\PhpParser;

use PhpParser\NodeVisitor\NameResolver as BaseNameResolver;
use PhpParser\Node;
use PhpParser\Node\Name;
use PhpParser\Node\Expr;
use PhpParser\Node\Stmt;

class NameResolver extends BaseNameResolver
{
    public function enterNode(Node $node) {

        if ($node instanceof Stmt\Function_) {
            $this->addNamespacedName($node);
            $this->resolveSignature($node);

        } elseif ($node instanceof Stmt\ClassMethod
                  || $node instanceof Expr\Closure
        ) {
            $this->resolveSignature($node);

        } elseif ($node instanceof Node\Param
            && $node->type instanceof Name
        ) {
            // already resolved
            return;

        } else {
            return parent::enterNode($node);
        }
    }

    /** @param Stmt\Function_|Stmt\ClassMethod|Expr\Closure $node */
    private function resolveSignature($node) {
        foreach ($node->params as $param) {
            if ($param->type instanceof Name) {
                $param->type = $this->resolveClassName($param->type);
            }
        }
        if ($node->returnType instanceof Name) {
            $node->returnType = $this->resolveClassName($node->returnType);
        }
    }
}
