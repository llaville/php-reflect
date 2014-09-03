<?php
/**
 * Complex model object builder.
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

namespace Bartlett\Reflect;

use Bartlett\Reflect\Model\PackageModel;
use Bartlett\Reflect\Model\ClassModel;
use Bartlett\Reflect\Model\MethodModel;
use Bartlett\Reflect\Model\FunctionModel;
use Bartlett\Reflect\Model\ConstantModel;
use Bartlett\Reflect\Model\PropertyModel;
use Bartlett\Reflect\Model\IncludeModel;
use Bartlett\Reflect\Model\ParameterModel;
use Bartlett\Reflect\Model\DependencyModel;

use PhpParser\NodeVisitorAbstract;
use PhpParser\Node;

/**
 * Concrete Builder.
 *
 * @category PHP
 * @package  PHP_Reflect
 * @author   Laurent Laville <pear@laurent-laville.org>
 * @license  http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version  Release: @package_version@
 * @link     http://php5.laurent-laville.org/reflect/
 * @since    Class available since Release 2.0.0RC1
 */
class Builder extends NodeVisitorAbstract
{
    private $packages     = array();
    private $classes      = array();
    private $interfaces   = array();
    private $traits       = array();
    private $functions    = array();
    private $constants    = array();
    private $includes     = array();
    private $dependencies = array();
    private $aliases      = array();
    private $file;

    /**
     * @var null|Name Current namespace
     */
    private $namespace;

    public function setCurrentFile($path)
    {
        $this->file = $path;
    }

    public function beforeTraverse(array $nodes)
    {
        $this->namespace = '+global';
    }

    public function enterNode(Node $node)
    {
        if ($node instanceof \PhpParser\Node\Stmt\Namespace_) {
            if (! isset($node->name)) {
                echo "Warning: No name node in ", $this->file, ' on line ', $node->getLine(), "\n";
                $node->name = new Node\Name(uniqid());
            }                
            $this->namespace = $node->name->__toString();
        }

        if ($node instanceof \PhpParser\Node\Stmt\Namespace_
            || $node instanceof \PhpParser\Node\Expr\Include_
            || $node instanceof \PhpParser\Node\Stmt\Class_
            || $node instanceof \PhpParser\Node\Stmt\Interface_
            || $node instanceof \PhpParser\Node\Stmt\Trait_
            || $node instanceof \PhpParser\Node\Stmt\ClassConst
        ) {
            // these nodes are processed in method leaveNode()
            return;
        }

        if ($node instanceof \PhpParser\Node\Expr\Assign
            && $node->expr instanceof \PhpParser\Node\Expr\New_
        ) {
            $var   = $node->var;
            $class = $node->expr->class;

            if ($class instanceof \PhpParser\Node\Name) {
                if ($var instanceof \PhpParser\Node\Expr\PropertyFetch) {
                    $this->aliases[$var->var->name .'_'. $var->name] = $class->__toString();

                } elseif ($var instanceof \PhpParser\Node\Expr\Variable) {
                    $this->aliases[$var->name] = $class->__toString();
                }
            }
        }

        $doc = $node->getDocComment();

        $nodeAttributes = array(
            'file'       => $this->file,
            'startLine'  => $node->getAttribute('startLine'),
            'endLine'    => $node->getAttribute('endLine'),
        );
        if ($doc instanceof \PhpParser\Comment) {
            $nodeAttributes['docComment'] = $doc->getText();
        }

        if ($node instanceof \PhpParser\Node\Expr\MethodCall) {

            $this->parseMethodCall($node, $nodeAttributes);

        } elseif ($node instanceof \PhpParser\Node\Expr\New_) {

            $this->parseNewStatement($node, $nodeAttributes);

        } elseif ($node instanceof \PhpParser\Node\Expr\FuncCall
            && $node->name instanceof \PhpParser\Node\Name
        ) {
            $nodeAttributes['arguments']
                = $this->parseArguments($node->args);

            $this->parseInternalFunction($node, $nodeAttributes);

        } elseif ($node instanceof \PhpParser\Node\Stmt\Function_) {

            $this->parseUserFunction($node, $nodeAttributes);

        } elseif ($node instanceof \PhpParser\Node\Expr\Closure) {

            $nodeAttributes['closure'] = true;
            $this->parseUserFunction($node, $nodeAttributes);

        } elseif ($node instanceof \PhpParser\Node\Stmt\Const_) {
            // const is used outside of object scope

            $const = $node->consts[0];
            $qualifiedName = $const->namespacedName->__toString();
            $nodeAttributes['value'] = $const->value->value;
            $model = $this->buildConstant($qualifiedName, $nodeAttributes);

            $attributes = array('constants' => array($model));
            $package = $this->buildPackage($this->namespace);
            $package->update($attributes);

        } elseif ($node instanceof \PhpParser\Node\Expr\ConstFetch
            || $node instanceof \PhpParser\Node\Scalar\MagicConst
        ) {
            if ($node instanceof \PhpParser\Node\Scalar\MagicConst) {
                $nodeAttributes['magic'] = true;
                $qualifiedName = $node->getName();
            } else {
                $qualifiedName = $node->name->__toString();
            }

            $model = $this->buildConstant($qualifiedName, $nodeAttributes);

            $attributes = array('constants' => array($model));
            $package = $this->buildPackage($this->namespace);
            $package->update($attributes);
        }
    }

    public function leaveNode(Node $node)
    {
        $doc = $node->getDocComment();

        $nodeAttributes = array(
            'startLine'  => $node->getAttribute('startLine'),
            'endLine'    => $node->getAttribute('endLine'),
        );
        if ($doc instanceof \PhpParser\Comment) {
            $nodeAttributes['docComment'] = $doc->getText();
        }

        if ($node instanceof \PhpParser\Node\Stmt\Namespace_) {

            $this->buildPackage($node->name->__toString(), $nodeAttributes);

        } elseif ($node instanceof \PhpParser\Node\Expr\Include_) {

            if ($node->type === 1) {
                $type = 'include';
            } elseif ($node->type === 2) {
                $type = 'include_once';
            } elseif ($node->type === 3) {
                $type = 'require';
            } else {
                $type = 'require_once';
            }
            $prettyPrinter = new \PhpParser\PrettyPrinter\Standard;
            $filepath = trim(
                $prettyPrinter->prettyPrint(array($node->expr)),
                ';'
            );
            $nodeAttributes['type'] = $type;
            $model = $this->buildInclude($filepath, $nodeAttributes);

            $attributes = array('includes' => array($model));
            $package = $this->buildPackage($this->namespace);
            $package->update($attributes);

        } elseif ($node instanceof \PhpParser\Node\Stmt\Class_
            || $node instanceof \PhpParser\Node\Stmt\Interface_
            || $node instanceof \PhpParser\Node\Stmt\Trait_
        ) {
            // @link http://www.php.net/manual/en/language.oop5.interfaces.php

            $qualifiedClassName = $node->namespacedName->__toString();

            if ($node->extends) {
                $parent = $node->extends;
                if (is_array($parent)) {
                    // multiple direct inheritance is not allowed in PHP, but PHP-Parser ?
                    $parent = $parent[0];
                }
                $parent = $parent->__toString();

                if (isset($this->classes[$parent])) {
                    // load the parent model
                    $parent = $this->classes[$parent];
                }

            } else {
                $parent = false;
            }

            $interfaces = array();
            if ($node->implements
                && !empty($node->implements)
            ) {
                foreach ($node->implements as $interface) {
                    $interfaces[] = $interface->__toString();
                }
            }

            $visibility = function ($stmt) {
                $visibility = null;

                if ($stmt instanceof \PhpParser\Node\Stmt\Property
                    || $stmt instanceof \PhpParser\Node\Stmt\ClassMethod
                ) {
                    if ($stmt->isProtected()) {
                        $visibility = 'protected';
                    } elseif ($stmt->isPrivate()) {
                        $visibility = 'private';
                    } else {
                        $visibility = 'public';
                    }
                }
                return $visibility;
            };

            $modifiers = function ($stmt) {
                $modifiers = null;

                if ($stmt instanceof \PhpParser\Node\Stmt\Class_
                    || $stmt instanceof \PhpParser\Node\Stmt\ClassMethod
                ) {
                    if ($stmt->isFinal()) {
                        $modifiers[] = 'final';
                    }
                    if ($stmt->isAbstract()) {
                        $modifiers[] = 'abstract';
                    }
                }
                if ($stmt instanceof \PhpParser\Node\Stmt\ClassMethod
                    || $stmt instanceof \PhpParser\Node\Stmt\Property
                ) {
                    if ($stmt->isStatic()) {
                        $modifiers[] = 'static';
                    }
                }
                return $modifiers;
            };

            $constants  = array();
            $properties = array();
            $methods    = array();

            foreach ($node->stmts as $stmt) {

                $doc = $stmt->getDocComment();

                $stmtAttributes = array(
                    'file'       => $this->file,
                    'startLine'  => $stmt->getAttribute('startLine'),
                    'endLine'    => $stmt->getAttribute('endLine'),
                );
                if ($doc instanceof \PhpParser\Comment) {
                    $stmtAttributes['docComment'] = $doc->getText();
                }

                if (is_array($attr = $modifiers($stmt))) {
                    $stmtAttributes['modifiers'] = $attr;
                }
                if (is_string($attr = $visibility($stmt))) {
                    $stmtAttributes['visibility'] = $attr;
                }

                if ($stmt instanceof \PhpParser\Node\Stmt\ClassConst) {
                    // @link http://www.php.net/manual/en/language.oop5.constants.php

                    foreach ($stmt->consts as $const) {
                        if ($const->value instanceof \PhpParser\Node\Scalar) {
                            $value = $const->value->value;
                        } else {
                            $value = null;
                        }

                        $constants[$const->name] = new ConstantModel(
                            $const->name,
                            array(
                                'value'     => $value,
                                'startLine' => $const->getAttribute('startLine'),
                                'endLine'   => $const->getAttribute('endLine'),
                            )
                        );
                    }

                } elseif ($stmt instanceof \PhpParser\Node\Stmt\Property) {
                    // @link http://www.php.net/manual/en/language.oop5.properties.php

                    $props = $stmt->props;
                    if ($props[0]->default) {
                        $stmtAttributes['value'] = $props[0]->default->value;
                    } else {
                        $stmtAttributes['value'] = null;
                    }
                    $properties[] = new PropertyModel(
                        $qualifiedClassName,
                        $props[0]->name,
                        $stmtAttributes
                    );

                } elseif ($stmt instanceof \PhpParser\Node\Stmt\ClassMethod) {

                    $stmtAttributes['arguments']
                        = $this->parseFunctionArguments($stmt->params);

                    $methods[$stmt->name] = new MethodModel(
                        $qualifiedClassName,
                        $stmt->name,
                        $stmtAttributes
                    );
                }
            }

            $deps = array();
            // interfaces implemented are collected as interface dependency
            foreach ($interfaces as $interfaceName) {
                $dep = $this->buildDependency($interfaceName, $nodeAttributes);
                $dep->incCalls();
                if ($dep->getCalls() == 1) {
                    $deps[] = $dep;
                }
            }
            if (!empty($deps)) {
                $attributes = array('interfaces' => $deps);
                $package = $this->buildPackage($this->namespace);
                $package->update($attributes);
            }

            $nodeAttributes['interfaces'] = $interfaces;
            $nodeAttributes['parent']     = $parent;
            $nodeAttributes['constants']  = $constants;
            $nodeAttributes['properties'] = $properties;
            $nodeAttributes['methods']    = $methods;

            if ($node instanceof \PhpParser\Node\Stmt\Interface_) {
                $nodeAttributes['interface'] = true;
                $model = $this->buildInterface($qualifiedClassName, $nodeAttributes);
                $attributes = array('interfaces' => array($model));

            } elseif ($node instanceof \PhpParser\Node\Stmt\Trait_) {
                $nodeAttributes['trait'] = true;
                $model = $this->buildTrait($qualifiedClassName, $nodeAttributes);
                $attributes = array('traits' => array($model));

            } else {
                if (is_array($modifiers = $modifiers($node))) {
                    $nodeAttributes['modifiers'] = $modifiers;
                }
                $model = $this->buildClass($qualifiedClassName, $nodeAttributes);
                $attributes = array('classes' => array($model));
            }

            $package = $this->buildPackage($this->namespace);
            $package->update($attributes);
        }
    }

    /**
     * This method parses a method-call-expression.
     *
     * @param object AST Node Expression
     */
    protected function parseMethodCall($node, $nodeAttributes)
    {
        $var = $node->var;

        if (!is_string($node->name)) {
            // indirect method call
            return;
        }

        if ($var instanceof \PhpParser\Node\Expr\PropertyFetch) {
            if (!isset($this->aliases[$var->var->name .'_'. $var->name])) {
                // class name resolver failure
                return;
            }
            $qualifiedClassName = $this->aliases[$var->var->name .'_'. $var->name];

        } elseif ($var instanceof \PhpParser\Node\Expr\Variable) {
            if (!isset($this->aliases[$var->name])) {
                // class name resolver failure
                return;
            }
            $qualifiedClassName = $this->aliases[$var->name];
        }

        if (!isset($qualifiedClassName)) {
            // stop here if class name resolver failed
            return;
        }

        $dep = $this->buildDependency(
            $qualifiedClassName . '::' . $node->name,
            $nodeAttributes
        );
        $dep->incCalls();

        if ($dep->getCalls() > 1) {
            return;
        }
        $attributes = array('dependencies' => array($dep));

        $package = $this->buildPackage($this->namespace);
        $package->update($attributes);
    }

    /**
     * This method parses a new-statement.
     *
     * <code>
     *
     *  new bar\Baz();
     *
     *  new Foo();
     *
     *  new Bar;
     *
     * (new Foo)->bar();
     *
     * ( new Foo ('Baz') )->bar();
     *
     * </code>
     *
     * @param object AST Node Expression
     * @link  http://www.php.net/manual/en/language.oop5.php
     */
    protected function parseNewStatement($node, $nodeAttributes)
    {
        if (!$node->class instanceof \PhpParser\Node\Name) {
            return;
        }
        $qualifiedClassName = $node->class->__toString();

        $dep = $this->buildDependency($qualifiedClassName, $nodeAttributes);
        $dep->incCalls();

        if ($dep->getCalls() > 1) {
            return;
        }
        $attributes = array('classes' => array($dep));
        $package = $this->buildPackage($this->namespace);
        $package->update($attributes);
    }

    /**
     * Parses user constants.
     *
     * <code>
     *
     * define("FOO",     "something");
     * define("FOO2",    "something else");
     * define("FOO_BAR", "something more");
     *
     * </code>
     *
     * @param object $exprList AST node expressions for define arguments
     *
     * @return object AST Node Expression
     * @link   http://www.php.net/manual/en/language.constants.php
     */
    protected function parseUserConstant($node, $nodeAttributes)
    {
        $qualifiedName = $node->args[0]->value->value;
        $nodeAttributes['value'] = $node->args[1]->value->value;
        $model = $this->buildConstant($qualifiedName, $nodeAttributes);

        $attributes = array('constants' => array($model));
        $package = $this->buildPackage($this->namespace);
        $package->update($attributes);
    }

    /**
     * This method parses any user function.
     *
     * @link http://www.php.net/manual/en/functions.user-defined.php
     */
    protected function parseUserFunction($node, $nodeAttributes)
    {
        if (isset($node->name)) {
            $qualifiedName = $node->namespacedName->__toString();
        } else {
            $qualifiedName = sprintf(
                '%s\\closure-%d-%d',
                $this->namespace,
                $nodeAttributes['startLine'],
                $nodeAttributes['endLine']
            );
        }

        $nodeAttributes['arguments']
            = $this->parseFunctionArguments($node->params);

        $model = $this->buildFunction($qualifiedName, $nodeAttributes);

        $attributes = array('functions' => array($model));
        $package = $this->buildPackage($this->namespace);
        $package->update($attributes);
    }

    /**
     * This method parses any internal function.
     *
     * @return object AST Node Expression
     * @link    http://www.php.net/manual/en/functions.internal.php
     */
    protected function parseInternalFunction($node, $nodeAttributes)
    {
        $functionName = $node->name->__toString();

        if ('define' == $functionName) {
            $this->parseUserConstant($node, $nodeAttributes);
        }

        $hash = array(
            $nodeAttributes['startLine'],
            $nodeAttributes['endLine'],
            $nodeAttributes['file']
        );
        $nodeAttributes['hash'] = sha1(serialize($hash));

        $dep = $this->buildDependency($functionName, $nodeAttributes);
        $dep->incCalls();

        if ($dep->getCalls() > 1) {
            return;
        }
        $attributes = array('dependencies' => array($dep));
        $package = $this->buildPackage($this->namespace);
        $package->update($attributes);
    }

    /**
     * @link http://www.php.net/manual/en/functions.arguments.php
     */
    protected function parseFunctionArguments($args)
    {
        $params = array();
        foreach ($args as $param) {
            $attr = array(
                'position'  => count($params),
                'startLine' => $param->getAttribute('startLine'),
                'endLine'   => $param->getAttribute('endLine'),
                'byRef'     => $param->byRef,
                'typeHint'  => $param->type instanceof \PhpParser\Node\Name
                    ? $param->type->__toString() : $param->type,
            );
            if ($param->default) {
                $prettyPrinter = new \PhpParser\PrettyPrinter\Standard;
                $attr['defaultValue'] = trim(
                    $prettyPrinter->prettyPrint(
                        array($param->default)
                    ),
                    ';'
                );
            }
            $params[] = new ParameterModel($param->name, $attr);
        }
        return $params;
    }

    /**
     * This method parses arguments of any internal function.
     *
     * @return array
     */
    protected function parseArguments($args)
    {
        $params = array();
        foreach ($args as $param) {

            $node = $param->value;

            $typeClass = $node->getType();

            if (in_array($typeClass, array('Scalar_String', 'Scalar_Encapsed'))) {
                $value = $node->value;
            }
            elseif ('Expr_Variable' == $typeClass) {
                $value = $node->name;
            }
            else {
                $value = '';
            }

            $params[] = array(
                'position'  => count($params),
                'startLine' => $param->getAttribute('startLine'),
                'endLine'   => $param->getAttribute('endLine'),
                'byRef'     => $param->byRef,
                'type'      => $typeClass,
                'value'     => $value
            );
        }
        return $params;
    }

    /**
     * Build a unique package model defined by its qualified name.
     *
     * @param string $qualifiedName Full qualified name of a package/namespace
     *
     * @return PackageModel
     */
    public function buildPackage($qualifiedName, array $attributes = array())
    {
        if (!isset($this->packages[$qualifiedName])) {
            $model = new PackageModel($qualifiedName, $attributes);
            $model->setFile($this->file);
            $this->packages[$qualifiedName] = $model;
        }
        return $this->packages[$qualifiedName];
    }

    /**
     * Build a unique class model defined by its qualified name.
     *
     * @param string $qualifiedName Full qualified name of a class
     *
     * @return ClassModel
     */
    public function buildClass($qualifiedName, array $attributes = array())
    {
        if (!isset($this->classes[$qualifiedName])) {
            $model = new ClassModel($qualifiedName, $attributes);
            $model->setFile($this->file);
            $this->classes[$qualifiedName] = $model;
        }
        return $this->classes[$qualifiedName];
    }

    /**
     * Build a unique interface model defined by its qualified name.
     *
     * @param string $qualifiedName Full qualified name of an interface
     *
     * @return ClassModel
     */
    public function buildInterface($qualifiedName, array $attributes = array())
    {
        if (!isset($this->interfaces[$qualifiedName])) {
            $model = new ClassModel($qualifiedName, $attributes);
            $model->setFile($this->file);
            $this->interfaces[$qualifiedName] = $model;
        }
        return $this->interfaces[$qualifiedName];
    }

    /**
     * Build a unique trait model defined by its qualified name.
     *
     * @param string $qualifiedName Full qualified name of a trait
     *
     * @return ClassModel
     */
    public function buildTrait($qualifiedName, array $attributes = array())
    {
        if (!isset($this->traits[$qualifiedName])) {
            $model = new ClassModel($qualifiedName, $attributes);
            $model->setFile($this->file);
            $this->traits[$qualifiedName] = $model;
        }
        return $this->traits[$qualifiedName];
    }

    /**
     * Build a unique function model defined by its qualified name.
     *
     * @param string $qualifiedName Full qualified name of a function
     *
     * @return FunctionModel
     */
    public function buildFunction($qualifiedName, array $attributes = array())
    {
        if (!isset($this->functions[$qualifiedName])) {
            $model = new FunctionModel($qualifiedName, $attributes);
            $model->setFile($this->file);
            $this->functions[$qualifiedName] = $model;
        }
        return $this->functions[$qualifiedName];
    }

    /**
     * Build a unique constant model defined by its qualified name.
     *
     * @param string $qualifiedName Full qualified name of a constant
     *
     * @return ConstantModel
     */
    public function buildConstant($qualifiedName, array $attributes = array())
    {
        if (!isset($this->constants[$qualifiedName])) {
            $model = new ConstantModel($qualifiedName, $attributes);
            $model->setFile($this->file);
            $this->constants[$qualifiedName] = $model;
        }
        return $this->constants[$qualifiedName];
    }

    /**
     * Build a unique include model defined by its path.
     *
     * @param string $path Path to the file to include
     *
     * @return IncludeModel
     */
    public function buildInclude($path, array $attributes = array())
    {
        if (!isset($this->includes[$path])) {
            $model = new IncludeModel($path, $attributes);
            $model->setFile($this->file);
            $this->includes[$path] = $model;
        }
        return $this->includes[$path];
    }

    /**
     * Build a unique dependency model defined by its component qualified name.
     *
     * @param string $qualifiedName Full qualified name of a dependency
     *
     * @return DependencyModel
     */
    public function buildDependency($qualifiedName, array $attributes = array())
    {
        if (!isset($attributes['hash'])) {
            $attributes['hash'] = '';
        }

        if (!isset($this->dependencies[$qualifiedName . $attributes['hash']])) {
            $model = new DependencyModel($qualifiedName, $attributes);
            $model->setFile($this->file);
            $this->dependencies[$qualifiedName . $attributes['hash']] = $model;
        }
        return $this->dependencies[$qualifiedName . $attributes['hash']];
    }

    /**
     * Returns list of packages built.
     *
     * @return array Array of PackageModel object
     */
    public function getPackages()
    {
        return $this->packages;
    }

    /**
     * Returns list of classes built.
     *
     * @return array Array of ClassModel object
     */
    public function getClasses()
    {
        return $this->classes;
    }

    /**
     * Returns list of interfaces built.
     *
     * @return array Array of ClassModel object
     */
    public function getInterfaces()
    {
        return $this->interfaces;
    }

    /**
     * Returns list of traits built.
     *
     * @return array Array of ClassModel object
     */
    public function getTraits()
    {
        return $this->traits;
    }

    /**
     * Returns list of functions built.
     *
     * @return array Array of FunctionModel object
     */
    public function getFunctions()
    {
        return $this->functions;
    }

    /**
     * Returns list of constants built.
     *
     * @return array Array of ConstantModel object
     */
    public function getConstants()
    {
        return $this->constants;
    }

    /**
     * Returns list of includes built.
     *
     * @return array Array of IncludeModel object
     */
    public function getIncludes()
    {
        return $this->includes;
    }

    /**
     * Returns list of dependencies built.
     *
     * @return array Array of DependencyModel object
     */
    public function getDependencies()
    {
        return $this->dependencies;
    }
}
