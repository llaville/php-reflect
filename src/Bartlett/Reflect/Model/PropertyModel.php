<?php declare(strict_types=1);

/**
 * PropertyModel represents a class property definition.
 *
 * PHP version 7
 *
 * @category PHP
 * @package  PHP_Reflect
 * @author   Laurent Laville <pear@laurent-laville.org>
 * @license  https://opensource.org/licenses/BSD-3-Clause The 3-Clause BSD License
 */

namespace Bartlett\Reflect\Model;

use PhpParser\Node;

/**
 * The PropertyModel class reports information about a class property.
 *
 * @category PHP
 * @package  PHP_Reflect
 * @author   Laurent Laville <pear@laurent-laville.org>
 * @license  https://opensource.org/licenses/BSD-3-Clause The 3-Clause BSD License
 * @since    Class available since Release 2.0.0RC1
 */
class PropertyModel extends AbstractModel
{
    private $declaringClass;

    /**
     * Creates a new PropertyModel instance.
     *
     * @param ClassModel $class
     * @param Node\Stmt\Property $property
     */
    public function __construct(ClassModel $class, Node\Stmt\Property $property)
    {
        parent::__construct($property);
        $this->declaringClass = $class;
    }

    /**
     * Gets declaring class
     *
     * @return ClassModel
     */
    public function getDeclaringClass(): ClassModel
    {
        return $this->declaringClass;
    }

    /**
     * Gets the name of the property.
     *
     * @return string
     */
    public function getName(): string
    {
        return (string) $this->node->props[0]->name;
    }

    /**
     * Gets the property value.
     *
     * @return mixed
     */
    public function getValue()
    {
        if (!empty($this->node->props[0]->default->value)) {
            return $this->node->props[0]->default->value;
        }
        return;
    }

    /**
     * Checks if default value.
     *
     * @return bool TRUE if the property was declared at compile-time, or
     *              FALSE if it was created at run-time.
     */
    public function isDefault(): bool
    {
        return !empty($this->node->props[0]->default);
    }

    /**
     * Checks if the property is private.
     *
     * @return bool  TRUE if the property is private, otherwise FALSE
     */
    public function isPrivate(): bool
    {
        return $this->node->isPrivate();
    }

    /**
     * Checks if the property is protected.
     *
     * @return bool  TRUE if the property is protected, otherwise FALSE
     */
    public function isProtected(): bool
    {
        return $this->node->isProtected();
    }

    /**
     * Checks if the property is public.
     *
     * @return bool  TRUE if the property is public, otherwise FALSE
     */
    public function isPublic(): bool
    {
        return $this->node->isPublic();
    }

    /**
     * Checks if the property is static.
     *
     * @return bool  TRUE if the property is static, otherwise FALSE
     */
    public function isStatic(): bool
    {
        return $this->node->isStatic();
    }

    /**
     * Checks if the property is implicitly public (PHP4 syntax).
     *
     * @return bool
     */
    public function isImplicitlyPublic(): bool
    {
        return $this->node->getAttribute('implicitlyPublic', false);
    }

    /**
     * Returns the string representation of the PropertyModel object.
     *
     * @return string
     */
    public function __toString(): string
    {
        if ($this->isPrivate()) {
            $visibility = 'private';
        } elseif ($this->isProtected()) {
            $visibility = 'protected';
        } else {
            $visibility = 'public';
        }

        $static = $this->isStatic() ? ' static' : '';
        $eol = "\n";

        return sprintf(
            'Property [ %s%s $%s ]%s',
            $visibility,
            $static,
            $this->getName(),
            $eol
        );
    }
}
