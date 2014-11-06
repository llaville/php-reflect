<?php
/**
 * DependencyModel represents all external dependencies.
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
 * The DependencyModel class reports information about internal/extension
 * functions, constants, globals.
 *
 * @category PHP
 * @package  PHP_Reflect
 * @author   Laurent Laville <pear@laurent-laville.org>
 * @license  http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version  Release: @package_version@
 * @link     http://php5.laurent-laville.org/reflect/
 * @since    Class available since Release 2.0.0RC2
 */
class DependencyModel extends AbstractModel implements Visitable
{
    /**
     * Constructs a new DependencyModel instance.
     *
     * @param string $name Name of the package or namespace
     */
    public function __construct($qualifiedName, $attributes)
    {
        if (!isset($attributes['arguments'])) {
            $attributes['arguments'] = array();
        }
        if (!isset($attributes['class'])) {
            $attributes['class'] = false;
        }
        if (!isset($attributes['conditionalFunction'])) {
            $attributes['conditionalFunction'] = false;
        }
        if (!isset($attributes['internalFunction'])) {
            $attributes['internalFunction'] = false;
        }

        parent::__construct($attributes);

        $this->name = $qualifiedName;

        if (strpos($qualifiedName, '::')) {
            $this->struct['classMethod'] = true;
        } else {
            $this->struct['classMethod'] = false;
        }
    }

    /**
     * Gets the starting line number of the dependency.
     *
     * @return int
     * @see    getEndLine()
     */
    public function getStartLine()
    {
        return $this->struct['startLine'];
    }

    /**
     * Gets the ending line number of the dependency.
     *
     * @return int
     * @see    getStartLine()
     */
    public function getEndLine()
    {
        return $this->struct['endLine'];
    }

    /**
     * Gets the name of the dependency.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Gets the namespace name.
     *
     * @return string
     */
    public function getNamespaceName()
    {
        $parts     = explode('\\', $this->getName());
        $className = array_pop($parts);

        return implode('\\', $parts);
    }

    /**
     * Gets the arguments list of an internal function.
     *
     * @return array
     */
    public function getArguments()
    {
        return $this->struct['arguments'];
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
     * Checks if the dependency is a class method.
     *
     * @return bool TRUE if the dependency is a class method, otherwise FALSE
     */
    public function isClassMethod()
    {
        return $this->struct['classMethod'];
    }

    /**
     * Checks if the dependency is a php/extension function.
     *
     * @return bool TRUE if the dependency is an internal function, otherwise FALSE
     */
    public function isInternalFunction()
    {
        return $this->struct['internalFunction'];
    }

    /**
     * Checks if the function is a conditional code.
     *
     * @return bool TRUE if conditional code, otherwise FALSE
     */
    public function isConditionalFunction()
    {
        return $this->struct['conditionalFunction'];
    }

    /**
     * Checks whether the dependency is a class.
     *
     * @return bool TRUE if the class is an interface, otherwise FALSE
     */
    public function isClass()
    {
        return $this->struct['class'] !== false;
    }

    /**
     * Returns the string representation of the DependencyModel object.
     *
     * @return string
     */
    public function __toString()
    {
        $eol = "\n";
        $str = '';
        // TODO
        return $str;
    }
}
