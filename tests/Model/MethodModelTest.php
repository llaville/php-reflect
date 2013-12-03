<?php

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
 * @author Laurent Laville
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

        foreach ($reflect->getInterfaces() as $rc) {
            self::$interfaces[] = $rc;
        }
        foreach ($reflect->getClasses() as $rc) {
            self::$classes[] = $rc;
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

        $this->assertEquals(
            '/* static meth: */',
            self::$classes[$c]->getMethods()[$m]->getDocComment(),
            self::$classes[$c]->getMethods()[$m]->getName()
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

        $this->assertEquals(
            54,
            self::$classes[$c]->getMethods()[$m]->getStartLine(),
            self::$classes[$c]->getMethods()[$m]->getName()
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

        $this->assertEquals(
            57,
            self::$classes[$c]->getMethods()[$m]->getEndLine(),
            self::$classes[$c]->getMethods()[$m]->getName()
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

        $this->assertEquals(
            TEST_FILES_PATH . 'classes.php',
            self::$classes[$c]->getMethods()[$m]->getFileName(),
            self::$classes[$c]->getMethods()[$m]->getName()
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

        $this->assertEquals(
            'MyDestructableClass::dump',
            self::$classes[$c]->getMethods()[$m]->getName(),
            self::$classes[$c]->getMethods()[$m]->getName()
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

        $this->assertEquals(
            'user',
            self::$classes[$c]->getMethods()[$m]->getExtensionName(),
            self::$classes[$c]->getMethods()[$m]->getName()
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

        $this->assertTrue(
            self::$classes[$c]->getMethods()[$m]->isConstructor(),
            self::$classes[$c]->getMethods()[$m]->getName()
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

        $this->assertTrue(
            self::$classes[$c]->getMethods()[$m]->isConstructor(),
            self::$classes[$c]->getMethods()[$m]->getName()
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

        $this->assertTrue(
            self::$classes[$c]->getMethods()[$m]->isDestructor(),
            self::$classes[$c]->getMethods()[$m]->getName()
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

        $this->assertTrue(
            self::$classes[$c]->getMethods()[$m]->isAbstract(),
            self::$classes[$c]->getMethods()[$m]->getName()
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

        $this->assertTrue(
            self::$classes[$c]->getMethods()[$m]->isFinal(),
            self::$classes[$c]->getMethods()[$m]->getName()
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

        $this->assertTrue(
            self::$classes[$c]->getMethods()[$m]->isStatic(),
            self::$classes[$c]->getMethods()[$m]->getName()
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

        $this->assertTrue(
            self::$classes[$c]->getMethods()[$m]->isPrivate(),
            self::$classes[$c]->getMethods()[$m]->getName()
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

        $this->assertTrue(
            self::$classes[$c]->getMethods()[$m]->isProtected(),
            self::$classes[$c]->getMethods()[$m]->getName()
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

        $this->assertTrue(
            self::$classes[$c]->getMethods()[$m]->isPublic(),
            self::$classes[$c]->getMethods()[$m]->getName()
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

        $this->assertCount(
            1,
            self::$interfaces[$i]->getMethods()[$m]->getParameters(),
            self::$interfaces[$i]->getMethods()[$m]->getName()
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

        print(
            self::$classes[$c]->getMethods()[$m]->__toString()
        );
    }

}
