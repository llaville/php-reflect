<?php
/**
 * The Reflect Structure analyser accessible through the AnalyserPlugin.
 *
 * It analyse source code like Sebastian Bergmann phploc solution
 * (https://github.com/sebastianbergmann/phploc), and give a text report
 * as follow :
 *
 * <code>
 * Directories                                         50
 * Files                                              374
 *
 * Structure Analysis
 *   Namespaces                                         1
 *   Interfaces                                        15
 *   Traits                                             0
 *   Classes                                          384
 *     Abstract Classes                                27 (7.03%)
 *     Concrete Classes                               357 (92.97%)
 *   Methods                                         3531
 *     Scope
 *       Non-Static Methods                          3435 (97.28%)
 *       Static Methods                                96 (2.72%)
 *     Visibility
 *       Public Method                               3174 (89.89%)
 *       Protected Method                             207 (5.86%)
 *       Private Method                               150 (4.25%)
 *   Functions                                          0
 *     Named Functions                                  0 (0.00%)
 *     Anonymous Functions                              0 (0.00%)
 *   Constants                                        157
 *     Global Constants                                17 (10.83%)
 *     Class Constants                                140 (89.17%)
 *
 * </code>
 *
 * @category PHP
 * @package  PHP_Reflect
 * @author   Laurent Laville <pear@laurent-laville.org>
 * @license  http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version  GIT: $Id$
 * @link     http://php5.laurent-laville.org/reflect/
 * @link
 */

namespace Bartlett\Reflect\Analyser;

use Bartlett\Reflect\Printer\Text;

use Symfony\Component\Console\Output\OutputInterface;

/**
 * This analyzer collects different count metrics for code artifacts like
 * classes, methods, functions, constants or packages.
 *
 * @category PHP
 * @package  PHP_Reflect
 * @author   Laurent Laville <pear@laurent-laville.org>
 * @license  http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version  Release: @package_version@
 * @link     http://php5.laurent-laville.org/reflect/
 * @since    Class available since Release 2.0.0RC3
 */
class StructureAnalyser extends AbstractAnalyser
{
    /**
     * Initializes all metrics.
     *
     * @return void
     */
    protected function init()
    {
        $this->count = array(
            'interfaces'            => 0,
            'traits'                => 0,
            'classes'               => 0,
            'abstractClasses'       => 0,
            'concreteClasses'       => 0,
            'functions'             => 0,
            'namedFunctions'        => 0,
            'anonymousFunctions'    => 0,
            'methods'               => 0,
            'publicMethods'         => 0,
            'protectedMethods'      => 0,
            'privateMethods'        => 0,
            'nonStaticMethods'      => 0,
            'staticMethods'         => 0,
            'constants'             => 0,
            'classConstants'        => 0,
            'globalConstants'       => 0,
            'testClasses'           => 0,
            'testMethods'           => 0,
        );
    }

    /**
     * Renders analyser report to output.
     *
     * @param object OutputInterface $output Console Output
     *
     * @return void
     */
    public function render(OutputInterface $output)
    {
        $count = $this->count;

        $count['constants'] = $count['classConstants'] + $count['globalConstants'];

        $lines = array();

        $lines['namespaces'] = array(
            '  Namespaces                                %10d',
            array($count['namespaces'])
        );
        $lines['interfaces'] = array(
            '  Interfaces                                %10d',
            array($count['interfaces'])
        );
        $lines['traits'] = array(
            '  Traits                                    %10d',
            array($count['traits'])
        );

        $lines['classes'] = array(
            '  Classes                                   %10d',
            array($count['classes'])
        );
        $lines['abstractClasses'] = array(
            '    Abstract Classes                        %10d (%.2f%%)',
            array(
                $count['abstractClasses'],
                $count['classes'] > 0 ? ($count['abstractClasses'] / $count['classes']) * 100 : 0,
            )
        );
        $lines['concreteClasses'] = array(
            '    Concrete Classes                        %10d (%.2f%%)',
            array(
                $count['concreteClasses'],
                $count['classes'] > 0 ? ($count['concreteClasses'] / $count['classes']) * 100 : 0,
            )
        );

        $lines['methods'] = array(
            '  Methods                                   %10d',
            array($count['methods'])
        );
        $lines['methodsScope'] = array(
            '    Scope',
            array()
        );
        $lines['nonStaticMethods'] = array(
            '      Non-Static Methods                    %10d (%.2f%%)',
            array(
                $count['nonStaticMethods'],
                $count['methods'] > 0 ? ($count['nonStaticMethods'] / $count['methods']) * 100 : 0,
            )
        );
        $lines['staticMethods'] = array(
            '      Static Methods                        %10d (%.2f%%)',
            array(
                $count['staticMethods'],
                $count['methods'] > 0 ? ($count['staticMethods'] / $count['methods']) * 100 : 0,
            )
        );
        $lines['methodsVisibility'] = array(
            '    Visibility',
            array()
        );
        $lines['publicMethods'] = array(
            '      Public Method                         %10d (%.2f%%)',
            array(
                $count['publicMethods'],
                $count['methods'] > 0 ? ($count['publicMethods'] / $count['methods']) * 100 : 0,
            )
        );
        $lines['protectedMethods'] = array(
            '      Protected Method                      %10d (%.2f%%)',
            array(
                $count['protectedMethods'],
                $count['methods'] > 0 ? ($count['protectedMethods'] / $count['methods']) * 100 : 0,
            )
        );
        $lines['privateMethods'] = array(
            '      Private Method                        %10d (%.2f%%)',
            array(
                $count['privateMethods'],
                $count['methods'] > 0 ? ($count['privateMethods'] / $count['methods']) * 100 : 0,
            )
        );

        $lines['functions'] = array(
            '  Functions                                 %10d',
            array($count['functions'])
        );
        $lines['namedFunctions'] = array(
            '    Named Functions                         %10d (%.2f%%)',
            array(
                $count['namedFunctions'],
                $count['functions'] > 0 ? ($count['namedFunctions'] / $count['functions']) * 100 : 0,
            )
        );
        $lines['anonymousFunctions'] = array(
            '    Anonymous Functions                     %10d (%.2f%%)',
            array(
                $count['anonymousFunctions'],
                $count['functions'] > 0 ? ($count['anonymousFunctions'] / $count['functions']) * 100 : 0,
            )
        );

        $lines['constants'] = array(
            '  Constants                                 %10d',
            array($count['constants'])
        );
        $lines['globalConstants'] = array(
            '    Global Constants                        %10d (%.2f%%)',
            array(
                $count['globalConstants'],
                $count['constants'] > 0 ? ($count['globalConstants'] / $count['constants']) * 100 : 0,
            )
        );
        $lines['classConstants'] = array(
            '    Class Constants                         %10d (%.2f%%)',
            array(
                $count['classConstants'],
                $count['constants'] > 0 ? ($count['classConstants'] / $count['constants']) * 100 : 0,
            )
        );

        $output->writeln('<info>Structure Analysis</info>');
        $printer = new Text;
        $printer->write($output, $lines);
    }

    /**
     * Explore all classes (ClassModel), functions (FunctionModel)
     * and constants (ConstantModel) in each namespace (PackageModel).
     *
     * @param object $package Reflect the current namespace explored
     *
     * @return void
     */
    public function visitPackageModel($package)
    {
        parent::visitPackageModel($package);

        foreach ($package->getClasses() as $class) {
            $class->accept($this);
        }

        foreach ($package->getFunctions() as $function) {
            $function->accept($this);
        }

        foreach ($package->getConstants() as $constant) {
            $constant->accept($this);
        }
    }

    /**
     * Explore user classes (ClassModel) found in the current namespace.
     *
     * @param object $class Reflect the current user class explored
     *
     * @return void
     */
    public function visitClassModel($class)
    {
        parent::visitClassModel($class);

        if ($class->isTrait()) {
            $this->count['traits']++;

        } elseif ($class->isInterface()) {
            $this->count['interfaces']++;

        } else {
            $this->count['classes']++;

            if ($this->testClass) {
                $this->count['testClasses']++;
            } else {
                if ($class->isAbstract()) {
                    $this->count['abstractClasses']++;
                } else {
                    $this->count['concreteClasses']++;
                }
                $this->count['classConstants'] += count($class->getConstants());
            }
        }

        foreach ($class->getMethods() as $method) {
            $method->accept($this);
        }
    }

    /**
     * Explore methods (MethodModel) of each user classes
     * found in the current namespace.
     *
     * @param object $method Reflect the current method explored
     *
     * @return void
     */
    public function visitMethodModel($method)
    {
        if ($this->testClass) {
            if (strpos($method->getShortName(), 'test') === 0) {
                $this->count['testMethods']++;
            } elseif (strpos($method->getDocComment(), '@test')) {
                $this->count['testMethods']++;
            }
            return;
        }
        $this->count['methods']++;

        if ($method->isPrivate()) {
            $this->count['privateMethods']++;
        } elseif ($method->isProtected()) {
            $this->count['protectedMethods']++;
        } else {
            $this->count['publicMethods']++;
        }

        if ($method->isStatic()) {
            $this->count['staticMethods']++;
        } else {
            $this->count['nonStaticMethods']++;
        }
    }

    /**
     * Explore user functions (FunctionModel) found in the current namespace.
     *
     * @param object $function Reflect the current user function explored
     *
     * @return void
     */
    public function visitFunctionModel($function)
    {
        $this->count['functions']++;

        if ($function->isClosure()) {
            $this->count['anonymousFunctions']++;
        } else {
            $this->count['namedFunctions']++;
        }
    }

    /**
     * Explore user or magic constants (ConstantModel)
     * found in the current namespace.
     *
     * @param object $constant Reflect the current constant explored
     *
     * @return void
     */
    public function visitConstantModel($constant)
    {
        $this->count['globalConstants']++;
    }
}
