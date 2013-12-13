<?php
/**
 * FunctionToken represents the T_FUNCTION token.
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
 * Reports information about a user function or class method.
 *
 * @category PHP
 * @package  PHP_Reflect
 * @author   Laurent Laville <pear@laurent-laville.org>
 * @license  http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version  Release: @package_version@
 * @link     http://php5.laurent-laville.org/reflect/
 * @since    Class available since Release 2.0.0RC1
 */
class FunctionToken extends TokenWithArgument
{
    protected $ccn;
    protected $name;
    protected $signature;
    protected $closure;

    /**
     * Gets the name of the user function or class method
     *
     * @return string
     */
    public function getName()
    {
        if ($this->name !== null) {
            return $this->name;
        }
        $this->closure = false;

        for ($i = $this->id + 1; $i < count($this->tokenStream); $i++) {
            if ($this->tokenStream[$i][0] == 'T_STRING') {
                $this->name = $this->tokenStream[$i][1];
                break;

            } elseif ($this->tokenStream[$i][0] == 'T_AMPERSAND'
                && $this->tokenStream[$i][0] == 'T_STRING'
            ) {
                $this->name = $this->tokenStream[$i+1][1];
                break;

            } elseif ($this->tokenStream[$i][0] == 'T_OPEN_BRACKET') {
                $this->name = '';
                $this->closure = true;
                break;
            }
        }

        return $this->name;
    }

    /**
     * Gets the Cyclomatic Complexity Number (CCN)
     *
     * @return int
     */
    public function getCCN()
    {
        if ($this->ccn !== null) {
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

    /**
     * Gets the signature (name + parameters) of a user function or class method.
     *
     * @return string
     */
    public function getSignature()
    {
        if ($this->signature !== null) {
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

    /**
     * Checks if its an anonymous function (closure)
     *
     * @return bool TRUE if its a closure, FALSE otherwise
     */
    public function isClosure()
    {
        return $this->closure;
    }
}
