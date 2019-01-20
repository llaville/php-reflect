<?php

declare(strict_types=1);

/**
 * Unit Test Case that covers the Property Model representative.
 *
 * PHP version 5
 *
 * @category   PHP
 * @package    PHP_Reflect
 * @subpackage Tests
 * @author     Laurent Laville <pear@laurent-laville.org>
 * @license    https://opensource.org/licenses/BSD-3-Clause The 3-Clause BSD License
 * @link       http://php5.laurent-laville.org/reflect/
 * @since      Class available since Release 2.0.0RC2
 */

namespace Bartlett\Tests\Reflect\Model;

/**
 * Unit Test Case that covers Bartlett\Reflect\Model\PropertyModel
 *
 * @category   PHP
 * @package    PHP_Reflect
 * @subpackage Tests
 * @author     Laurent Laville <pear@laurent-laville.org>
 * @license    https://opensource.org/licenses/BSD-3-Clause The 3-Clause BSD License
 * @link       http://php5.laurent-laville.org/reflect/
 */
class PropertyModelTest extends GenericModelTest
{
    /**
     * Sets up the shared fixture.
     *
     * @return void
     */
    public static function setUpBeforeClass()
    {
        self::$fixture = 'properties.php';
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
        $c = 0;  // class SimpleClass
        $p = 5;  // var6 property

        $properties = self::$models[$c]->getProperties();

        $this->assertEquals(
            '/** This is allowed only in PHP 5.3.0 and later. */',
            $properties[$p]->getDocComment(),
            $properties[$p]->getName()
            . ' doc comment does not match.'
        );
    }

    /**
     * Tests name of the property.
     *
     *  covers Bartlett\Reflect\Model\PropertyModel::getName
     * @group  reflection
     * @return void
     */
    public function testNameAccessor()
    {
        $c = 0;  // class SimpleClass
        $p = 1;  // var2 property

        $properties = self::$models[$c]->getProperties();

        $this->assertEquals(
            'var2',
            $properties[$p]->getName(),
            $properties[$p]->getName()
            . ", property #$p name does not match."
        );
    }

    /**
     * Tests declaring class of the property.
     *
     *  covers Bartlett\Reflect\Model\PropertyModel::getDeclaringClass
     * @group  reflection
     * @return void
     */
    public function testDeclaringClassAccessor()
    {
        $c = 0;  // class SimpleClass
        $p = 1;  // var2 property

        $properties = self::$models[$c]->getProperties();

        $this->assertEquals(
            'SimpleClass',
            $properties[$p]->getDeclaringClass()->getName(),
            $properties[$p]->getName()
            . ", property #$p declaring class does not match."
        );
    }

    /**
     * Tests if property is defined at run-time or compile-time.
     *
     *  covers Bartlett\Reflect\Model\PropertyModel::isDefault
     * @group  reflection
     * @return void
     */
    public function testDefaultValue()
    {
        $c = 0;  // class SimpleClass
        $p = 1;  // var2 property

        $properties = self::$models[$c]->getProperties();

        $this->assertTrue(
            $properties[$p]->isDefault(),
            $properties[$p]->getName()
            . ", property #$p was not declared at compile-time."
        );
    }

    /**
     * Tests default value of the property.
     *
     *  covers Bartlett\Reflect\Model\PropertyModel::getValue
     * @group  reflection
     * @return void
     */
    public function testValueAccessor()
    {
        $c = 0;  // class SimpleClass
        $p = 5;  // var6 property

        $properties = self::$models[$c]->getProperties();

        $expected = 'hello world';

        $this->assertEquals(
            $expected,
            $properties[$p]->getValue(),
            $properties[$p]->getName()
            . ", property #$p value does not match."
        );
    }

    /**
     * Tests property with private visibility.
     *
     *  covers Bartlett\Reflect\Model\PropertyModel::isPrivate
     * @group  reflection
     * @return void
     */
    public function testPrivateProperty()
    {
        $c = 0;  // class SimpleClass
        $p = 2;  // var3 property

        $properties = self::$models[$c]->getProperties();

        $this->assertTrue(
            $properties[$p]->isPrivate(),
            $properties[$p]->getName()
            . ' is not a private property.'
        );
    }

    /**
     * Tests property with protected visibility.
     *
     *  covers Bartlett\Reflect\Model\PropertyModel::isProtected
     * @group  reflection
     * @return void
     */
    public function testProtectedProperty()
    {
        $c = 0;  // class SimpleClass
        $p = 3;  // var4 property

        $properties = self::$models[$c]->getProperties();

        $this->assertTrue(
            $properties[$p]->isProtected(),
            $properties[$p]->getName()
            . ' is not a protected property.'
        );
    }

    /**
     * Tests property with public visibility.
     *
     *  covers Bartlett\Reflect\Model\PropertyModel::isPublic
     * @group  reflection
     * @return void
     */
    public function testPublicProperty()
    {
        $c = 0;  // class SimpleClass
        $p = 4;  // var5 property

        $properties = self::$models[$c]->getProperties();

        $this->assertTrue(
            $properties[$p]->isPublic(),
            $properties[$p]->getName()
            . ' is not a public property.'
        );
    }

    /**
     * Tests property with static keyword.
     *
     *  covers Bartlett\Reflect\Model\PropertyModel::isStatic
     * @group  reflection
     * @return void
     */
    public function testStaticProperty()
    {
        $c = 0;  // class SimpleClass
        $p = 2;  // var3 property

        $properties = self::$models[$c]->getProperties();

        $this->assertTrue(
            $properties[$p]->isStatic(),
            $properties[$p]->getName()
            . ' is not a static property.'
        );
    }

    /**
     * Tests property with implicit public visibility.
     *
     *  covers Bartlett\Reflect\Model\PropertyModel::isImplicitlyPublic
     * @group  reflection
     * @return void
     */
    public function testImplicitlyPublicProperty()
    {
        $c = 0;  // class SimpleClass
        $p = 0;  // debug property

        $properties = self::$models[$c]->getProperties();

        $this->assertTrue(
            $properties[$p]->isImplicitlyPublic(),
            $properties[$p]->getName()
            . ' is not implicitly public.'
        );
    }

    /**
     * Tests string representation of the PropertyModel object
     * for a static property.
     *
     *  covers Bartlett\Reflect\Model\PropertyModel::__toString
     * @group  reflection
     * @return void
     */
    public function testToStringStaticProperty()
    {
        $c = 0;  // class SimpleClass
        $p = 2;  // var3 property

        $properties = self::$models[$c]->getProperties();

        $this->assertEquals(
            'Property [ private static $var3 ]' . "\n",
            $properties[$p]->__toString(),
            $properties[$p]->getName()
            . ", property #$p string representation does not match."
        );
    }
}
