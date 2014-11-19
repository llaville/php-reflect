<?php
/**
 * Unit Test Case that covers the Package Model representative.
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
 * @since      Class available since Release 2.7.0
 */

namespace Bartlett\Tests\Reflect\Model;

use Bartlett\Reflect;
use Bartlett\Reflect\ProviderManager;
use Bartlett\Reflect\Provider\SymfonyFinderProvider;
use Bartlett\Reflect\Exception\ModelException;
use Symfony\Component\Finder\Finder;

/**
 * Unit Test Case that covers Bartlett\Reflect\Model\PackageModel
 *
 * @category   PHP
 * @package    PHP_Reflect
 * @subpackage Tests
 * @author     Laurent Laville <pear@laurent-laville.org>
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    Release: @package_version@
 * @link       http://php5.laurent-laville.org/reflect/
 */
class PackageModelTest extends \PHPUnit_Framework_TestCase
{
    protected static $fixtures;
    protected static $fixture;
    protected static $namespaces;
    protected static $classes;

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

        self::$fixture = self::$fixtures . 'packages.php';

        $finder = new Finder();
        $finder->files()
            ->name(basename(self::$fixture))
            ->in(self::$fixtures);

        $pm = new ProviderManager;
        $pm->set('test_files', new SymfonyFinderProvider($finder));

        $reflect = new Reflect();
        $reflect->setProviderManager($pm);
        $reflect->parse();

        $n = 0;

        foreach ($reflect->getPackages() as $package) {
            self::$namespaces[$n] = $package;

            foreach ($package->getClasses() as $rc) {
                self::$classes[$n][] = $rc;
            }
            $n++;
        }
    }

    /**
     * Tests the Doc comment accessor.
     *
     *  covers PackageModel::getDocComment
     * @return void
     */
    public function testDocCommentAccessor()
    {
        $n = 0;    // empty namespace

        $expected = '/** Global (empty) namespace */';
        $this->assertEquals(
            $expected,
            self::$namespaces[$n]->getDocComment(),
            self::$namespaces[$n]->getName() . ' namespace, doc comment does not match.'
        );
    }

    /**
     * Tests starting line number accessor.
     *
     *  covers PackageModel::getStartLine
     * @return void
     */
    public function testStartLineAccessor()
    {
        $n = 0;    // empty namespace

        $this->assertEquals(
            3,
            self::$namespaces[$n]->getStartLine(),
            self::$namespaces[$n]->getName() . ' namespace, starting line does not match.'
        );
    }

    /**
     * Tests ending line number accessor.
     *
     *  covers PackageModel::getEndLine
     * @return void
     */
    public function testEndLineAccessor()
    {
        $n = 0;    // empty namespace

        $this->assertEquals(
            11,
            self::$namespaces[$n]->getEndLine(),
            self::$namespaces[$n]->getName() . ' namespace, ending line does not match.'
        );
    }

    /**
     * Tests file name accessor.
     *
     *  covers PackageModel::getFileName
     * @return void
     */
    public function testFileNameAccessor()
    {
        $n = 0;    // empty namespace

        $this->assertEquals(
            self::$fixture,
            self::$namespaces[$n]->getFileName(),
            self::$namespaces[$n]->getName() . ' namespace, file name does not match.'
        );
    }

    /**
     * Tests name accessor.
     *
     *  covers PackageModel::getName
     * @return void
     */
    public function testNameAccessor()
    {
        $n = 0;    // empty namespace

        $this->assertEquals(
            '+global',
            self::$namespaces[$n]->getName(),
            self::$namespaces[$n]->getName() . ' namespace, name does not match.'
        );
    }

    /**
     * Tests string representation of the PackageModel object
     *
     *  covers PackageModel::__toString
     * @return void
     */
    public function testToString()
    {
        $n = 0;    // empty namespace

        $expected = <<<EOS
Package [ +global ] {
  @@ %fixture% 3 - 11
}

EOS;
        $this->expectOutputString(
            str_replace('%fixture%', self::$fixture, $expected)
        );

        print(
            self::$namespaces[$n]->__toString()
        );
    }

    /**
     * Handle namespaces without name
     *
     * @return void
     * @link   https://github.com/llaville/php-reflect/pull/4 by Eric Colinet
     */
    public function testHandleEmptyNamespace()
    {
        $n = 0;    // empty namespace
        $c = 0;    // class MyGlobalClass

        $this->assertInstanceOf(
            'Bartlett\Reflect\Model\ClassModel',
            self::$classes[$n][$c]
        );
        $this->assertEquals('MyGlobalClass', self::$classes[$n][$c]->getName());
    }
}
