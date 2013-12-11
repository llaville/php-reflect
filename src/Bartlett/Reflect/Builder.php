<?php

namespace Bartlett\Reflect;

use Bartlett\Reflect\Model\PackageModel;
use Bartlett\Reflect\Model\ClassModel;
use Bartlett\Reflect\Model\MethodModel;
use Bartlett\Reflect\Model\FunctionModel;
use Bartlett\Reflect\Model\ConstantModel;
use Bartlett\Reflect\Model\IncludeModel;

class Builder
{
    private $packages   = array();
    private $classes    = array();
    private $interfaces = array();
    private $traits     = array();
    private $functions  = array();
    private $constants  = array();
    private $includes   = array();

    public function buildPackage($qualifiedName)
    {
        if (!isset($this->packages[$qualifiedName])) {
            $this->packages[$qualifiedName] = new PackageModel($qualifiedName);
        }
        return $this->packages[$qualifiedName];
    }

    public function buildClass($qualifiedName)
    {
        if (!isset($this->classes[$qualifiedName])) {
            $this->classes[$qualifiedName] = new ClassModel($qualifiedName);
        }
        return $this->classes[$qualifiedName];
    }

    public function buildInterface($qualifiedName)
    {
        if (!isset($this->interfaces[$qualifiedName])) {
            $this->interfaces[$qualifiedName] = new ClassModel($qualifiedName);
        }
        return $this->interfaces[$qualifiedName];
    }

    public function buildTrait($qualifiedName)
    {
        if (!isset($this->traits[$qualifiedName])) {
            $this->traits[$qualifiedName] = new ClassModel($qualifiedName);
        }
        return $this->traits[$qualifiedName];
    }

    public function buildMethod($className, $methodName)
    {
        $method = new MethodModel($className, $methodName);
        return $method;
    }

    public function buildFunction($qualifiedName)
    {
        if (!isset($this->functions[$qualifiedName])) {
            $this->functions[$qualifiedName] = new FunctionModel($qualifiedName);
        }
        return $this->functions[$qualifiedName];
    }

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

    public function buildInclude($path)
    {
        if (!isset($this->includes[$path])) {
            $this->includes[$path] = new IncludeModel($path);
        }
        return $this->includes[$path];
    }

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

    public function getPackages()
    {
        return $this->packages;
    }

    public function getClasses()
    {
        return $this->classes;
    }

    public function getInterfaces()
    {
        return $this->interfaces;
    }

    public function getTraits()
    {
        return $this->traits;
    }

    public function getFunctions()
    {
        return $this->functions;
    }

    public function getConstants()
    {
        return $this->constants;
    }

    public function getIncludes()
    {
        return $this->includes;
    }
}
