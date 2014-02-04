<?php
/**
 * Unit Test Case that covers the Function Model representative.
 *
 * PHP version 5
 *
 * @category   PHP
 * @package    PHP_Reflect
 * @subpackage Tests
 * @author     Laurent Laville <pear@laurent-laville.org>
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    GIT: $Id$
 * @link       http://php5.laurent-laville.org/reflect/
 * @since      Class available since Release 2.0.0RC1
 */

namespace Bartlett\Tests\Reflect\Model;

use Bartlett\Reflect;
use Bartlett\Reflect\ProviderManager;
use Bartlett\Reflect\Provider\SymfonyFinderProvider;
use Symfony\Component\Finder\Finder;

if (!defined('TEST_FILES_PATH')) {
    define(
        'TEST_FILES_PATH',
        dirname(__DIR__) . DIRECTORY_SEPARATOR .
        '_files' . DIRECTORY_SEPARATOR
    );
}

/**
 * Unit Test Case that covers Bartlett\Reflect\Model\FunctionModel
 *
 * @category   PHP
 * @package    PHP_Reflect
 * @subpackage Tests
 * @author     Laurent Laville <pear@laurent-laville.org>
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    Release: @package_version@
 * @link       http://php5.laurent-laville.org/reflect/
 */
class FunctionModelTest extends \PHPUnit_Framework_TestCase
{
    protected static $interfaces;
    protected static $classes;
    protected static $functions;

    /**
     * Sets up the shared fixture.
     *
     * @return void
     * @link   http://phpunit.de/manual/current/en/fixtures.html#fixtures.sharing-fixture
     */
    public static function setUpBeforeClass()
    {
        $finder = new Finder();
        $finder->files()
            ->name('namespaces.php')
            ->in(TEST_FILES_PATH);

        $pm = new ProviderManager;
        $pm->set('test_files', new SymfonyFinderProvider($finder));

        $reflect = new Reflect();
        $reflect->setProviderManager($pm);
        $reflect->parse();

        foreach ($reflect->getInterfaces() as $rc) {
            self::$interfaces[] = $rc;
        }
        foreach ($reflect->getClasses() as $rc) {
            self::$classes[] = $rc;
        }
        foreach ($reflect->getFunctions() as $rf) {
            self::$functions[] = $rf;
        }
    }

    /**
     * Tests the Doc comment accessor.
     *
     *  covers AbstractFunctionModel::getDocComment
     * @return void
     */
    public function testDocCommentAccessor()
    {
        $c = 0;  // class glob\Foo
        $m = 0;  // method myfunction

        $expected = '/**
     * @param stdClass $param
     * @param mixed    $otherparam
     */';

        $methods = self::$classes[$c]->getMethods();

        $this->assertEquals(
            $expected,
            $methods[$m]->getDocComment(),
            $methods[$m]->getName()
            . ' doc comment does not match.'
        );
    }

    /**
     * Tests starting line number accessor.
     *
     *  covers AbstractFunctionModel::getStartLine
     * @return void
     */
    public function testStartLineAccessor()
    {
        $f = 1;  // function glob\myprocess

        $this->assertEquals(
            29,
            self::$functions[$f]->getStartLine(),
            self::$functions[$f]->getName() . ' starting line does not match.'
        );
    }

    /**
     * Tests ending line number accessor.
     *
     *  covers AbstractFunctionModel::getEndLine
     * @return void
     */
    public function testEndLineAccessor()
    {
        $f = 1;  // function glob\myprocess

        $this->assertEquals(
            32,
            self::$functions[$f]->getEndLine(),
            self::$functions[$f]->getName() . ' ending line does not match.'
        );
    }

    /**
     * Tests file name accessor.
     *
     *  covers AbstractFunctionModel::getFileName
     * @return void
     */
    public function testFileNameAccessor()
    {
        $f = 1;  // function glob\myprocess

        $this->assertEquals(
            TEST_FILES_PATH . 'namespaces.php',
            self::$functions[$f]->getFileName(),
            self::$functions[$f]->getName() . ' file name does not match.'
        );
    }

    /**
     * Tests method name accessor.
     *
     *  covers AbstractFunctionModel::getName
     * @return void
     */
    public function testNameAccessor()
    {
        $f = 1;  // function glob\myprocess

        $this->assertEquals(
            'glob\myprocess',
            self::$functions[$f]->getName(),
            self::$functions[$f]->getName() . ' function name does not match.'
        );
    }

    /**
     * Tests method extension name acessor.
     *
     *  covers AbstractFunctionModel::getExtensionName
     * @return void
     */
    public function testExtensionNameAccessor()
    {
        $f = 1;  // function glob\myprocess

        $this->assertEquals(
            'user',
            self::$functions[$f]->getExtensionName(),
            self::$functions[$f]->getName() . ' extension name does not match.'
        );
    }

    /**
     * Tests the namespace name accessor.
     *
     *  covers AbstractFunctionModel::getNamespaceName
     * @return void
     */
    public function testNamespaceNameAccessor()
    {
        $f = 2;  // function nemo\nobody

        $this->assertEquals(
            'nemo',
            self::$functions[$f]->getNamespaceName(),
            self::$functions[$f]->getName() . ' namespace does not match.'
        );
    }

    /**
     * Tests function short name accessor.
     *
     *  covers AbstractFunctionModel::getShortName
     * @return void
     */
    public function testShortNameAccessor()
    {
        $f = 0;  // function glob\singleFunction

        $this->assertEquals(
            'singleFunction',
            self::$functions[$f]->getShortName(),
            self::$functions[$f]->getName() . ' short name does not match.'
        );
    }

    /**
     * Tests the number of parameters that a function defines.
     *
     *  covers AbstractFunctionModel::getNumberOfParameters
     * @return void
     */
    public function testNumberOfParametersAccessor()
    {
        $f = 0;  // function glob\singleFunction

        $this->assertEquals(
            3,
            self::$functions[$f]->getNumberOfParameters(),
            self::$functions[$f]->getName() . ' number of parameters does not match.'
        );
    }

    /**
     * Tests the number of required parameters that a function defines.
     *
     *  covers AbstractFunctionModel::getNumberOfRequiredParameters
     * @return void
     */
    public function testNumberOfRequiredParametersAccessor()
    {
        $f = 0;  // function glob\singleFunction

        $this->assertEquals(
            2,
            self::$functions[$f]->getNumberOfRequiredParameters(),
            self::$functions[$f]->getName() . ' number of required parameters does not match.'
        );
    }

    /**
     * Tests parameters of the class method.
     *
     *  covers AbstractFunctionModel::getParameters
     * @return void
     */
    public function testParametersAccessor()
    {
        $f = 0;  // function glob\singleFunction

        $this->assertCount(
            3,
            self::$functions[$f]->getParameters(),
            self::$functions[$f]->getName() . ' parameters number does not match.'
        );
    }

    /**
     * Tests whether a function is defined in a namespace.
     *
     *  covers AbstractFunctionModel::inNamespace
     * @return void
     */
    public function testInNamespace()
    {
        $f = 0;  // function glob\singleFunction

        $this->assertTrue(
            self::$functions[$f]->inNamespace(),
            self::$functions[$f]->getName() . ' is defined in a namespace.'
        );
    }

    /**
     * Tests whether it's an anonymous function (closure).
     *
     *  covers AbstractFunctionModel::isClosure
     * @return void
     */
    public function testAnonymousFunction()
    {
        $f = 0;  // function glob\singleFunction

        $this->assertFalse(
            self::$functions[$f]->isClosure(),
            self::$functions[$f]->getName() . ' is not an anonymous function.'
        );
    }

    /**
     * Tests whether it's a closure in a user namespace.
     *
     *  covers AbstractFunctionModel::isClosure
     * @return void
     */
    public function testClosureInNamespace()
    {
        $f = 3;  // closure in nemo namespace

        $this->assertTrue(
            self::$functions[$f]->isClosure(),
            self::$functions[$f]->getName() . ' is a closure.'
        );
    }

    /**
     * Tests whether it's an internal function.
     *
     *  covers AbstractFunctionModel::isInternal
     * @return void
     */
    public function testInternalFunction()
    {
        $f = 0;  // function glob\singleFunction

        $this->assertFalse(
            self::$functions[$f]->isInternal(),
            self::$functions[$f]->getName() . ' is a user-defined function.'
        );
    }

    /**
     * Tests string representation of the FunctionModel object
     *
     *  covers MethodModel::__toString
     * @return void
     */
    public function testToString()
    {
        $f = 0;  // function glob\singleFunction

        $expected = <<<EOS
Function [ <user> function glob\\singleFunction ] {
  @@ %path%namespaces.php 25 - 27

  - Parameters [3] {
    Parameter #0 [ <required> Array \$someparam ]
    Parameter #1 [ <required> stdClass \$somethingelse ]
    Parameter #2 [ <optional> \$lastone = NULL ]
  }
}

EOS;
        $this->expectOutputString(
            str_replace('%path%', TEST_FILES_PATH, $expected)
        );

        print(
            self::$functions[$f]->__toString()
        );
    }
}
