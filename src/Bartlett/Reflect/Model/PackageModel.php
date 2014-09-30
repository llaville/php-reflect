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

use Bartlett\Reflect\Model\AbstractModel;

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
class PackageModel extends AbstractModel implements Visitable
{
    /**
     * Constructs a new PackageModel instance.
     *
     * @param string $name Name of the package or namespace
     */
    public function __construct($name, $attributes)
    {
        $struct = array(
            'import'       => false,
            'alias'        => '',
            'classes'      => array(),
            'interfaces'   => array(),
            'traits'       => array(),
            'functions'    => array(),
            'constants'    => array(),
            'includes'     => array(),
            'dependencies' => array(),
        );
        $struct = array_merge($struct, $attributes);
        parent::__construct($struct);

        $this->name = $name;
    }

    public function update($data)
    {
        $keys = array('classes', 'interfaces', 'traits', 'functions', 'constants', 'includes', 'dependencies');

        foreach ($data as $index => $values) {
            if (in_array($index, $keys)) {
                $this->struct[$index] = array_merge($this->struct[$index], $values);
            }
        }
    }

    /**
     * Gets the classes defined on this namespace.
     *
     * @return iterator that list ClassModel objects reflecting each class.
     */
    public function getClasses()
    {
        return $this->struct['classes'];
    }

    /**
     * Gets the interfaces defined on this namespace.
     *
     * @return iterator that list ClassModel objects reflecting each interface.
     */
    public function getInterfaces()
    {
        return $this->struct['interfaces'];
    }

    /**
     * Gets the traits defined on this namespace.
     *
     * @return iterator that list ClassModel objects reflecting each trait.
     */
    public function getTraits()
    {
        return $this->struct['traits'];
    }

    /**
     * Gets the user-functions defined on this namespace.
     *
     * @return iterator that list FunctionModel objects reflecting each function.
     */
    public function getFunctions()
    {
        return $this->struct['functions'];
    }

    /**
     * Gets the (user|magic) constants defined on this namespace.
     *
     * @return iterator that list ConstantModel objects reflecting each constant.
     */
    public function getConstants()
    {
        return $this->struct['constants'];
    }

    /**
     * Gets the includes used on this namespace.
     *
     * @return iterator that list IncludeModel objects reflecting each constant.
     */
    public function getIncludes()
    {
        return $this->struct['includes'];
    }

    /**
     * Gets internal components provided by PHP extensions.
     *
     * @return iterator that list DependencyModel objects reflecting each
     *         internal components.
     */
    public function getDependencies()
    {
        return $this->struct['dependencies'];
    }

    /**
     * Get a Doc comment from a namespace.
     *
     * @return string
     */
    public function getDocComment()
    {
        return $this->struct['docComment'];
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
        return $this->struct['alias'];
    }

    /**
     * Checks if the package is imported or declared.
     *
     * @return bool
     */
    public function isImported()
    {
        return $this->struct['import'];
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
