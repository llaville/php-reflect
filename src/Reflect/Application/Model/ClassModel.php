<?php

declare(strict_types=1);

namespace Bartlett\Reflect\Application\Model;

use Bartlett\Reflect\Application\Exception\ModelException;

use PhpParser\Node;
use PhpParser\PrettyPrinter;

/**
 * The ClassModel class reports information about a class.
 *
 * PHP version 7
 *
 * @category PHP
 * @package  bartlett/php-reflect
 * @author   Laurent Laville <pear@laurent-laville.org>
 * @license  https://opensource.org/licenses/BSD-3-Clause The 3-Clause BSD License
 * @link     http://php5.laurent-laville.org/reflect/
 */
class ClassModel extends AbstractModel
{
    const IS_PUBLIC    = 1;
    const IS_PROTECTED = 2;
    const IS_PRIVATE   = 4;
    const IS_STATIC    = 8;
    const IS_ABSTRACT  = 16;
    const IS_FINAL     = 32;

    private $constants;
    private $properties;
    private $staticProperties;
    private $methods;

    /**
     * Gets the class modifiers
     *
     * @return int
     */
    public function getModifiers()
    {
        return $this->node->type;
    }

    /**
     * Gets the interface names.
     *
     * @return array A numerical array with interface names as the values.
     */
    public function getInterfaceNames()
    {
        $interfaces = [];

        if ($this->node instanceof Node\Stmt\Class_) {
            foreach ($this->node->implements as $interface) {
                $interfaces[] = (string) $interface;
            }
        }

        return $interfaces;
    }

    /**
     * Gets class name
     *
     * @return string
     */
    public function getName()
    {
        return (string) $this->node->namespacedName;
    }

    /**
     * Gets the namespace name.
     *
     * @return string
     */
    public function getNamespaceName()
    {
        $parts = $this->node->namespacedName->parts;
        array_pop($parts);
        return implode('\\', $parts);
    }

    /**
     * Gets the parent class.
     *
     * @return mixed string if parent exists, false otherwise
     */
    public function getParentClassName()
    {
        if ($this->isTrait()) {
            return false;
        }
        $parent = $this->node->extends;

        if (!empty($parent)) {
            if ($this->isInterface()) {
                $parent = array_pop($parent);
            }
            return (string) $parent;
        }
        return false;
    }

    /**
     * Gets the short name of the class, the part without the namespace.
     *
     * @return string
     */
    public function getShortName()
    {
        return $this->node->namespacedName->getLast();
    }

    /**
     * Gets the interfaces.
     *
     * @return array An associative array of interfaces, with keys as interface names
     *         and the array values as ClassModel objects.
     */
    public function getInterfaces()
    {
        return $interfaces = [];
    }

    /**
     * Gets the constants.
     *
     * @return array An array of constants. Constant name in key, constant value in value.
     */
    public function getConstants()
    {
        if ($this->constants === null) {
            $prettyPrinter = new PrettyPrinter\Standard;
            // lazy load class constants list
            $this->constants = [];
            foreach ($this->node->stmts as $stmt) {
                if ($stmt instanceof Node\Stmt\ClassConst) {
                    foreach ($stmt->consts as $const) {
                        $this->constants[$const->name->name] = trim(
                            $prettyPrinter->prettyPrintExpr($const->value),
                            '"\''
                        );
                    }
                }
            }
        }
        return $this->constants;
    }

    /**
     * Gets defined constant.
     *
     * @param string $name Name of the constant
     *
     * @return mixed constant value or FALSE if constant does not exist
     */
    public function getConstant($name)
    {
        $constants = $this->getConstants();
        $value = array_key_exists($name, $constants);
        if ($value) {
            $value = $constants[$name];
        }
        return $value;
    }

    /**
     * Checks if constant is defined.
     *
     * @param string $name The name of the constant being checked for
     *
     * @return bool TRUE if it has the constant, otherwise FALSE
     */
    public function hasConstant($name)
    {
        $constants = $this->getConstants();
        return array_key_exists($name, $constants);
    }

    /**
     * Gets an array of methods.
     *
     * @param int $filter Filter the results to include only methods with certain attributes.
     *                    Defaults to no filtering.
     *
     * @return array An array of MethodModel objects reflecting each method.
     */
    public function getMethods($filter = null)
    {
        if ($this->methods === null) {
            // lazy load class methods list
            $this->methods = [];
            foreach ($this->node->stmts as $stmt) {
                if ($stmt instanceof Node\Stmt\ClassMethod) {
                    $stmt->setAttribute('fileName', $this->getFileName());
                    $this->methods[(string) $stmt->name] = new MethodModel($this, $stmt);
                }
            }
        }

        if (isset($filter)) {
            // @TODO must implement the filter feature
            $methods =& $this->methods;
        } else {
            $methods =& $this->methods;
        }

        return array_values($methods);
    }

    /**
     * Gets a MethodModel for a class method.
     *
     * @param string $name The method name to reflect.
     *
     * @return MethodModel
     * @throws ModelException if the method does not exist.
     */
    public function getMethod($name)
    {
        if ($this->hasMethod($name)) {
            return $this->methods[$name];
        }
        throw new ModelException(
            'Method ' . $name . ' does not exist.'
        );
    }

    /**
     * Checks if method is defined.
     *
     * @param string $name Name of the method being checked for
     *
     * @return bool TRUE if it has the method, otherwise FALSE
     */
    public function hasMethod($name)
    {
        $this->getMethods();
        return array_key_exists($name, $this->methods);
    }

    /**
     * Gets the static properties.
     *
     * @return array An array of static properties. Property name in key, property value in value.
     */
    public function getStaticProperties()
    {
        if ($this->staticProperties === null) {
            // lazy load class properties list
            foreach ($this->node->stmts as $stmt) {
                if ($stmt instanceof Node\Stmt\Property) {
                    if ($stmt->isStatic() === false) {
                        continue;
                    }
                    foreach ($stmt->props as $prop) {
                        $this->staticProperties[] = new PropertyModel($this, $prop);
                    }
                }
            }
        }
        return $this->staticProperties;
    }

    /**
     * Gets static property value.
     *
     * @param string Name of static property
     *
     * @return mixed
     * @throws ModelException if the property does not exist or is not static
     */
    public function getStaticPropertyValue($name)
    {
        $properties = $this->getStaticProperties();

        if (array_key_exists($name, $properties)) {
            return $properties[$name];
        }
        throw new ModelException(
            'Property ' . $name . ' does not exist or is not static.'
        );
    }

    /**
     * Gets the properties.
     *
     * @return array An array of PropertyModel objects reflecting each property.
     */
    public function getProperties()
    {
        if ($this->properties === null) {
            // lazy load class properties list
            $this->properties = [];
            foreach ($this->node->stmts as $stmt) {
                if ($stmt instanceof Node\Stmt\Property) {
                    $this->properties[] = new PropertyModel($this, $stmt);
                }
            }
        }
        return array_values($this->properties);
    }

    /**
     * Gets default properties.
     *
     * @return array An array of default properties, with the key being the name of the property
     *         and the value being the default value of the property
     *         or NULL if the property doesn't have a default value.
     *         The function does not distinguish between static and non static properties
     *         and does not take visibility modifiers into account.
     */
    public function getDefaultProperties()
    {
    }

    /**
     * Gets a PropertyModel for a class property.
     *
     * @param string $name Property name to reflect.
     *
     * @return PropertyModel
     * @throws ModelException if the property does not exist.
     */
    public function getProperty($name)
    {
        if ($this->hasProperty($name)) {
            return $this->properties[$name];
        }
        throw new ModelException(
            'Property ' . $name . ' does not exist.'
        );
    }

    /**
     * Checks if property is defined.
     *
     * @param string $name Name of the property being checked for
     *
     * @return bool TRUE if it has the property, otherwise FALSE
     */
    public function hasProperty($name)
    {
        $this->getProperties();
        return array_key_exists($name, $this->properties);
    }

    /**
     * Checks if this class is defined in a namespace.
     *
     * @return bool TRUE if it's in a namespace, otherwise FALSE
     */
    public function inNamespace()
    {
        $ns = $this->getNamespaceName();
        return !empty($ns);
    }

    /**
     * Checks if the class is abstract.
     *
     * @return bool TRUE if the class is abstract, otherwise FALSE
     */
    public function isAbstract()
    {
        return $this->node instanceof Node\Stmt\Class_
            && $this->node->isAbstract();
    }

    /**
     * Checks whether the class is an interface.
     *
     * @return bool TRUE if the class is an interface, otherwise FALSE
     */
    public function isInterface()
    {
        return $this->node instanceof Node\Stmt\Interface_;
    }

    /**
     * Checks whether the class is a trait.
     *
     * @return bool TRUE if the class is a trait, otherwise FALSE
     */
    public function isTrait()
    {
        return $this->node instanceof Node\Stmt\Trait_;
    }

    /**
     * Checks whether the class is user-defined, as opposed to internal.
     *
     * @return bool TRUE if the class is a user-defined class, otherwise FALSE
     */
    public function isUserDefined()
    {
        return ($this->extension === 'user');
    }

    /**
     * Checks whether the class is iterateable.
     *
     * @return bool TRUE if the class is iterateable, otherwise FALSE
     */
    public function isIterateable()
    {
        $interfaces = $this->getInterfaceNames();

        // @TODO must finished to implement feature (see unit tests)
        return in_array('Iterator', $interfaces);
    }

    /**
     * Checks whether this class is cloneable.
     *
     * @return bool TRUE if the class is cloneable, FALSE otherwise.
     */
    public function isCloneable()
    {
        if ($this->isTrait() || $this->isInterface() || $this->isAbstract()) {
            return false;
        }

        $method = '__clone';
        if ($this->hasMethod($method)) {
            return $this->getMethod($method)->isPublic();
        }
        return true;
    }

    /**
     * Checks whether this class is final.
     *
     * @return bool TRUE if the class is final, FALSE otherwise.
     */
    public function isFinal()
    {
        if ($this->node instanceof Node\Stmt\Class_) {
            return $this->node->isFinal();
        }
        return false;
    }

    /**
     * Checks whether this class is instantiable.
     *
     * @return bool TRUE if the class is instantiable, FALSE otherwise.
     */
    public function isInstantiable()
    {
        if ($this->isTrait() || $this->isInterface() || $this->isAbstract()) {
            return false;
        }

        foreach (array('__construct', $this->getShortName()) as $method) {
            if ($this->hasMethod($method)) {
                return $this->getMethod($method)->isPublic();
            }
        }
        return true;
    }

    /**
     * Checks if the class is a subclass of a specified class
     * or implements a specified interface.
     *
     * @param string $class The class name being checked against.
     *
     * @return bool TRUE if the class is a subclass, FALSE otherwise.
     */
    public function isSubclassOf($class)
    {
        return false;  // @FIXME see unit tests
    }

    /**
     * Returns the string representation of the ClassModel object.
     *
     * @return string
     */
    public function __toString()
    {
        $eol = "\n";
        $str = '';

        $extends    = $this->getParentClassName() ? : '';
        $interfaces = $this->getInterfaceNames();
        $implements = empty($interfaces) ? '' : implode(', ', $interfaces);

        if ($this->isInterface()) {
            $type = 'interface';
        } elseif ($this->isTrait()) {
            $type = 'trait';
        } else {
            $type = 'class';
        }

        $modifiers = '';
        if ($this->isFinal()) {
            $modifiers .= ' final';
        }
        if ($this->isAbstract()) {
            $modifiers .= ' abstract';
        }

        $str .= sprintf(
            '%s [ <%s>%s %s %s%s%s ] {%s',
            ucfirst($type),
            $this->getExtensionName(),
            $modifiers,
            $type,
            $this->getName(),
            !empty($extends) ? " extends $extends" : '',
            !empty($implements) ? " implements $implements" : '',
            $eol
        );

        $str .= sprintf(
            '  @@ %s %d - %d%s',
            $this->getFileName(),
            $this->getStartLine(),
            $this->getEndLine(),
            $eol
        );

        // Constants
        $constants = $this->getConstants();
        $str .= sprintf(
            '%s  - Constants [%d] {%s',
            $eol,
            count($constants),
            $eol
        );
        foreach ($constants as $name => $value) {
            $str .= '    ' .
                sprintf(
                    'Constant [ %s ] { %s }%s',
                    $name,
                    $value,
                    $eol
                )
            ;
        }
        $str .= '  }' . $eol;

        // Properties
        $properties = $this->getProperties();
        $str .= sprintf(
            '%s  - Properties [%d] {%s',
            $eol,
            count($properties),
            $eol
        );
        foreach ($properties as $property) {
            $str .= '    ';
            // re-indent each property ouput
            $lines = explode($eol, $property->__toString());
            $str  .= implode("$eol    ", $lines);
            $str   = str_replace("$eol    $eol", "$eol", $str);
            $str  .= $eol;
        }
        $str  = rtrim($str);
        $str .= $eol . '  }' . $eol;

        // Methods
        $methods = $this->getMethods();
        $str .= sprintf(
            '%s  - Methods [%d] {%s',
            $eol,
            count($methods),
            $eol
        );
        foreach ($methods as $method) {
            $str .= '    ';
            // re-indent each method ouput
            $lines = explode($eol, $method->__toString());
            $str  .= implode("$eol    ", $lines);
            $str   = str_replace("$eol    $eol", "$eol$eol", $str);
            $str  .= $eol;
        }
        $str  = rtrim($str);
        $str .= $eol . '  }' . $eol;

        $str .= '}' . $eol;

        return $str;
    }
}
