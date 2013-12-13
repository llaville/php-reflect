<?php
/**
 * Abstract class that support tokens with a scope context.
 *
 * PHP version 5
 *
 * @category PHP
 * @package  PHP_Reflect
 * @author   Laurent Laville <pear@laurent-laville.org>
 * @license  http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version  GIT: $Id$
 * @link     http://php5.laurent-laville.org/reflect/
 * @link     http://www.php.net/manual/en/tokens.php
 */

namespace Bartlett\Reflect\Token;

/**
 * Abstract class that support tokens with a scope context.
 *
 * @category PHP
 * @package  PHP_Reflect
 * @author   Laurent Laville <pear@laurent-laville.org>
 * @license  http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version  Release: @package_version@
 * @link     http://php5.laurent-laville.org/reflect/
 * @since    Class available since Release 2.0.0RC1
 */
abstract class TokenWithScope extends AbstractToken
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

    /**
     * Gets the visibility (Public, Protected, Private) of an element.
     *
     * @return string
     */
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
        return '';
    }

    /**
     * Gets the access modifiers for a class or method.
     *
     * @return array
     */
    public function getModifiers()
    {
        $modifiers = array();

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
                $modifiers[] = strtolower(
                    str_replace('T_', '', $this->tokenStream[$i][0])
                );
            }
        }

        return $modifiers;
    }

    /**
     * Returns index of last token stack in the current scope context.
     *
     * @return int
     */
    public function getEndTokenId()
    {
        $block = 0;
        $i     = $this->id + 1;

        if ($this instanceof NamespaceToken) {
            for ($j = $this->id + 3;; $j += 1) {
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

        while ($this->endTokenId === null && isset($this->tokenStream[$i])) {

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
                && ($this instanceof FunctionToken
                || $this instanceof RequireOnceToken
                || $this instanceof RequireToken
                || $this instanceof IncludeOnceToken
                || $this instanceof IncludeToken
                || $this instanceof UseToken
                || $this instanceof VariableToken)
            ) {
                if ($block === 0) {
                    $this->endTokenId = $i;
                }

            } elseif ($tokenName == 'T_NAMESPACE'
                && $t_ns_open == 'ns_open_semicolon'
            ) {
                // multiple namespace without bracketed syntax ending
                $this->endTokenId = $i - 1;
            }

            $i++;
        }

        if ($this->endTokenId === null) {
            if ($t_ns_open == 'ns_open_semicolon') {
                // simple namespace without bracketed syntax ending
                $this->endTokenId = $i - 1;
            } else {
                $this->endTokenId = $this->id;
            }
        }

        return $this->endTokenId;
    }

    /**
     * Gets the line number in source code where is implemented this token.
     *
     * @return int
     */
    public function getEndLine()
    {
        return $this->tokenStream[$this->getEndTokenId()][2];
    }
}
