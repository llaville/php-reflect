<?php

namespace Bartlett\Reflect\Model;

use Bartlett\Reflect\Exception\ModelException;

class ConstantModel extends AbstractModel implements Visitable
{
    protected $short_name;

    /**
     * Constructs a new ConstantModel instance.
     */
    public function __construct($qualifiedName)
    {
        parent::__construct();

        $this->name = $qualifiedName;

        $parts = explode('::', $qualifiedName);
        if (count($parts) > 1) {
            // class constant
            $this->short_name = array_pop($parts);
        } else {
            // user or magic constants
            $parts = explode('\\', $parts[0]);
            $this->short_name = array_pop($parts);
        }
    }

    public function update($data)
    {
        if ($data['magic']) {
            $data['extension'] = 'core';
        }

        $ns = ltrim($data['namespace'] . '\\', '\\');

        if (strcasecmp('__FILE__', $this->short_name) === 0) {
            $data['value'] = $data['file'];

        } elseif (strcasecmp('__LINE__', $this->short_name) === 0) {
            $data['value'] = $data['line'];

        } elseif (strcasecmp('__DIR__', $this->short_name) === 0) {
            $data['value'] = dirname($data['file']);

        } elseif (strcasecmp('__TRAIT__', $this->short_name) === 0) {
            $data['value'] = $ns . $data['trait'];

        } elseif (strcasecmp('__CLASS__', $this->short_name) === 0) {
            $data['value'] = $ns . $data['class'];

        } elseif (strcasecmp('__METHOD__', $this->short_name) === 0) {
            $data['value'] = $ns . $data['class'] .
                '::' . $data['function'];

        } elseif (strcasecmp('__FUNCTION__', $this->short_name) === 0) {
            $data['value'] = $ns . $data['function'];

        } elseif (strcasecmp('__NAMESPACE__', $this->short_name) === 0) {
            $data['value'] = $data['namespace'];
        }

        parent::update($data);
    }

    /**
     * Get a Doc comment from a constant.
     *
     * @return string
     */
    public function getDocComment()
    {
        return $this->struct['docblock'];
    }

    /**
     * Gets the file name from a user-defined function.
     *
     * @return mixed FALSE for an internal constant (when isInternal() returns TRUE),
     *               otherwise string
     */
    public function getFileName()
    {
        if ($this->isInternal()) {
            return false;
        }
        return $this->struct['file'];
    }

    /**
     * Gets the extension information of this constant.
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
     * Gets the extension name of this constant.
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
     * Get the namespace name where the constant is defined.
     *
     * @return string
     */
    public function getNamespaceName()
    {
        return $this->struct['namespace'];
    }

    /**
     * Gets the constant name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Get the short name of the constant (without the namespace part).
     *
     * @return string
     */
    public function getShortName()
    {
        return $this->short_name;
    }

    /**
     * Gets the constant value.
     *
     * @return mixed
     */
    public function getValue()
    {
        return $this->struct['value'];
    }

    /**
     * Checks whether a constant is defined in a namespace.
     *
     * @return bool TRUE if it's in a namespace, otherwise FALSE
     */
    public function inNamespace()
    {
        return (!empty($this->struct['namespace']));
    }

    /**
     * Checks whether it's an internal constant.
     *
     * @return bool TRUE if it's internal, otherwise FALSE
     */
    public function isInternal()
    {
        return ($this->struct['magic'] || $this->getExtensionName() !== 'user');
    }

    /**
     * Checks whether it's a magic constant.
     *
     * @link http://www.php.net/manual/en/language.constants.predefined.php
     * @return bool TRUE if it's magic, otherwise FALSE
     */
    public function isMagic()
    {
        return $this->struct['magic'];
    }

    /**
     * Returns the string representation of the ConstantModel object.
     *
     * @return string
     */
    public function __toString()
    {
        $eol = "\n";

        return sprintf(
            'Constant [ %s ] { %s }%s',
            $this->getName(),
            $this->getValue(),
            $eol
        );
    }
}
