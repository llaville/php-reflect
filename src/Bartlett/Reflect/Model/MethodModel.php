<?php declare(strict_types=1);

/**
 * MethodModel represents a method definition.
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
 * The MethodModel class reports information about a method.
 *
 * @category PHP
 * @package  PHP_Reflect
 * @author   Laurent Laville <pear@laurent-laville.org>
 * @license  https://opensource.org/licenses/BSD-3-Clause The 3-Clause BSD License
 * @since    Class available since Release 2.0.0RC1
 */
class MethodModel extends AbstractFunctionModel
{
    protected $declaringClass;

    /**
     * Creates a new MethodModel instance.
     *
     * @param ClassModel $class
     * @param Node\Stmt\ClassMethod $method
     */
    public function __construct(ClassModel $class, Node\Stmt\ClassMethod $method)
    {
        parent::__construct($method);
        $this->declaringClass = $class;
    }

    /**
     * Gets the method modifiers
     *
     * @return int
     */
    public function getModifiers(): int
    {
        return $this->node->type;
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
     * Checks if the method is abstract.
     *
     * @return bool  TRUE if the method is abstract, otherwise FALSE
     */
    public function isAbstract(): bool
    {
        return $this->node->isAbstract();
    }

    /**
     * Checks if the method is a constructor.
     *
     * @return bool  TRUE if the method is a constructor, otherwise FALSE
     */
    public function isConstructor(): bool
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
    public function isDestructor(): bool
    {
        return $this->getShortName() === '__destruct';
    }

    /**
     * Checks if the method is final.
     *
     * @return bool  TRUE if the method is final, otherwise FALSE
     */
    public function isFinal(): bool
    {
        return $this->node->isFinal();
    }

    /**
     * Checks if the method is static.
     *
     * @return bool  TRUE if the method is static, otherwise FALSE
     */
    public function isStatic(): bool
    {
        return $this->node->isStatic();
    }

    /**
     * Checks if the method is private.
     *
     * @return bool  TRUE if the method is private, otherwise FALSE
     */
    public function isPrivate(): bool
    {
        return $this->node->isPrivate();
    }

    /**
     * Checks if the method is protected.
     *
     * @return bool  TRUE if the method is protected, otherwise FALSE
     */
    public function isProtected(): bool
    {
        return $this->node->isProtected();
    }

    /**
     * Checks if the method is public.
     *
     * @return bool  TRUE if the method is public, otherwise FALSE
     */
    public function isPublic(): bool
    {
        return $this->node->isPublic();
    }

    /**
     * Checks if the method is implicitly public (PHP4 syntax).
     *
     * @return bool
     */
    public function isImplicitlyPublic(): bool
    {
        return $this->node->getAttribute('implicitlyPublic', false);
    }

    /**
     * Returns the string representation of the MethodModel object.
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
