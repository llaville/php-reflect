<?php
/**
 * StringToken represents the T_STRING token.
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
 * Reports information about an identifiers like internal function names,
 * but not the class/method names or user function names.
 *
 * @category PHP
 * @package  PHP_Reflect
 * @author   Laurent Laville <pear@laurent-laville.org>
 * @license  http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version  Release: @package_version@
 * @link     http://php5.laurent-laville.org/reflect/
 * @since    Class available since Release 2.0.0RC1
 */
class StringToken extends TokenWithArgument
{
    protected $arguments;
    protected $name;

    /**
     * Gets the name of the identifier.
     *
     * @return string
     */
    public function getName()
    {
        if ($this->name !== null) {
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
