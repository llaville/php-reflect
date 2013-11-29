<?php

namespace Bartlett\Reflect\Model;

class MethodModel
    extends AbstractFunctionModel
    implements Visitable
{
    protected $class_name;

    /**
     * Constructs a new MethodModel instance.
     */
    public function __construct($class, $name)
    {
        parent::__construct();

        $this->short_name = $name;
        $this->class_name = $class;

        $this->name = "$class::$name";
    }

    /**
     * Checks if the method is abstract.
     *
     * @return bool  TRUE if the method is abstract, otherwise FALSE
     */
    public function isAbstract()
    {
        return in_array('abstract', $this->struct['modifiers']);
    }

    /**
     * Checks if the method is a constructor.
     *
     * @return bool  TRUE if the method is a constructor, otherwise FALSE
     */
    public function isConstructor()
    {
        $name = explode('\\', $this->class_name);
        $name = array_pop($name);

        return in_array($this->short_name, array('__construct', $name));
    }

    /**
     * Checks if the method is a destructor.
     *
     * @return bool  TRUE if the method is a destructor, otherwise FALSE
     */
    public function isDestructor()
    {
        return $this->short_name === '__destruct';
    }

    /**
     * Checks if the method is final.
     *
     * @return bool  TRUE if the method is final, otherwise FALSE
     */
    public function isFinal()
    {
        return in_array('final', $this->struct['modifiers']);
    }

    /**
     * Checks if the method is static.
     *
     * @return bool  TRUE if the method is static, otherwise FALSE
     */
    public function isStatic()
    {
        return in_array('static', $this->struct['modifiers']);
    }

    /**
     * Checks if the method is private.
     *
     * @return bool  TRUE if the method is private, otherwise FALSE
     */
    public function isPrivate()
    {
        return $this->struct['visibility'] === 'private';
    }

    /**
     * Checks if the method is protected.
     *
     * @return bool  TRUE if the method is protected, otherwise FALSE
     */
    public function isProtected()
    {
        return $this->struct['visibility'] === 'protected';
    }

    /**
     * Checks if the method is public.
     *
     * @return bool  TRUE if the method is public, otherwise FALSE
     */
    public function isPublic()
    {
        return $this->struct['visibility'] === 'public';
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

        $parameters = $this->getParameters();
        if (count($parameters)) {
            $str .= sprintf(
                '%s  - Parameters [%d] {%s',
                $eol,
                count($parameters),
                $eol
            );
            foreach($parameters as $parameter) {
                $str .= '    ' . $parameter->__toString();
            }
            $str .= '  }' . $eol;
        }
        $str .= '}' . $eol;

        return $str;
    }

}
