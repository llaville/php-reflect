<?php
/**
 * Unit Test Case that covers the Dependency Model representative.
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
 * @since      Class available since Release 2.2.0
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
 * Unit Test Case that covers Bartlett\Reflect\Model\DependencyModel
 *
 * @category   PHP
 * @package    PHP_Reflect
 * @subpackage Tests
 * @author     Laurent Laville <pear@laurent-laville.org>
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    Release: @package_version@
 * @link       http://php5.laurent-laville.org/reflect/
 */
class DependencyModelTest extends \PHPUnit_Framework_TestCase
{
    protected static $dependencies;

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
            ->name('dependencies.php')
            ->in(TEST_FILES_PATH);

        $pm = new ProviderManager;
        $pm->set('test_files', new SymfonyFinderProvider($finder));

        $reflect = new Reflect();
        $reflect->setProviderManager($pm);
        $reflect->parse();

        foreach ($reflect->getPackages() as $package) {
            foreach ($package->getDependencies() as $rd) {
                self::$dependencies[] = $rd;
            }
        }
    }

    /**
     * Tests starting line number accessor.
     *
     *  covers DependencyModel::getStartLine
     * @return void
     */
    public function testStartLineAccessor()
    {
        $d = 1;  // DateTime::diff

        $this->assertEquals(
            5,
            self::$dependencies[$d]->getStartLine(),
            self::$dependencies[$d]->getName()
            . ' starting line does not match.'
        );
    }

    /**
     * Tests ending line number accessor.
     *
     *  covers DependencyModel::getEndLine
     * @return void
     */
    public function testEndLineAccessor()
    {
        $d = 1;  // DateTime::diff

        $this->assertEquals(
            6,
            self::$dependencies[$d]->getEndLine(),
            self::$dependencies[$d]->getName()
            . ' ending line does not match.'
        );
    }

    /**
     * Tests file name accessor.
     *
     *  covers DependencyModel::getFileName
     * @return void
     */
    public function testFileNameAccessor()
    {
        $d = 1;  // DateTime::diff

        $this->assertEquals(
            TEST_FILES_PATH . 'dependencies.php',
            self::$dependencies[$d]->getFileName(),
            self::$dependencies[$d]->getName()
            . ' file name does not match.'
        );
    }

    /**
     * Tests method name accessor.
     *
     *  covers DependencyModel::getName
     * @return void
     */
    public function testNameAccessor()
    {
        $d = 1;  // DateTime::diff

        $this->assertEquals(
            'DateTime::diff',
            self::$dependencies[$d]->getName(),
            self::$dependencies[$d]->getName()
            . ' dependency name does not match.'
        );
    }

    /**
     * Tests the namespace name accessor.
     *
     *  covers DependencyModel::getNamespaceName
     * @return void
     */
    public function testNamespaceNameAccessor()
    {
        $d = 1;  // DateTime::diff

        $this->assertEquals(
            '',
            self::$dependencies[$d]->getNamespaceName(),
            self::$dependencies[$d]->getName() . ' namespace does not match.'
        );
    }

    /**
     * Tests if the dependency is a class method.
     *
     *  covers DependencyModel::isClassMethod
     * @return void
     */
    public function testClassMethod()
    {
        $d = 1;  // DateTime::diff

        $this->assertTrue(
            self::$dependencies[$d]->isClassMethod(),
            self::$dependencies[$d]->getName() . ' is not a class method.'
        );
    }

    /**
     * Tests if the dependency is a php/extension function.
     *
     *  covers DependencyModel::isInternalFunction
     * @return void
     */
    public function testInternalFunction()
    {
        $d = 3;  // extension_loaded

        $this->assertTrue(
            self::$dependencies[$d]->isInternalFunction(),
            self::$dependencies[$d]->getName() . ' is not an internal function.'
        );
    }

    /**
     * Tests arguments list of a dependency.
     *
     *  covers DependencyModel::getArguments
     * @return void
     */
    public function testArgumentsAccessor()
    {
        $d = 3;  // extension_loaded
        $p = 0;

        $args = self::$dependencies[$d]->getArguments();

        $this->assertEquals(
            'date',
            $args[$p]['value'],
            self::$dependencies[$d]->getName()
            . " argument #$p value does not match."
        );
    }

    /**
     * Tests if the dependency is a conditional function.
     *
     *  covers DependencyModel::isConditionalFunction
     * @return void
     */
    public function testConditionalFunction()
    {
        $d = 3;  // extension_loaded

        $this->assertTrue(
            self::$dependencies[$d]->isConditionalFunction(),
            self::$dependencies[$d]->getName() . ' is not a conditional function.'
        );
    }

    /**
     * Tests if the dependency is a class (introduced by the new statement).
     *
     *  covers DependencyModel::isClass
     * @return void
     */
    public function testClass()
    {
        $d = 4;  // new Finder

        $this->assertTrue(
            self::$dependencies[$d]->isClass(),
            self::$dependencies[$d]->getName() . ' is not a class.'
        );
    }
}
