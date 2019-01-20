<?php

declare(strict_types=1);

/**
 * Unit Test Case that covers the Class Model representative.
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

use Bartlett\Reflect\Exception\ModelException;

/**
 * Unit Test Case that covers Bartlett\Reflect\Model\ClassModel
 *
 * @category   PHP
 * @package    PHP_Reflect
 * @subpackage Tests
 * @author     Laurent Laville <pear@laurent-laville.org>
 * @license    https://opensource.org/licenses/BSD-3-Clause The 3-Clause BSD License
 * @link       http://php5.laurent-laville.org/reflect/
 */
class ClassModelTest extends GenericModelTest
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
     * Tests the Doc comment accessor.
     *
     *  covers Bartlett\Reflect\Model\AbstractModel::getDocComment
     * @group  reflection
     * @return void
     */
    public function testDocCommentAccessor()
    {
        $c = 3;  // class Foo implements iB

        $expected = '/** short desc for class that implement a unique interface */';
        $this->assertEquals(
            $expected,
            self::$models[$c]->getDocComment(),
            self::$models[$c]->getName() . ' doc comment does not match.'
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
        $c = 3;  // class Foo implements iB

        $this->assertEquals(
            24,
            self::$models[$c]->getStartLine(),
            self::$models[$c]->getName() . ' starting line does not match.'
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
        $c = 3;  // class Foo implements iB

        $this->assertEquals(
            36,
            self::$models[$c]->getEndLine(),
            self::$models[$c]->getName() . ' ending line does not match.'
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
        $c = 3;  // class Foo implements iB

        $this->assertEquals(
            self::$fixture,
            self::$models[$c]->getFileName(),
            self::$models[$c]->getName() . ' file name does not match.'
        );
    }

    /**
     * Tests class name accessor.
     *
     *  covers Bartlett\Reflect\Model\ClassModel::getName
     * @group  reflection
     * @return void
     */
    public function testNameAccessor()
    {
        $c = 3;  // class Foo implements iB

        $this->assertEquals(
            'Foo',
            self::$models[$c]->getName(),
            self::$models[$c]->getName() . ' class name does not match.'
        );
    }

    /**
     * Tests the namespace name accessor.
     *
     *  covers Bartlett\Reflect\Model\ClassModel::getNamespaceName
     * @group  reflection
     * @return void
     */
    public function testNamespaceNameAccessor()
    {
        $c = 3;  // class Foo implements iB

        $this->assertEquals(
            '',
            self::$models[$c]->getNamespaceName(),
            self::$models[$c]->getName() . ' namespace does not match.'
        );
    }

    /**
     * Tests class short name accessor.
     *
     *  covers Bartlett\Reflect\Model\ClassModel::getShortName
     * @group  reflection
     * @return void
     */
    public function testShortNameAccessor()
    {
        $c = 3;  // class Foo implements iB

        $this->assertEquals(
            'Foo',
            self::$models[$c]->getShortName(),
            self::$models[$c]->getName() . ' short name does not match.'
        );
    }

    /**
     * Tests class constants accessor.
     *
     *  covers Bartlett\Reflect\Model\ClassModel::getConstants
     * @group  reflection
     * @return void
     */
    public function testConstantsAccessor()
    {
        $c = 6;  // class Bar

        $this->assertCount(
            2,
            self::$models[$c]->getConstants(),
            self::$models[$c]->getName() . ' constants number does not match.'
        );
    }

    /**
     * Tests class constant accessor.
     *
     *  covers Bartlett\Reflect\Model\ClassModel::getConstant
     * @group  reflection
     * @return void
     */
    public function testConstantAccessor()
    {
        $c = 6;      // class Bar
        $k = 'ONE';  // constant ONE

        $this->assertEquals(
            'Number one',
            self::$models[$c]->getConstant($k),
            self::$models[$c]->getName() . "::$k constant value does not match."
        );
    }

    /**
     * Tests class constant accessor.
     *
     *  covers Bartlett\Reflect\Model\ClassModel::getConstant
     * @group  reflection
     * @return void
     */
    public function testUndefinedConstant()
    {
        $c = 6;      // class Bar
        $k = 'FOO';  // constant FOO is not implemented

        $this->assertFalse(
            self::$models[$c]->getConstant($k),
            "Constant [$k] is defined."
        );
    }

    /**
     * Tests whether a specific constant is defined in a class.
     *
     *  covers Bartlett\Reflect\Model\ClassModel::hasConstant
     * @group  reflection
     * @return void
     */
    public function testHasConstant()
    {
        $c = 6;      // class Bar
        $k = 'TWO';  // constant TWO

        $this->assertTrue(
            self::$models[$c]->hasConstant($k),
            self::$models[$c]->getName() . " $k constant does not exist."
        );
    }

    /**
     * Tests class methods accessor.
     *
     *  covers Bartlett\Reflect\Model\ClassModel::getMethods
     * @group  reflection
     * @return void
     */
    public function testMethodsAccessor()
    {
        $c = 6;  // class Bar

        $this->assertCount(
            2,
            self::$models[$c]->getMethods(),
            self::$models[$c]->getName() . ' methods number does not match.'
        );
    }

    /**
     * Tests class method accessor.
     *
     *  covers Bartlett\Reflect\Model\ClassModel::getMethod
     * @group  reflection
     * @return void
     */
    public function testMethodAccessor()
    {
        $c = 6;  // class Bar
        $m = 'otherfunction';

        $this->assertInstanceOf(
            'Bartlett\Reflect\Model\MethodModel',
            self::$models[$c]->getMethod($m),
            'This is not a MethodModel object'
        );
    }

    /**
     * Tests class method accessor.
     *
     *  covers Bartlett\Reflect\Model\ClassModel::getMethod
     * @group  reflection
     * @return void
     */
    public function testUndefinedMethod()
    {
        try {
            $c = 6;      // class Bar
            $m = 'nemo'; // method nemo is not implemented

            self::$models[$c]->getMethod($m);

        } catch (ModelException $expected) {
            $this->assertEquals(
                "Method $m does not exist.",
                $expected->getMessage(),
                'Expected exception message does not match'
            );
            return;
        }
        $this->fail(
            'An expected Bartlett\Reflect\Exception\ModelException exception' .
            ' has not been raised.'
        );
    }

    /**
     * Tests whether a specific method is defined in a class.
     *
     *  covers Bartlett\Reflect\Model\ClassModel::hasMethod
     * @group  reflection
     * @return void
     */
    public function testHasMethod()
    {
        $c = 6;      // class Bar
        $m = 'otherfunction';

        $this->assertTrue(
            self::$models[$c]->hasMethod($m),
            self::$models[$c]->getName() . " $m method does not exist."
        );
    }

    /**
     * Tests whether a class is defined in a namespace.
     *
     *  covers Bartlett\Reflect\Model\ClassModel::inNamespace
     * @group  reflection
     * @return void
     */
    public function testInNamespace()
    {
        $c = 6;  // class Bar

        $this->assertFalse(
            self::$models[$c]->inNamespace(),
            self::$models[$c]->getName() . " is in a namespace."
        );
    }

    /**
     * Tests if the class is abstract.
     *
     *  covers Bartlett\Reflect\Model\ClassModel::isAbstract
     * @group  reflection
     * @return void
     */
    public function testAbstractClass()
    {
        $c = 4;  // abstract class AbstractClass

        $this->assertTrue(
            self::$models[$c]->isAbstract(),
            self::$models[$c]->getName() . ' is not an abstract class.'
        );
    }

    /**
     * Tests if the class is an interface.
     *
     *  covers Bartlett\Reflect\Model\ClassModel::isInterface
     * @group  reflection
     * @return void
     */
    public function testInterfaceClass()
    {
        $c = 4;  // abstract class AbstractClass

        $this->assertFalse(
            self::$models[$c]->isInterface(),
            self::$models[$c]->getName() . ' is an abstract class.'
        );
    }

    /**
     * Tests if the class is a trait.
     *
     *  covers Bartlett\Reflect\Model\ClassModel::isTrait
     * @group  reflection
     * @return void
     */
    public function testTraitClass()
    {
        $i = 1;  // interface iA

        $this->assertFalse(
            self::$models[$i]->isTrait(),
            self::$models[$i]->getName() . ' is an interface.'
        );
    }

    /**
     * Tests if the class is a user-defined class.
     *
     *  covers Bartlett\Reflect\Model\ClassModel::isUserDefined
     * @group  reflection
     * @return void
     */
    public function testUserDefinedClass()
    {
        $c = 5;  // class MyDestructableClass

        $this->assertTrue(
            self::$models[$c]->isUserDefined(),
            self::$models[$c]->getName() . ' is not a user-defined class.'
        );
    }

    /**
     * Tests if the class is iterateable.
     *
     *  covers Bartlett\Reflect\Model\ClassModel::isIterateable
     * @group  reflection
     * @return void
     */
    public function testIterateableClass()
    {
        $c = 7;  // class IteratorClass implements Iterator

        $this->assertTrue(
            self::$models[$c]->isIterateable(),
            self::$models[$c]->getName() . ' is not iterateable.'
        );
    }

    /**
     * Tests if the class is iterateable by inheritance.
     *
     *  covers Bartlett\Reflect\Model\ClassModel::isIterateable
     * @group  reflection
     * @return void
     */
    public function testIterateableClassByInheritance()
    {
        $this->markTestIncomplete('Not yet fully implemented (FIXME)');

        $c = 8;  // class DerivedClass

        $this->assertTrue(
            self::$models[$c]->isIterateable(),
            self::$models[$c]->getName() . ' is not iterateable.'
        );
    }

    /**
     * Tests if the class is cloneable.
     *
     *  covers Bartlett\Reflect\Model\ClassModel::isCloneable
     * @group  reflection
     * @return void
     */
    public function testNotCloneableClass()
    {
        $c = 9;  // class NotCloneable

        $this->assertFalse(
            self::$models[$c]->isCloneable(),
            self::$models[$c]->getName() . ' is cloneable and should not be.'
        );
    }

    /**
     * Tests if the class is cloneable.
     *
     *  covers Bartlett\Reflect\Model\ClassModel::isCloneable
     * @group  reflection
     * @return void
     */
    public function testCloneableClass()
    {
        $c = 10;  // class Cloneable

        $this->assertTrue(
            self::$models[$c]->isCloneable(),
            self::$models[$c]->getName() . ' is not cloneable.'
        );
    }

    /**
     * Tests if the class is final.
     *
     *  covers Bartlett\Reflect\Model\ClassModel::isFinal
     * @group  reflection
     * @return void
     */
    public function testNotFinalClass()
    {
        $c = 3;  // class Foo implements iB

        $this->assertFalse(
            self::$models[$c]->isFinal(),
            self::$models[$c]->getName() . ' should not be a final class.'
        );
    }

    /**
     * Tests if the class is final.
     *
     *  covers Bartlett\Reflect\Model\ClassModel::isFinal
     * @group  reflection
     * @return void
     */
    public function testFinalClass()
    {
        $c = 11;  // class TestFinalClass

        $this->assertTrue(
            self::$models[$c]->isFinal(),
            self::$models[$c]->getName() . ' is not a final class.'
        );
    }

    /**
     * Tests if the class is instantiable.
     *
     *  covers Bartlett\Reflect\Model\ClassModel::isInstantiable
     * @group  reflection
     * @return void
     */
    public function testNotInstantiableClass()
    {
        $i = 0; // interface iTemplate

        $this->assertFalse(
            self::$models[$i]->isInstantiable(),
            self::$models[$i]->getName() . ' should not be instantiable.'
        );
    }

    /**
     * Tests if the class is instantiable.
     *
     *  covers Bartlett\Reflect\Model\ClassModel::isInstantiable
     * @group  reflection
     * @return void
     */
    public function testInstantiableClass()
    {
        $c = 3;  // class Foo implements iB

        $this->assertTrue(
            self::$models[$c]->isInstantiable(),
            self::$models[$c]->getName() . ' is not instantiable.'
        );
    }

    /**
     * Tests if the class is a subclass of a specified class.
     *
     *  covers Bartlett\Reflect\Model\ClassModel::isSubclassOf
     * @group  reflection
     * @return void
     */
    public function testSubclassOfInterface()
    {
        $this->markTestIncomplete('Not yet fully implemented (FIXME)');

        $c = 8;  // class DerivedClass extends IteratorClass
        $n = 'Iterator';

        $this->assertTrue(
            self::$models[$c]->isSubclassOf($n),
            self::$models[$c]->getName() . " is not a subclass of $n."
        );
    }

    /**
     * Tests if the class is a subclass of a specified class.
     *
     *  covers Bartlett\Reflect\Model\ClassModel::isSubclassOf
     * @group  reflection
     * @return void
     */
    public function testSubclassOfClass()
    {
        $this->markTestIncomplete('Not yet fully implemented (FIXME)');

        $c = 8;  // class DerivedClass extends IteratorClass
        $n = 'IteratorClass';

        $this->assertTrue(
            self::$models[$c]->isSubclassOf($n),
            self::$models[$c]->getName() . " is not a subclass of $n."
        );
    }

    /**
     * Tests string representation of the ClassModel object
     *
     *  covers Bartlett\Reflect\Model\ClassModel::__toString
     * @group  reflection
     * @return void
     */
    public function testToString()
    {
        $c = 6;  // class Bar

        $expected = <<<'EOS'
Class [ <user> class Bar ] {
  @@ %filename% 63 - 75

  - Constants [2] {
    Constant [ ONE ] { Number one }
    Constant [ TWO ] { Number two }
  }

  - Properties [0] {
  }

  - Methods [2] {
    Method [ <user> public method myfunction ] {
      @@ %filename% 68 - 69

      - Parameters [2] {
        Parameter #0 [ <optional> stdClass $param = \NULL ]
        Parameter #1 [ <optional> $otherparam = \TRUE ]
      }
    }

    Method [ <user> protected method otherfunction ] {
      @@ %filename% 71 - 73

      - Parameters [2] {
        Parameter #0 [ <required> Baz $baz ]
        Parameter #1 [ <required> $param ]
      }
    }
  }
}

EOS;
        $this->expectOutputString(
            str_replace('%filename%', self::$fixture, $expected)
        );

        print(
            self::$models[$c]->__toString()
        );
    }
}
