<?php
/**
 * PackageModel represents a package/namespace definition.
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

/**
 * The PackageModel class reports information about a package/namespace.
 *
 * @category PHP
 * @package  PHP_Reflect
 * @author   Laurent Laville <pear@laurent-laville.org>
 * @license  http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version  Release: @package_version@
 * @link     http://php5.laurent-laville.org/reflect/
 * @since    Class available since Release 2.0.0RC1
 */
class PackageModel extends AbstractModel implements Visitable, \IteratorAggregate
{
    protected $elements = array();

    /**
     * Constructs a new PackageModel instance.
     *
     * @param string $name Name of the package or namespace
     */
    public function __construct($name)
    {
        $this->name = $name;

        $this->struct = array(
            'docblock'  => '',
            'startLine' => 0,
            'endLine'   => 0,
            'file'      => '',
        );
    }

    /**
     * Returns internal iterator that allow to iterate over array of elements
     *
     * @return iterator
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->elements);
    }

    /**
     * Adds a new element that is part of the namespace
     *
     * @param AbstractModel $element A Model representation of the new element
     *
     * @return void
     */
    public function addElement($element)
    {
        $this->elements[] = $element;
    }

    /**
     * Get a Doc comment from a namespace.
     *
     * @return string
     */
    public function getDocComment()
    {
        return $this->struct['docblock'];
    }

    /**
     * Gets the starting line number of the namespace.
     *
     * @return int
     * @see    PackageModel::getEndLine()
     */
    public function getStartLine()
    {
        return $this->struct['startLine'];
    }

    /**
     * Gets the ending line number of the namespace.
     *
     * @return int
     * @see    PackageModel::getStartLine()
     */
    public function getEndLine()
    {
        return $this->struct['endLine'];
    }

    /**
     * Gets the file name from a user-defined namespace.
     *
     * @return string
     */
    public function getFileName()
    {
        return $this->struct['file'];
    }

    /**
     * Gets the full name of the namespace.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Gets the short name (alias) of the namespace.
     *
     * @return string
     */
    public function getShortName()
    {
        return $this->name;
    }

    /**
     * Returns the string representation of the PackageModel object.
     *
     * @return string
     */
    public function __toString()
    {
        $eol = "\n";
        $str = '';
        $str .= sprintf(
            'Package [ %s ] {%s',
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
