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

use Bartlett\Reflect;
use Bartlett\Reflect\ProviderManager;
use Bartlett\Reflect\Provider\SymfonyFinderProvider;
use Symfony\Component\Finder\Finder;

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
class ConstantModelTest extends \PHPUnit_Framework_TestCase
{
    protected static $fixtures;
    protected static $constants;

    /**
     * Sets up the shared fixture.
     *
     * @return void
     * @link   http://phpunit.de/manual/current/en/fixtures.html#fixtures.sharing-fixture
     */
    public static function setUpBeforeClass()
    {
        self::$fixtures = dirname(__DIR__) . DIRECTORY_SEPARATOR
            . '_files' . DIRECTORY_SEPARATOR;

        $finder = new Finder();
        $finder->files()
            ->name('constants.php')
            ->in(self::$fixtures);

        $pm = new ProviderManager;
        $pm->set('test_files', new SymfonyFinderProvider($finder));

        $reflect = new Reflect();
        $reflect->setProviderManager($pm);
        $reflect->parse();

        foreach ($reflect->getPackages() as $package) {
            foreach ($package->getConstants() as $rc) {
                self::$constants[] = $rc;
            }
        }
    }

    /**
     * Tests the Doc comment accessor.
     *
     *  covers ConstantModel::getDocComment
     * @return void
     */
    public function testDocCommentAccessor()
    {
        $c = 0;  // sandbox\CONNECT_OK

        $expected = '/** connection semaphore */';

        $this->assertEquals(
            $expected,
            self::$constants[$c]->getDocComment(),
            self::$constants[$c]->getName() . ' doc comment does not match.'
        );
    }

    /**
     * Tests file name accessor.
     *
     *  covers ConstantModel::getFileName
     * @return void
     */
    public function testFileNameAccessor()
    {
        $c = 0;  // sandbox\CONNECT_OK

        $this->assertEquals(
            self::$fixtures . 'constants.php',
            self::$constants[$c]->getFileName(),
            self::$constants[$c]->getName() . ' file name does not match.'
        );
    }

    /**
     * Tests file name accessor for an internal constant.
     *
     *  covers ConstantModel::getFileName
     * @return void
     */
    public function testInternalConstantFileNameAccessor()
    {
        $c = 3;  // __METHOD__ from sandbox\Connection::connect()

        $this->assertFalse(
            self::$constants[$c]->getFileName(),
            self::$constants[$c]->getName() . ' does not expect a file name.'
        );
    }

    /**
     * Tests extension name acessor.
     *
     *  covers ConstantModel::getExtensionName
     * @return void
     */
    public function testExtensionNameAccessor()
    {
        $c = 3;  // __METHOD__ from sandbox\Connection::connect()

        $this->assertEquals(
            'Core',
            self::$constants[$c]->getExtensionName(),
            self::$constants[$c]->getName() . ' extension name does not match.'
        );
    }

    /**
     * Tests name accessor.
     *
     *  covers ConstantModel::getName
     * @return void
     */
    public function testNameAccessor()
    {
        $c = 0;  // sandbox\CONNECT_OK

        $this->assertEquals(
            'sandbox\CONNECT_OK',
            self::$constants[$c]->getName(),
            self::$constants[$c]->getName() . ' constant name does not match.'
        );
    }

    /**
     * Tests value accessor of a user constant.
     *
     *  covers ConstantModel::getValue
     * @return void
     */
    public function testUserConstantValueAccessor()
    {
        $c = 0;  // sandbox\CONNECT_OK

        $this->assertEquals(
            1,
            self::$constants[$c]->getValue(),
            self::$constants[$c]->getName() . ' user constant value does not match.'
        );
    }

    /**
     * Tests value accessor on the __CLASS__ magic constant.
     *
     *  covers ConstantModel::getValue
     * @return void
     */
    public function testClassMagicConstantValueAccessor()
    {
        $c = 2;  // __CLASS__ from sandbox\Connection::connect()

        $this->assertNull(
            self::$constants[$c]->getValue(),
            self::$constants[$c]->getName() . ' magic constant value does not match.'
        );
    }

    /**
     * Tests value accessor on the __METHOD__ magic constant.
     *
     *  covers ConstantModel::getValue
     * @return void
     */
    public function testMethodMagicConstantValueAccessor()
    {
        $c = 3;  // __METHOD__ from sandbox\Connection::connect()

        $this->assertNull(
            self::$constants[$c]->getValue(),
            self::$constants[$c]->getName() . ' magic constant value does not match.'
        );
    }

    /**
     * Tests value accessor on the __DIR__ magic constant.
     *
     *  covers ConstantModel::getValue
     * @return void
     */
    public function testDirMagicConstantValueAccessor()
    {
        $c = 8;  // __DIR__

        $this->assertNull(
            self::$constants[$c]->getValue(),
            self::$constants[$c]->getName() . ' magic constant value does not match.'
        );
    }

    /**
     * Tests value accessor on the __FILE__ magic constant.
     *
     *  covers ConstantModel::getValue
     * @return void
     */
    public function testFileMagicConstantValueAccessor()
    {
        $c = 1;  // __FILE__

        $this->assertNull(
            self::$constants[$c]->getValue(),
            self::$constants[$c]->getName() . ' magic constant value does not match.'
        );
    }

    /**
     * Tests value accessor on the __LINE__ magic constant.
     *
     *  covers ConstantModel::getValue
     * @return void
     */
    public function testLineMagicConstantValueAccessor()
    {
        $c = 6;  // __LINE__ from sandbox\connect()

        $this->assertNull(
            self::$constants[$c]->getValue(),
            self::$constants[$c]->getName() . ' magic constant value does not match.'
        );
    }

    /**
     * Tests value accessor on the __FUNCTION__ magic constant.
     *
     *  covers ConstantModel::getValue
     * @return void
     */
    public function testFunctionMagicConstantValueAccessor()
    {
        $c = 5;  // __FUNCTION__ from sandbox\connect()

        $this->assertNull(
            self::$constants[$c]->getValue(),
            self::$constants[$c]->getName() . ' magic constant value does not match.'
        );
    }

    /**
     * Tests value accessor on the __NAMESPACE__ magic constant.
     *
     *  covers ConstantModel::getValue
     * @return void
     */
    public function testNamespaceMagicConstantValueAccessor()
    {
        $c = 7;  // __NAMESPACE__

        $this->assertNull(
            self::$constants[$c]->getValue(),
            self::$constants[$c]->getName() . ' magic constant value does not match.'
        );
    }

    /**
     * Tests value accessor on the __TRAIT__ magic constant.
     *
     *  covers ConstantModel::getValue
     * @return void
     */
    public function testTraitMagicConstantValueAccessor()
    {
        $c = 9;  // __TRAIT__ from sandbox\PeanutButter::traitName()

        $this->assertNull(
            self::$constants[$c]->getValue(),
            self::$constants[$c]->getName() . ' magic constant value does not match.'
        );
    }

    /**
     * Tests the namespace name accessor.
     *
     *  covers ConstantModel::getNamespaceName
     * @return void
     */
    public function testNamespaceNameAccessor()
    {
        $c = 0;  // sandbox\CONNECT_OK

        $this->assertEquals(
            'sandbox',
            self::$constants[$c]->getNamespaceName(),
            self::$constants[$c]->getName() . ' namespace does not match.'
        );
    }

    /**
     * Tests function short name accessor.
     *
     *  covers ConstantModel::getShortName
     * @return void
     */
    public function testShortNameAccessor()
    {
        $c = 0;  // sandbox\CONNECT_OK

        $this->assertEquals(
            'CONNECT_OK',
            self::$constants[$c]->getShortName(),
            self::$constants[$c]->getName() . ' short name does not match.'
        );
    }

    /**
     * Tests whether a constant is defined in a namespace.
     *
     *  covers ConstantModel::inNamespace
     * @return void
     */
    public function testInNamespace()
    {
        $c = 0;  // sandbox\CONNECT_OK

        $this->assertTrue(
            self::$constants[$c]->inNamespace(),
            self::$constants[$c]->getName() . ' is defined in a namespace.'
        );
    }

    /**
     * Tests whether a constant is magic.
     *
     *  covers ConstantModel::isMagic
     * @return void
     */
    public function testIsMagic()
    {
        $c = 2;  // __CLASS__ from sandbox\Connection::connect()

        $this->assertTrue(
            self::$constants[$c]->isMagic(),
            self::$constants[$c]->getName() . ' is not a magic constant.'
        );
    }

    /**
     * Tests whether a constant is scalar.
     *
     *  covers ConstantModel::isScalar
     * @return void
     */
    public function testIsScalar()
    {
        $c = 10;  // sandbox\TWO

        $this->assertFalse(
            self::$constants[$c]->isScalar(),
            self::$constants[$c]->getName() . ' is a scalar constant.'
        );
    }

    /**
     * Tests string representation of the FunctionModel object
     *
     *  covers ConstantModel::__toString
     * @return void
     */
    public function testToString()
    {
        $c = 2;  // __CLASS__ from sandbox\Connection::connect()

        $expected = <<<EOS
Constant [ __CLASS__ ] {  }

EOS;
        $this->expectOutputString($expected);

        print(
            self::$constants[$c]->__toString()
        );
    }
}
