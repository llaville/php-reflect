<?php
/**
 * Unit Test Case that covers the Method Model representative.
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
 * Unit Test Case that covers Bartlett\Reflect\Model\MethodModel
 *
 * @category   PHP
 * @package    PHP_Reflect
 * @subpackage Tests
 * @author     Laurent Laville <pear@laurent-laville.org>
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    Release: @package_version@
 * @link       http://php5.laurent-laville.org/reflect/
 */
class MethodModelTest extends \PHPUnit_Framework_TestCase
{
    protected static $interfaces;
    protected static $classes;

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
            ->name('classes.php')
            ->in(TEST_FILES_PATH);

        $pm = new ProviderManager;
        $pm->set('test_files', new SymfonyFinderProvider($finder));

        $reflect = new Reflect();
        $reflect->setProviderManager($pm);
        $reflect->parse();

        foreach ($reflect->getPackages() as $package) {
            foreach ($package->getInterfaces() as $rc) {
                self::$interfaces[] = $rc;
            }
            foreach ($package->getClasses() as $rc) {
                self::$classes[] = $rc;
            }
        }
    }

    /**
     * Tests doc comment accessor.
     *
     *  covers MethodModel::getDocComment
     * @return void
     */
    public function testDocCommentAccessor()
    {
        $c = 1;  // abstract class AbstractClass
        $m = 0;  // method lambdaMethod

        $methods = iterator_to_array(
            self::$classes[$c]->getMethods(),
            false
        );

        $this->assertEquals(
            '/** static meth: */',
            $methods[$m]->getDocComment(),
            $methods[$m]->getName()
            . ' doc comment does not match.'
        );
    }

    /**
     * Tests starting line number accessor.
     *
     *  covers MethodModel::getStartLine
     * @return void
     */
    public function testStartLineAccessor()
    {
        $c = 2;  // class MyDestructableClass
        $m = 2;  // method dump

        $methods = iterator_to_array(
            self::$classes[$c]->getMethods(),
            false
        );

        $this->assertEquals(
            54,
            $methods[$m]->getStartLine(),
            $methods[$m]->getName()
            . ' starting line does not match.'
        );
    }

    /**
     * Tests ending line number accessor.
     *
     *  covers MethodModel::getEndLine
     * @return void
     */
    public function testEndLineAccessor()
    {
        $c = 2;  // class MyDestructableClass
        $m = 2;  // method dump

        $methods = iterator_to_array(
            self::$classes[$c]->getMethods(),
            false
        );

        $this->assertEquals(
            57,
            $methods[$m]->getEndLine(),
            $methods[$m]->getName()
            . ' ending line does not match.'
        );
    }

    /**
     * Tests file name accessor.
     *
     *  covers MethodModel::getFileName
     * @return void
     */
    public function testFileNameAccessor()
    {
        $c = 2;  // class MyDestructableClass
        $m = 2;  // method dump

        $methods = iterator_to_array(
            self::$classes[$c]->getMethods(),
            false
        );

        $this->assertEquals(
            TEST_FILES_PATH . 'classes.php',
            $methods[$m]->getFileName(),
            $methods[$m]->getName()
            . ' file name does not match.'
        );
    }

    /**
     * Tests method name accessor.
     *
     *  covers MethodModel::getName
     * @return void
     */
    public function testNameAccessor()
    {
        $c = 2;  // class MyDestructableClass
        $m = 2;  // method dump

        $methods = iterator_to_array(
            self::$classes[$c]->getMethods(),
            false
        );

        $this->assertEquals(
            'MyDestructableClass::dump',
            $methods[$m]->getName(),
            $methods[$m]->getName()
            . ' method name does not match.'
        );
    }

    /**
     * Tests method extension name acessor.
     *
     *  covers MethodModel::getExtensionName
     * @return void
     */
    public function testExtensionNameAccessor()
    {
        $c = 2;  // class MyDestructableClass
        $m = 2;  // method dump

        $methods = iterator_to_array(
            self::$classes[$c]->getMethods(),
            false
        );

        $this->assertEquals(
            'user',
            $methods[$m]->getExtensionName(),
            $methods[$m]->getName()
            . ' extension name does not match.'
        );
    }

    /**
     * Tests class method is a PHP4 constructor.
     *
     *  covers MethodModel::isConstructor
     * @return void
     */
    public function testPHP4Constructor()
    {
        $c = 0;  // class Foo implements iB
        $m = 0;  // method Foo

        $methods = iterator_to_array(
            self::$classes[$c]->getMethods(),
            false
        );

        $this->assertTrue(
            $methods[$m]->isConstructor(),
            $methods[$m]->getName()
            . ' is not a class constructor.'
        );
    }

    /**
     * Tests class method is a PHP5 constructor.
     *
     *  covers MethodModel::isConstructor
     * @return void
     */
    public function testPHP5Constructor()
    {
        $c = 2;  // class MyDestructableClass
        $m = 0;  // method __construct

        $methods = iterator_to_array(
            self::$classes[$c]->getMethods(),
            false
        );

        $this->assertTrue(
            $methods[$m]->isConstructor(),
            $methods[$m]->getName()
            . ' is not a class constructor.'
        );
    }

    /**
     * Tests class method is a destructor.
     *
     *  covers MethodModel::isDestructor
     * @return void
     */
    public function testDestructor()
    {
        $c = 2;  // class MyDestructableClass
        $m = 1;  // method __destruct

        $methods = iterator_to_array(
            self::$classes[$c]->getMethods(),
            false
        );

        $this->assertTrue(
            $methods[$m]->isDestructor(),
            $methods[$m]->getName()
            . ' is not a class destructor.'
        );
    }

    /**
     * Tests class method with abstract keyword.
     *
     *  covers MethodModel::isAbstract
     * @return void
     */
    public function testAbstractMethod()
    {
        $c = 1;  // abstract class AbstractClass
        $m = 1;  // method abstractMethod

        $methods = iterator_to_array(
            self::$classes[$c]->getMethods(),
            false
        );

        $this->assertTrue(
            $methods[$m]->isAbstract(),
            $methods[$m]->getName()
            . ' is not an abstract class method.'
        );
    }

    /**
     * Tests class method with final keyword.
     *
     *  covers MethodModel::isFinal
     * @return void
     */
    public function testFinalMethod()
    {
        $c = 0;  // class Foo implements iB
        $m = 2;  // method baz

        $methods = iterator_to_array(
            self::$classes[$c]->getMethods(),
            false
        );

        $this->assertTrue(
            $methods[$m]->isFinal(),
            $methods[$m]->getName()
            . ' is not a final class method.'
        );
    }

    /**
     * Tests class method with static keyword.
     *
     *  covers MethodModel::isStatic
     * @return void
     */
    public function testStaticMethod()
    {
        $c = 1;  // abstract class AbstractClass
        $m = 0;  // method lambdaMethod

        $methods = iterator_to_array(
            self::$classes[$c]->getMethods(),
            false
        );

        $this->assertTrue(
            $methods[$m]->isStatic(),
            $methods[$m]->getName()
            . ' is not a static class method.'
        );
    }

    /**
     * Tests class method with private visibility.
     *
     *  covers MethodModel::isPrivate
     * @return void
     */
    public function testPrivateMethod()
    {
        $c = 0;  // class Foo implements iB
        $m = 1;  // method FooBaz

        $methods = iterator_to_array(
            self::$classes[$c]->getMethods(),
            false
        );

        $this->assertTrue(
            $methods[$m]->isPrivate(),
            $methods[$m]->getName()
            . ' is not a private class method.'
        );
    }

    /**
     * Tests class method with protected visibility.
     *
     *  covers MethodModel::isProtected
     * @return void
     */
    public function testProtectedMethod()
    {
        $c = 3;  // class Bar
        $m = 1;  // method otherfunction

        $methods = iterator_to_array(
            self::$classes[$c]->getMethods(),
            false
        );

        $this->assertTrue(
            $methods[$m]->isProtected(),
            $methods[$m]->getName()
            . ' is not a protected class method.'
        );
    }

    /**
     * Tests class method with public visibility.
     *
     *  covers MethodModel::isPublic
     * @return void
     */
    public function testPublicMethod()
    {
        $c = 1;  // abstract class AbstractClass
        $m = 0;  // method lambdaMethod

        $methods = iterator_to_array(
            self::$classes[$c]->getMethods(),
            false
        );

        $this->assertTrue(
            $methods[$m]->isPublic(),
            $methods[$m]->getName()
            . ' is not a public class method.'
        );
    }

    /**
     * Tests parameters of the class method.
     *
     *  covers MethodModel::getParameters
     * @return void
     */
    public function testParametersAccessor()
    {
        $i = 2;  // interface iB extends iA
        $m = 0;  // method baz

        $methods = iterator_to_array(
            self::$interfaces[$i]->getMethods(),
            false
        );

        $this->assertCount(
            1,
            $methods[$m]->getParameters(),
            $methods[$m]->getName()
            . ' parameters number does not match.'
        );
    }

    /**
     * Tests string representation of the MethodModel object
     *
     *  covers MethodModel::__toString
     * @return void
     */
    public function testToString()
    {
        $c = 3;  // class Bar
        $m = 0;  // method myfunction

        $expected = <<<EOS
Method [ <user> public method myfunction ] {
  @@ %path%classes.php 65 - 66

  - Parameters [2] {
    Parameter #0 [ <optional> stdClass \$param = NULL ]
    Parameter #1 [ <optional> \$otherparam = TRUE ]
  }
}

EOS;
        $this->expectOutputString(
            str_replace('%path%', TEST_FILES_PATH, $expected)
        );

        $methods = iterator_to_array(
            self::$classes[$c]->getMethods(),
            false
        );

        print(
            $methods[$m]->__toString()
        );
    }
}
