<?php
/**
 * Abstract class that support tokens about magic constants.
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
 * @link     http://www.php.net/manual/en/language.constants.predefined.php
 *           Magic constants
 */

namespace Bartlett\Reflect\Token;

/**
 * Abstract class that support tokens about magic constants.
 *
 * @category PHP
 * @package  PHP_Reflect
 * @author   Laurent Laville <pear@laurent-laville.org>
 * @license  http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version  Release: @package_version@
 * @link     http://php5.laurent-laville.org/reflect/
 * @since    Class available since Release 2.0.0RC1
 */
abstract class TokenWithMagicConstant extends TokenWithScope
{
    protected $name;

    /**
     * Gets the name of the magic constant.
     *
     * @return string
     */
    public function getName()
    {
        if ($this->name === null) {
            $this->name = $this->text;
        }
        return $this->name;
    }
}
