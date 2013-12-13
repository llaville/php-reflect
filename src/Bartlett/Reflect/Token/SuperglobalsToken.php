<?php
/**
 * SuperglobalsToken represents the T_VARIABLE token used in global context.
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
 * Reports information about a global variable.
 *
 * @category PHP
 * @package  PHP_Reflect
 * @author   Laurent Laville <pear@laurent-laville.org>
 * @license  http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version  Release: @package_version@
 * @link     http://php5.laurent-laville.org/reflect/
 * @since    Class available since Release 2.0.0RC1
 */
abstract class SuperglobalsToken extends TokenWithScope
{
    protected $name;
    protected $type;

    /**
     * Gets the name of the variable.
     *
     * @return string
     */
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

        if ($this->name !== null) {
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
    /**
     * Identify if variable is used in global context.
     *
     * @return string
     */
    public function getType()
    {
        $this->getName();
        return $this->type;
    }
}
