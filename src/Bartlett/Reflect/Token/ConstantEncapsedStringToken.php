<?php
/**
 * ConstantEncapsedStringToken represents the T_CONSTANT_ENCAPSED_STRING token.
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
 * Reports information about a string syntax "foo" or 'bar', used by constants.
 *
 * @category PHP
 * @package  PHP_Reflect
 * @author   Laurent Laville <pear@laurent-laville.org>
 * @license  http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version  Release: @package_version@
 * @link     http://php5.laurent-laville.org/reflect/
 * @since    Class available since Release 2.0.0RC1
 */
class ConstantEncapsedStringToken extends TokenWithScope
{
    protected $name;
    protected $type;

    /**
     * Returns name of the constant.
     *
     * @return string
     */
    public function getName()
    {
        static $const = array('define', 'defined', 'constant');

        if ($this->name !== null) {
            return $this->name;
        }

        for ($i = $this->id - 1; $i > $this->id - 5; $i -= 1) {
            if (!isset($this->tokenStream[$i])) {
                return;
            }
            if ($this->tokenStream[$i][0] !== 'T_OPEN_BRACKET'
                && $this->tokenStream[$i][0] !== 'T_WHITESPACE'
                && $this->tokenStream[$i][0] !== 'T_STRING'
            ) {
                /*
                    look for signatures
                        constant ( "CONSTANT" )
                        define ( 'CONSTANT' )
                        defined('CONSTANT')
                 */
                return;
            }
            if (in_array(strtolower($this->tokenStream[$i][1]), $const)) {
                $this->type = 'constant';
                $this->name = trim($this->tokenStream[$this->id][1], "'\"");
                break;
            }
        }
        return $this->name;
    }

    /**
     * Identify if string syntax is used for constants.
     *
     * @return string
     */
    public function getType()
    {
        $this->getName();
        return $this->type;
    }

    /**
     * Returns value of constant/string syntax
     *
     * @return string
     */
    public function getValue()
    {
        for ($i = $this->id + 1; $i < $this->id + 5; $i++) {
            if ($this->tokenStream[$i][0] == 'T_COMMA') {
                $j = $i + 1;
                if ($this->tokenStream[$j][0] == 'T_WHITESPACE') {
                    $j++;
                }
                return trim($this->tokenStream[$j][1], "'\"");
            }
        }
    }
}
