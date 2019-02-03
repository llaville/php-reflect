<?php

declare(strict_types=1);

namespace Bartlett\Reflect\Application\Model;

use Bartlett\Reflect\Application\Exception\ModelException;

use PhpParser\Node;
use PhpParser\PrettyPrinter;

/**
 * The ParameterModel class reports information about a parameter.
 *
 * PHP version 7
 *
 * @category PHP
 * @package  bartlett/php-reflect
 * @author   Laurent Laville <pear@laurent-laville.org>
 * @license  https://opensource.org/licenses/BSD-3-Clause The 3-Clause BSD License
 * @link     http://php5.laurent-laville.org/reflect/
 * @since    Class available since Release 2.0.0RC1
 */
class ParameterModel extends AbstractModel
{
    private $position;

    /**
     * Creates a new ParameterModel instance.
     *
     */
    public function __construct(Node\Param $param, $position)
    {
        parent::__construct($param);
        $this->position = $position;
    }

    /**
     * Gets the name of the parameter.
     *
     * @return string
     */
    public function getName()
    {
        return $this->node->var->name;
    }

    /**
     * Gets the name of the parameter.
     *
     * @return int The position of the parameter, left to right,
     *             starting at position #0.
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * Gets the type of the parameter.
     *
     * @return mixed Blank when none, 'Closure', 'callable', 'array', or class name.
     */
    public function getTypeHint()
    {
        $typeHint = $this->node->type;
        if ($typeHint instanceof Node\Name) {
            $typeHint = (string) $typeHint;
        }
        return $typeHint;
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
        if (!empty($this->node->type)) {
            // with type hint, checks if NULL constant provided
            if ($this->node->default instanceof Node\Expr\ConstFetch
                && $this->node->default->name instanceof Node\Name
                && strcasecmp('null', (string)$this->node->default->name) === 0
            ) {
                return true;
            }
            return false;
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
        return $this->isDefaultValueAvailable();
    }

    /**
     * Checks if the parameter is passed in by reference.
     *
     * @return bool TRUE if the parameter is passed in by reference, otherwise FALSE
     */
    public function isPassedByReference()
    {
        return $this->node->byRef;
    }

    /**
     * Checks if the parameter is variadic.
     *
     * @return bool TRUE if the parameter is variadic, otherwise FALSE
     */
    public function isVariadic()
    {
        return $this->node->variadic;
    }

    /**
     * Checks if the parameter expects an array.
     *
     * @return bool TRUE if an array is expected, FALSE otherwise.
     */
    public function isArray()
    {
        return ((string) $this->node->type === 'array');
    }

    /**
     * Checks if the parameter is callable.
     *
     * @return bool TRUE if the parameter is callable, otherwise FALSE
     */
    public function isCallable()
    {
        return ((string) $this->node->type === 'callable');
    }

    /**
     * Checks if a default value for the parameter is available.
     *
     * @return bool TRUE if a default value is available, otherwise FALSE
     */
    public function isDefaultValueAvailable()
    {
        return !empty($this->node->default);
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
        if ($this->isDefaultValueAvailable()) {
            $prettyPrinter = new PrettyPrinter\Standard;
            return $prettyPrinter->prettyPrintExpr($this->node->default);
        }
        throw new ModelException(
            sprintf(
                'Parameter #%d [$%s] is not optional.',
                $this->getPosition(),
                $this->getName()
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

        $typeHint = $this->getTypeHint();
        if (!empty($typeHint)) {
            $typeHint .= ' ';
        }

        return sprintf(
            'Parameter #%d [ <%s> %s%s$%s%s ]%s',
            $this->getPosition(),
            $this->isOptional() ? 'optional' : 'required',
            $typeHint,
            $this->isPassedByReference() ? '&' : '',
            $this->getName(),
            $this->isDefaultValueAvailable() ? ' = ' . $this->getDefaultValue() : '',
            $eol
        );
    }
}
