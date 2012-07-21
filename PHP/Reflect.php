<?php
/**
 * Copyright (c) 2011-2012, Laurent Laville <pear@laurent-laville.org>
 *
 * Credits to Sebastian Bergmann on base concept from phpunit/PHP_Token_Stream
 *
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions
 * are met:
 *
 *     * Redistributions of source code must retain the above copyright
 *       notice, this list of conditions and the following disclaimer.
 *     * Redistributions in binary form must reproduce the above copyright
 *       notice, this list of conditions and the following disclaimer in the
 *       documentation and/or other materials provided with the distribution.
 *     * Neither the name of the authors nor the names of its contributors
 *       may be used to endorse or promote products derived from this software
 *       without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS"
 * AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE
 * IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE
 * ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR CONTRIBUTORS
 * BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR
 * CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF
 * SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS
 * INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN
 * CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE)
 * ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 *
 * PHP version 5
 *
 * @category PHP
 * @package  PHP_Reflect
 * @author   Laurent Laville <pear@laurent-laville.org>
 * @license  http://www.opensource.org/licenses/bsd-license.php  BSD
 * @version  SVN: $Id$
 * @link     http://php5.laurent-laville.org/reflect/
 */

/**
 * PHP_Reflect adds the ability to reverse-engineer
 * classes, interfaces, functions, constants and more.
 *
 * @category PHP
 * @package  PHP_Reflect
 * @author   Laurent Laville <pear@laurent-laville.org>
 * @license  http://www.opensource.org/licenses/bsd-license.php  BSD
 * @version  Release: @package_version@
 * @link     http://php5.laurent-laville.org/reflect/
 */
class PHP_Reflect implements ArrayAccess
{
    const NAMESPACES_WITHOUT_IMPORT = '1';
    const NAMESPACES_ONLY_IMPORT    = '2';
    const NAMESPACES_ALL            = '3';

    /**
     * Support for interface ArrayAccess
     * @var  array
     * @link http://www.php.net/manual/en/class.arrayaccess.php
     */
    private $_container;

    /**
     * @var array
     */
    protected static $customTokens = array(
        '(' => 'T_OPEN_BRACKET',
        ')' => 'T_CLOSE_BRACKET',
        '[' => 'T_OPEN_SQUARE',
        ']' => 'T_CLOSE_SQUARE',
        '{' => 'T_OPEN_CURLY',
        '}' => 'T_CLOSE_CURLY',
        ';' => 'T_SEMICOLON',
        '.' => 'T_DOT',
        ',' => 'T_COMMA',
        '=' => 'T_EQUAL',
        '<' => 'T_LT',
        '>' => 'T_GT',
        '+' => 'T_PLUS',
        '-' => 'T_MINUS',
        '*' => 'T_MULT',
        '/' => 'T_DIV',
        '?' => 'T_QUESTION_MARK',
        '!' => 'T_EXCLAMATION_MARK',
        ':' => 'T_COLON',
        '"' => 'T_DOUBLE_QUOTES',
        '@' => 'T_AT',
        '&' => 'T_AMPERSAND',
        '%' => 'T_PERCENT',
        '|' => 'T_PIPE',
        '$' => 'T_DOLLAR',
        '^' => 'T_CARET',
        '~' => 'T_TILDE',
        '`' => 'T_BACKTICK'
    );

    /**
     * @var array
     */
    protected $tokens = array();

    /**
     * @var array
     */
    protected $parserToken;

    /**
     * @var array
     */
    public $options;

    /**
     * @var string
     */
    protected $filename;

    /**
     * @var array
     */
    protected $linesOfCode = array('loc' => 0, 'cloc' => 0, 'ncloc' => 0);

    /**
     * Class constructor
     *
     * @param array $options (OPTIONAL) Configure options
     *
     * @throws RuntimeException
     */
    public function __construct($options = NULL)
    {
        $defaultOptions = array(
            // default containers to store results from parsing
            'containers' => array(
                'use'          => 'namespaces',
                'namespace'    => 'namespaces',
                'trait'        => 'traits',
                'interface'    => 'interfaces',
                'class'        => 'classes',
                'function'     => 'functions',
                'require_once' => 'includes',
                'require'      => 'includes',
                'include_once' => 'includes',
                'include'      => 'includes',
                'variable'     => 'globals'
            ),
            // properties for each component to provide on final result
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
                    'keywords', 'parent', 'methods'
                ),
                'class' => array(
                    'file', 'startEndLines', 'docblock', 'namespace',
                    'keywords', 'parent', 'methods', 'interfaces', 'package'
                ),
                'function' => array(
                    'file', 'startEndLines', 'docblock', 'namespace',
                    'keywords', 'signature', 'arguments', 'ccn'
                ),
                'require_once' => array(
                    'file', 'startEndLines', 'docblock', 'namespace',
                ),
                'require' => array(
                    'file', 'startEndLines', 'docblock', 'namespace',
                ),
                'include_once' => array(
                    'file', 'startEndLines', 'docblock', 'namespace',
                ),
                'include' => array(
                    'file', 'startEndLines', 'docblock', 'namespace',
                ),
                'variable' => array(
                    'file', 'startEndLines', 'docblock', 'namespace',
                ),
            ),
        );

        $this->options = $defaultOptions;
        if (NULL !== $options) {
            if (is_array($options)) {
                foreach ($options as $key => $values) {
                    $this->options[$key] = array_merge(
                        $defaultOptions[$key], $values
                    );
                }
            } else {
                throw new RuntimeException('Invalid options');
            }
        }

        // default parsers for interfaces, classes, functions, includes
        $this->parserToken = array(
            'T_USE'    => array(
                'PHP_Reflect_Token_USE', array($this, 'parseToken')
            ),
            'T_NAMESPACE'    => array(
                'PHP_Reflect_Token_NAMESPACE', array($this, 'parseToken')
            ),
            'T_TRAIT'    => array(
                'PHP_Reflect_Token_TRAIT', array($this, 'parseToken')
            ),
            'T_INTERFACE'    => array(
                'PHP_Reflect_Token_INTERFACE', array($this, 'parseToken')
            ),
            'T_CLASS'        => array(
                'PHP_Reflect_Token_CLASS', array($this, 'parseToken')
            ),
            'T_FUNCTION'     => array(
                'PHP_Reflect_Token_FUNCTION', array($this, 'parseToken')
            ),
            'T_REQUIRE_ONCE' => array(
                'PHP_Reflect_Token_REQUIRE_ONCE', array($this, 'parseToken')
            ),
            'T_REQUIRE'      => array(
                'PHP_Reflect_Token_REQUIRE', array($this, 'parseToken')
            ),
            'T_INCLUDE_ONCE' => array(
                'PHP_Reflect_Token_INCLUDE_ONCE', array($this, 'parseToken')
            ),
            'T_INCLUDE'      => array(
                'PHP_Reflect_Token_INCLUDE', array($this, 'parseToken')
            ),
            'T_VARIABLE'     => array(
                'PHP_Reflect_Token_VARIABLE', array($this, 'parseToken')
            ),
        );
    }

    /**
     * Connect additionnal tokens for parsing
     *
     * @param string $tokenName  Token name T_ prefixed
     * @param string $tokenClass Token class corresponding
     * @param mixed  $callback   Function to connect to token for parsing
     *
     * @return void
     * @throws RuntimeException
     */
    public function connect($tokenName, $tokenClass, $callback)
    {
        if (!class_exists($tokenClass, TRUE)) {
            throw new RuntimeException(
                "Invalid token name provided. " .
                "Given '" . (string)$tokenName . "'"
            );
        }
        if (!is_callable($callback)) {
            throw new RuntimeException(
                "Cannot connect to function provided"
            );
        }
        $this->parserToken[$tokenName] = array($tokenClass, $callback);
    }

    /**
     * Scans the source for sequences of characters and converts them into a
     * stream of tokens.
     *
     * @param string $sourceCode Filename or raw php code line
     *
     * @return array
     * @throws RuntimeException
     */
    public function scan($sourceCode)
    {
        if (is_file($sourceCode)) {
            $this->filename = $sourceCode;
            $sourceCode     = file_get_contents($sourceCode);
        } elseif (!is_string($sourceCode)) {
            throw new RuntimeException('sourceCode wrong parameter');
        }

        $line = 1;
        $this->tokens = token_get_all($sourceCode);

        foreach ($this->tokens as $id => $token) {

            if (is_array($token)) {
                $text      = $token[1];
                $tokenName = token_name($token[0]);
            } else {
                $text      = $token;
                $tokenName = self::$customTokens[$token];
                $this->tokens[$id] = array(1 => $text);
            }
            $this->tokens[$id][2] = $line;
            $this->tokens[$id][0] = $tokenName;
            $lines = substr_count($text, "\n");
            $line += $lines;

            if ('T_HALT_COMPILER' == $tokenName) {
                break;

            } elseif ($tokenName == 'T_COMMENT'
                || $tokenName == 'T_DOC_COMMENT'
            ) {
                $this->linesOfCode['cloc'] += $lines + 1;
            }
        }

        $this->linesOfCode['loc'] = substr_count($sourceCode, "\n");
        $this->linesOfCode['ncloc']
            = $this->linesOfCode['loc'] - $this->linesOfCode['cloc'];

        $this->parse();

        return $this->tokens;
    }

    /**
     * Magic methods to get informations on parsing results about
     * includes, interfaces, classes, functions, constants
     *
     * @param string $name Method name invoked
     * @param array  $args Method arguments provided
     *
     * @return array
     * @throws RuntimeException
     */
    public function __call($name, $args)
    {
        $methods = array_map(
            'ucfirst',
            array_unique(array_values($this->options['containers']))
        );
        $pattern = '/get' .
            '(?>(' . implode('|', $methods) . '))/';
        if (preg_match($pattern, $name, $matches)) {
            $container = strtolower($matches[1]{0}) . substr($matches[1], 1);

            if ($container == 'namespaces') {
                $namespaces = null;

                $option = (isset($args[0]) && is_string($args[0]))
                    ? $args[0] : self::NAMESPACES_WITHOUT_IMPORT;

                $tmp = $this->offsetGet($container);

                if (is_array($tmp)) {
                    $namespaces = array();

                    switch ($option) {
                    case self::NAMESPACES_ONLY_IMPORT :
                        $import = true;
                        break;
                    case self::NAMESPACES_ALL :
                        return $tmp;
                    case self::NAMESPACES_WITHOUT_IMPORT :
                    default:
                        $import = false;
                        break;
                    }

                    foreach ($tmp as $name => $data) {
                        if ($data['import'] == $import) {
                            $namespaces[$name] = $data;
                        }
                    }
                }
                return $namespaces;
            } else {
                $namespace = (isset($args[0]) && is_string($args[0]))
                    ? $args[0] : false;
                if ($namespace === FALSE) {
                    // get data from all namespaces
                    return $this->offsetGet($container);
                } else {
                    // get data from specified namespace
                    if ($this->offsetExists(array($container => $namespace))) {
                        return $this->offsetGet(array($container => $namespace));
                    } else {
                        return array();
                    }
                }
            }
        } else {
            throw new RuntimeException(
                "Invalid method. Given '$name'"
            );
        }
    }

    /**
     * Gets the names of all files that have been included
     * using include(), include_once(), require() or require_once().
     *
     * Parameter $categorize set to TRUE causing this function to return a
     * multi-dimensional array with categories in the keys of the first dimension
     * and constants and their values in the second dimension.
     *
     * Parameter $category allow to filter following specific inclusion type
     *
     * @param bool   $categorize OPTIONAL
     * @param string $category   OPTIONAL Either 'require_once', 'require',
     *                                           'include_once', 'include'
     * @param string $namespace  OPTIONAL Default is global namespace
     *
     * @return array
     */
    public function getIncludes($categorize = FALSE, $category = NULL,
        $namespace = FALSE)
    {
        if ($namespace === FALSE) {
            // global namespace
            $ns = '\\';
        } else {
            $ns = $namespace;
        }

        $includes = $this->offsetGet(array('includes' => $ns));

        foreach (array('require_once', 'require', 'include_once', 'include')
            as $key) {

            if (!isset($includes[$key])) {
                $includes[$key] = array();
            }
        }

        if (isset($includes[$category])) {
            $includes = $includes[$category];

        } elseif ($categorize === FALSE) {
            $includes = array_merge(
                $includes['require_once'],
                $includes['require'],
                $includes['include_once'],
                $includes['include']
            );
        }

        return $includes;
    }

    /**
     * Gets global variables defined in source scanned
     *
     * Parameter $categorize set to TRUE causing this function to return a
     * multi-dimensional array with categories in the keys of the first dimension
     * and constants and their values in the second dimension.
     *
     * Parameter $category allow to filter following specific global type
     *
     * @param bool   $categorize OPTIONAL
     * @param string $category   OPTIONAL
     * @param string $namespace  OPTIONAL Default is global namespace
     *
     * @return array
     */
    public function getGlobals($categorize = FALSE, $category = NULL,
        $namespace = FALSE)
    {
        static $glob = array(
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
            '$_ENV',
        );

        if ($namespace === FALSE) {
            // global namespace
            $ns = '\\';
        } else {
            $ns = $namespace;
        }

        $globals = $this->offsetGet(array('globals' => $ns));

        foreach ($glob as $key) {
            if (!isset($globals[$key])) {
                $globals[$key] = array();
            }
        }

        if (isset($globals[$category])) {
            $globals = $globals[$category];

        } elseif ($categorize === FALSE) {
            $globals = array_merge(
                $globals['global'],
                $globals['$GLOBALS'],
                $globals['$HTTP_SERVER_VARS'],
                $globals['$_SERVER'],
                $globals['$HTTP_GET_VARS'],
                $globals['$_GET'],
                $globals['$HTTP_POST_VARS'],
                $globals['$HTTP_POST_FILES'],
                $globals['$_POST'],
                $globals['$HTTP_COOKIE_VARS'],
                $globals['$_COOKIE'],
                $globals['$HTTP_SESSION_VARS'],
                $globals['$_SESSION'],
                $globals['$HTTP_ENV_VARS'],
                $globals['$_ENV']
            );
        }
        ksort($globals);
        return $globals;
    }

    /**
     * Returns number of lines (code, comment, total) in source code parsed
     *
     * @return array
     */
    public function getLinesOfCode()
    {
        return $this->linesOfCode;
    }

    /**
     * Main Parser
     *
     * @return void
     */
    protected function parse()
    {
        $namespace        = FALSE;
        $namespaceEndLine = FALSE;
        $class            = FALSE;
        $classEndLine     = FALSE;
        $interface        = FALSE;
        $interfaceEndLine = FALSE;
        $trait            = FALSE;
        $traitEndLine     = FALSE;

        foreach ($this->tokens as $id => $token) {

            if ('T_HALT_COMPILER' == $token[0]) {
                break;
            }

            $tokenName  = $token[0];
            $text       = $token[1];
            $line       = $token[2];

            $context = array(
                'namespace' => $namespace,
                'class'     => $class,
                'interface' => $interface,
                'trait'     => $trait,
                'context'   => strtolower(str_replace('T_', '', $tokenName))
            );

            switch ($tokenName) {
            case 'T_CLOSE_CURLY':
                if ($namespaceEndLine !== FALSE
                    && $namespaceEndLine == $line
                ) {
                    $namespace        = FALSE;
                    $namespaceEndLine = FALSE;
                }
                if ($classEndLine !== FALSE
                    && $classEndLine == $line
                ) {
                    $class        = FALSE;
                    $classEndLine = FALSE;
                }
                if ($interfaceEndLine !== FALSE
                    && $interfaceEndLine == $line
                ) {
                    $interface        = FALSE;
                    $interfaceEndLine = FALSE;
                }
                if ($traitEndLine !== FALSE
                    && $traitEndLine == $line
                ) {
                    $trait        = FALSE;
                    $traitEndLine = FALSE;
                }
                break;
            default:
                if (isset($this->parserToken[$tokenName])) {
                    $tokenClass = $this->parserToken[$tokenName][0];
                    $token = new $tokenClass($text, $line, $id, $this->tokens);

                    call_user_func_array(
                        $this->parserToken[$tokenName][1],
                        array(&$this, $context, $token)
                    );
                }
                break;
            }

            if ($tokenName == 'T_NAMESPACE') {
                $namespace        = $token->getName();
                $namespaceEndLine = $token->getEndLine();

            } elseif ($tokenName == 'T_TRAIT') {
                $trait        = $token->getName();
                $traitEndLine = $token->getEndLine();

            } elseif ($tokenName == 'T_INTERFACE') {
                $interface        = $token->getName();
                $interfaceEndLine = $token->getEndLine();

            } elseif ($tokenName == 'T_CLASS') {
                $class        = $token->getName();
                $classEndLine = $token->getEndLine();
            }
        }
    }

    /**
     * Default parser for tokens
     * T_NAMESPACE, T_INTERFACE, T_CLASS, T_FUNCTION,
     * T_REQUIRE_ONCE, T_REQUIRE, T_INCLUDE_ONCE, T_INCLUDE,
     * T_VARIABLE
     *
     * @return void
     */
    protected function parseToken()
    {
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

        list($subject, $context, $token) = func_get_args();
        extract($context);

        $container = $subject->options['containers'][$context];
        if ($container === NULL) {
            return;
        }

        $tmp  = array();
        $name = $token->getName();
        if ($name === NULL) {
            return;
        }

        $inc = in_array(
            $context, array('require_once', 'require', 'include_once', 'include')
        );

        if (method_exists($token, 'getType')) {
            $type = $token->getType();
            $glob = in_array($type, $globals);
        } else {
            $glob = FALSE;
        }

        if (isset($subject->options['properties'][$context])) {
            $properties = $subject->options['properties'][$context];
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
            if (in_array('startEndLines', $properties)) {
                $tmp['startLine'] = $token->getLine();
                $tmp['endLine']   = $token->getEndLine();
            }
            if (in_array('file', $properties)) {
                $tmp['file'] = $subject->filename;
            }
            if (in_array('namespace', $properties)) {
                $tmp['namespace'] = (($namespace === FALSE) ? '' : $namespace);
            }
            break;
        }

        foreach ($properties as $property) {
            $method = 'get' . ucfirst($property);
            if (method_exists($token, $method)) {
                $tmp[$property] = $token->{$method}();
            }
        }

        if ($namespace === FALSE) {
            // global namespace
            $ns = '\\';
        } else {
            $ns = $namespace;
        }

        if ($context == 'function') {
            $properties = $subject->options['properties'];

            if ($class === FALSE && $interface === FALSE && $trait === FALSE) {
                // update user functions
                $_ns = $subject->offsetGet(array($container => $ns));
                $_ns[$name] = $tmp;
                $subject->offsetSet(array($container => $ns), $_ns);

            } elseif ($interface === FALSE && $trait === FALSE) {
                if (!in_array('methods', $properties['class'])) {
                    return;
                }
                $container = $subject->options['containers']['class'];

                if ($container !== NULL) {
                    // update class methods
                    if (isset($tmp['namespace'])) {
                        unset($tmp['namespace']);
                    }

                    $_ns = $subject->offsetGet(array($container => $ns));
                    $_ns[$class]['methods'][$name] = $tmp;
                    $subject->offsetSet(array($container => $ns), $_ns);
                }

            } else {
                $propertyKey = ($interface) ? 'interface' : 'trait';

                if (!in_array('methods', $properties[$propertyKey])) {
                    return;
                }
                $container = $subject->options['containers'][$propertyKey];

                if ($container !== NULL) {
                    // update interface methods
                    if (isset($tmp['namespace'])) {
                        unset($tmp['namespace']);
                    }

                    if ($namespace === FALSE) {
                        // global namespace
                        $ns = '\\';
                    } else {
                        $ns = $namespace;
                    }
                    $_ns = $subject->offsetGet(array($container => $ns));
                    if ($interface) {
                        $_ns[$interface]['methods'][$name] = $tmp;
                    } else {
                        $_ns[$trait]['methods'][$name] = $tmp;
                    }
                    $subject->offsetSet(array($container => $ns), $_ns);
                }
            }

        } elseif ($inc === TRUE || $glob === TRUE) {
            // update includes or globals
            $_ns = $subject->offsetGet(array($container => $ns));
            $_ns[$type][$name] = $tmp;
            $subject->offsetSet(array($container => $ns), $_ns);

        } elseif ($context == 'use' || $context == 'namespace') {
            $tmp['import'] = $token->isImported();
            $subject->offsetSet(array($container => $name), $tmp);

        } else {
            $_ns = $subject->offsetGet(array($container => $ns));
            $_ns[$name] = $tmp;
            $subject->offsetSet(array($container => $ns), $_ns);
        }
    }

    /**
     * Whether or not an offset exists
     *
     * @param mixed $offset An offset to check for
     *
     * @return bool
     */
    public function offsetExists($offset)
    {
        if (is_array($offset)) {
            list ($container, $namespace) = each($offset);

            return isset($this->_container[$container][$namespace]);
        } else {
            return isset($this->_container[$offset]);
        }
    }

    /**
     * Returns the value at specified offset, or null if offset does not exists
     *
     * @param mixed $offset The offset to retrieve
     *
     * @return mixed
     */
    public function offsetGet($offset)
    {
        if (is_array($offset)) {
            list ($container, $namespace) = each($offset);

            if (isset($this->_container[$container][$namespace])) {
                return $this->_container[$container][$namespace];
            }

        } else {
            if (isset($this->_container[$offset])) {
                return $this->_container[$offset];
            }
        }
    }

    /**
     * Assigns a value to the specified offset
     *
     * @param mixed $offset The offset to assign the value to
     * @param mixed $value  The value to set
     *
     * @return void
     */
    public function offsetSet($offset, $value)
    {
        if (is_null($offset)) {
            $this->_container[] = $value;
        } elseif (is_array($offset)) {
            list ($container, $namespace) = each($offset);
            $this->_container[$container][$namespace] = $value;
        } else {
            $this->_container[$offset] = $value;
        }
    }

    /**
     * Unsets an offset
     *
     * @param mixed $offset The offset to unset
     *
     * @return void
     */
    public function offsetUnset($offset)
    {
        if (is_array($offset)) {
            list ($container, $namespace) = each($offset);
            unset($this->_container[$container][$namespace]);
        } else {
            unset($this->_container[$offset]);
        }
    }

}
