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

        $n = 0;

        foreach ($reflect->getPackages() as $package) {
            foreach ($package->getInterfaces() as $rc) {
                self::$interfaces[$n][] = $rc;
            }
            foreach ($package->getClasses() as $rc) {
                self::$classes[$n][] = $rc;
            }
            foreach ($package->getFunctions() as $rf) {
                self::$functions[$n][] = $rf;
            }
            $n++;
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
        $n = 0;  // namespace glob
        $c = 0;  // class glob\Foo
        $m = 'myfunction';

        $methods = self::$classes[$n][$c]->getMethods();

        $expected = '/**
     * @param stdClass $param
     * @param mixed    $otherparam
     */';

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
        $n = 0;  // namespace glob
        $f = 1;  // function glob\myprocess

        $this->assertEquals(
            29,
            self::$functions[$n][$f]->getStartLine(),
            self::$functions[$n][$f]->getName() . ' starting line does not match.'
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
        $n = 0;  // namespace glob
        $f = 1;  // function glob\myprocess

        $this->assertEquals(
            32,
            self::$functions[$n][$f]->getEndLine(),
            self::$functions[$n][$f]->getName() . ' ending line does not match.'
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
        $n = 0;  // namespace glob
        $f = 1;  // function glob\myprocess

        $this->assertEquals(
            TEST_FILES_PATH . 'namespaces.php',
            self::$functions[$n][$f]->getFileName(),
            self::$functions[$n][$f]->getName() . ' file name does not match.'
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
        $n = 0;  // namespace glob
        $f = 1;  // function glob\myprocess

        $this->assertEquals(
            'glob\myprocess',
            self::$functions[$n][$f]->getName(),
            self::$functions[$n][$f]->getName() . ' function name does not match.'
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
        $n = 0;  // namespace glob
        $f = 1;  // function glob\myprocess

        $this->assertEquals(
            'user',
            self::$functions[$n][$f]->getExtensionName(),
            self::$functions[$n][$f]->getName() . ' extension name does not match.'
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
        $n = 1;  // namespace nemo
        $f = 0;  // function nemo\nobody

        $this->assertEquals(
            'nemo',
            self::$functions[$n][$f]->getNamespaceName(),
            self::$functions[$n][$f]->getName() . ' namespace does not match.'
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
        $n = 0;  // namespace glob
        $f = 0;  // function glob\singleFunction

        $this->assertEquals(
            'singleFunction',
            self::$functions[$n][$f]->getShortName(),
            self::$functions[$n][$f]->getName() . ' short name does not match.'
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
        $n = 0;  // namespace glob
        $f = 0;  // function glob\singleFunction

        $this->assertEquals(
            3,
            self::$functions[$n][$f]->getNumberOfParameters(),
            self::$functions[$n][$f]->getName() . ' number of parameters does not match.'
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
        $n = 0;  // namespace glob
        $f = 0;  // function glob\singleFunction

        $this->assertEquals(
            2,
            self::$functions[$n][$f]->getNumberOfRequiredParameters(),
            self::$functions[$n][$f]->getName() . ' number of required parameters does not match.'
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
        $n = 0;  // namespace glob
        $f = 0;  // function glob\singleFunction

        $this->assertCount(
            3,
            self::$functions[$n][$f]->getParameters(),
            self::$functions[$n][$f]->getName() . ' parameters number does not match.'
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
        $n = 0;  // namespace glob
        $f = 0;  // function glob\singleFunction

        $this->assertTrue(
            self::$functions[$n][$f]->inNamespace(),
            self::$functions[$n][$f]->getName() . ' is defined in a namespace.'
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
        $n = 0;  // namespace glob
        $f = 0;  // function glob\singleFunction

        $this->assertFalse(
            self::$functions[$n][$f]->isClosure(),
            self::$functions[$n][$f]->getName() . ' is not an anonymous function.'
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
        $n = 1;  // namespace nemo
        $f = 1;  // closure in nemo namespace

        $this->assertTrue(
            self::$functions[$n][$f]->isClosure(),
            self::$functions[$n][$f]->getName() . ' is a closure.'
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
        $n = 0;  // namespace glob
        $f = 0;  // function glob\singleFunction

        $this->assertFalse(
            self::$functions[$n][$f]->isInternal(),
            self::$functions[$n][$f]->getName() . ' is a user-defined function.'
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
        $n = 0;  // namespace glob
        $f = 0;  // function glob\singleFunction

        $expected = <<<EOS
Function [ <user> function glob\\singleFunction ] {
  @@ %path%namespaces.php 25 - 27

  - Parameters [3] {
    Parameter #0 [ <required> array \$someparam ]
    Parameter #1 [ <required> stdClass \$somethingelse ]
    Parameter #2 [ <optional> \$lastone = NULL ]
  }
}

EOS;
        $this->expectOutputString(
            str_replace('%path%', TEST_FILES_PATH, $expected)
        );

        print(
            self::$functions[$n][$f]->__toString()
        );
    }
}
