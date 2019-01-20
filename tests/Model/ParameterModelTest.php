<?php

declare(strict_types=1);

/**
 * Unit Test Case that covers the Parameter Model representative.
 *
 * PHP version 5
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
 * Unit Test Case that covers Bartlett\Reflect\Model\ParameterModel
 *
 * @category   PHP
 * @package    PHP_Reflect
 * @subpackage Tests
 * @author     Laurent Laville <pear@laurent-laville.org>
 * @license    https://opensource.org/licenses/BSD-3-Clause The 3-Clause BSD License
 * @link       http://php5.laurent-laville.org/reflect/
 */
class ParameterModelTest extends GenericModelTest
{
    /**
     * Sets up the shared fixture.
     *
     * @return void
     */
    public static function setUpBeforeClass()
    {
        self::$fixture = 'functions.php';
        parent::setUpBeforeClass();
    }

    /**
     * Tests name of the parameter.
     *
     *  covers Bartlett\Reflect\Model\ParameterModel::getName
     * @group  reflection
     * @return void
     */
    public function testNameAccessor()
    {
        $c = 1;  // class Foo
        $m = 1;  // method otherfunction
        $p = 0;  // parameter $baz

        $methods    = self::$models[$c]->getMethods();
        $parameters = $methods[$m]->getParameters();

        $this->assertEquals(
            'baz',
            $parameters[$p]->getName(),
            $methods[$m]->getName()
            . ", parameter #$p name does not match."
        );
    }

    /**
     * Tests position of the parameter.
     *
     *  covers Bartlett\Reflect\Model\ParameterModel::getPosition
     * @group  reflection
     * @return void
     */
    public function testPositionAccessor()
    {
        $f = 2;  // function singleFunction
        $p = 1;  // parameter $somethingelse

        $parameters = self::$models[$f]->getParameters();

        $this->assertEquals(
            1,
            $parameters[$p]->getPosition(),
            self::$models[$f]->getName() . ", parameter #$p position does not match."
        );
    }

    /**
     * Tests type hinting of the parameter.
     *
     *  covers Bartlett\Reflect\Model\ParameterModel::getTypeHint
     * @group  reflection
     * @return void
     */
    public function testTypeHintAccessor()
    {
        $f = 2;  // function singleFunction
        $p = 0;  // parameter $someparam

        $parameters = self::$models[$f]->getParameters();

        $this->assertEquals(
            'array',
            $parameters[$p]->getTypeHint(),
            self::$models[$f]->getName() . ", parameter #$p type hint does not match."
        );
    }

    /**
     * Tests whether the parameter allows NULL
     * when type hint is defined without default value for a class method.
     *
     *  covers Bartlett\Reflect\Model\ParameterModel::allowsNull
     * @group  reflection
     * @return void
     */
    public function testAllowsNullWhenOnlyTypeHintDefinedOnClassMethod()
    {
        $i = 0;  // interface iB extends iA
        $m = 0;  // method baz
        $p = 0;  // parameter $baz

        $methods    = self::$models[$i]->getMethods();
        $parameters = $methods[$m]->getParameters();

        $this->assertFalse(
            $parameters[$p]->allowsNull(),
            self::$models[$i]->getName()
            . '::'
            . $methods[$m]->getName()
            . ", parameter #$p does not allow null with type hint without null default value."
        );
    }

    /**
     * Tests whether the parameter allows NULL
     * when type hint is defined with default value is null for a class method.
     *
     *  covers Bartlett\Reflect\Model\ParameterModel::allowsNull
     * @group  reflection
     * @return void
     */
    public function testAllowsNullWhenTypeHintDefinedWithNullDefaultValueOnClassMethod()
    {
        $c = 1;  // class Foo
        $m = 0;  // method myfunction
        $p = 0;  // parameter $param

        $methods    = self::$models[$c]->getMethods();
        $parameters = $methods[$m]->getParameters();

        $this->assertTrue(
            $parameters[$p]->allowsNull(),
            self::$models[$c]->getName()
            . '::'
            . $methods[$m]->getName()
            . ", parameter #$p allows null with type hint and null default value."
        );
    }

    /**
     * Tests whether the parameter allows NULL
     * when type hint is not defined for a class method.
     *
     *  covers Bartlett\Reflect\Model\ParameterModel::allowsNull
     * @group  reflection
     * @return void
     */
    public function testAllowsNullWithoutTypeHintDefinedOnClassMethod()
    {
        $c = 1;  // class Foo
        $m = 1;  // method otherfunction
        $p = 1;  // parameter $param

        $methods    = self::$models[$c]->getMethods();
        $parameters = $methods[$m]->getParameters();

        $this->assertTrue(
            $parameters[$p]->allowsNull(),
            $methods[$m]->getName()
            . ", parameter #$p allows null without type hint."
        );
    }

    /**
     * Tests whether the parameter allows NULL
     * for a user-defined function.
     *
     *  covers Bartlett\Reflect\Model\ParameterModel::allowsNull
     * @group  reflection
     * @return void
     */
    public function testAllowsNullOnUserFunction()
    {
        $f = 2;  // function singleFunction
        $p = 2;  // parameter $lastone

        $parameters = self::$models[$f]->getParameters();

        $this->assertTrue(
            $parameters[$p]->allowsNull(),
            self::$models[$f]->getName() . ", parameter #$p allows null when default value is null."
        );
    }

    /**
     * Tests if the parameter is optional.
     *
     *  covers Bartlett\Reflect\Model\ParameterModel::isOptional
     * @group  reflection
     * @return void
     */
    public function testIsOptional()
    {
        $f = 2;  // function singleFunction
        $p = 1;  // parameter $somethingelse

        $parameters = self::$models[$f]->getParameters();

        $this->assertFalse(
            $parameters[$p]->isOptional(),
            self::$models[$f]->getName() . ", parameter #$p is required."
        );
    }

    /**
     * Checks if the parameter is passed in by reference.
     *
     *  covers Bartlett\Reflect\Model\ParameterModel::isPassedByReference
     * @group  reflection
     * @return void
     */
    public function testIsPassedByReference()
    {
        $f = 3;  // function myprocess
        $p = 1;  // parameter $myresult

        $parameters = self::$models[$f]->getParameters();

        $this->assertTrue(
            $parameters[$p]->isPassedByReference(),
            self::$models[$f]->getName() . ", parameter #$p is passed by reference."
        );
    }

    /**
     * Checks if the parameter is variadic.
     *
     *  covers Bartlett\Reflect\Model\ParameterModel::isVariadic
     * @group  reflection
     * @link   http://php.net/manual/en/migration56.new-features.php#migration56.new-features.variadics
     * @return void
     */
    public function testIsVariadic()
    {
        $f = 3;  // function myprocess
        $p = 2;  // parameter $opt

        $parameters = self::$models[$f]->getParameters();

        $this->assertTrue(
            $parameters[$p]->isVariadic(),
            self::$models[$f]->getName() . ", parameter #$p is not variadic."
        );
    }

    /**
     * Tests if the parameter expects an array.
     *
     *  covers Bartlett\Reflect\Model\ParameterModel::isArray
     * @group  reflection
     * @return void
     */
    public function testIsArray()
    {
        $f = 2;  // function singleFunction
        $p = 0;  // parameter $someparam

        $parameters = self::$models[$f]->getParameters();

        $this->assertTrue(
            $parameters[$p]->isArray(),
            self::$models[$f]->getName() . ", parameter #$p expects an array."
        );
    }

    /**
     * Tests if the parameter expects a callback.
     *
     *  covers Bartlett\Reflect\Model\ParameterModel::isCallable
     * @group  reflection
     * @return void
     */
    public function testIsCallable()
    {
        $f = 3;  // function myprocess
        $p = 0;  // parameter $param

        $parameters = self::$models[$f]->getParameters();

        $this->assertTrue(
            $parameters[$p]->isCallable(),
            self::$models[$f]->getName() . ", parameter #$p expects a callback."
        );
    }

    /**
     * Tests if a default value for the parameter is available
     * for a user-defined function.
     *
     *  covers Bartlett\Reflect\Model\ParameterModel::isDefaultValueAvailable
     * @group  reflection
     * @return void
     */
    public function testIsDefaultValueAvailableOnUserFunction()
    {
        $f = 2;  // function singleFunction
        $p = 2;  // parameter $lastone

        $parameters = self::$models[$f]->getParameters();

        $this->assertTrue(
            $parameters[$p]->isDefaultValueAvailable(),
            self::$models[$f]->getName() . ", parameter #$p have a default value."
        );
    }

    /**
     * Tests the default value of the parameter for a class method.
     *
     *  covers Bartlett\Reflect\Model\ParameterModel::getDefaultValue
     * @group  reflection
     * @return void
     */
    public function testDefaultValueAccessorOnClassMethod()
    {
        $c = 1;  // class Foo
        $m = 0;  // method myfunction
        $p = 1;  // parameter $otherparam

        $methods    = self::$models[$c]->getMethods();
        $parameters = $methods[$m]->getParameters();

        $this->assertEquals(
            '\TRUE',
            $parameters[$p]->getDefaultValue(),
            $methods[$m]->getName()
            . ", parameter #$p default value does not match."
        );
    }

    /**
     * Tests the default value of the parameter for a user-defined function.
     *
     *  covers Bartlett\Reflect\Model\ParameterModel::getDefaultValue
     * @group  reflection
     * @return void
     */
    public function testDefaultValueAccessorOnUserFunction()
    {
        $f = 2;  // function singleFunction
        $p = 2;  // parameter $lastone

        $parameters = self::$models[$f]->getParameters();

        $this->assertEquals(
            '\NULL',
            $parameters[2]->getDefaultValue(),
            self::$models[$f]->getName() . ", parameter #$p default value does not match."
        );
    }

    /**
     * Tests the default value of a parameter not optional.
     *
     *  covers Bartlett\Reflect\Model\ParameterModel::getDefaultValue
     * @group  reflection
     * @return void
     */
    public function testDefaultValueAccessorOnRequiredParameter()
    {
        try {
            $f = 2;  // function singleFunction
            $p = 0;  // parameter $someparam

            $parameters = self::$models[$f]->getParameters();

            $parameters[$p]->getDefaultValue();

        } catch (ModelException $expected) {
            $this->assertEquals(
                'Parameter #0 [$someparam] is not optional.',
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
     * Tests string representation of the ParameterModel object
     * for a required parameter.
     *
     *  covers Bartlett\Reflect\Model\ParameterModel::__toString
     * @group  reflection
     * @return void
     */
    public function testToStringRequiredParameter()
    {
        $f = 3;  // function myprocess
        $p = 1;  // parameter $myresult

        $parameters = self::$models[$f]->getParameters();

        $this->assertEquals(
            'Parameter #1 [ <required> &$myresult ]' . "\n",
            $parameters[$p]->__toString(),
            self::$models[$f]->getName() . ", parameter #$p string representation does not match."
        );
    }

    /**
     * Tests string representation of the ParameterModel object
     * for an optional parameter.
     *
     *  covers Bartlett\Reflect\Model\ParameterModel::__toString
     * @group  reflection
     * @return void
     */
    public function testToStringOptionalParameter()
    {
        $c = 1;  // class Foo
        $m = 0;  // method myfunction
        $p = 0;  // parameter $param

        $methods    = self::$models[$c]->getMethods();
        $parameters = $methods[$m]->getParameters();

        $this->assertEquals(
            'Parameter #0 [ <optional> stdClass $param = \NULL ]' . "\n",
            $parameters[$p]->__toString(),
            $methods[$m]->getName()
            . ", parameter #$p string representation does not match."
        );
    }
}
