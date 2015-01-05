<?php
/**
 * Unit Test Case that covers the Constant Model representative.
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

/**
 * Unit Test Case that covers Bartlett\Reflect\Model\ConstantModel
 *
 * @category   PHP
 * @package    PHP_Reflect
 * @subpackage Tests
 * @author     Laurent Laville <pear@laurent-laville.org>
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    Release: @package_version@
 * @link       http://php5.laurent-laville.org/reflect/
 */
class ConstantModelTest extends GenericModelTest
{
    /**
     * Sets up the shared fixture.
     *
     * @return void
     */
    public static function setUpBeforeClass()
    {
        self::$fixture = 'constants.php';
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
        $c = 0;  // sandbox\CONNECT_OK

        $expected = '/** connection semaphore */';

        $this->assertEquals(
            $expected,
            self::$models[$c]->getDocComment(),
            self::$models[$c]->getName() . ' doc comment does not match.'
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
        $c = 0;  // sandbox\CONNECT_OK

        $this->assertEquals(
            self::$fixture,
            self::$models[$c]->getFileName(),
            self::$models[$c]->getName() . ' file name does not match.'
        );
    }

    /**
     * Tests name accessor.
     *
     *  covers Bartlett\Reflect\Model\ConstantModel::getName
     * @group  reflection
     * @return void
     */
    public function testNameAccessor()
    {
        $c = 0;  // sandbox\CONNECT_OK

        $this->assertEquals(
            'sandbox\CONNECT_OK',
            self::$models[$c]->getName(),
            self::$models[$c]->getName() . ' constant name does not match.'
        );
    }

    /**
     * Tests value accessor of a user constant.
     *
     *  covers Bartlett\Reflect\Model\ConstantModel::getValue
     * @group  reflection
     * @return void
     */
    public function testUserConstantValueAccessor()
    {
        $c = 0;  // sandbox\CONNECT_OK

        $this->assertEquals(
            1,
            self::$models[$c]->getValue(),
            self::$models[$c]->getName() . ' user constant value does not match.'
        );
    }

    /**
     * Tests the namespace name accessor.
     *
     *  covers Bartlett\Reflect\Model\ConstantModel::getNamespaceName
     * @group  reflection
     * @return void
     */
    public function testNamespaceNameAccessor()
    {
        $c = 0;  // sandbox\CONNECT_OK

        $this->assertEquals(
            'sandbox',
            self::$models[$c]->getNamespaceName(),
            self::$models[$c]->getName() . ' namespace does not match.'
        );
    }

    /**
     * Tests function short name accessor.
     *
     *  covers Bartlett\Reflect\Model\ConstantModel::getShortName
     * @group  reflection
     * @return void
     */
    public function testShortNameAccessor()
    {
        $c = 0;  // sandbox\CONNECT_OK

        $this->assertEquals(
            'CONNECT_OK',
            self::$models[$c]->getShortName(),
            self::$models[$c]->getName() . ' short name does not match.'
        );
    }

    /**
     * Tests whether a constant is defined in a namespace.
     *
     *  covers Bartlett\Reflect\Model\ConstantModel::inNamespace
     * @group  reflection
     * @return void
     */
    public function testInNamespace()
    {
        $c = 0;  // sandbox\CONNECT_OK

        $this->assertTrue(
            self::$models[$c]->inNamespace(),
            self::$models[$c]->getName() . ' is defined in a namespace.'
        );
    }

    /**
     * Tests whether a constant is internal.
     *
     *  covers Bartlett\Reflect\Model\ConstantModel::isInternal
     * @group  reflection
     * @return void
     */
    public function testIsInternal()
    {
        $c = 0;  // sandbox\CONNECT_OK

        $this->assertFalse(
            self::$models[$c]->isInternal(),
            self::$models[$c]->getName() . ' is an internal constant.'
        );
    }

    /**
     * Tests whether a constant is magic.
     *
     *  covers Bartlett\Reflect\Model\ConstantModel::isMagic
     * @group  reflection
     * @return void
     */
    public function testIsMagic()
    {
        $c = 0;  // sandbox\CONNECT_OK

        $this->assertFalse(
            self::$models[$c]->isMagic(),
            self::$models[$c]->getName() . ' is a magic constant.'
        );
    }

    /**
     * Tests whether a constant is scalar.
     *
     *  covers Bartlett\Reflect\Model\ConstantModel::isScalar
     * @group  reflection
     * @return void
     */
    public function testIsScalar()
    {
        $c = 4;  // sandbox\TWO

        $this->assertFalse(
            self::$models[$c]->isScalar(),
            self::$models[$c]->getName() . ' is a scalar constant.'
        );
    }

    /**
     * Tests string representation of the FunctionModel object
     *
     *  covers Bartlett\Reflect\Model\ConstantModel::__toString
     * @group  reflection
     * @return void
     */
    public function testToString()
    {
        $c = 4;  // sandbox\TWO

        $expected = <<<EOS
Constant [ sandbox\TWO ] { ONE + 1 }

EOS;
        $this->expectOutputString($expected);

        print(
            self::$models[$c]->__toString()
        );
    }
}
