<?php
/**
 * Copyright (c) 2011, Laurent Laville <pear@laurent-laville.org>
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

require_once dirname(__FILE__) . '/Reflect/Autoload.php';
 
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
                'interface'    => 'interfaces',
                'class'        => 'classes',
                'function'     => 'functions',
                'require_once' => 'includes',
                'require'      => 'includes',
                'include_once' => 'includes',
                'include'      => 'includes',
            ),
            // properties for each component to provide on final result
            'properties' => array(
                'interface' => array(
                    'file', 'startEndLines', 'docblock',
                    'keywords', 'parent', 'methods'
                ),
                'class' => array(
                    'file', 'startEndLines', 'docblock',
                    'keywords', 'parent', 'methods', 'interfaces', 'package'
                ),
                'function' => array(
                    'file', 'startEndLines', 'docblock',
                    'keywords', 'signature', 'ccn'
                ),
                'require_once' => array(
                    'file', 'startEndLines', 'docblock'
                ),
                'require' => array(
                    'file', 'startEndLines', 'docblock'
                ),
                'include_once' => array(
                    'file', 'startEndLines', 'docblock'
                ),
                'include' => array(
                    'file', 'startEndLines', 'docblock'
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
            $line += substr_count($text, "\n");
        }

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

            return $this->offsetGet($container);

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
     *
     * @return array
     */
    public function getIncludes($categorize = FALSE, $category = NULL)
    {
        $includes = $this->offsetGet('includes');

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
     * Main Parser
     *
     * @return void
     */
    protected function parse()
    {
        $class            = FALSE;
        $classEndLine     = FALSE;
        $interface        = FALSE;
        $interfaceEndLine = FALSE;

        foreach ($this->tokens as $id => $token) {

            if ('T_HALT_COMPILER' == $token[0]) {
                break;
            }

            $tokenName  = $token[0];
            $text       = $token[1];
            $line       = $token[2];

            $context = array(
                'class'     => $class,
                'interface' => $interface,
                'context'   => strtolower(str_replace('T_', '', $tokenName))
            );

            switch ($tokenName) {
            case 'T_CLOSE_CURLY':
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

            if ($tokenName == 'T_INTERFACE') {
                $interface        = $token->getName();
                $interfaceEndLine = $token->getEndLine();

            } elseif ($tokenName == 'T_CLASS') {
                $class        = $token->getName();
                $classEndLine = $token->getEndLine();
            }
        }
    }

    /**
     * Default parser for tokens T_INTERFACE, T_CLASS, T_FUNCTION
     *
     * @return void
     */
    protected function parseToken()
    {
        list($subject, $context, $token) = func_get_args();
        extract($context);

        $name = $token->getName();
        $tmp  = array();

        $inc = in_array(
            $context, array('require_once', 'require', 'include_once', 'include')
        );

        if (isset($subject->options['properties'][$context])) {
            $properties = $subject->options['properties'][$context];
        } else {
            $properties = array();
        }

        switch ($context) {
        case 'interface':
        case 'class':
        case 'function':
        case 'require_once':
        case 'require':
        case 'include_once':
        case 'include':
            if (in_array('startEndLines', $properties)) {
                $tmp['startLine'] = $token->getLine();
                $tmp['endLine']   = $token->getEndLine();
            }
            if (in_array('file', $properties)) {
                $tmp['file'] = $subject->filename;
            }
            break;
        }

        foreach ($properties as $property) {
            $method = 'get' . ucfirst($property);
            if (method_exists($token, $method)) {
                $tmp[$property] = $token->{$method}();
            }
        }

        $container = $subject->options['containers'][$context];
        if ($container === NULL) {
            return;
        }

        if ($context == 'function') {
            $properties = $subject->options['properties'];

            if ($class === FALSE && $interface === FALSE) {
                // update user functions
                $subject->offsetSet(array($container => $name), $tmp);

            } elseif ($interface === FALSE) {
                if (!in_array('methods', $properties['class'])) {
                    return;
                }
                $container = $subject->options['containers']['class'];

                if ($container !== NULL) {
                    // update class methods
                    $_class = $subject->offsetGet(array($container => $class));
                    $_class['methods'][$name] = $tmp;
                    $subject->offsetSet(array($container => $class), $_class);
                }

            } else {
                if (!in_array('methods', $properties['interface'])) {
                    return;
                }
                $container = $subject->options['containers']['interface'];

                if ($container !== NULL) {
                    // update interface methods
                    $_interface = $subject->offsetGet(
                        array($container => $interface)
                    );
                    $_interface['methods'][$name] = $tmp;
                    $subject->offsetSet(
                        array($container => $interface), $_interface
                    );
                }
            }

        } elseif ($inc === TRUE) {
            $type = $token->getType();
            // update includes
            $_inc = $subject->offsetGet($container);
            $_inc[$type][$name] = $tmp;
            $subject->offsetSet($container, $_inc);

        } else {
            $subject->offsetSet(array($container => $name), $tmp);
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
        return isset($this->_container[$offset]);
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
            list ($container, $name) = each($offset);

            if (isset($this->_container[$container][$name])) {
                return $this->_container[$container][$name];
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
            list ($container, $name) = each($offset);
            $this->_container[$container][$name] = $value;
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
            list ($container, $name) = each($offset);
            unset($this->_container[$container][$name]);
        } else {
            unset($this->_container[$offset]);
        }
    }

}
