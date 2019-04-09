<?php

declare(strict_types=1);

namespace Bartlett\Reflect\Application\Analyser;

use PhpParser\Node;

/**
 * This analyzer collects different count metrics for code artifacts like
 * classes, methods, functions, constants or packages.
 *
 * It analyse source code like Sebastian Bergmann phploc solution
 * (https://github.com/sebastianbergmann/phploc), and give a text report
 * as follow :
 *
 * <code>
 * Directories                                         50
 * Files                                              374
 *
 * Structure
 *   Namespaces                                         1
 *   Interfaces                                        15
 *   Traits                                             0
 *   Classes                                          384
 *     Abstract Classes                                27 (7.03%)
 *     Concrete Classes                               357 (92.97%)
 *   Methods                                         3531
 *     Scope
 *       Non-Static Methods                          3435 (97.28%)
 *       Static Methods                                96 (2.72%)
 *     Visibility
 *       Public Method                               3174 (89.89%)
 *       Protected Method                             207 (5.86%)
 *       Private Method                               150 (4.25%)
 *   Functions                                          0
 *     Named Functions                                  0 (0.00%)
 *     Anonymous Functions                              0 (0.00%)
 *   Constants                                        157
 *     Global Constants                                17 (10.83%)
 *     Magic Constants                                  0 (0.00%)
 *     Class Constants                                140 (89.17%)
 * </code>
 *
 * PHP version 7
 *
 * @category PHP
 * @package  bartlett/php-reflect
 * @author   Laurent Laville <pear@laurent-laville.org>
 * @license  https://opensource.org/licenses/BSD-3-Clause The 3-Clause BSD License
 * @link     http://php5.laurent-laville.org/reflect/
 */
class StructureAnalyser extends AbstractAnalyser
{
    private $constants  = [];

    public function __construct()
    {
        $this->metrics = [
            'namespaces'            => 0,
            'interfaces'            => 0,
            'traits'                => 0,
            'classes'               => 0,
            'abstractClasses'       => 0,
            'concreteClasses'       => 0,
            'functions'             => 0,
            'namedFunctions'        => 0,
            'anonymousFunctions'    => 0,
            'methods'               => 0,
            'publicMethods'         => 0,
            'protectedMethods'      => 0,
            'privateMethods'        => 0,
            'nonStaticMethods'      => 0,
            'staticMethods'         => 0,
            'constants'             => 0,
            'classConstants'        => 0,
            'globalConstants'       => 0,
            'magicConstants'        => 0,
            'testClasses'           => 0,
            'testMethods'           => 0,
        ];
    }

    public function afterTraverse(array $nodes)
    {
        parent::afterTraverse($nodes);

        $this->metrics['namespaces']
            = count(array_unique($this->namespaces));

        $this->metrics['magicConstants']
            = count(array_unique($this->constants));
    }

    public function enterNode(Node $node)
    {
        parent::enterNode($node);

        if ($node instanceof Node\Stmt\Namespace_) {
            $this->visitNamespace($node);
        } elseif ($node instanceof Node\Stmt\Class_) {
            $this->visitClass($node);
        } elseif ($node instanceof Node\Stmt\Interface_) {
            $this->visitInterface($node);
        } elseif ($node instanceof Node\Stmt\Trait_) {
            $this->visitTrait($node);
        } elseif ($node instanceof Node\Stmt\Function_
            || $node instanceof Node\Expr\Closure
        ) {
            $this->visitFunction($node);
        } elseif ($node instanceof Node\Scalar\MagicConst) {
            $this->constants[] = $node->getName();
        } elseif ($node instanceof Node\Expr\FuncCall
            && $node->name instanceof Node\Name
        ) {
            if (strcasecmp('define', (string) $node->name) === 0) {
                $this->visitConstant($node);
            }
        }
    }

    protected function visitTrait(Node\Stmt\Trait_ $trait): void
    {
        $this->metrics['traits']++;
    }

    protected function visitInterface(Node\Stmt\Interface_ $interface): void
    {
        $this->metrics['interfaces']++;
    }

    protected function visitClass(Node\Stmt\Class_ $class): void
    {
        parent::visitClass($class);

        if ($this->testClass) {
            $this->metrics['testClasses']++;

            foreach ($class->stmts as $stmt) {
                if ($stmt instanceof Node\Stmt\ClassMethod) {
                    $this->visitMethod($stmt);
                }
            }
        } else {
            $this->metrics['classes']++;

            if ($class->isAbstract()) {
                $this->metrics['abstractClasses']++;
            } else {
                $this->metrics['concreteClasses']++;
            }

            foreach ($class->stmts as $stmt) {
                if ($stmt instanceof Node\Stmt\ClassConst) {
                    $this->metrics['classConstants']++;
                } elseif ($stmt instanceof Node\Stmt\Property) {
                    // @TODO
                } elseif ($stmt instanceof Node\Stmt\ClassMethod) {
                    $this->visitMethod($stmt);
                }
            }
        }
    }

    /**
     * Explore methods of each user classes
     * found in the current namespace.
     *
     * @param Node\Stmt\ClassMethod $method The current method explored
     *
     * @return void
     */
    protected function visitMethod(Node\Stmt\ClassMethod $method): void
    {
        if ($this->testClass) {
            if (strpos($method->name, 'test') === 0) {
                $this->metrics['testMethods']++;
            } elseif (strpos($method->getDocComment(), '@test')) {
                $this->metrics['testMethods']++;
            }
            return;
        }
        $this->metrics['methods']++;

        if ($method->isPrivate()) {
            $this->metrics['privateMethods']++;
        } elseif ($method->isProtected()) {
            $this->metrics['protectedMethods']++;
        } else {
            $this->metrics['publicMethods']++;
        }

        if ($method->isStatic()) {
            $this->metrics['staticMethods']++;
        } else {
            $this->metrics['nonStaticMethods']++;
        }
    }

    /**
     * Explore user functions found in the current namespace.
     *
     * @param Node $function The current user function explored
     *
     * @return void
     */
    protected function visitFunction(Node $function): void
    {
        $this->metrics['functions']++;

        if ($function instanceof Node\Expr\Closure) {
            $this->metrics['anonymousFunctions']++;
        } else {
            $this->metrics['namedFunctions']++;
        }
    }

    /**
     * Explore user constants found in the current namespace.
     *
     * @param Node $node The current node explored
     *
     * @return void
     */
    protected function visitConstant(Node $node): void
    {
        $this->metrics['globalConstants']++;
    }
}
