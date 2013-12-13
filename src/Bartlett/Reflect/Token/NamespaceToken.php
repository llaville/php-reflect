<?php
/**
 * NamespaceToken represents the T_NAMESPACE token.
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
 * Reports information about a namespace.
 *
 * @category PHP
 * @package  PHP_Reflect
 * @author   Laurent Laville <pear@laurent-laville.org>
 * @license  http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version  Release: @package_version@
 * @link     http://php5.laurent-laville.org/reflect/
 * @since    Class available since Release 2.0.0RC1
 */
class NamespaceToken extends TokenWithScope
{
    protected $namespace;
    protected $alias;

    /**
     * Gets the full qualified name of the namespace
     *
     * @return string
     */
    public function getName()
    {
        if ($this->namespace !== null) {
            return $this->namespace;
        }

        $this->namespace = $this->tokenStream[$this->id+2][1];

        for ($i = $this->id + 3;; $i += 2) {
            if (!isset($this->tokenStream[$i])) {
                break;
            }
            if ($this->tokenStream[$i][0] == 'T_NS_SEPARATOR') {
                $this->namespace .= '\\' . $this->tokenStream[$i+1][1];
            } else {
                break;
            }
        }

        return $this->namespace;
    }

    /**
     * Gets alias of a namespace
     *
     * @return string
     */
    public function getAlias()
    {
        if ($this->alias !== null) {
            return $this->alias;
        }

        $this->getName();

        $tmp         = explode('\\', $this->namespace);
        $this->alias = array_pop($tmp);

        return $this->alias;
    }

    /**
     * Checks if namespace is imported.
     *
     * @return bool FALSE
     */
    public function isImported()
    {
        return false;
    }
}
