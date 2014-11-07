<?php
/**
 * UseModel represents a use statement.
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

namespace Bartlett\Reflect\Model;

use Bartlett\Reflect\Model\AbstractModel;

/**
 * The UseModel class reports information about a use statement.
 *
 * @category PHP
 * @package  PHP_Reflect
 * @author   Laurent Laville <pear@laurent-laville.org>
 * @license  http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version  Release: @package_version@
 * @link     http://php5.laurent-laville.org/reflect/
 * @since    Class available since Release 3.6.0
 */
class UseModel extends AbstractModel implements Visitable
{
    const TYPE_NORMAL   = 1;
    const TYPE_FUNCTION = 2;
    const TYPE_CONSTANT = 3;

    /**
     * Constructs a new UseModel instance.
     *
     * @param string $name Fully Qualified Name of the use statement
     */
    public function __construct($name, $attributes)
    {
        parent::__construct($attributes);

        $this->name = $name;
    }

    /**
     * Get a Doc comment from a use statement.
     *
     * @return string
     */
    public function getDocComment()
    {
        return $this->struct['docComment'];
    }

    /**
     * Gets the starting line number of the use statement.
     *
     * @return int
     * @see    UseModel::getEndLine()
     */
    public function getStartLine()
    {
        return $this->struct['startLine'];
    }

    /**
     * Gets the ending line number of the use statement.
     *
     * @return int
     * @see    UseModel::getStartLine()
     */
    public function getEndLine()
    {
        return $this->struct['endLine'];
    }

    /**
     * Gets the file name.
     *
     * @return string
     */
    public function getFileName()
    {
        return $this->struct['file'];
    }

    /**
     * Gets the full qualified name of the use statement.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Gets the short name (alias) of the use statement.
     *
     * @return string
     */
    public function getShortName()
    {
        return $this->struct['alias'];
    }

    /**
     * Checks if it's a normal use statement.
     *
     * @return bool TRUE if it's a normal use statement, FALSE otherwise
     */
    public function isNormal()
    {
        return ($this->struct['type'] == self::TYPE_NORMAL);
    }

    /**
     * Checks if it's a function use statement.
     *
     * @link http://php.net/manual/en/migration56.new-features.php#migration56.new-features.use
     * @return bool TRUE if it's a function use statement, FALSE otherwise
     */
    public function isFunction()
    {
        return ($this->struct['type'] == self::TYPE_FUNCTION);
    }

    /**
     * Checks if it's a constant use statement.
     *
     * @link http://php.net/manual/en/migration56.new-features.php#migration56.new-features.use
     * @return bool TRUE if it's a constant use statement, FALSE otherwise
     */
    public function isConstant()
    {
        return ($this->struct['type'] == self::TYPE_CONSTANT);
    }

    /**
     * Returns the string representation of the UseModel object.
     *
     * @return string
     */
    public function __toString()
    {
        if ($this->isFunction()) {
            $type = 'function ';
        } elseif ($this->isConstant()) {
            $type = 'const ';
        } else {
            $type = '';
        }

        $eol = "\n";
        $str = '';
        $str .= sprintf(
            'Use %s[ %s ] {%s',
            $type,
            $this->getName(),
            $eol
        );

        $str .= sprintf(
            '  @@ %s %d - %d%s',
            $this->getFileName(),
            $this->getStartLine(),
            $this->getEndLine(),
            $eol
        );

        $str .= '}' . $eol;

        return $str;
    }
}
