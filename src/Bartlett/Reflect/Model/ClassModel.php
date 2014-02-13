<?php
/**
 * ClassModel represents a class definition.
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
use Bartlett\Reflect\Model\Visitable;
use Bartlett\Reflect\Exception\ModelException;

/**
 * The ClassModel class reports information about a class.
 *
 * @category PHP
 * @package  PHP_Reflect
 * @author   Laurent Laville <pear@laurent-laville.org>
 * @license  http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version  Release: @package_version@
 * @link     http://php5.laurent-laville.org/reflect/
 * @since    Class available since Release 2.0.0RC1
 */
class ClassModel extends AbstractModel implements Visitable
{
    /**
     * Constructs a new ClassModel instance.
     *
     * @param string $qualifiedName The full qualified name of the class
     */
    public function __construct($qualifiedName, $attributes)
    {
        $struct = array(
            'trait'      => false,
            'interface'  => false,
            'parent'     => false,
            'modifiers'  => array(),
            'interfaces' => array(),
            'constants'  => array(),
            'properties' => array(),
            'methods'    => array(),
        );
        $struct = array_merge($struct, $attributes);
        parent::__construct($struct);

        $this->name = ltrim($qualifiedName, '\\');
    }

    /**
     * Get a Doc comment from a class.
     *
     * @return string
     */
    public function getDocComment()
    {
        return $this->struct['docComment'];
    }

    /**
     * Gets the starting line number of the class.
     *
     * @return int
     * @see    ClassModel::getEndLine()
     */
    public function getStartLine()
    {
        return $this->struct['startLine'];
    }

    /**
     * Gets the ending line number of the class.
     *
     * @return int
     * @see    ClassModel::getStartLine()
     */
    public function getEndLine()
    {
        return $this->struct['endLine'];
    }

    /**
     * Gets the file name from a user-defined class.
     *
     * @return string
     */
    public function getFileName()
    {
        return $this->struct['file'];
    }

    /**
     * Gets the interface names.
     *
     * @return array A numerical array with interface names as the values.
     */
    public function getInterfaceNames()
    {
        if (!empty($this->struct['interfaces'])) {
            $interfaces = array();

            foreach ($this->struct['interfaces'] as $interface) {
                if (is_string($interface)) {
                    // build on demand
                    $interfaces[] = new ClassModel($interface, array());
                }
            }
            $this->struct['interfaces'] = $interfaces;
        }

        return $this->struct['interfaces'];
    }

    /**
     * Gets class name
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
     * Gets the parent class
     *
     * @return mixed a ClassModel instance if parent exists, false otherwise
     */
    public function getParentClass()
    {
        if (is_string($this->struct['parent'])) {
            // build on demand
            $obj = new ClassModel($this->struct['parent'], array());
            // lazy loading
            $this->struct['parent'] = $obj;
        }
        return $this->struct['parent'];
    }

    /**
     * Gets the short name of the class, the part without the namespace.
     *
     * @return string
     */
    public function getShortName()
    {
        $parts = explode('\\', $this->getName());

        return array_pop($parts);
    }

    /**
     * Gets the extension information of a class.
     *
     * @return ReflectionExtension instance that contains the extension information
     * @throws ModelException if extension does not exist (user(405) or not loaded(404))
     */
    public function getExtension()
    {
        if ($this->struct['extension'] === 'user') {
            throw new ModelException(
                'Extension ' . $this->struct['extension'] . ' does not exist.',
                405
            );
        } elseif (!extension_loaded($this->struct['extension'])) {
            throw new ModelException(
                'Extension ' . $this->struct['extension'] . ' does not exist.',
                404
            );
        }

        return new \ReflectionExtension($this->struct['extension']);
    }

    /**
     * Gets the extension name of the class.
     *
     * @return string
     */
    public function getExtensionName()
    {
        try {
            $name = $this->getExtension()->getName();

        } catch (ModelException $e) {
            if ($e->getCode() === 404) {
                throw $e;  // re-throws original exception
            }
            $name = 'user';
        }
        return $name;
    }

    /**
     * Gets an array of interfaces for the class.
     *
     * @return array ClassModel objects reflecting each class interface.
     */
    public function getInterfaces()
    {
        return array_values($this->struct['interfaces']);
    }

    /**
     * Gets an array of constants for the class.
     *
     * @return iterator that list ConstantModel objects reflecting each class constant.
     */
    public function getConstants()
    {
        return $this->struct['constants'];
    }

    /**
     * Gets the defined constant value.
     *
     * @param string $name The name of the constant
     *
     * @return mixed constant value
     * @throws ModelException if constant is not defined
     */
    public function getConstant($name)
    {
        if ($this->hasConstant($name)) {
            return $this->struct['constants'][$name]->getValue();
        }
        throw new ModelException(
            sprintf(
                'Constant [%s] is not defined.',
                $name
            )
        );
    }

    /**
     * Checks whether a specific constant is defined in a class.
     *
     * @param string $name The name of the constant being checked for
     *
     * @return bool TRUE if it has the constant, otherwise FALSE
     */
    public function hasConstant($name)
    {
        return isset($this->struct['constants'][$name]);
    }

    /**
     * Gets an array of methods for the class.
     *
     * @return iterator that list MethodModel objects reflecting each class method.
     */
    public function getMethods()
    {
        return $this->struct['methods'];
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
            return $this->struct['methods'][$name];
        }
        throw new ModelException(
            'Method ' . $this->name . '::' .  $name . ' does not exist.'
        );
    }

    /**
     * Checks whether a specific method is defined in a class.
     *
     * @param string $name The name of the method being checked for
     *
     * @return bool TRUE if it has the method, otherwise FALSE
     */
    public function hasMethod($name)
    {
        return isset($this->struct['methods'][$name]);
    }

    /**
     * Gets an array of static properties for the class.
     *
     * @return array Names of static properties
     */
    public function getStaticProperties()
    {
        static $properties;

        if (!isset($properties)) {
            $properties = array();
            foreach ($this->getProperties() as $name => $property) {
                if ($property->isStatic()) {
                    $properties[] = $name;
                }
            }
        }
        return $properties;
    }

    /**
     * Gets value of a static property for the class.
     *
     * @param string Name of static property
     * @return mixed
     * @throws ModelException if the property does not exist or is not static
     */
    public function getStaticPropertyValue($name)
    {
        $property = $this->getProperty($name);
        if ($property->isStatic()) {
            return $property->getValue();
        }
        throw new ModelException(
            'Property ' . $this->name . '::' .  $name . ' is not static.'
        );
    }

    /**
     * Gets an array of properties for the class.
     *
     * @return iterator that list PropertyModel objects reflecting each class property.
     */
    public function getProperties()
    {
        return $this->struct['properties'];
    }

    /**
     * Gets an array of properties defined at compile-time.
     *
     * @return array Names and values of default properties.
     */
    public function getDefaultProperties()
    {
        static $properties;

        if (!isset($properties)) {
            $properties = array();
            foreach ($this->getProperties() as $name => $property) {
                if ($property->isDefault()) {
                    $properties[$name] = $property->getValue();
                }
            }
        }
        return $properties;
    }

    /**
     * Gets a PropertyModel for a class property.
     *
     * @param string $name The property name to reflect.
     *
     * @return PropertyModel
     * @throws ModelException if the property does not exist.
     */
    public function getProperty($name)
    {
        if ($this->hasProperty($name)) {
            return $this->getChild(
                $this->struct['properties'][$name]
            );
        }
        throw new ModelException(
            'Property ' . $this->name . '::' .  $name . ' does not exist.'
        );
    }

    /**
     * Checks whether a specific property is defined in a class.
     *
     * @param string $name The name of the property being checked for
     *
     * @return bool TRUE if it has the property, otherwise FALSE
     */
    public function hasProperty($name)
    {
        $this->getProperties();
        return array_key_exists($name, $this->struct['properties']);
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
        return in_array('abstract', $this->struct['modifiers']);
    }

    /**
     * Checks whether the class is an interface.
     *
     * @return bool TRUE if the class is an interface, otherwise FALSE
     */
    public function isInterface()
    {
        return $this->struct['interface'] !== false;
    }

    /**
     * Checks whether the class is a trait.
     *
     * @return bool TRUE if the class is a trait, otherwise FALSE
     */
    public function isTrait()
    {
        return $this->struct['trait'] !== false;
    }

    /**
     * Checks whether the class is user-defined, as opposed to internal.
     *
     * @return bool TRUE if the class is a user-defined class, otherwise FALSE
     */
    public function isUserDefined()
    {
        return ($this->getExtensionName() === 'user');
    }

    /**
     * Checks whether the class is iterateable.
     *
     * @return bool TRUE if the class is iterateable, otherwise FALSE
     */
    public function isIterateable()
    {
        $interfaces = $this->struct['interfaces'];

        if (!$this->isInterface()) {
            $parent = $this->getParentClass();
            if (!empty($parent)) {
                $interfaces = array_merge(
                    $interfaces,
                    $parent->getInterfaceNames()
                );
            }
        }
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
            if ($this->getMethod($method)->isPublic()) {
                return true;
            }
            return false;
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
        return in_array('final', $this->struct['modifiers']);
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
                if ($this->getMethod($method)->isPublic()) {
                    return true;
                }
                return false;
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
        if (in_array($class, $this->struct['interfaces'])) {
            // checks first if implement a specified interface
            return true;
        }

        $parent = $this->getParentClass();
        if (!empty($parent)) {
            // checks second inheritance

            if ($parent->getName() === $class) {
                // checks class name
                return true;
            }
            // then checks interfaces implemented
            return in_array($class, $parent->getInterfaceNames());
        }
        return false;
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
        $str .= sprintf(
            'Class [ <%s> class %s ] {%s',
            $this->getExtensionName(),
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

        $constants = array();
        if (count($constants)) {
            $str .= sprintf(
                '%s  - Constants [%d] {%s',
                $eol,
                count($constants),
                $eol
            );
            foreach ($constants as $constant) {
                $str .= '    ' . $constant->__toString();
            }
            $str .= '  }' . $eol;
        }

        $methods = $this->getMethods();
        if (count($methods)) {
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
                //array_pop($lines);
                $str  .= implode("$eol    ", $lines);
                $str   = str_replace("$eol    $eol", "$eol$eol", $str);
                $str  .= $eol;
            }
            $str  = rtrim($str);
            $str .= $eol . '  }' . $eol;
        }
        $str .= '}' . $eol;

        return $str;
    }
}
