<?php

namespace Bartlett\Reflect\Model;

use Bartlett\Reflect\Model\AbstractModel;
use Bartlett\Reflect\Model\Visitable;
use Bartlett\Reflect\Exception\ModelException;

/**
 * The ClassModel class reports information about a class.
 */
class ClassModel
    extends AbstractModel
    implements Visitable
{
    /**
     * Constructs a new ClassModel instance.
     */
    public function __construct($qualifiedName)
    {
        parent::__construct();

        $this->name = $qualifiedName;

        $this->struct['interfaces'] = array();
        $this->struct['constants']  = array();
        $this->struct['methods']    = array();
    }

    public function update($data)
    {
        if (isset($data['interfaces'])) {
            $data['interfaces'] = array_merge_recursive(
                $this->struct['interfaces'],
                $data['interfaces']
            );
        }
        elseif (isset($data['constants'])) {
            $data['constants'] = array_merge_recursive(
                $this->struct['constants'],
                $data['constants']
            );
        }
        elseif (isset($data['methods'])) {
            $data['methods'] = array_merge_recursive(
                $this->struct['methods'],
                $data['methods']
            );
        }

        parent::update($data);
    }

    /**
     * Get a Doc comment from a class.
     *
     * @return string
     */
    public function getDocComment()
    {
        return $this->struct['docblock'];
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
                'Extension ' . $this->struct['extension'] . ' does not exist.', 405
            );
        } elseif (!extension_loaded($this->struct['extension'])) {
            throw new ModelException(
                'Extension ' . $this->struct['extension'] . ' does not exist.', 404
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
        }
        catch (ModelException $e) {
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
     * @return array ConstantModel objects reflecting each class constant.
     */
    public function getConstants()
    {
        return array_values($this->struct['constants']);
    }

    /**
     * Gets the defined constant value.
     *
     * @return mixed constant value
     * @throws ModelException if constant is not defined
     */
    public function getConstant($name)
    {
        if (isset($this->struct['constants'][$name])) {
            return $this->struct['constants'][$name]['value'];
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
     * @return bool TRUE if it has the constant, otherwise FALSE
     */
    public function hasConstant($name)
    {
        return array_key_exists($name, $this->struct['constants']);
    }

    /**
     * Gets an array of methods for the class.
     *
     * @return array MethodModel objects reflecting each class method.
     */
    public function getMethods()
    {
        return array_values($this->struct['methods']);
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
     * @return bool TRUE if it has the method, otherwise FALSE
     */
    public function hasMethod($name)
    {
        return array_key_exists($name, $this->struct['methods']);
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
        return $this->struct['interface'] !== FALSE;
    }

    /**
     * Checks whether the class is a trait.
     *
     * @return bool TRUE if the class is a trait, otherwise FALSE
     */
    public function isTrait()
    {
        return $this->struct['trait'] !== FALSE;
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
            if (!empty($this->struct['parent'])) {
                reset($this->struct['parent']);
                list($parent, $values) = each($this->struct['parent']);

                if (isset($values['interfaces'])
                    && is_array($values['interfaces'])
                ) {
                    $interfaces = array_merge($interfaces, $values['interfaces']);
                }
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
            return FALSE;
        }

        $method = '__clone';
        if (array_key_exists($method, $this->struct['methods'])) {
            if (empty($this->struct['methods'][$method]['visibility'])) {
                return TRUE;
            }
            if ($this->struct['methods'][$method]['visibility'] === 'public') {
                return TRUE;
            }
            return FALSE;
        }
        return TRUE;
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
            return FALSE;
        }

        foreach(array('__construct', $this->getShortName()) as $method) {
            if (array_key_exists($method, $this->struct['methods'])) {

                if (empty($this->struct['methods'][$method]['visibility'])) {
                    return TRUE;
                }
                if ($this->struct['methods'][$method]['visibility'] === 'public') {
                    return TRUE;
                }
                return FALSE;
            }
        }
        return TRUE;
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
            return TRUE;
        }

        if (!empty($this->struct['parent'])) {
            // checks second inheritance
            reset($this->struct['parent']);
            list($parent, $values) = each($this->struct['parent']);

            if ($parent === $class) {
                // checks class name
                return TRUE;
            }
            // then checks interfaces implemented
            return in_array($class, $values['interfaces']);
        }
        return FALSE;
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
            foreach($constants as $constant) {
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
            foreach($methods as $method) {
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
