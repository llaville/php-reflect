<?php

declare(strict_types=1);

namespace Bartlett\Reflect\Application\Model;

use PhpParser\Node;

/**
 * The MethodModel class reports information about a method.
 *
 * PHP version 7
 *
 * @category PHP
 * @package  bartlett/php-reflect
 * @author   Laurent Laville <pear@laurent-laville.org>
 * @license  https://opensource.org/licenses/BSD-3-Clause The 3-Clause BSD License
 * @link     http://php5.laurent-laville.org/reflect/
 */
class MethodModel extends AbstractFunctionModel
{
    protected $declaringClass;

    /**
     * Creates a new MethodModel instance.
     *
     */
    public function __construct($class, Node\Stmt\ClassMethod $method)
    {
        parent::__construct($method);
        $this->declaringClass = $class;
    }

    /**
     * Gets the method modifiers
     *
     * @return int
     */
    public function getModifiers()
    {
        return $this->node->type;
    }

    /**
     * Gets declaring class
     *
     * @return ClassModel
     */
    public function getDeclaringClass()
    {
        return $this->declaringClass;
    }

    /**
     * Checks if the method is abstract.
     *
     * @return bool  TRUE if the method is abstract, otherwise FALSE
     */
    public function isAbstract()
    {
        return $this->node->isAbstract();
    }

    /**
     * Checks if the method is a constructor.
     *
     * @return bool  TRUE if the method is a constructor, otherwise FALSE
     */
    public function isConstructor()
    {
        $name = explode('\\', $this->declaringClass->getName());
        $name = array_pop($name);

        return in_array($this->getShortName(), array('__construct', $name));
    }

    /**
     * Checks if the method is a destructor.
     *
     * @return bool  TRUE if the method is a destructor, otherwise FALSE
     */
    public function isDestructor()
    {
        return $this->getShortName() === '__destruct';
    }

    /**
     * Checks if the method is final.
     *
     * @return bool  TRUE if the method is final, otherwise FALSE
     */
    public function isFinal()
    {
        return $this->node->isFinal();
    }

    /**
     * Checks if the method is static.
     *
     * @return bool  TRUE if the method is static, otherwise FALSE
     */
    public function isStatic()
    {
        return $this->node->isStatic();
    }

    /**
     * Checks if the method is private.
     *
     * @return bool  TRUE if the method is private, otherwise FALSE
     */
    public function isPrivate()
    {
        return $this->node->isPrivate();
    }

    /**
     * Checks if the method is protected.
     *
     * @return bool  TRUE if the method is protected, otherwise FALSE
     */
    public function isProtected()
    {
        return $this->node->isProtected();
    }

    /**
     * Checks if the method is public.
     *
     * @return bool  TRUE if the method is public, otherwise FALSE
     */
    public function isPublic()
    {
        return $this->node->isPublic();
    }

    /**
     * Checks if the method is implicitly public (PHP4 syntax).
     *
     * @return bool
     */
    public function isImplicitlyPublic()
    {
        return $this->node->getAttribute('implicitlyPublic', false);
    }

    /**
     * Returns the string representation of the MethodModel object.
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
        $str = '';

        // Method
        $str .= sprintf(
            'Method [ <%s> %s method %s ] {%s',
            $this->getExtensionName(),
            $visibility,
            $this->getShortName(),
            $eol
        );

        $str .= sprintf(
            '  @@ %s %d - %d%s',
            $this->getFileName(),
            $this->getStartLine(),
            $this->getEndLine(),
            $eol
        );

        // Parameters
        $parameters = $this->getParameters();
        $str .= sprintf(
            '%s  - Parameters [%d] {%s',
            $eol,
            count($parameters),
            $eol
        );
        foreach ($parameters as $parameter) {
            $str .= '    ' . $parameter->__toString();
        }
        $str .= '  }' . $eol;

        $str .= '}' . $eol;

        return $str;
    }
}
