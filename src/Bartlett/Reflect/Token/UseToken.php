<?php
/**
 * UseToken represents the T_USE token.
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
 * Reports information about a namespace or trait import.
 *
 * @category PHP
 * @package  PHP_Reflect
 * @author   Laurent Laville <pear@laurent-laville.org>
 * @license  http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version  Release: @package_version@
 * @link     http://php5.laurent-laville.org/reflect/
 * @since    Class available since Release 2.0.0RC1
 */
class UseToken extends TokenWithScope
{
    protected $trait;
    protected $namespace;
    protected $alias;

    /**
     * Gets the name of the namespace or trait imported.
     *
     * @param bool $class Tells if we are in class context (TRUE) or not (FALSE)
     *
     * @return string
     */
    public function getName($class)
    {
        if ($class === false) {
            return $this->getNamespace();
        }

        return $this->getTrait();
    }

    /**
     * Gets the name of trait imported.
     *
     * @return string
     */
    protected function getTrait()
    {
        if ($this->trait !== null) {
            return $this->trait;
        }

        $this->trait = array();

        for ($i = $this->id + 2;; $i++) {
            if ($this->tokenStream[$i][0] == 'T_STRING') {
                $this->trait[] = $this->tokenStream[$i][1];

            } elseif ($this->tokenStream[$i][0] == 'T_SEMICOLON'
                || $this->tokenStream[$i][0] == 'T_OPEN_CURLY'
            ) {
                break;
            }
        }
        return $this->trait;
    }

    /**
     * Gets the name of namespace imported.
     *
     * @return string
     */
    protected function getNamespace()
    {
        if ($this->namespace !== null) {
            return $this->namespace;
        }

        $i = $this->id + 2;

        if ($this->tokenStream[$i][0] == 'T_NS_SEPARATOR') {
            $this->namespace = '';
        } else {
            $this->namespace = $this->tokenStream[$i][1];
            $i++;
        }

        for (;; $i += 2) {
            if (!isset($this->tokenStream[$i])) {
                break;
            }
            if ($this->tokenStream[$i][0] == 'T_NS_SEPARATOR') {
                $this->namespace .= '\\' . $this->tokenStream[$i+1][1];
            } elseif ($this->tokenStream[$i][0] !== 'T_STRING') {

                if ($this->tokenStream[$i+1][0] == 'T_AS') {
                    $this->alias = $this->tokenStream[$i+3][1];
                }
                break;
            }
        }

        return $this->namespace;
    }

    /**
     * Gets the alias used to identify the namespace imported.
     *
     * @return string
     */
    public function getAlias()
    {
        if ($this->alias !== null) {
            return $this->alias;
        }

        $this->getName(true);

        $tmp         = explode('\\', $this->namespace);
        $this->alias = array_pop($tmp);

        return $this->alias;

    }

    /**
     * Checks if namespace is imported.
     *
     * @return bool TRUE
     */
    public function isImported()
    {
        return true;
    }
}
