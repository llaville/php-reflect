<?php
/**
 * Base class to all analysers accessible through the AnalyserPlugin.
 *
 * @category PHP
 * @package  PHP_Reflect
 * @author   Laurent Laville <pear@laurent-laville.org>
 * @license  http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version  GIT: $Id$
 * @link     http://php5.laurent-laville.org/reflect/
 */

namespace Bartlett\Reflect\Analyser;

use Bartlett\Reflect;
use Bartlett\Reflect\Visitor\AbstractVisitor;

/**
 * Provides common metrics for all analysers.
 *
 * @category PHP
 * @package  PHP_Reflect
 * @author   Laurent Laville <pear@laurent-laville.org>
 * @license  http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version  Release: @package_version@
 * @link     http://php5.laurent-laville.org/reflect/
 * @since    Class available since Release 2.0.0RC2
 */
abstract class AbstractAnalyser extends AbstractVisitor implements AnalyserInterface
{
    protected $directories;
    protected $packages;
    protected $testClass;
    protected $count;

    /**
     * Runs the source code analyse
     *
     * @param Reflect $reflect Reverse source code
     *
     * @return array Metrics
     */
    public function analyse($reflect)
    {
        if (method_exists($this, 'init')) {
            $this->init();
        } else {
            $this->count = array();
        }
        $this->count['files'] = 0;
        $this->directories    = array();
        $this->packages       = array();

        // explore elements results of data source parsed
        foreach ($reflect->getPackages() as $package) {
            $package->accept($this);
        }

        // explore files parsed
        foreach ($reflect->getFiles() as $file) {
            $this->count['files']++;
            $this->directories[] = dirname($file->getPathname());
        }
        $directories = array_unique($this->directories);

        $this->count['directories'] = count($directories);
        $this->count['namespaces']  = count($this->packages);

        return $this->count;
    }

    /**
     * Visits a package model.
     *
     * Counts the namespaces in the data source.
     *
     * @param PackageModel $package Represents a namespace in the data source
     *
     * @return void
     */
    public function visitPackageModel($package)
    {
        $this->packages[] = $package->getName();
    }

    /**
     * Visits a class model.
     *
     * Counts the namespaces in the data source.
     *
     * @param ClassModel $class Represents a class in the package
     *
     * @return void
     */
    public function visitClassModel($class)
    {
        $this->testClass = false;

        if (!$class->isTrait()
            && !$class->isInterface()
        ) {
            $parent = $class->getParentClass();

            if ($parent === false) {
                // No ancestry
                // Treat the class as a test case class if the name
                // of the parent class ends with "TestCase".

                if (substr($class->getShortName(), -8) == 'TestCase') {
                    $this->testClass = true;
                }
            } else {
                // Ancestry
                // Treat the class as a test case class if the name
                // of the parent class equals to "PHPUnit_Framework_TestCase".

                if ($parent->getShortName() === 'PHPUnit_Framework_TestCase') {
                    $this->testClass = true;
                }
            }
        }
    }

    public function visitConstantModel($constant)
    {
    }

    public function visitDependencyModel($dependency)
    {
    }

    public function visitFunctionModel($function)
    {
    }

    public function visitIncludeModel($include)
    {
    }

    public function visitMethodModel($method)
    {
    }

    public function visitParameterModel($parameter)
    {
    }

    public function visitPropertyModel($property)
    {
    }

    public function visitUseModel($use)
    {
    }
}
