<?php
/**
 * Abstract class that support includes family tokens.
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
 * Abstract class that support includes family tokens:
 * T_INCLUDE, T_INCLUDE_ONCE, T_REQUIRE, T_REQUIRE_ONCE
 *
 * @category PHP
 * @package  PHP_Reflect
 * @author   Laurent Laville <pear@laurent-laville.org>
 * @license  http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version  Release: @package_version@
 * @link     http://php5.laurent-laville.org/reflect/
 * @since    Class available since Release 2.0.0RC1
 */
abstract class TokenWithInclude extends TokenWithScope
{
    protected $name;
    protected $type;

    /**
     * Gets the path to resource included.
     *
     * @return string
     */
    public function getName()
    {
        if ($this->name !== null) {
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
        if ($this->name !== null) {
            $this->type = strtolower(
                str_replace('T_', '', $this->tokenStream[$this->id][0])
            );
        }
        return trim($this->name);
    }

    /**
     * Identify the type of include.
     *
     * @return string
     */
    public function getType()
    {
        $this->getName();
        return $this->type;
    }
}
