<?php
/**
 * Complex model object builder.
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

namespace Bartlett\Reflect;

use Bartlett\Reflect\Model\PackageModel;
use Bartlett\Reflect\Model\ClassModel;
use Bartlett\Reflect\Model\MethodModel;
use Bartlett\Reflect\Model\FunctionModel;
use Bartlett\Reflect\Model\ConstantModel;
use Bartlett\Reflect\Model\IncludeModel;

/**
 * Concrete Builder.
 *
 * @category PHP
 * @package  PHP_Reflect
 * @author   Laurent Laville <pear@laurent-laville.org>
 * @license  http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version  Release: @package_version@
 * @link     http://php5.laurent-laville.org/reflect/
 * @since    Class available since Release 2.0.0RC1
 */
class Builder
{
    private $packages   = array();
    private $classes    = array();
    private $interfaces = array();
    private $traits     = array();
    private $functions  = array();
    private $constants  = array();
    private $includes   = array();

    /**
     * Build a unique package model defined by its qualified name.
     *
     * @param string $qualifiedName Full qualified name of a package/namespace
     *
     * @return PackageModel
     */
    public function buildPackage($qualifiedName)
    {
        if (!isset($this->packages[$qualifiedName])) {
            $this->packages[$qualifiedName] = new PackageModel($qualifiedName);
        }
        return $this->packages[$qualifiedName];
    }

    /**
     * Build a unique class model defined by its qualified name.
     *
     * @param string $qualifiedName Full qualified name of a class
     *
     * @return ClassModel
     */
    public function buildClass($qualifiedName)
    {
        if (!isset($this->classes[$qualifiedName])) {
            $this->classes[$qualifiedName] = new ClassModel($qualifiedName);
        }
        return $this->classes[$qualifiedName];
    }

    /**
     * Build a unique interface model defined by its qualified name.
     *
     * @param string $qualifiedName Full qualified name of an interface
     *
     * @return ClassModel
     */
    public function buildInterface($qualifiedName)
    {
        if (!isset($this->interfaces[$qualifiedName])) {
            $this->interfaces[$qualifiedName] = new ClassModel($qualifiedName);
        }
        return $this->interfaces[$qualifiedName];
    }

    /**
     * Build a unique trait model defined by its qualified name.
     *
     * @param string $qualifiedName Full qualified name of a trait
     *
     * @return ClassModel
     */
    public function buildTrait($qualifiedName)
    {
        if (!isset($this->traits[$qualifiedName])) {
            $this->traits[$qualifiedName] = new ClassModel($qualifiedName);
        }
        return $this->traits[$qualifiedName];
    }

    /**
     * Build a method model defined by its class and method names
     *
     * @param string $className  Name of the class that contains the method
     * @param string $methodName Name of the method
     *
     * @return MethodModel
     */
    public function buildMethod($className, $methodName)
    {
        $method = new MethodModel($className, $methodName);
        return $method;
    }

    /**
     * Build a unique function model defined by its qualified name.
     *
     * @param string $qualifiedName Full qualified name of a function
     *
     * @return FunctionModel
     */
    public function buildFunction($qualifiedName)
    {
        if (!isset($this->functions[$qualifiedName])) {
            $this->functions[$qualifiedName] = new FunctionModel($qualifiedName);
        }
        return $this->functions[$qualifiedName];
    }

    /**
     * Build a unique constant model defined by its qualified name.
     *
     * @param string $qualifiedName Full qualified name of a constant
     *
     * @return ConstantModel
     */
    public function buildConstant($qualifiedName)
    {
        if (!isset($this->constants[$qualifiedName])) {
            $constant = new ConstantModel($qualifiedName);
            if (strpos($qualifiedName, '::')) {
                // do not keep global constant in builder context
                return $constant;
            }
            $this->constants[$qualifiedName] = $constant;
        }
        return $this->constants[$qualifiedName];
    }

    /**
     * Build a unique include model defined by its path.
     *
     * @param string $path Path to the file to include
     *
     * @return IncludeModel
     */
    public function buildInclude($path)
    {
        if (!isset($this->includes[$path])) {
            $this->includes[$path] = new IncludeModel($path);
        }
        return $this->includes[$path];
    }

    /**
     * Build objects from a previous cached request
     *
     * @param array $cacheData List of models store in cache for the current
     *                         file parsed
     *
     * @return void
     */
    public function buildFromCache($cacheData)
    {
        while (!empty($cacheData)) {
            $element = array_shift($cacheData);

            if ($element instanceof ClassModel) {
                $qualifiedName = $element->getName();
                if ($element->isInterface()) {
                    $this->interfaces[$qualifiedName] = $element;

                } elseif ($element->isTrait()) {
                    $this->traits[$qualifiedName] = $element;

                } else {
                    $this->classes[$qualifiedName] = $element;
                }

            } elseif ($element instanceof FunctionModel) {
                $qualifiedName = $element->getName();
                $this->functions[$qualifiedName] = $element;
            }
        }
    }

    /**
     * Returns list of packages built.
     *
     * @return array Array of PackageModel object
     */
    public function getPackages()
    {
        return $this->packages;
    }

    /**
     * Returns list of classes built.
     *
     * @return array Array of ClassModel object
     */
    public function getClasses()
    {
        return $this->classes;
    }

    /**
     * Returns list of interfaces built.
     *
     * @return array Array of ClassModel object
     */
    public function getInterfaces()
    {
        return $this->interfaces;
    }

    /**
     * Returns list of traits built.
     *
     * @return array Array of ClassModel object
     */
    public function getTraits()
    {
        return $this->traits;
    }

    /**
     * Returns list of functions built.
     *
     * @return array Array of FunctionModel object
     */
    public function getFunctions()
    {
        return $this->functions;
    }

    /**
     * Returns list of constants built.
     *
     * @return array Array of ConstantModel object
     */
    public function getConstants()
    {
        return $this->constants;
    }

    /**
     * Returns list of includes built.
     *
     * @return array Array of IncludeModel object
     */
    public function getIncludes()
    {
        return $this->includes;
    }
}
