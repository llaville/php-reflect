<?php
/**
 * A parent class for FunctionModel.
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
use Bartlett\Reflect\Exception\ModelException;

/**
 * A parent class for concrete FunctionModel.
 *
 * @category PHP
 * @package  PHP_Reflect
 * @author   Laurent Laville <pear@laurent-laville.org>
 * @license  http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version  Release: @package_version@
 * @link     http://php5.laurent-laville.org/reflect/
 * @since    Class available since Release 2.0.0RC1
 */
abstract class AbstractFunctionModel extends AbstractModel
{
    protected $short_name;

    /**
     * Get a Doc comment from a function.
     *
     * @return string
     */
    public function getDocComment()
    {
        return $this->struct['docComment'];
    }

    /**
     * Gets the starting line number of the function.
     *
     * @return int
     * @see    AbstractFunctionModel::getEndLine()
     */
    public function getStartLine()
    {
        return $this->struct['startLine'];
    }

    /**
     * Gets the ending line number of the function.
     *
     * @return int
     * @see    AbstractFunctionModel::getStartLine()
     */
    public function getEndLine()
    {
        return $this->struct['endLine'];
    }

    /**
     * Gets the file name from a user-defined function.
     *
     * @return mixed FALSE for an internal function (when isInternal() returns TRUE),
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
     * Gets the extension information of a function.
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
     * Gets the extension name of the function.
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
     * Get the name of the function.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Get the namespace name where the class or user-function is defined.
     *
     * @return string
     */
    public function getNamespaceName()
    {
        $parts = explode('\\', $this->getName());
        $functionName = array_pop($parts);

        return implode('\\', $parts);
    }

    /**
     * Get the short name of the function (without the namespace part).
     *
     * @return string
     */
    public function getShortName()
    {
        return $this->short_name;
    }

    /**
     * Get the number of parameters that a function defines,
     * both optional and required.
     *
     * @return int The number of parameters
     */
    public function getNumberOfParameters()
    {
        $args = isset($this->struct['arguments'])
            ? $this->struct['arguments'] : array();

        return count($args);
    }

    /**
     * Get the number of required parameters that a function defines.
     *
     * @return int The number of required parameters
     */
    public function getNumberOfRequiredParameters()
    {
        $args = isset($this->struct['arguments'])
            ? $this->struct['arguments'] : array();

        $count = 0;
        foreach ($args as $arg) {
            if (!$arg->isDefaultValueAvailable()) {
                $count++;
            }
        }
        return $count;
    }

    /**
     * Get the parameters as an array of ParameterModel.
     *
     * @return array
     */
    public function getParameters()
    {
        return $this->struct['arguments'];
    }

    /**
     * Checks whether a function is defined in a namespace.
     *
     * @return bool TRUE if it's in a namespace, otherwise FALSE
     */
    public function inNamespace()
    {
        return (!empty($this->struct['namespace']));
    }

    /**
     * Checks whether it's a closure.
     *
     * @return bool TRUE if it's a closure, otherwise FALSE
     */
    public function isClosure()
    {
        return $this->struct['closure'];
    }

    /**
     * Checks whether it's an internal function.
     *
     * @return bool TRUE if it's internal, otherwise FALSE
     */
    public function isInternal()
    {
        return ($this->getExtensionName() !== 'user');
    }
}
