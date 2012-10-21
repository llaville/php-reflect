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

/**
 * Autoloader for PHP_Reflect
 *
 * @param string $className name of the class (and namespace) being instantiated.
 *
 * @return void
 */
function PHP_Reflect_autoload($className)
{
    static $classes = null;
    static $path    = null;

    if ($classes === null) {

        $classes['PHP_Reflect'] = 'PHP/Reflect.php';

        $classNames = array(
            'PHP_Reflect_Token',
            'PHP_Reflect_TokenWithScope',
            'PHP_Reflect_Token_Includes',
            'PHP_Reflect_Token_REQUIRE_ONCE',
            'PHP_Reflect_Token_REQUIRE',
            'PHP_Reflect_Token_EVAL',
            'PHP_Reflect_Token_INCLUDE_ONCE',
            'PHP_Reflect_Token_INCLUDE',
            'PHP_Reflect_Token_LOGICAL_OR',
            'PHP_Reflect_Token_LOGICAL_XOR',
            'PHP_Reflect_Token_LOGICAL_AND',
            'PHP_Reflect_Token_PRINT',
            'PHP_Reflect_Token_SR_EQUAL',
            'PHP_Reflect_Token_SL_EQUAL',
            'PHP_Reflect_Token_XOR_EQUAL',
            'PHP_Reflect_Token_OR_EQUAL',
            'PHP_Reflect_Token_AND_EQUAL',
            'PHP_Reflect_Token_MOD_EQUAL',
            'PHP_Reflect_Token_CONCAT_EQUAL',
            'PHP_Reflect_Token_DIV_EQUAL',
            'PHP_Reflect_Token_MUL_EQUAL',
            'PHP_Reflect_Token_MINUS_EQUAL',
            'PHP_Reflect_Token_PLUS_EQUAL',
            'PHP_Reflect_Token_BOOLEAN_OR',
            'PHP_Reflect_Token_BOOLEAN_AND',
            'PHP_Reflect_Token_IS_NOT_IDENTICAL',
            'PHP_Reflect_Token_IS_IDENTICAL',
            'PHP_Reflect_Token_IS_NOT_EQUAL',
            'PHP_Reflect_Token_IS_EQUAL',
            'PHP_Reflect_Token_IS_GREATER_OR_EQUAL',
            'PHP_Reflect_Token_IS_SMALLER_OR_EQUAL',
            'PHP_Reflect_Token_SR',
            'PHP_Reflect_Token_SL',
            'PHP_Reflect_Token_INSTANCEOF',
            'PHP_Reflect_Token_UNSET_CAST',
            'PHP_Reflect_Token_BOOL_CAST',
            'PHP_Reflect_Token_OBJECT_CAST',
            'PHP_Reflect_Token_ARRAY_CAST',
            'PHP_Reflect_Token_STRING_CAST',
            'PHP_Reflect_Token_DOUBLE_CAST',
            'PHP_Reflect_Token_INT_CAST',
            'PHP_Reflect_Token_DEC',
            'PHP_Reflect_Token_INC',
            'PHP_Reflect_Token_CLONE',
            'PHP_Reflect_Token_NEW',
            'PHP_Reflect_Token_EXIT',
            'PHP_Reflect_Token_IF',
            'PHP_Reflect_Token_ELSEIF',
            'PHP_Reflect_Token_ELSE',
            'PHP_Reflect_Token_ENDIF',
            'PHP_Reflect_Token_LNUMBER',
            'PHP_Reflect_Token_DNUMBER',
            'PHP_Reflect_Token_STRING',
            'PHP_Reflect_Token_STRING_VARNAME',
            'PHP_Reflect_Token_VARIABLE',
            'PHP_Reflect_Token_NUM_STRING',
            'PHP_Reflect_Token_INLINE_HTML',
            'PHP_Reflect_Token_CHARACTER',
            'PHP_Reflect_Token_BAD_CHARACTER',
            'PHP_Reflect_Token_ENCAPSED_AND_WHITESPACE',
            'PHP_Reflect_Token_CONSTANT_ENCAPSED_STRING',
            'PHP_Reflect_Token_ECHO',
            'PHP_Reflect_Token_DO',
            'PHP_Reflect_Token_WHILE',
            'PHP_Reflect_Token_ENDWHILE',
            'PHP_Reflect_Token_FOR',
            'PHP_Reflect_Token_ENDFOR',
            'PHP_Reflect_Token_FOREACH',
            'PHP_Reflect_Token_ENDFOREACH',
            'PHP_Reflect_Token_DECLARE',
            'PHP_Reflect_Token_ENDDECLARE',
            'PHP_Reflect_Token_AS',
            'PHP_Reflect_Token_SWITCH',
            'PHP_Reflect_Token_ENDSWITCH',
            'PHP_Reflect_Token_CASE',
            'PHP_Reflect_Token_DEFAULT',
            'PHP_Reflect_Token_BREAK',
            'PHP_Reflect_Token_CONTINUE',
            'PHP_Reflect_Token_GOTO',
            'PHP_Reflect_Token_CALLABLE',
            'PHP_Reflect_Token_INSTEADOF',
            'PHP_Reflect_Token_FUNCTION',
            'PHP_Reflect_Token_CONST',
            'PHP_Reflect_Token_RETURN',
            'PHP_Reflect_Token_TRY',
            'PHP_Reflect_Token_CATCH',
            'PHP_Reflect_Token_THROW',
            'PHP_Reflect_Token_USE',
            'PHP_Reflect_Token_GLOBAL',
            'PHP_Reflect_Token_PUBLIC',
            'PHP_Reflect_Token_PROTECTED',
            'PHP_Reflect_Token_PRIVATE',
            'PHP_Reflect_Token_FINAL',
            'PHP_Reflect_Token_ABSTRACT',
            'PHP_Reflect_Token_STATIC',
            'PHP_Reflect_Token_VAR',
            'PHP_Reflect_Token_UNSET',
            'PHP_Reflect_Token_ISSET',
            'PHP_Reflect_Token_EMPTY',
            'PHP_Reflect_Token_HALT_COMPILER',
            'PHP_Reflect_Token_INTERFACE',
            'PHP_Reflect_Token_CLASS',
            'PHP_Reflect_Token_TRAIT',
            'PHP_Reflect_Token_EXTENDS',
            'PHP_Reflect_Token_IMPLEMENTS',
            'PHP_Reflect_Token_OBJECT_OPERATOR',
            'PHP_Reflect_Token_DOUBLE_ARROW',
            'PHP_Reflect_Token_LIST',
            'PHP_Reflect_Token_ARRAY',
            'PHP_Reflect_Token_CLASS_C',
            'PHP_Reflect_Token_TRAIT_C',
            'PHP_Reflect_Token_METHOD_C',
            'PHP_Reflect_Token_FUNC_C',
            'PHP_Reflect_Token_LINE',
            'PHP_Reflect_Token_FILE',
            'PHP_Reflect_Token_COMMENT',
            'PHP_Reflect_Token_DOC_COMMENT',
            'PHP_Reflect_Token_OPEN_TAG',
            'PHP_Reflect_Token_OPEN_TAG_WITH_ECHO',
            'PHP_Reflect_Token_CLOSE_TAG',
            'PHP_Reflect_Token_WHITESPACE',
            'PHP_Reflect_Token_START_HEREDOC',
            'PHP_Reflect_Token_END_HEREDOC',
            'PHP_Reflect_Token_DOLLAR_OPEN_CURLY_BRACES',
            'PHP_Reflect_Token_CURLY_OPEN',
            'PHP_Reflect_Token_PAAMAYIM_NEKUDOTAYIM',
            'PHP_Reflect_Token_NAMESPACE',
            'PHP_Reflect_Token_NS_C',
            'PHP_Reflect_Token_DIR',
            'PHP_Reflect_Token_NS_SEPARATOR',
            'PHP_Reflect_Token_DOUBLE_COLON',
            'PHP_Reflect_Token_OPEN_BRACKET',
            'PHP_Reflect_Token_CLOSE_BRACKET',
            'PHP_Reflect_Token_OPEN_SQUARE',
            'PHP_Reflect_Token_CLOSE_SQUARE',
            'PHP_Reflect_Token_OPEN_CURLY',
            'PHP_Reflect_Token_CLOSE_CURLY',
            'PHP_Reflect_Token_SEMICOLON',
            'PHP_Reflect_Token_DOT',
            'PHP_Reflect_Token_COMMA',
            'PHP_Reflect_Token_EQUAL',
            'PHP_Reflect_Token_LT',
            'PHP_Reflect_Token_GT',
            'PHP_Reflect_Token_PLUS',
            'PHP_Reflect_Token_MINUS',
            'PHP_Reflect_Token_MULT',
            'PHP_Reflect_Token_DIV',
            'PHP_Reflect_Token_QUESTION_MARK',
            'PHP_Reflect_Token_EXCLAMATION_MARK',
            'PHP_Reflect_Token_COLON',
            'PHP_Reflect_Token_DOUBLE_QUOTES',
            'PHP_Reflect_Token_AT',
            'PHP_Reflect_Token_AMPERSAND',
            'PHP_Reflect_Token_PERCENT',
            'PHP_Reflect_Token_PIPE',
            'PHP_Reflect_Token_DOLLAR',
            'PHP_Reflect_Token_CARET',
            'PHP_Reflect_Token_TILDE',
            'PHP_Reflect_Token_BACKTICK',
        );
        foreach ($classNames as $class) {
            $classes[$class] = 'PHP/Reflect/Token.php';
        }
        $path = dirname(dirname(dirname(__FILE__))) . DIRECTORY_SEPARATOR;
    }

    if (isset($classes[$className])) {
        include $path . $classes[$className];
    }
}

spl_autoload_register('PHP_Reflect_autoload');
