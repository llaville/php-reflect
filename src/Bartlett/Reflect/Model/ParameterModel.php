<?php
/**
 * ParameterModel represents a parameter definition.
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
use Bartlett\Reflect\Exception\ModelException;

/**
 * The ParameterModel class reports information about a parameter.
 *
 * @category PHP
 * @package  PHP_Reflect
 * @author   Laurent Laville <pear@laurent-laville.org>
 * @license  http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version  Release: @package_version@
 * @link     http://php5.laurent-laville.org/reflect/
 * @since    Class available since Release 2.0.0RC1
 */
class ParameterModel extends AbstractModel implements Visitable
{
    /**
     * Constructs a new ParameterModel instance.
     *
     * @param string $name Name of the parameter
     */
    public function __construct($paramName, $attributes)
    {
        parent::__construct($attributes);

        $this->name = $paramName;
    }

    /**
     * Gets the name of the parameter.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Gets the name of the parameter.
     *
     * @return int The position of the parameter, left to right,
     *             starting at position #0.
     */
    public function getPosition()
    {
        return $this->struct['position'];
    }

    /**
     * Gets the type of the parameter.
     *
     * @return mixed Blank when none, 'callable', 'array', or class name.
     */
    public function getTypeHint()
    {
        return $this->struct['typeHint'];
    }

    /**
     * Checks whether the parameter allows NULL.
     *
     * If a type is defined, null is allowed only if default value is null.
     *
     * @return bool TRUE if NULL is allowed, otherwise FALSE
     */
    public function allowsNull()
    {
        if (isset($this->struct['typeHint'])) {
            return (
                isset($this->struct['defaultValue'])
                && strtolower($this->struct['defaultValue']) === 'null'
            );
        }
        return true;
    }

    /**
     * Checks if the parameter is optional.
     *
     * @return bool TRUE if the parameter is optional, otherwise FALSE
     */
    public function isOptional()
    {
        return (isset($this->struct['defaultValue']));
    }

    /**
     * Checks if the parameter is passed in by reference.
     *
     * @return bool TRUE if the parameter is passed in by reference, otherwise FALSE
     */
    public function isPassedByReference()
    {
        return $this->struct['byRef'];
    }

    /**
     * Checks if the parameter expects an array.
     *
     * @return bool TRUE if an array is expected, FALSE otherwise.
     */
    public function isArray()
    {
        return (strtolower($this->struct['typeHint']) === 'array');
    }

    /**
     * Checks if a default value for the parameter is available.
     *
     * @return bool TRUE if a default value is available, otherwise FALSE
     */
    public function isDefaultValueAvailable()
    {
        return isset($this->struct['defaultValue']);
    }

    /**
     * Gets the default value of the parameter for a user-defined function or method.
     * If the parameter is not optional a ModelException will be thrown.
     *
     * @return mixed
     * @throws ModelException if the parameter is not optional
     */
    public function getDefaultValue()
    {
        if (isset($this->struct['defaultValue'])) {
            return $this->struct['defaultValue'];
        }
        throw new ModelException(
            sprintf(
                'Parameter #%d [$%s] is not optional.',
                $this->struct['position'],
                $this->name
            )
        );
    }

    /**
     * Returns the string representation of the ParameterModel object.
     *
     * @return string
     */
    public function __toString()
    {
        $eol = "\n";

        return sprintf(
            'Parameter #%d [ <%s> %s%s$%s%s ]%s',
            $this->getPosition(),
            $this->isOptional() ? 'optional' : 'required',
            isset($this->struct['typeHint']) ? $this->struct['typeHint'].' ' : '',
            $this->isPassedByReference() ? '&' : '',
            $this->getName(),
            $this->isDefaultValueAvailable() ? ' = ' . $this->getDefaultValue() : '',
            $eol
        );
    }
}
