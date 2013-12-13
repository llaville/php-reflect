<?php
/**
 * Token base class.
 *
 * PHP version 5
 *
 * @category PHP
 * @package  PHP_Reflect
 * @author   Laurent Laville <pear@laurent-laville.org>
 * @license  http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version  GIT: $Id$
 * @link     http://php5.laurent-laville.org/reflect/
 */

namespace Bartlett\Reflect\Token;

/**
 * Token base class.
 *
 * @category PHP
 * @package  PHP_Reflect
 * @author   Laurent Laville <pear@laurent-laville.org>
 * @license  http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version  Release: @package_version@
 * @link     http://php5.laurent-laville.org/reflect/
 * @since    Class available since Release 2.0.0RC1
 */
abstract class AbstractToken
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
     * @param string  $text   Token string value
     * @param integer $line   Line where token is used
     * @param integer $id     Index of token in the stack
     * @param array   $tokens Tokens stack
     */
    public function __construct($text, $line, $id, $tokens)
    {
        $this->text        = $text;
        $this->line        = $line;
        $this->id          = $id;
        $this->tokenStream = $tokens;
    }

    /**
     * String representation of the token.
     *
     * @return string
     */
    public function __toString()
    {
        return $this->text;
    }

    /**
     * Gets line where the token is used.
     *
     * @return integer
     */
    public function getLine()
    {
        return $this->line;
    }
}
