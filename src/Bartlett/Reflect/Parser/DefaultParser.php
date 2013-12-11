<?php

namespace Bartlett\Reflect\Parser;

use Bartlett\Reflect\Builder;

class DefaultParser implements ParserInterface
{
    protected $builder;
    protected $tokenMap;

    private $defaultOptions = array(
        'properties' => array(
            'use' => array(
                'file', 'startEndLines', 'docblock', 'alias'
            ),
            'namespace' => array(
                'file', 'startEndLines', 'docblock', 'alias'
            ),
            'trait' => array(
                'file', 'startEndLines', 'docblock', 'namespace',
                'parent', 'methods'
            ),
            'interface' => array(
                'file', 'startEndLines', 'docblock', 'namespace',
                'modifiers', 'parent', 'methods'
            ),
            'class' => array(
                'file', 'startEndLines', 'docblock', 'namespace',
                'modifiers', 'parent', 'methods', 'interfaces', 'package'
            ),
            'function' => array(
                'file', 'startEndLines', 'docblock', 'namespace',
                'modifiers', 'signature', 'arguments', 'ccn', 'visibility'
            ),
            'require_once' => array(
                'file', 'startEndLines', 'docblock', 'namespace', 'type'
            ),
            'require' => array(
                'file', 'startEndLines', 'docblock', 'namespace', 'type'
            ),
            'include_once' => array(
                'file', 'startEndLines', 'docblock', 'namespace', 'type'
            ),
            'include' => array(
                'file', 'startEndLines', 'docblock', 'namespace', 'type'
            ),
            'variable' => array(
                'file', 'startEndLines', 'docblock', 'namespace',
            ),
            'constant' => array(
                'file', 'line', 'docblock', 'namespace', 'value'
            ),
            'constant_encapsed_string' => array(
                'file', 'line', 'docblock', 'namespace', 'value'
            ),
        ),
    );

    /**
     * Constructs a new DefaultParser instance.
     *
     * @param Builder $builder Concrete Model object builder
     *
     * @return DefaultParser
     */
    public function __construct(Builder $builder)
    {
        $this->builder = $builder;

        $prefixTokenClass = "Bartlett\Reflect\Token\\";
        $this->tokenMap   = array(
            'T_USE'          => $prefixTokenClass . 'UseToken',
            'T_NAMESPACE'    => $prefixTokenClass . 'NamespaceToken',
            'T_TRAIT'        => $prefixTokenClass . 'TraitToken',
            'T_INTERFACE'    => $prefixTokenClass . 'InterfaceToken',
            'T_CLASS'        => $prefixTokenClass . 'ClassToken',
            'T_FUNCTION'     => $prefixTokenClass . 'FunctionToken',
            'T_REQUIRE_ONCE' => $prefixTokenClass . 'RequireOnceToken',
            'T_REQUIRE'      => $prefixTokenClass . 'RequireToken',
            'T_INCLUDE_ONCE' => $prefixTokenClass . 'IncludeOnceToken',
            'T_INCLUDE'      => $prefixTokenClass . 'IncludeToken',
            'T_VARIABLE'     => $prefixTokenClass . 'VariableToken',
            'T_LINE'         => $prefixTokenClass . 'LineToken',
            'T_FILE'         => $prefixTokenClass . 'FileToken',
            'T_DIR'          => $prefixTokenClass . 'DirToken',
            'T_FUNC_C'       => $prefixTokenClass . 'FuncCToken',
            'T_CLASS_C'      => $prefixTokenClass . 'ClassCToken',
            'T_TRAIT_C'      => $prefixTokenClass . 'TraitCToken',
            'T_METHOD_C'     => $prefixTokenClass . 'MethodCToken',
            'T_NS_C'         => $prefixTokenClass . 'NsCToken',
            'T_CONST'        => $prefixTokenClass . 'ConstToken',
            'T_CONSTANT_ENCAPSED_STRING'
                             => $prefixTokenClass . 'ConstantEncapsedStringToken',
        );
    }

    /**
     * {@inheritdoc}
     */
    public function handle($request)
    {
        $tokenStack = $request['tokens'];

        $token = $tokenStack->current();
        $tokenName = $token[0];

        if (!isset($this->tokenMap[$tokenName])) {
            // this handler cannot handle the current $request
            return false;
        }
        $tokenClass = $this->tokenMap[$tokenName];
        $text       = $token[1];
        $line       = $token[2];
        $id         = $tokenStack->key();

        $token = new $tokenClass($text, $line, $id, $tokenStack);

        static $globals = array(
            'global',
            '$GLOBALS',
            '$HTTP_SERVER_VARS',
            '$_SERVER',
            '$HTTP_GET_VARS',
            '$_GET',
            '$HTTP_POST_VARS',
            '$HTTP_POST_FILES',
            '$_POST',
            '$HTTP_COOKIE_VARS',
            '$_COOKIE',
            '$HTTP_SESSION_VARS',
            '$_SESSION',
            '$HTTP_ENV_VARS',
            '$_ENV'
        );

        extract($request['context']);

        $context = strtolower(substr($tokenName, 2));

        $options = $this->defaultOptions;

        /**
         * @link http://www.php.net/manual/en/language.constants.php
         *       Constants
         * @link http://www.php.net/manual/en/language.constants.predefined.php
         *       Magic constants
         */
        $magic = array(
            'line', 'file', 'dir',
            'func_c', 'class_c', 'trait_c', 'method_c', 'ns_c'
        );
        $magicConst = in_array($context, $magic);

        if ('const' == $context || 'constant_encapsed_string' == $context
            || $magicConst
        ) {
            $context = 'constant';
        }

        if ('use' == $context) {
            $name = $token->getName($class);
        } else {
            $name = $token->getName();
        }
        if ($name === null) {
            return;
        }
        $tmp = array();

        $inc = in_array(
            $context,
            array('require_once', 'require', 'include_once', 'include')
        );

        if (method_exists($token, 'getType')) {
            $type = $token->getType();
            $glob = in_array($type, $globals);
        } else {
            $glob = false;
        }

        if (isset($options['properties'][$context])) {
            $properties = $options['properties'][$context];
        } else {
            $properties = array();
        }

        switch ($context) {
            case 'use':
            case 'namespace':
            case 'trait':
            case 'interface':
            case 'class':
            case 'function':
            case 'require_once':
            case 'require':
            case 'include_once':
            case 'include':
            case 'variable':
            case 'constant':
                if (in_array('startEndLines', $properties)) {
                    $tmp['startLine'] = $token->getLine();
                    $tmp['endLine']   = $token->getEndLine();
                }
                $tmp['file'] = $request['filename'];

                $tmp['trait']     = ('trait'     === $context) ? $name : $trait;
                $tmp['interface'] = ('interface' === $context) ? $name : $interface;
                $tmp['class']     = ('class'     === $context) ? $name : $class;
                $tmp['function']  = ('function'  === $context) ? $name : $function;

                break;
        }

        foreach ($properties as $property) {
            $method = 'get' . ucfirst($property);
            if (method_exists($token, $method)) {
                $tmp[$property] = $token->{$method}();
            }
        }

        $pkg = $namespace === false ? '+global' : $namespace;

        $package = $this->builder->buildPackage($pkg);

        if ('class' == $context) {
            $parts = array(
                ($namespace === false ? '' : $namespace),
                $name
            );
            $element = $this->builder->buildClass(
                implode('\\', $parts)
            );

            $parent = $token->getParent();
            if (!empty($parent)) {
                if (array_key_exists($parent, $aliases)) {
                    // parent class has a namespace found in aliases
                    $parent = $aliases[ $parent ];
                    $parts  = explode('\\', $parent);

                } elseif (substr($parent, 0, 1) == '\\') {
                    // parent class is in global namespace
                    $parts  = explode('\\', $parent);

                } else {
                    $parts = array(
                        ($namespace === false ? '' : $namespace),
                        $parent
                    );
                }
                $tmp['parent'] = $this->builder->buildClass(
                    implode('\\', $parts)
                );
            }

        } elseif ('interface' == $context) {
            $parts = array(
                ($namespace === false ? '' : $namespace),
                $name
            );
            $element = $this->builder->buildInterface(
                implode('\\', $parts)
            );

        } elseif ('trait' == $context) {
            $parts = array(
                ($namespace === false ? '' : $namespace),
                $name
            );
            $element = $this->builder->buildTrait(
                implode('\\', $parts)
            );

        } elseif ('function' == $context) {
            if ($class === false && $interface === false && $trait === false) {
                $parts = array(
                    ($namespace === false ? '' : $namespace),
                    $name
                );
                // update user functions
                $tmp['closure'] = $token->isClosure();

                $element = $this->builder->buildFunction(
                    implode('\\', $parts)
                );
            } else {
                $parts = array(
                    ($namespace === false ? '' : $namespace)
                );
                if ($class !== false) {
                    array_push($parts, $class);
                } elseif ($interface !== false) {
                    array_push($parts, $interface);
                } else {
                    array_push($parts, $trait);
                }
                // class, interface or trait method
                $method = $this->builder->buildMethod(
                    implode('\\', $parts),
                    $name
                );
                $method->update($tmp);

                // update methods list
                if ($class !== false) {
                    $obj = $this->builder->buildClass(
                        implode('\\', $parts)
                    );

                } elseif ($interface !== false) {
                    $obj = $this->builder->buildInterface(
                        implode('\\', $parts)
                    );

                } else {
                    $obj = $this->builder->buildTrait(
                        implode('\\', $parts)
                    );
                }
                $obj->update(array('methods' => array($name => $method)));
            }

        } elseif ('use' == $context) {
            if ($class === false) {
                $tmp['import'] = $token->isImported();
                // use namespace
            } else {
                // use trait
            }

        } elseif ('namespace' == $context) {
            $tmp['import'] = $token->isImported();
            // namespace designed as a package
            $package = $this->builder->buildPackage($name);

        } elseif ('constant' == $context) {
            $tmp['magic']  = $magicConst;
            $tmp['namespace'] = $namespace;

            if (($class === false && $trait === false) || $magicConst) {
                // user or magic constant

                if ($magicConst) {
                    $parts = array($name);
                    $tmp['uses'] = 1;
                } else {
                    $parts = array(
                        ($namespace === false ? '' : $namespace),
                        $name
                    );
                }
                $element = $this->builder->buildConstant(
                    implode('\\', $parts)
                );

            } else {
                $parts = array(
                    ($namespace === false ? '' : $namespace),
                    $class
                );
                // class constant
                $constant = $this->builder->buildConstant(
                    implode('\\', $parts) . '::' . $name
                );
                $constant->update($tmp);

                // update constants list
                $obj = $this->builder->buildClass(
                    implode('\\', $parts)
                );
                $obj->update(array('constants' => array($name => $constant)));
            }

        } elseif ($inc === true) {
            $element = $this->builder->buildInclude($name);
        }

        if (isset($element)) {
            $element->update($tmp);
            $package->addElement($element);
        }

        return $token;
    }
}
