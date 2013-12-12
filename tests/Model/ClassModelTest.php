<?php

namespace Bartlett\Tests\Reflect\Model;

use Bartlett\Reflect;
use Bartlett\Reflect\ProviderManager;
use Bartlett\Reflect\Provider\SymfonyFinderProvider;
use Bartlett\Reflect\Exception\ModelException;
use Symfony\Component\Finder\Finder;

if (!defined('TEST_FILES_PATH')) {
    define(
        'TEST_FILES_PATH',
        dirname(__DIR__) . DIRECTORY_SEPARATOR .
        '_files' . DIRECTORY_SEPARATOR
    );
}

/**
 * Unit Test Case that covers Bartlett\Reflect\Model\ClassModel
 *
 * @author Laurent Laville
 */

class ClassModelTest extends \PHPUnit_Framework_TestCase
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
     * Tests the Doc comment accessor.
     *
     *  covers ClassModel::getDocComment
     * @return void
     */
    public function testDocCommentAccessor()
    {
        $c = 0;  // class Foo implements iB

        $expected = '// short desc for class that implement a unique interface
';
        $this->assertEquals(
            $expected,
            self::$classes[$c]->getDocComment(),
            self::$classes[$c]->getName() . ' doc comment does not match.'
        );
    }

    /**
     * Tests starting line number accessor.
     *
     *  covers ClassModel::getStartLine
     * @return void
     */
    public function testStartLineAccessor()
    {
        $c = 0;  // class Foo implements iB

        $this->assertEquals(
            21,
            self::$classes[$c]->getStartLine(),
            self::$classes[$c]->getName() . ' starting line does not match.'
        );
    }

    /**
     * Tests ending line number accessor.
     *
     *  covers ClassModel::getEndLine
     * @return void
     */
    public function testEndLineAccessor()
    {
        $c = 0;  // class Foo implements iB

        $this->assertEquals(
            33,
            self::$classes[$c]->getEndLine(),
            self::$classes[$c]->getName() . ' ending line does not match.'
        );
    }

    /**
     * Tests file name accessor.
     *
     *  covers ClassModel::getFileName
     * @return void
     */
    public function testFileNameAccessor()
    {
        $c = 0;  // class Foo implements iB

        $this->assertEquals(
            TEST_FILES_PATH . 'classes.php',
            self::$classes[$c]->getFileName(),
            self::$classes[$c]->getName() . ' file name does not match.'
        );
    }

    /**
     * Tests class name accessor.
     *
     *  covers ClassModel::getName
     * @return void
     */
    public function testNameAccessor()
    {
        $c = 0;  // class Foo implements iB

        $this->assertEquals(
            'Foo',
            self::$classes[$c]->getName(),
            self::$classes[$c]->getName() . ' class name does not match.'
        );
    }

    /**
     * Tests the namespace name accessor.
     *
     *  covers ClassModel::getNamespaceName
     * @return void
     */
    public function testNamespaceNameAccessor()
    {
        $c = 0;  // class Foo implements iB

        $this->assertEquals(
            '',
            self::$classes[$c]->getNamespaceName(),
            self::$classes[$c]->getName() . ' namespace does not match.'
        );
    }

    /**
     * Tests class short name accessor.
     *
     *  covers ClassModel::getShortName
     * @return void
     */
    public function testShortNameAccessor()
    {
        $c = 0;  // class Foo implements iB

        $this->assertEquals(
            'Foo',
            self::$classes[$c]->getShortName(),
            self::$classes[$c]->getName() . ' short name does not match.'
        );
    }

    /**
     * Tests class constants accessor.
     *
     *  covers ClassModel::getConstants
     * @return void
     */
    public function testConstantsAccessor()
    {
        $c = 3;  // class Bar

        $this->assertCount(
            2,
            self::$classes[$c]->getConstants(),
            self::$classes[$c]->getName() . ' constants number does not match.'
        );
    }

    /**
     * Tests class constant accessor.
     *
     *  covers ClassModel::getConstant
     * @return void
     */
    public function testConstantAccessor()
    {
        $c = 3;      // class Bar
        $k = 'ONE';  // constant ONE

        $this->assertEquals(
            'Number one',
            self::$classes[$c]->getConstant($k),
            self::$classes[$c]->getName() . "::$k constant value does not match."
        );
    }

    /**
     * Tests class constant accessor.
     *
     *  covers ClassModel::getConstant
     * @return void
     */
    public function testUndefinedConstant()
    {
        try {
            $c = 3;      // class Bar
            $k = 'FOO';  // constant FOO is not implemented

            self::$classes[$c]->getConstant($k);

        } catch (ModelException $expected) {
            $this->assertEquals(
                "Constant [$k] is not defined.",
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
     * Tests whether a specific constant is defined in a class.
     *
     *  covers ClassModel::hasConstant
     * @return void
     */
    public function testHasConstant()
    {
        $c = 3;      // class Bar
        $k = 'TWO';  // constant TWO

        $this->assertTrue(
            self::$classes[$c]->hasConstant($k),
            self::$classes[$c]->getName() . " $k constant does not exist."
        );
    }

    /**
     * Tests class methods accessor.
     *
     *  covers ClassModel::getMethods
     * @return void
     */
    public function testMethodsAccessor()
    {
        $c = 3;  // class Bar

        $this->assertCount(
            2,
            self::$classes[$c]->getMethods(),
            self::$classes[$c]->getName() . ' methods number does not match.'
        );
    }

    /**
     * Tests class method accessor.
     *
     *  covers ClassModel::getMethod
     * @return void
     */
    public function testMethodAccessor()
    {
        $c = 3;  // class Bar
        $m = 'otherfunction';

        $this->assertInstanceOf(
            'Bartlett\Reflect\Model\MethodModel',
            self::$classes[$c]->getMethod($m),
            'This is not a MethodModel object'
        );
    }

    /**
     * Tests class method accessor.
     *
     *  covers ClassModel::getMethod
     * @return void
     */
    public function testUndefinedMethod()
    {
        try {
            $c = 3;      // class Bar
            $m = 'nemo'; // method nemo is not implemented

            self::$classes[$c]->getMethod($m);

        } catch (ModelException $expected) {
            $this->assertEquals(
                "Method Bar::$m does not exist.",
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
     *  covers ClassModel::hasMethod
     * @return void
     */
    public function testHasMethod()
    {
        $c = 3;      // class Bar
        $m = 'otherfunction';

        $this->assertTrue(
            self::$classes[$c]->hasMethod($m),
            self::$classes[$c]->getName() . " $m method does not exist."
        );
    }

    /**
     * Tests whether a class is defined in a namespace.
     *
     *  covers ClassModel::inNamespace
     * @return void
     */
    public function testInNamespace()
    {
        $c = 3;  // class Bar

        $this->assertFalse(
            self::$classes[$c]->inNamespace(),
            self::$classes[$c]->getName() . " is in a namespace."
        );
    }

    /**
     * Tests if the class is abstract.
     *
     *  covers ClassModel::isAbstract
     * @return void
     */
    public function testAbstractClass()
    {
        $c = 1;  // abstract class AbstractClass

        $this->assertTrue(
            self::$classes[$c]->isAbstract(),
            self::$classes[$c]->getName() . ' is not an abstract class.'
        );
    }

    /**
     * Tests if the class is an interface.
     *
     *  covers ClassModel::isInterface
     * @return void
     */
    public function testInterfaceClass()
    {
        $c = 1;  // abstract class AbstractClass

        $this->assertFalse(
            self::$classes[$c]->isInterface(),
            self::$classes[$c]->getName() . ' is an abstract class.'
        );
    }

    /**
     * Tests if the class is a trait.
     *
     *  covers ClassModel::isTrait
     * @return void
     */
    public function testTraitClass()
    {
        $i = 1;  // interface iA

        $this->assertFalse(
            self::$interfaces[$i]->isTrait(),
            self::$interfaces[$i]->getName() . ' is an interface.'
        );
    }

    /**
     * Tests if the class is a user-defined class.
     *
     *  covers ClassModel::isUserDefined
     * @return void
     */
    public function testUserDefinedClass()
    {
        $c = 2;  // class MyDestructableClass

        $this->assertTrue(
            self::$classes[$c]->isUserDefined(),
            self::$classes[$c]->getName() . ' is not a user-defined class.'
        );
    }

    /**
     * Tests if the class is iterateable.
     *
     *  covers ClassModel::isIterateable
     * @return void
     */
    public function testIterateableClass()
    {
        $c = 4;  // class IteratorClass implements Iterator

        $this->assertTrue(
            self::$classes[$c]->isIterateable(),
            self::$classes[$c]->getName() . ' is not iterateable.'
        );
    }

    /**
     * Tests if the class is iterateable by inheritance.
     *
     *  covers ClassModel::isIterateable
     * @return void
     */
    public function testIterateableClassByInheritance()
    {
        $c = 5;  // class DerivedClass

        $this->assertTrue(
            self::$classes[$c]->isIterateable(),
            self::$classes[$c]->getName() . ' is not iterateable.'
        );
    }

    /**
     * Tests if the class is cloneable.
     *
     *  covers ClassModel::isCloneable
     * @return void
     */
    public function testNotCloneableClass()
    {
        $c = 6;  // class NotCloneable

        $this->assertFalse(
            self::$classes[$c]->isCloneable(),
            self::$classes[$c]->getName() . ' is cloneable and should not be.'
        );
    }

    /**
     * Tests if the class is cloneable.
     *
     *  covers ClassModel::isCloneable
     * @return void
     */
    public function testCloneableClass()
    {
        $c = 7;  // class Cloneable

        $this->assertTrue(
            self::$classes[$c]->isCloneable(),
            self::$classes[$c]->getName() . ' is not cloneable.'
        );
    }

    /**
     * Tests if the class is final.
     *
     *  covers ClassModel::isFinal
     * @return void
     */
    public function testNotFinalClass()
    {
        $c = 0;  // class Foo implements iB

        $this->assertFalse(
            self::$classes[$c]->isFinal(),
            self::$classes[$c]->getName() . ' should not be a final class.'
        );
    }

    /**
     * Tests if the class is final.
     *
     *  covers ClassModel::isFinal
     * @return void
     */
    public function testFinalClass()
    {
        $c = 8;  // class TestFinalClass

        $this->assertTrue(
            self::$classes[$c]->isFinal(),
            self::$classes[$c]->getName() . ' is not a final class.'
        );
    }

    /**
     * Tests if the class is instantiable.
     *
     *  covers ClassModel::isInstantiable
     * @return void
     */
    public function testNotInstantiableClass()
    {
        $i = 0; // interface iTemplate

        $this->assertFalse(
            self::$interfaces[$i]->isInstantiable(),
            self::$interfaces[$i]->getName() . ' should not be instantiable.'
        );
    }

    /**
     * Tests if the class is instantiable.
     *
     *  covers ClassModel::isInstantiable
     * @return void
     */
    public function testInstantiableClass()
    {
        $c = 0;  // class Foo implements iB

        $this->assertTrue(
            self::$classes[$c]->isInstantiable(),
            self::$classes[$c]->getName() . ' is not instantiable.'
        );
    }

    /**
     * Tests if the class is a subclass of a specified class.
     *
     *  covers ClassModel::isSubclassOf
     * @return void
     */
    public function testSubclassOfInterface()
    {
        $c = 5;  // class DerivedClass extends IteratorClass
        $n = 'Iterator';

        $this->assertTrue(
            self::$classes[$c]->isSubclassOf($n),
            self::$classes[$c]->getName() . " is not a subclass of $n."
        );
    }

    /**
     * Tests if the class is a subclass of a specified class.
     *
     *  covers ClassModel::isSubclassOf
     * @return void
     */
    public function testSubclassOfClass()
    {
        $c = 5;  // class DerivedClass extends IteratorClass
        $n = 'IteratorClass';

        $this->assertTrue(
            self::$classes[$c]->isSubclassOf($n),
            self::$classes[$c]->getName() . " is not a subclass of $n."
        );
    }

    /**
     * Tests string representation of the ClassModel object
     *
     *  covers ClassModel::__toString
     * @return void
     */
    public function testToString()
    {
        $c = 3;  // class Bar

        $expected = <<<EOS
Class [ <user> class Bar ] {
  @@ %path%classes.php 60 - 72

  - Methods [2] {
    Method [ <user> public method myfunction ] {
      @@ %path%classes.php 65 - 66

      - Parameters [2] {
        Parameter #0 [ <optional> stdClass \$param = NULL ]
        Parameter #1 [ <optional> \$otherparam = TRUE ]
      }
    }

    Method [ <user> protected method otherfunction ] {
      @@ %path%classes.php 68 - 70

      - Parameters [2] {
        Parameter #0 [ <required> Baz \$baz ]
        Parameter #1 [ <required> \$param ]
      }
    }
  }
}

EOS;
        $this->expectOutputString(
            str_replace('%path%', TEST_FILES_PATH, $expected)
        );

        print(
            self::$classes[$c]->__toString()
        );
    }
}
