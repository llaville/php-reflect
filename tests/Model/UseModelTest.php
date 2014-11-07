<?php
/**
 * Unit Test Case that covers the Use Model representative.
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
 * @since      Class available since Release 2.6.0
 */

namespace Bartlett\Tests\Reflect\Model;

use Bartlett\Reflect;
use Bartlett\Reflect\ProviderManager;
use Bartlett\Reflect\Provider\SymfonyFinderProvider;
use Symfony\Component\Finder\Finder;

/**
 * Unit Test Case that covers Bartlett\Reflect\Model\UseModel
 *
 * @category   PHP
 * @package    PHP_Reflect
 * @subpackage Tests
 * @author     Laurent Laville <pear@laurent-laville.org>
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    Release: @package_version@
 * @link       http://php5.laurent-laville.org/reflect/
 */
class UseModelTest extends \PHPUnit_Framework_TestCase
{
    protected static $fixtures;
    protected static $uses;

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
            ->name('uses.php')
            ->in(self::$fixtures);

        $pm = new ProviderManager;
        $pm->set('test_files', new SymfonyFinderProvider($finder));

        $reflect = new Reflect();
        $reflect->setProviderManager($pm);
        $reflect->parse();

        foreach ($reflect->getPackages() as $package) {
            foreach ($package->getUses() as $ru) {
                self::$uses[] = $ru;
            }
        }
    }

    /**
     * Tests starting line number accessor.
     *
     *  covers UseModel::getStartLine
     * @return void
     */
    public function testStartLineAccessor()
    {
        $d = 0;  // use Exception

        $this->assertEquals(
            3,
            self::$uses[$d]->getStartLine(),
            self::$uses[$d]->getName()
            . ' starting line does not match.'
        );
    }

    /**
     * Tests ending line number accessor.
     *
     *  covers UseModel::getEndLine
     * @return void
     */
    public function testEndLineAccessor()
    {
        $d = 0;  // use Exception

        $this->assertEquals(
            3,
            self::$uses[$d]->getEndLine(),
            self::$uses[$d]->getName()
            . ' ending line does not match.'
        );
    }

    /**
     * Tests file name accessor.
     *
     *  covers UseModel::getFileName
     * @return void
     */
    public function testFileNameAccessor()
    {
        $d = 0;  // use Exception

        $this->assertEquals(
            self::$fixtures . 'uses.php',
            self::$uses[$d]->getFileName(),
            self::$uses[$d]->getName()
            . ' file name does not match.'
        );
    }

    /**
     * Tests use name accessor.
     *
     *  covers UseModel::getName
     * @return void
     */
    public function testNameAccessor()
    {
        $d = 1;  // use const Name\Space\FOO;

        $this->assertEquals(
            'Name\\Space\\FOO',
            self::$uses[$d]->getName(),
            self::$uses[$d]->getName()
            . ' use name does not match.'
        );
    }

    /**
     * Tests alias name accessor.
     *
     *  covers UseModel::getShortName
     * @return void
     */
    public function testShortNameAccessor()
    {
        $d = 1;  // use const Name\Space\FOO;

        $this->assertEquals(
            'FOO',
            self::$uses[$d]->getShortName(),
            self::$uses[$d]->getShortName()
            . ' use alias does not match.'
        );
    }

    /**
     * Tests if it's a normal use statement.
     *
     *  covers UseModel::isNormal
     * @return void
     */
    public function testIsNormal()
    {
        $d = 0;  // use Exception

        $this->assertTrue(
            self::$uses[$d]->isNormal(),
            self::$uses[$d]->getName() . ' is not a normal use statement.'
        );
    }

    /**
     * Tests if it's a use function statement.
     *
     *  covers UseModel::isFunction
     * @return void
     */
    public function testIsFunction()
    {
        $d = 2;  // use function Name\Space\f

        $this->assertTrue(
            self::$uses[$d]->isFunction(),
            self::$uses[$d]->getName() . ' is not a function use statement.'
        );
    }

    /**
     * Tests if it's a use const statement.
     *
     *  covers UseModel::isConstant
     * @return void
     */
    public function testIsConstant()
    {
        $d = 1;  // use const Name\Space\FOO

        $this->assertTrue(
            self::$uses[$d]->isConstant(),
            self::$uses[$d]->getName() . ' is not a constant use statement.'
        );
    }
}
