<?php
/**
 * PropertyModel represents a class property definition.
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
 * The PropertyModel class reports information about a class property.
 *
 * @category PHP
 * @package  PHP_Reflect
 * @author   Laurent Laville <pear@laurent-laville.org>
 * @license  http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version  Release: @package_version@
 * @link     http://php5.laurent-laville.org/reflect/
 * @since    Class available since Release 2.0.0RC1
 */
class PropertyModel extends AbstractModel implements Visitable
{
    /**
     * Constructs a new PropertyModel instance.
     *
     * @param string $class The class name that contains the property
     * @param string $name  Name of the property
     */
    public function __construct($class, $name, $attributes)
    {
        $struct = array(
            'compileTime' => true,
            'modifiers'   => array(),
            'visibility'  => 'public',
            'implicitlyPublic' => true,
        );
        $struct = array_merge($struct, $attributes);
        parent::__construct($struct);

        $this->short_name = $name;
        $this->class_name = ltrim($class, '\\');

        $this->name = $this->class_name . "::$name";
    }

    /**
     * Gets class name for the reflected property.
     *
     * @return string
     */
    public function getClassName()
    {
        return $this->class_name;
    }

    /**
     * Gets Doc comment from the property.
     *
     * @return string
     */
    public function getDocComment()
    {
        return $this->struct['docComment'];
    }

    /**
     * Gets the name of the property.
     *
     * @return string
     */
    public function getName()
    {
        return $this->short_name;
    }

    /**
     * Gets the property value.
     *
     * @return mixed
     */
    public function getValue()
    {
        return $this->struct['value'];
    }

    /**
     * Checks if default value.
     *
     * @return bool TRUE if the property was declared at compile-time, or
     *              FALSE if it was created at run-time.
     */
    public function isDefault()
    {
        return isset($this->struct['compileTime']);
    }

    /**
     * Checks if the property is private.
     *
     * @return bool  TRUE if the property is private, otherwise FALSE
     */
    public function isPrivate()
    {
        return $this->struct['visibility'] === 'private';
    }

    /**
     * Checks if the property is protected.
     *
     * @return bool  TRUE if the property is protected, otherwise FALSE
     */
    public function isProtected()
    {
        return $this->struct['visibility'] === 'protected';
    }

    /**
     * Checks if the property is public.
     *
     * @return bool  TRUE if the property is public, otherwise FALSE
     */
    public function isPublic()
    {
        return $this->struct['visibility'] === 'public';
    }

    /**
     * Checks if the property is static.
     *
     * @return bool  TRUE if the property is static, otherwise FALSE
     */
    public function isStatic()
    {
        return in_array('static', $this->struct['modifiers']);
    }

    /**
     * Checks if the property is implicitly public (PHP4 syntax).
     *
     * @return bool
     */
    public function isImplicitlyPublic()
    {
        return $this->struct['implicitlyPublic'];
    }

    /**
     * Returns the string representation of the PropertyModel object.
     *
     * @return string
     */
    public function __toString()
    {
        if ($this->isPrivate()) {
            $visibility = 'private';
        } elseif ($this->isProtected()) {
            $visibility = 'protected';
        } else {
            $visibility = 'public';
        }

        $eol = "\n";

        return sprintf(
            'Property [ <%s> %s %s%s ]%s',
            '',
            $visibility,
            $this->getName(),
            $this->isDefault() ? ' = ' . $this->getValue() : '',
            $eol
        );
    }
}
