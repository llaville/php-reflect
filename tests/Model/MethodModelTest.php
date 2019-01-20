<?php

declare(strict_types=1);

/**
 * Unit Test Case that covers the Method Model representative.
 *
 * PHP version 7
 *
 * @category   PHP
 * @package    PHP_Reflect
 * @subpackage Tests
 * @author     Laurent Laville <pear@laurent-laville.org>
 * @license    https://opensource.org/licenses/BSD-3-Clause The 3-Clause BSD License
 * @link       http://php5.laurent-laville.org/reflect/
 * @since      Class available since Release 2.0.0RC1
 */

namespace Bartlett\Tests\Reflect\Model;

/**
 * Unit Test Case that covers Bartlett\Reflect\Model\MethodModel
 *
 * @category   PHP
 * @package    PHP_Reflect
 * @subpackage Tests
 * @author     Laurent Laville <pear@laurent-laville.org>
 * @license    https://opensource.org/licenses/BSD-3-Clause The 3-Clause BSD License
 * @link       http://php5.laurent-laville.org/reflect/
 */
class MethodModelTest extends GenericModelTest
{
    /**
     * Sets up the shared fixture.
     *
     * @return void
     */
    public static function setUpBeforeClass()
    {
        self::$fixture = 'classes.php';
        parent::setUpBeforeClass();
    }

    /**
     * Tests doc comment accessor.
     *
     *  covers Bartlett\Reflect\Model\AbstractModel::getDocComment
     * @group  reflection
     * @return void
     */
    public function testDocCommentAccessor()
    {
        $c = 4;  // abstract class AbstractClass
        $m = 0;  // method lambdaMethod

        $methods = self::$models[$c]->getMethods();

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
     *  covers Bartlett\Reflect\Model\AbstractModel::getStartLine
     * @group  reflection
     * @return void
     */
    public function testStartLineAccessor()
    {
        $c = 5;  // class MyDestructableClass
        $m = 2;  // method dump;

        $methods = self::$models[$c]->getMethods();

        $this->assertEquals(
            57,
            $methods[$m]->getStartLine(),
            $methods[$m]->getName()
            . ' starting line does not match.'
        );
    }

    /**
     * Tests ending line number accessor.
     *
     *  covers Bartlett\Reflect\Model\AbstractModel::getEndLine
     * @group  reflection
     * @return void
     */
    public function testEndLineAccessor()
    {
        $c = 5;  // class MyDestructableClass
        $m = 2;  // method dump;

        $methods = self::$models[$c]->getMethods();

        $this->assertEquals(
            60,
            $methods[$m]->getEndLine(),
            $methods[$m]->getName()
            . ' ending line does not match.'
        );
    }

    /**
     * Tests file name accessor.
     *
     *  covers Bartlett\Reflect\Model\AbstractModel::getFileName
     * @group  reflection
     * @return void
     */
    public function testFileNameAccessor()
    {
        $c = 5;  // class MyDestructableClass
        $m = 2;  // method dump;

        $methods = self::$models[$c]->getMethods();

        $this->assertEquals(
            self::$fixtures . 'classes.php',
            $methods[$m]->getFileName(),
            $methods[$m]->getName()
            . ' file name does not match.'
        );
    }

    /**
     * Tests method name accessor.
     *
     *  covers Bartlett\Reflect\Model\AbstractFunctionModel::getShortName
     * @group  reflection
     * @return void
     */
    public function testNameAccessor()
    {
        $c = 5;  // class MyDestructableClass
        $m = 2;  // method dump;

        $methods = self::$models[$c]->getMethods();

        $this->assertEquals(
            'dump',
            $methods[$m]->getShortName(),
            $methods[$m]->getName()
            . ' method name does not match.'
        );
    }

    /**
     * Tests declaring class of the method.
     *
     *  covers Bartlett\Reflect\Model\MethodModel::getDeclaringClass
     * @group  reflection
     * @return void
     */
    public function testDeclaringClassAccessor()
    {
        $c = 5;  // class MyDestructableClass
        $m = 2;  // method dump;

        $methods = self::$models[$c]->getMethods();

        $this->assertEquals(
            'MyDestructableClass',
            $methods[$m]->getDeclaringClass()->getName(),
            $methods[$m]->getName()
            . ", method #$m declaring class does not match."
        );
    }

    /**
     * Tests method extension name acessor.
     *
     *  covers Bartlett\Reflect\Model\AbstractModel::getExtensionName
     * @group  reflection
     * @return void
     */
    public function testExtensionNameAccessor()
    {
        $c = 5;  // class MyDestructableClass
        $m = 2;  // method dump;

        $methods = self::$models[$c]->getMethods();

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
     *  covers Bartlett\Reflect\Model\MethodModel::isConstructor
     * @group  reflection
     * @return void
     */
    public function testPHP4Constructor()
    {
        $c = 3;  // class Foo implements iB
        $m = 0;  // method Foo

        $methods = self::$models[$c]->getMethods();

        $this->assertTrue(
            $methods[$m]->isConstructor(),
            $methods[$m]->getName()
            . ' is not a class constructor.'
        );
    }

    /**
     * Tests class method is a PHP5 constructor.
     *
     *  covers Bartlett\Reflect\Model\MethodModel::isConstructor
     * @group  reflection
     * @return void
     */
    public function testPHP5Constructor()
    {
        $c = 5;  // class MyDestructableClass
        $m = 0;  // method __construct

        $methods = self::$models[$c]->getMethods();

        $this->assertTrue(
            $methods[$m]->isConstructor(),
            $methods[$m]->getName()
            . ' is not a class constructor.'
        );
    }

    /**
     * Tests class method is a destructor.
     *
     *  covers Bartlett\Reflect\Model\MethodModel::isDestructor
     * @group  reflection
     * @return void
     */
    public function testDestructor()
    {
        $c = 5;  // class MyDestructableClass
        $m = 1;  // method __destruct

        $methods = self::$models[$c]->getMethods();

        $this->assertTrue(
            $methods[$m]->isDestructor(),
            $methods[$m]->getName()
            . ' is not a class destructor.'
        );
    }

    /**
     * Tests class method with abstract keyword.
     *
     *  covers Bartlett\Reflect\Model\MethodModel::isAbstract
     * @group  reflection
     * @return void
     */
    public function testAbstractMethod()
    {
        $c = 4;  // abstract class AbstractClass
        $m = 1;  // method abstractMethod

        $methods = self::$models[$c]->getMethods();

        $this->assertTrue(
            $methods[$m]->isAbstract(),
            $methods[$m]->getName()
            . ' is not an abstract class method.'
        );
    }

    /**
     * Tests class method with final keyword.
     *
     *  covers Bartlett\Reflect\Model\MethodModel::isFinal
     * @group  reflection
     * @return void
     */
    public function testFinalMethod()
    {
        $c = 3;  // class Foo implements iB
        $m = 2;  // method baz

        $methods = self::$models[$c]->getMethods();

        $this->assertTrue(
            $methods[$m]->isFinal(),
            $methods[$m]->getName()
            . ' is not a final class method.'
        );
    }

    /**
     * Tests class method with static keyword.
     *
     *  covers Bartlett\Reflect\Model\MethodModel::isStatic
     * @group  reflection
     * @return void
     */
    public function testStaticMethod()
    {
        $c = 4;  // abstract class AbstractClass
        $m = 0;  // method lambdaMethod

        $methods = self::$models[$c]->getMethods();

        $this->assertTrue(
            $methods[$m]->isStatic(),
            $methods[$m]->getName()
            . ' is not a static class method.'
        );
    }

    /**
     * Tests class method with private visibility.
     *
     *  covers Bartlett\Reflect\Model\MethodModel::isPrivate
     * @group  reflection
     * @return void
     */
    public function testPrivateMethod()
    {
        $c = 3;  // class Foo implements iB
        $m = 1;  // method FooBaz

        $methods = self::$models[$c]->getMethods();

        $this->assertTrue(
            $methods[$m]->isPrivate(),
            $methods[$m]->getName()
            . ' is not a private class method.'
        );
    }

    /**
     * Tests class method with protected visibility.
     *
     *  covers Bartlett\Reflect\Model\MethodModel::isProtected
     * @group  reflection
     * @return void
     */
    public function testProtectedMethod()
    {
        $c = 6;  // class Bar
        $m = 1;  // method otherfunction

        $methods = self::$models[$c]->getMethods();

        $this->assertTrue(
            $methods[$m]->isProtected(),
            $methods[$m]->getName()
            . ' is not a protected class method.'
        );
    }

    /**
     * Tests class method with public visibility.
     *
     *  covers Bartlett\Reflect\Model\MethodModel::isPublic
     * @group  reflection
     * @return void
     */
    public function testPublicMethod()
    {
        $c = 4;  // abstract class AbstractClass
        $m = 0;  // method lambdaMethod

        $methods = self::$models[$c]->getMethods();

        $this->assertTrue(
            $methods[$m]->isPublic(),
            $methods[$m]->getName()
            . ' is not a public class method.'
        );
    }

    /**
     * Tests class method with implicit public visibility.
     *
     *  covers Bartlett\Reflect\Model\MethodModel::isImplicitlyPublic
     * @group  reflection
     * @return void
     */
    public function testImplicitlyPublicMethod()
    {
        $c = 5;  // class MyDestructableClass
        $m = 2;  // method dump;

        $methods = self::$models[$c]->getMethods();

        $this->assertTrue(
            $methods[$m]->isImplicitlyPublic(),
            $methods[$m]->getName()
            . ' is not implicitly public.'
        );
    }

    /**
     * Tests parameters of the class method.
     *
     *  covers Bartlett\Reflect\Model\AbstractFunctionModel::getParameters
     * @group  reflection
     * @return void
     */
    public function testParametersAccessor()
    {
        $i = 2;  // interface iB extends iA
        $m = 0;  // method baz

        $methods = self::$models[$i]->getMethods();

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
     *  covers Bartlett\Reflect\Model\MethodModel::__toString
     * @group  reflection
     * @return void
     */
    public function testToString()
    {
        $c = 6;  // class Bar
        $m = 0; // method myfunction

        $expected = <<<'EOS'
Method [ <user> public method myfunction ] {
  @@ %filename% 68 - 69

  - Parameters [2] {
    Parameter #0 [ <optional> stdClass $param = \NULL ]
    Parameter #1 [ <optional> $otherparam = \TRUE ]
  }
}

EOS;
        $this->expectOutputString(
            str_replace('%filename%', self::$fixture, $expected)
        );

        $methods = self::$models[$c]->getMethods();

        print(
            $methods[$m]->__toString()
        );
    }
}
