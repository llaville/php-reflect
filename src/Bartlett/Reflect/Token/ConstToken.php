<?php
/**
 * ConstToken represents the T_CONST token.
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
 * Reports information about a class constant.
 *
 * @category PHP
 * @package  PHP_Reflect
 * @author   Laurent Laville <pear@laurent-laville.org>
 * @license  http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version  Release: @package_version@
 * @link     http://php5.laurent-laville.org/reflect/
 * @since    Class available since Release 2.0.0RC1
 */
class ConstToken extends TokenWithScope
{
    protected $name;
    protected $value;

    /**
     * Returns the name of the class constant.
     *
     * @return string
     */
    public function getName()
    {
        if ($this->name !== null) {
            return $this->name;
        }
        $this->name = $this->tokenStream[$this->id + 2][1];

        for ($i = $this->id + 3; $i < $this->id + 7; $i++) {
            if (!isset($this->tokenStream[$i])) {
                return;
            }

            if ($this->tokenStream[$i][0] == 'T_EQUAL'
                || $this->tokenStream[$i][0] == 'T_WHITESPACE'
            ) {
                continue;
            }

            if ($this->tokenStream[$i][0] == 'T_CONSTANT_ENCAPSED_STRING') {
                $this->value = trim($this->tokenStream[$i][1], "'\"");
            } elseif ($this->tokenStream[$i][0] !== 'T_SEMICOLON') {
                $this->value = $this->tokenStream[$i][1];
            }
            break;
        }
        return $this->name;
    }

    /**
     * Returns the value of the class constant.
     *
     * @return string
     */
    public function getValue()
    {
        $this->getName();
        return $this->value;
    }
}
