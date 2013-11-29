<?php

namespace Bartlett\Reflect\Model;

use Bartlett\Reflect\Exception\ModelException;

class PropertyModel
    extends AbstractModel
    implements Visitable
{

    /**
     * Constructs a new PropertyModel instance.
     */
    public function __construct($qualifiedName)
    {
        parent::__construct();

        $this->name = $qualifiedName;
    }

    /**
     * Gets Doc comment from the property.
     *
     * @return string
     */
    public function getDocComment()
    {
        return $this->struct['docblock'];
    }

    /**
     * Gets the name of the property.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
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
     * Checks if a default value for the property is available.
     *
     * @return bool TRUE if a default value is available, otherwise FALSE
     */
    public function isDefault()
    {
        return isset($this->struct['defaultValue']);
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