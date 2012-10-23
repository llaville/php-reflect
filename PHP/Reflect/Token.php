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
 * @version  GIT: $Id$
 * @link     http://php5.laurent-laville.org/reflect/
 */

abstract class PHP_Reflect_Token
{
    /**
     * @var string
     */
    protected $text;

    /**
     * @var integer
     */
    protected $line;

    /**
     * @var integer
     */
    protected $id;

    /**
     * @var array
     */
    protected $tokenStream;

    /**
     * Constructor.
     *
     * @param string  $text
     * @param integer $line
     * @param integer $id
     * @param array   $tokens
     */
    public function __construct($text, $line, $id, $tokens)
    {
        $this->text        = $text;
        $this->line        = $line;
        $this->id          = $id;
        $this->tokenStream = $tokens;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->text;
    }

    /**
     * @return integer
     */
    public function getLine()
    {
        return $this->line;
    }

}

abstract class PHP_Reflect_TokenWithScope extends PHP_Reflect_Token
{
    protected $endTokenId;

    /**
     * Get the docblock for this token
     *
     * This method will fetch the docblock belonging to the current token. The
     * docblock must be placed on the line directly above the token to be
     * recognized.
     *
     * @return string|null Returns the docblock as a string if found
     */
    public function getDocblock()
    {
        $currentLineNumber = $this->tokenStream[$this->id][2];
        $prevLineNumber    = $currentLineNumber - 1;

        for ($i = $this->id - 1; $i > 0; $i--) {

            if ('T_FUNCTION' == $this->tokenStream[$i][0]
                || 'T_CLASS' == $this->tokenStream[$i][0]
            ) {
                // Some other class or function,
                // no docblock can be used for the current token
                break;
            }

            $line = $this->tokenStream[$i][2];

            if ($line == $currentLineNumber
                || (($line == $prevLineNumber)
                && ('T_WHITESPACE' == $this->tokenStream[$i][0]))
            ) {
                continue;
            }

            if ($line == $prevLineNumber
                && ('T_COMMENT' == $this->tokenStream[$i][0])
            ) {
                return $this->tokenStream[$i][1];
            }

            if (($line < $currentLineNumber)
                && ('T_DOC_COMMENT' !== $this->tokenStream[$i][0])
            ) {
                break;
            }

            return $this->tokenStream[$i][1];
        }
    }

    public function getVisibility()
    {
        for ($i = $this->id - 2; $i > $this->id - 7; $i -= 2) {
            if ($i < 0) {
                break;
            }

            if (true === ($this->tokenStream[$i][0] == 'T_PRIVATE'
                || $this->tokenStream[$i][0] == 'T_PROTECTED'
                || $this->tokenStream[$i][0] == 'T_PUBLIC')
            ) {
                return strtolower(
                    str_replace('T_', '', $this->tokenStream[$i][0])
                );
            }
            if (false === ($this->tokenStream[$i][0] == 'T_STATIC'
                || $this->tokenStream[$i][0] == 'T_FINAL'
                || $this->tokenStream[$i][0] == 'T_ABSTRACT')
            ) {
                // no keywords; stop visibility search
                break;
            }
        }
    }

    public function getKeywords()
    {
        $keywords = array();

        for ($i = $this->id - 2; $i > $this->id - 7; $i -= 2) {
            if ($i < 0) {
                break;
            }

            if (true === ($this->tokenStream[$i][0] == 'T_PRIVATE'
                || $this->tokenStream[$i][0] == 'T_PROTECTED'
                || $this->tokenStream[$i][0] == 'T_PUBLIC')
            ) {
                continue;
            }

            if (true === ($this->tokenStream[$i][0] == 'T_STATIC'
                || $this->tokenStream[$i][0] == 'T_FINAL'
                || $this->tokenStream[$i][0] == 'T_ABSTRACT')
            ) {
                $keywords[] = strtolower(
                    str_replace('T_', '', $this->tokenStream[$i][0])
                );
            }
        }

        return implode(',', $keywords);
    }

    public function getEndTokenId()
    {
        $block = 0;
        $i     = $this->id + 1;

        if ($this instanceof PHP_Reflect_Token_NAMESPACE) {
            for ($j = $this->id + 3; ; $j += 1) {
                if (isset($this->tokenStream[$j])) {
                    if ($this->tokenStream[$j][0] == 'T_OPEN_CURLY') {
                        $t_ns_open = 'ns_open_curly';
                        break;
                    } elseif ($this->tokenStream[$j][0] == 'T_SEMICOLON') {
                        $t_ns_open = 'ns_open_semicolon';
                        break;
                    }
                }
            }
        } else {
            $t_ns_open = false;
        }

        while ($this->endTokenId === NULL && isset($this->tokenStream[$i])) {

            $tokenName = $this->tokenStream[$i][0];

            if ($tokenName == 'T_OPEN_CURLY'
                || $tokenName == 'T_CURLY_OPEN'
            ) {
                $block++;

            } elseif ($tokenName == 'T_CLOSE_CURLY') {
                $block--;

                if ($block === 0
                    && (!$t_ns_open || $t_ns_open == 'ns_open_curly')
                ) {
                    $this->endTokenId = $i;
                }

            } elseif ($tokenName == 'T_SEMICOLON'
                && ($this instanceof PHP_Reflect_Token_FUNCTION
                || $this instanceof PHP_Reflect_Token_REQUIRE_ONCE
                || $this instanceof PHP_Reflect_Token_REQUIRE
                || $this instanceof PHP_Reflect_Token_INCLUDE_ONCE
                || $this instanceof PHP_Reflect_Token_INCLUDE
                || $this instanceof PHP_Reflect_Token_USE
                || $this instanceof PHP_Reflect_Token_VARIABLE)) {

                if ($block === 0) {
                    $this->endTokenId = $i;
                }

            } elseif ($tokenName == 'T_NAMESPACE'
                && $t_ns_open == 'ns_open_semicolon') {
                // multiple namespace without bracketed syntax ending
                $this->endTokenId = $i - 1;
            }

            $i++;
        }

        if ($this->endTokenId === NULL) {
            if ($t_ns_open == 'ns_open_semicolon') {
                // simple namespace without bracketed syntax ending
                $this->endTokenId = $i - 1;
            } else {
                $this->endTokenId = $this->id;
            }
        }

        return $this->endTokenId;
    }

    public function getEndLine()
    {
        return $this->tokenStream[$this->getEndTokenId()][2];
    }
}

abstract class PHP_Reflect_Token_Includes extends PHP_Reflect_TokenWithScope
{
    protected $name;
    protected $type;

    public function getName()
    {
        if ($this->name !== NULL) {
            return $this->name;
        }

        $i = $this->id + 1;
        while (isset($this->tokenStream[$i])
            && $this->tokenStream[$i][0] !== 'T_SEMICOLON') {
            if ($this->tokenStream[$i][0] == 'T_CONSTANT_ENCAPSED_STRING') {
                $this->name .= trim($this->tokenStream[$i][1], "'\"");
            } elseif ($this->tokenStream[$i][0] == 'T_VARIABLE') {
                $this->name .= $this->tokenStream[$i][1] . ' ';
            }
            $i++;
        }
        if ($this->name !== NULL) {
            $this->type = strtolower(
                str_replace('T_', '', $this->tokenStream[$this->id][0])
            );
        }
        return trim($this->name);
    }

    public function getType()
    {
        $this->getName();
        return $this->type;
    }
}

abstract class PHP_Reflect_TokenWithArgument extends PHP_Reflect_TokenWithScope
{
    protected $arguments;

    public function getArguments()
    {
        if ($this->arguments !== NULL) {
            return $this->arguments;
        }

        $this->arguments = array();
        $i               = $this->id + 1;
        $nextArgument    = array();

        if (get_class($this) === 'PHP_Reflect_Token_FUNCTION') {
            // ampersand before function-name
            if ($this->tokenStream[$i+1][0] == 'T_AMPERSAND') {
              $i = $i + 3;
            } else {
              $i = $i + 2;
            }
        }

        while (isset($this->tokenStream[$i])
            && $this->tokenStream[$i][0] != 'T_CLOSE_BRACKET'
        ) {
            if ($this->tokenStream[$i][0] == 'T_WHITESPACE'
                || $this->tokenStream[$i][0] == 'T_OPEN_BRACKET'
            ) {
                // do nothing

            } elseif (in_array($this->tokenStream[$i][0], array('T_STRING', 'T_ARRAY'))
                && !isset($nextArgument['name'])
            ) {
                if (($this->tokenStream[$i+1][0] == 'T_OPEN_BRACKET'
                    || $this->tokenStream[$i+2][0] == 'T_OPEN_BRACKET')
                ) {
                    if ($this->tokenStream[$i][0] == 'T_STRING') {
                        $nextArgument['typeHint'] = 'mixed';
                        $nextArgument['name']     = $this->tokenStream[$i][1];
                    } else {
                        $nextArgument['typeHint'] = '';
                    }

                    // allow for anything inside the brackets
                    while ($this->tokenStream[$i][0] != 'T_CLOSE_BRACKET') {
                        if (!isset($nextArgument['name'])) {
                            $nextArgument['typeHint'] .= $this->tokenStream[$i][1];
                        }
                        $i++;
                    }
                    if (!isset($nextArgument['name'])) {
                        $nextArgument['typeHint'] .= $this->tokenStream[$i][1];
                    }

                } else {
                    $nextArgument['typeHint'] = $this->tokenStream[$i][1];
                }

            } elseif ($this->tokenStream[$i][0] == 'T_VARIABLE') {
                $nextArgument['name'] = $this->tokenStream[$i][1];

            } elseif ($this->tokenStream[$i][0] == 'T_EQUAL') {
                // just do nothing - next tokens will contain the defaultValue

            } elseif (
                ($this->tokenStream[$i][0] == 'T_STRING') ||
                ($this->tokenStream[$i][0] == 'T_CONSTANT_ENCAPSED_STRING') ||
                ($this->tokenStream[$i][0] == 'T_LNUMBER')
            ) {
                $nextArgument['defaultValue'] = $this->tokenStream[$i][1];

            } elseif ($this->tokenStream[$i][0] == 'T_ARRAY') {
                $nextArgument['defaultValue'] = $this->tokenStream[$i++][1];

                // allow for anything inside the array, including nested arrays
                $bracketCount = 0;
                while (($this->tokenStream[$i][0] != 'T_CLOSE_BRACKET')
                    || ($bracketCount > 1)
                ) {
                    if ($this->tokenStream[$i][0] == 'T_OPEN_BRACKET') {
                        $bracketCount++;

                    } elseif ($this->tokenStream[$i][0] == 'T_CLOSE_BRACKET') {
                        $bracketCount--;
                    }

                    if (($this->tokenStream[$i][0] == 'T_COMMENT') ||
                        ($this->tokenStream[$i][0] == 'T_DOC_COMMENT')) {
                        // skip comments and doc-comments
                        $i++;
                    } else {
                        $nextArgument['defaultValue'] .= $this->tokenStream[$i++][1];
                    }
                }
                // T_CLOSE_BRACKET
                $nextArgument['defaultValue'] .= $this->tokenStream[$i][1];

            } elseif ($this->tokenStream[$i][0] == 'T_COMMA') {
                if (isset($nextArgument['typeHint'])
                    && !isset($nextArgument['name'])
                ) {
                    $nextArgument['defaultValue'] = $nextArgument['typeHint'];
                    unset($nextArgument['typeHint']);
                    if ('stdClass' == $nextArgument['defaultValue']) {
                        $nextArgument['typeHint'] = 'object';
                    }
                }
                // flush argument to array
                $this->arguments[] = $nextArgument;
                $nextArgument      = array();
            }

            $i++;
        }
        if (!empty($nextArgument)) {
            if (isset($nextArgument['typeHint'])
                && !isset($nextArgument['name'])
            ) {
                $nextArgument['defaultValue'] = $nextArgument['typeHint'];
                unset($nextArgument['typeHint']);
                if ('stdClass' == $nextArgument['defaultValue']) {
                    $nextArgument['typeHint'] = 'object';
                }
            }
            $this->arguments[] = $nextArgument;
        }

        return $this->arguments;
    }
}

class PHP_Reflect_Token_REQUIRE_ONCE extends PHP_Reflect_Token_Includes {}
class PHP_Reflect_Token_REQUIRE extends PHP_Reflect_Token_Includes {}
class PHP_Reflect_Token_EVAL extends PHP_Reflect_Token {}
class PHP_Reflect_Token_INCLUDE_ONCE extends PHP_Reflect_Token_Includes {}
class PHP_Reflect_Token_INCLUDE extends PHP_Reflect_Token_Includes {}
class PHP_Reflect_Token_LOGICAL_OR extends PHP_Reflect_Token {}
class PHP_Reflect_Token_LOGICAL_XOR extends PHP_Reflect_Token {}
class PHP_Reflect_Token_LOGICAL_AND extends PHP_Reflect_Token {}
class PHP_Reflect_Token_PRINT extends PHP_Reflect_Token {}
class PHP_Reflect_Token_SR_EQUAL extends PHP_Reflect_Token {}
class PHP_Reflect_Token_SL_EQUAL extends PHP_Reflect_Token {}
class PHP_Reflect_Token_XOR_EQUAL extends PHP_Reflect_Token {}
class PHP_Reflect_Token_OR_EQUAL extends PHP_Reflect_Token {}
class PHP_Reflect_Token_AND_EQUAL extends PHP_Reflect_Token {}
class PHP_Reflect_Token_MOD_EQUAL extends PHP_Reflect_Token {}
class PHP_Reflect_Token_CONCAT_EQUAL extends PHP_Reflect_Token {}
class PHP_Reflect_Token_DIV_EQUAL extends PHP_Reflect_Token {}
class PHP_Reflect_Token_MUL_EQUAL extends PHP_Reflect_Token {}
class PHP_Reflect_Token_MINUS_EQUAL extends PHP_Reflect_Token {}
class PHP_Reflect_Token_PLUS_EQUAL extends PHP_Reflect_Token {}
class PHP_Reflect_Token_BOOLEAN_OR extends PHP_Reflect_Token {}
class PHP_Reflect_Token_BOOLEAN_AND extends PHP_Reflect_Token {}
class PHP_Reflect_Token_IS_NOT_IDENTICAL extends PHP_Reflect_Token {}
class PHP_Reflect_Token_IS_IDENTICAL extends PHP_Reflect_Token {}
class PHP_Reflect_Token_IS_NOT_EQUAL extends PHP_Reflect_Token {}
class PHP_Reflect_Token_IS_EQUAL extends PHP_Reflect_Token {}
class PHP_Reflect_Token_IS_GREATER_OR_EQUAL extends PHP_Reflect_Token {}
class PHP_Reflect_Token_IS_SMALLER_OR_EQUAL extends PHP_Reflect_Token {}
class PHP_Reflect_Token_SR extends PHP_Reflect_Token {}
class PHP_Reflect_Token_SL extends PHP_Reflect_Token {}
class PHP_Reflect_Token_INSTANCEOF extends PHP_Reflect_Token {}
class PHP_Reflect_Token_UNSET_CAST extends PHP_Reflect_Token {}
class PHP_Reflect_Token_BOOL_CAST extends PHP_Reflect_Token {}
class PHP_Reflect_Token_OBJECT_CAST extends PHP_Reflect_Token {}
class PHP_Reflect_Token_ARRAY_CAST extends PHP_Reflect_Token {}
class PHP_Reflect_Token_STRING_CAST extends PHP_Reflect_Token {}
class PHP_Reflect_Token_DOUBLE_CAST extends PHP_Reflect_Token {}
class PHP_Reflect_Token_INT_CAST extends PHP_Reflect_Token {}
class PHP_Reflect_Token_DEC extends PHP_Reflect_Token {}
class PHP_Reflect_Token_INC extends PHP_Reflect_Token {}
class PHP_Reflect_Token_CLONE extends PHP_Reflect_Token {}
class PHP_Reflect_Token_NEW extends PHP_Reflect_Token {}
class PHP_Reflect_Token_EXIT extends PHP_Reflect_Token {}
class PHP_Reflect_Token_IF extends PHP_Reflect_Token {}
class PHP_Reflect_Token_ELSEIF extends PHP_Reflect_Token {}
class PHP_Reflect_Token_ELSE extends PHP_Reflect_Token {}
class PHP_Reflect_Token_ENDIF extends PHP_Reflect_Token {}
class PHP_Reflect_Token_LNUMBER extends PHP_Reflect_Token {}
class PHP_Reflect_Token_DNUMBER extends PHP_Reflect_Token {}

class PHP_Reflect_Token_STRING extends PHP_Reflect_TokenWithArgument
{
    protected $arguments;
    protected $name;

    public function getName()
    {
        if ($this->name !== NULL) {
            return $this->name;
        }

        if (($this->tokenStream[$this->id+1][0] == 'T_OPEN_BRACKET'
            || $this->tokenStream[$this->id+2][0] == 'T_OPEN_BRACKET')
            && $this->tokenStream[$this->id-2][0] !== 'T_NEW'
            && $this->tokenStream[$this->id-2][0] !== 'T_FUNCTION'
        ) {
            $this->name = $this->text;
        }

        return $this->name;
    }

}

class PHP_Reflect_Token_STRING_VARNAME extends PHP_Reflect_Token {}

abstract class PHP_Reflect_Token_Globals extends PHP_Reflect_TokenWithScope
{
    protected $name;
    protected $type;

    public function getName()
    {
        // @link http://www.php.net/manual/en/language.variables.superglobals.php
        static $superglobals = array(
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

        if ($this->name !== NULL) {
            return $this->name;
        }

        if (in_array($this->tokenStream[$this->id][1], $superglobals)) {
            $i = $this->id + 2;
            if ($this->tokenStream[$this->id+1][0] == 'T_WHITESPACE') {
                $i++;
            }
            if ($this->tokenStream[$i][0] == 'T_CONSTANT_ENCAPSED_STRING') {
                $this->name = trim($this->tokenStream[$i][1], "'\"");
            } elseif ($this->tokenStream[$i][0] == 'T_VARIABLE') {
                $this->name = $this->tokenStream[$i][1];
            } else {
                $this->name = '';
            }
            $this->type = $this->tokenStream[$this->id][1];
        } else {
            $i = $this->id - 2;
            while ($i > 0 && $this->tokenStream[$i][0] == 'T_COMMA') {
                $i -= 3;
            }
            if ($i > 0 && $this->tokenStream[$i][0] == 'T_GLOBAL') {
                $this->name = $this->tokenStream[$this->id][1];
                $this->type = 'global';
            }
        }

        return $this->name;
    }

    public function getType()
    {
        $this->getName();
        return $this->type;
    }
}

class PHP_Reflect_Token_VARIABLE extends PHP_Reflect_Token_Globals {}
class PHP_Reflect_Token_NUM_STRING extends PHP_Reflect_Token {}
class PHP_Reflect_Token_INLINE_HTML extends PHP_Reflect_Token {}
class PHP_Reflect_Token_CHARACTER extends PHP_Reflect_Token {}
class PHP_Reflect_Token_BAD_CHARACTER extends PHP_Reflect_Token {}
class PHP_Reflect_Token_ENCAPSED_AND_WHITESPACE extends PHP_Reflect_Token {}
class PHP_Reflect_Token_CONSTANT_ENCAPSED_STRING extends PHP_Reflect_Token {}
class PHP_Reflect_Token_ECHO extends PHP_Reflect_Token {}
class PHP_Reflect_Token_DO extends PHP_Reflect_Token {}
class PHP_Reflect_Token_WHILE extends PHP_Reflect_Token {}
class PHP_Reflect_Token_ENDWHILE extends PHP_Reflect_Token {}
class PHP_Reflect_Token_FOR extends PHP_Reflect_Token {}
class PHP_Reflect_Token_ENDFOR extends PHP_Reflect_Token {}
class PHP_Reflect_Token_FOREACH extends PHP_Reflect_Token {}
class PHP_Reflect_Token_ENDFOREACH extends PHP_Reflect_Token {}
class PHP_Reflect_Token_DECLARE extends PHP_Reflect_Token {}
class PHP_Reflect_Token_ENDDECLARE extends PHP_Reflect_Token {}
class PHP_Reflect_Token_AS extends PHP_Reflect_Token {}
class PHP_Reflect_Token_SWITCH extends PHP_Reflect_Token {}
class PHP_Reflect_Token_ENDSWITCH extends PHP_Reflect_Token {}
class PHP_Reflect_Token_CASE extends PHP_Reflect_Token {}
class PHP_Reflect_Token_DEFAULT extends PHP_Reflect_Token {}
class PHP_Reflect_Token_BREAK extends PHP_Reflect_Token {}
class PHP_Reflect_Token_CONTINUE extends PHP_Reflect_Token {}
class PHP_Reflect_Token_GOTO extends PHP_Reflect_Token {}
class PHP_Reflect_Token_CALLABLE extends PHP_Reflect_Token {}
class PHP_Reflect_Token_INSTEADOF extends PHP_Reflect_Token {}

class PHP_Reflect_Token_FUNCTION extends PHP_Reflect_TokenWithArgument
{
    protected $ccn;
    protected $name;
    protected $signature;

    public function getName()
    {
        if ($this->name !== NULL) {
            return $this->name;
        }

        if ($this->tokenStream[$this->id+2][0] == 'T_STRING') {
            $this->name = $this->tokenStream[$this->id+2][1];
        }

        else if ($this->tokenStream[$this->id+2][0] == 'T_AMPERSAND' &&
                 $this->tokenStream[$this->id+3][0] == 'T_STRING') {
            $this->name = $this->tokenStream[$this->id+3][1];
        }

        else {
            $this->name = 'anonymous function';
        }

        return $this->name;
    }

    public function getCCN()
    {
        if ($this->ccn !== NULL) {
            return $this->ccn;
        }

        $this->ccn = 1;
        $end       = $this->getEndTokenId();

        for ($i = $this->id; $i <= $end; $i++) {
            switch ($this->tokenStream[$i][0]) {
                case 'T_IF':
                case 'T_ELSEIF':
                case 'T_FOR':
                case 'T_FOREACH':
                case 'T_WHILE':
                case 'T_CASE':
                case 'T_CATCH':
                case 'T_BOOLEAN_AND':
                case 'T_LOGICAL_AND':
                case 'T_BOOLEAN_OR':
                case 'T_LOGICAL_OR':
                case 'T_QUESTION_MARK':
                    $this->ccn++;
                    break;
            }
        }

        return $this->ccn;
    }

    public function getSignature()
    {
        if ($this->signature !== NULL) {
            return $this->signature;
        }

        $this->signature = '';

        $i = $this->id + 2;

        while ($this->tokenStream[$i][0] != 'T_OPEN_CURLY'
            && $this->tokenStream[$i][0] != 'T_SEMICOLON'
        ) {
            $this->signature .= $this->tokenStream[$i++][1];
        }

        return trim($this->signature);
    }
}

class PHP_Reflect_Token_CONST extends PHP_Reflect_Token {}
class PHP_Reflect_Token_RETURN extends PHP_Reflect_Token {}
class PHP_Reflect_Token_TRY extends PHP_Reflect_Token {}
class PHP_Reflect_Token_CATCH extends PHP_Reflect_Token {}
class PHP_Reflect_Token_THROW extends PHP_Reflect_Token {}
class PHP_Reflect_Token_USE extends PHP_Reflect_TokenWithScope
{
    protected $trait;
    protected $namespace;
    protected $alias;

    public function getName($class)
    {
        if ($class === FALSE) {
            return $this->getNamespace();
        }

        return $this->getTrait();
    }

    protected function getTrait()
    {
        if ($this->trait !== NULL) {
            return $this->trait;
        }

        $this->trait = array();

        for ($i = $this->id + 2; ; $i++) {
            if ($this->tokenStream[$i][0] == 'T_STRING') {
                $this->trait[] = $this->tokenStream[$i][1];
            }
            elseif ($this->tokenStream[$i][0] == 'T_SEMICOLON'
                || $this->tokenStream[$i][0] == 'T_OPEN_CURLY'
            ) {
                break;
            }
        }
        return $this->trait;
    }

    protected function getNamespace()
    {
        if ($this->namespace !== NULL) {
            return $this->namespace;
        }

        $i = $this->id + 2;

        if ($this->tokenStream[$i][0] == 'T_NS_SEPARATOR') {
            $this->namespace = '';
        } else {
            $this->namespace = $this->tokenStream[$i][1];
            $i++;
        }

        for (; ; $i += 2) {
            if (!isset($this->tokenStream[$i])) {
                break;
            }
            if ($this->tokenStream[$i][0] == 'T_NS_SEPARATOR') {
                $this->namespace .= '\\' . $this->tokenStream[$i+1][1];
            } elseif ($this->tokenStream[$i][0] !== 'T_STRING') {

                if ($this->tokenStream[$i+1][0] == 'T_AS') {
                    $this->alias = $this->tokenStream[$i+3][1];
                }
                break;
            }
        }

        return $this->namespace;
    }

    public function getAlias()
    {
        if ($this->alias !== NULL) {
            return $this->alias;
        }

        $this->getName(TRUE);

        $tmp         = explode('\\', $this->namespace);
        $this->alias = array_pop($tmp);

        return $this->alias;

    }

    public function isImported()
    {
        return true;
    }

}
class PHP_Reflect_Token_GLOBAL extends PHP_Reflect_Token {}
class PHP_Reflect_Token_PUBLIC extends PHP_Reflect_Token {}
class PHP_Reflect_Token_PROTECTED extends PHP_Reflect_Token {}
class PHP_Reflect_Token_PRIVATE extends PHP_Reflect_Token {}
class PHP_Reflect_Token_FINAL extends PHP_Reflect_Token {}
class PHP_Reflect_Token_ABSTRACT extends PHP_Reflect_Token {}
class PHP_Reflect_Token_STATIC extends PHP_Reflect_Token {}
class PHP_Reflect_Token_VAR extends PHP_Reflect_Token {}
class PHP_Reflect_Token_UNSET extends PHP_Reflect_Token {}
class PHP_Reflect_Token_ISSET extends PHP_Reflect_Token {}
class PHP_Reflect_Token_EMPTY extends PHP_Reflect_Token {}
class PHP_Reflect_Token_HALT_COMPILER extends PHP_Reflect_Token {}

class PHP_Reflect_Token_INTERFACE extends PHP_Reflect_TokenWithScope
{
    protected $interfaces;

    public function getName()
    {
        $token = $this->tokenStream[$this->id + 2];
        $text  = $token[1];
        return $text;
    }

    public function hasParent()
    {
        return
            (isset($this->tokenStream[$this->id + 4]) &&
            $this->tokenStream[$this->id + 4][0] == 'T_EXTENDS');
    }

    public function getParent()
    {
        if (!$this->hasParent()) {
            return FALSE;
        }

        $i         = $this->id + 6;
        $className = $this->tokenStream[$i][1];

        while (isset($this->tokenStream[$i+1]) &&
            $this->tokenStream[$i+1][0] != 'T_WHITESPACE'
        ) {
            $className .= $this->tokenStream[++$i][1];
        }

        return $className;
    }

    public function getPackage()
    {
        $className  = $this->getName();
        $docComment = $this->getDocblock();

        $result = array(
            'namespace'   => '',
            'fullPackage' => '',
            'category'    => '',
            'package'     => '',
            'subpackage'  => ''
        );

        for ($i = $this->id; $i; --$i) {
            if ($this->tokenStream[$i][0] == 'T_NAMESPACE') {
                $ns = new PHP_Reflect_Token_NAMESPACE(
                    $this->tokenStream[$i][1], $this->tokenStream[$i][2],
                    $i, $this->tokenStream
                );
                $result['namespace'] = $ns->getName();
                break;
            }
        }

        if (preg_match('/@category[\s]+([\.\w]+)/', $docComment, $matches)) {
            $result['category'] = $matches[1];
        }

        if (preg_match('/@package[\s]+([\.\w]+)/', $docComment, $matches)) {
            $result['package']     = $matches[1];
            $result['fullPackage'] = $matches[1];
        }

        if (preg_match('/@subpackage[\s]+([\.\w]+)/', $docComment, $matches)) {
            $result['subpackage']   = $matches[1];
            $result['fullPackage'] .= '.' . $matches[1];
        }

        if (empty($result['fullPackage'])) {
            $result['fullPackage'] = $this->arrayToName(
              explode('_', str_replace('\\', '_', $className)), '.'
            );
        }

        return $result;
    }

    protected function arrayToName(array $parts, $join = '\\')
    {
        $result = '';

        if (count($parts) > 1) {
            array_pop($parts);

            $result = join($join, $parts);
        }

        return $result;
    }

    public function hasInterfaces()
    {
        if ((isset($this->tokenStream[$this->id + 4])
            && $this->tokenStream[$this->id + 4][0] == 'T_IMPLEMENTS') ||
           (isset($this->tokenStream[$this->id + 8])
            && $this->tokenStream[$this->id + 8][0] == 'T_IMPLEMENTS')){
            return true;
        }
        return false;
    }

    public function getInterfaces()
    {
        if ($this->interfaces !== NULL) {
            return $this->interfaces;
        }

        if (!$this->hasInterfaces()) {
            return ($this->interfaces = FALSE);
        }

        if ($this->tokenStream[$this->id + 4][0] == 'T_IMPLEMENTS') {
            $i = $this->id + 3;
        } else {
            $i = $this->id + 7;
        }

        while ($this->tokenStream[$i+1][0] != 'T_OPEN_CURLY') {
            $i++;
            if ($this->tokenStream[$i][0] == 'T_STRING') {
                $this->interfaces[] = $this->tokenStream[$i][1];
            }
        }
        return $this->interfaces;
    }
}

class PHP_Reflect_Token_CLASS extends PHP_Reflect_Token_INTERFACE {}
class PHP_Reflect_Token_TRAIT extends PHP_Reflect_Token_INTERFACE {}
class PHP_Reflect_Token_EXTENDS extends PHP_Reflect_Token {}
class PHP_Reflect_Token_IMPLEMENTS extends PHP_Reflect_Token {}
class PHP_Reflect_Token_OBJECT_OPERATOR extends PHP_Reflect_Token {}
class PHP_Reflect_Token_DOUBLE_ARROW extends PHP_Reflect_Token {}
class PHP_Reflect_Token_LIST extends PHP_Reflect_Token {}
class PHP_Reflect_Token_ARRAY extends PHP_Reflect_Token {}
class PHP_Reflect_Token_CLASS_C extends PHP_Reflect_Token {}
class PHP_Reflect_Token_TRAIT_C extends PHP_Reflect_Token {}
class PHP_Reflect_Token_METHOD_C extends PHP_Reflect_Token {}
class PHP_Reflect_Token_FUNC_C extends PHP_Reflect_Token {}
class PHP_Reflect_Token_LINE extends PHP_Reflect_Token {}
class PHP_Reflect_Token_FILE extends PHP_Reflect_Token {}
class PHP_Reflect_Token_COMMENT extends PHP_Reflect_Token {}
class PHP_Reflect_Token_DOC_COMMENT extends PHP_Reflect_Token {}
class PHP_Reflect_Token_OPEN_TAG extends PHP_Reflect_Token {}
class PHP_Reflect_Token_OPEN_TAG_WITH_ECHO extends PHP_Reflect_Token {}
class PHP_Reflect_Token_CLOSE_TAG extends PHP_Reflect_Token {}
class PHP_Reflect_Token_WHITESPACE extends PHP_Reflect_Token {}
class PHP_Reflect_Token_START_HEREDOC extends PHP_Reflect_Token {}
class PHP_Reflect_Token_END_HEREDOC extends PHP_Reflect_Token {}
class PHP_Reflect_Token_DOLLAR_OPEN_CURLY_BRACES extends PHP_Reflect_Token {}
class PHP_Reflect_Token_CURLY_OPEN extends PHP_Reflect_Token {}
class PHP_Reflect_Token_PAAMAYIM_NEKUDOTAYIM extends PHP_Reflect_Token {}

class PHP_Reflect_Token_NAMESPACE extends PHP_Reflect_TokenWithScope
{
    protected $namespace;
    protected $alias;

    public function getName()
    {
        if ($this->namespace !== NULL) {
            return $this->namespace;
        }

        $this->namespace = $this->tokenStream[$this->id+2][1];

        for ($i = $this->id + 3; ; $i += 2) {
            if (!isset($this->tokenStream[$i])) {
                break;
            }
            if ($this->tokenStream[$i][0] == 'T_NS_SEPARATOR') {
                $this->namespace .= '\\' . $this->tokenStream[$i+1][1];
            } else {
                break;
            }
        }

        return $this->namespace;
    }

    public function getAlias()
    {
        if ($this->alias !== NULL) {
            return $this->alias;
        }

        $this->getName();

        $tmp         = explode('\\', $this->namespace);
        $this->alias = array_pop($tmp);

        return $this->alias;
    }

    public function isImported()
    {
        return false;
    }

}

class PHP_Reflect_Token_NS_C extends PHP_Reflect_Token {}
class PHP_Reflect_Token_DIR extends PHP_Reflect_Token {}
class PHP_Reflect_Token_NS_SEPARATOR extends PHP_Reflect_Token {}
class PHP_Reflect_Token_DOUBLE_COLON extends PHP_Reflect_Token {}
class PHP_Reflect_Token_OPEN_BRACKET extends PHP_Reflect_Token {}
class PHP_Reflect_Token_CLOSE_BRACKET extends PHP_Reflect_Token {}
class PHP_Reflect_Token_OPEN_SQUARE extends PHP_Reflect_Token {}
class PHP_Reflect_Token_CLOSE_SQUARE extends PHP_Reflect_Token {}
class PHP_Reflect_Token_OPEN_CURLY extends PHP_Reflect_Token {}
class PHP_Reflect_Token_CLOSE_CURLY extends PHP_Reflect_Token {}
class PHP_Reflect_Token_SEMICOLON extends PHP_Reflect_Token {}
class PHP_Reflect_Token_DOT extends PHP_Reflect_Token {}
class PHP_Reflect_Token_COMMA extends PHP_Reflect_Token {}
class PHP_Reflect_Token_EQUAL extends PHP_Reflect_Token {}
class PHP_Reflect_Token_LT extends PHP_Reflect_Token {}
class PHP_Reflect_Token_GT extends PHP_Reflect_Token {}
class PHP_Reflect_Token_PLUS extends PHP_Reflect_Token {}
class PHP_Reflect_Token_MINUS extends PHP_Reflect_Token {}
class PHP_Reflect_Token_MULT extends PHP_Reflect_Token {}
class PHP_Reflect_Token_DIV extends PHP_Reflect_Token {}
class PHP_Reflect_Token_QUESTION_MARK extends PHP_Reflect_Token {}
class PHP_Reflect_Token_EXCLAMATION_MARK extends PHP_Reflect_Token {}
class PHP_Reflect_Token_COLON extends PHP_Reflect_Token {}
class PHP_Reflect_Token_DOUBLE_QUOTES extends PHP_Reflect_Token {}
class PHP_Reflect_Token_AT extends PHP_Reflect_Token {}
class PHP_Reflect_Token_AMPERSAND extends PHP_Reflect_Token {}
class PHP_Reflect_Token_PERCENT extends PHP_Reflect_Token {}
class PHP_Reflect_Token_PIPE extends PHP_Reflect_Token {}
class PHP_Reflect_Token_DOLLAR extends PHP_Reflect_Token {}
class PHP_Reflect_Token_CARET extends PHP_Reflect_Token {}
class PHP_Reflect_Token_TILDE extends PHP_Reflect_Token {}
class PHP_Reflect_Token_BACKTICK extends PHP_Reflect_Token {}
