<?php
/**
 * Reflect v2 can analyse source code like Sebastian Bergmann phploc solution.
 *
 * Here are structure results obtained when parsing Phing 2.6.1 source code :
 *
 * <code>
 * Directories                                         50
 * Files                                              374
 *
 * Structure
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
 * </code>
 *
 * @category PHP
 * @package  PHP_Reflect
 * @author   Laurent Laville <pear@laurent-laville.org>
 * @license  http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version  GIT: $Id$
 * @link     http://php5.laurent-laville.org/reflect/
 * @link     https://github.com/sebastianbergmann/phploc
 * @since    2.0.0RC2
 */

require_once __DIR__ . DIRECTORY_SEPARATOR . 'autoload.php';

use Bartlett\Reflect;
use Bartlett\Reflect\Visitor\AbstractVisitor;
use Bartlett\Reflect\Model\ClassModel;
use Bartlett\Reflect\Model\FunctionModel;
use Bartlett\Reflect\Model\ConstantModel;
use Bartlett\Reflect\ProviderManager;
use Bartlett\Reflect\Provider\SymfonyFinderProvider;
use Symfony\Component\Finder\Finder;
use Symfony\Component\EventDispatcher\GenericEvent;

class Analyser extends AbstractVisitor
{
    private $packages = array();
    private $testClass;
    private $count = array(
        'namespaces'                  => 0,
        'directories'                 => 0,
        'files'                       => 0,
        'interfaces'                  => 0,
        'traits'                      => 0,
        'classes'                     => 0,
        'abstractClasses'             => 0,
        'concreteClasses'             => 0,
        'functions'                   => 0,
        'namedFunctions'              => 0,
        'anonymousFunctions'          => 0,
        'methods'                     => 0,
        'publicMethods'               => 0,
        'protectedMethods'            => 0,
        'privateMethods'              => 0,
        'nonStaticMethods'            => 0,
        'staticMethods'               => 0,
        'constants'                   => 0,
        'classConstants'              => 0,
        'globalConstants'             => 0,
        'testClasses'                 => 0,
        'testMethods'                 => 0,
    );

    public function visitPackageModel($package)
    {
        $this->packages[] = $package->getName();

        foreach ($package as $element) {
            if ($element instanceof ClassModel
                || $element instanceof FunctionModel
                || $element instanceof ConstantModel
            ) {
                $element->accept($this);
            }
        }
    }

    public function visitClassModel($class)
    {
        $this->testClass = false;

        if ($class->isTrait()) {
            $this->count['traits']++;

        } elseif ($class->isInterface()) {
            $this->count['interfaces']++;

        } else {
            $this->count['classes']++;

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

            if ($this->testClass) {
                $this->count['testClasses']++;
            }

            if ($class->isAbstract()) {
                $this->count['abstractClasses']++;
            } else {
                $this->count['concreteClasses']++;
            }
            $this->count['classConstants'] += count($class->getConstants());
        }

        foreach ($class->getMethods() as $method) {
            $method->accept($this);
        }
    }

    public function visitMethodModel($method)
    {
        if ($this->testClass) {
            if (strpos($method->getShortName(), 'test') === 0) {
                $this->count['testMethods']++;
            } elseif (strpos($method->getDocComment(), '@test')) {
                $this->count['testMethods']++;
            }
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

    public function visitFunctionModel($function)
    {
        $this->count['functions']++;

        if ($function->isClosure()) {
            $this->count['anonymousFunctions']++;
        } else {
            $this->count['namedFunctions']++;
        }
    }

    public function visitConstantModel($constant)
    {
        $this->count['globalConstants']++;
    }

    public function __construct($reflect)
    {
        // explore elements results of data source parsed
        foreach ($reflect->getPackages() as $package) {
            $package->accept($this);
        }

        // count number of directory and file parsed
        $directories = array();
        $files       = array();

        foreach ($reflect->getProviderManager()->all() as $alias => $provider) {

            foreach ($provider as $file) {
                $directories[] = $file->getPathInfo();
                $files[]       = $file->getFilename();
            }
        }
        $directories = array_unique($directories);

        $this->count['directories'] = count($directories);
        $this->count['files']       = count($files);
        $this->count['namespaces']  = count($this->packages);
    }

    public function __toString()
    {
        $str = '';

        $count = $this->count;

        $count['constants'] = $count['classConstants'] + $count['globalConstants'];

        if ($count['directories'] > 0) {
            $str .= sprintf(
                "Directories                                 %10d\n" .
                "Files                                       %10d\n\n",
                $count['directories'],
                $count['files']
            );
        }

        $format = <<<END
Structure
  Namespaces                                %10d
  Interfaces                                %10d
  Traits                                    %10d
  Classes                                   %10d
    Abstract Classes                        %10d (%.2f%%)
    Concrete Classes                        %10d (%.2f%%)
  Methods                                   %10d
    Scope
      Non-Static Methods                    %10d (%.2f%%)
      Static Methods                        %10d (%.2f%%)
    Visibility
      Public Method                         %10d (%.2f%%)
      Protected Method                      %10d (%.2f%%)
      Private Method                        %10d (%.2f%%)
  Functions                                 %10d
    Named Functions                         %10d (%.2f%%)
    Anonymous Functions                     %10d (%.2f%%)
  Constants                                 %10d
    Global Constants                        %10d (%.2f%%)
    Class Constants                         %10d (%.2f%%)

END;

        $str .= sprintf(
            $format,
            // Structure
            $count['namespaces'],
            $count['interfaces'],
            $count['traits'],
            $count['classes'],
            $count['abstractClasses'],
            $count['classes'] > 0 ? ($count['abstractClasses'] / $count['classes']) * 100 : 0,
            $count['concreteClasses'],
            $count['classes'] > 0 ? ($count['concreteClasses'] / $count['classes']) * 100 : 0,
            $count['methods'],
            $count['nonStaticMethods'],
            $count['methods'] > 0 ? ($count['nonStaticMethods'] / $count['methods']) * 100 : 0,
            $count['staticMethods'],
            $count['methods'] > 0 ? ($count['staticMethods'] / $count['methods']) * 100 : 0,
            $count['publicMethods'],
            $count['methods'] > 0 ? ($count['publicMethods'] / $count['methods']) * 100 : 0,
            $count['protectedMethods'],
            $count['methods'] > 0 ? ($count['protectedMethods'] / $count['methods']) * 100 : 0,
            $count['privateMethods'],
            $count['methods'] > 0 ? ($count['privateMethods'] / $count['methods']) * 100 : 0,
            $count['functions'],
            $count['namedFunctions'],
            $count['functions'] > 0 ? ($count['namedFunctions'] / $count['functions']) * 100 : 0,
            $count['anonymousFunctions'],
            $count['functions'] > 0 ? ($count['anonymousFunctions'] / $count['functions']) * 100 : 0,
            $count['constants'],
            $count['globalConstants'],
            $count['constants'] > 0 ? ($count['globalConstants'] / $count['constants']) * 100 : 0,
            $count['classConstants'],
            $count['constants'] > 0 ? ($count['classConstants'] / $count['constants']) * 100 : 0
        );

        if ($count['testClasses'] > 0) {
            // Tests
            $str .= sprintf(
                "\nTests\n" .
                "  Classes                                   %10d\n" .
                "  Methods                                   %10d\n",
                $count['testClasses'],
                $count['testMethods']
            );
        }

        return $str;
    }
}

$tgz = 'phar://' . __DIR__ . '/archives/phing-2.6.1.tgz';

$tgzFinder = new Finder();
$tgzFinder->files()
    ->name('*.php')
    ->in($tgz);

$pm = new ProviderManager;
$pm->set('PhingSource', new SymfonyFinderProvider($tgzFinder));

$reflect = new Reflect;
$reflect->setProviderManager($pm);

$progress = strtolower(array_pop($argv));

if ($progress === '--progress') {
    // Add a listener that will echo out files when they are parsed
    $reflect->getEventDispatcher()->addListener(
        'reflect.progress',
        function (GenericEvent $e) {
            printf(
                'Parsing Data source "%s" in progress ... File "%s"' . PHP_EOL,
                $e['source'],
                $e['filename']
            );
        }
    );
}
$reflect->parse();

$analyser = new Analyser($reflect);
echo $analyser;
