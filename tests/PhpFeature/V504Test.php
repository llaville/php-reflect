<?php
/**
 * Unit Test Case that covers PHP 5.4 features.
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
 * @since      Class available since Release 2.6.2
 */

namespace Bartlett\Tests\Reflect\PhpFeature;

use Bartlett\Reflect;
use Bartlett\Reflect\ProviderManager;
use Bartlett\Reflect\Provider\SymfonyFinderProvider;
use Symfony\Component\Finder\Finder;

/**
 * Tests for specific PHP 5.4 features.
 *
 * @category   PHP
 * @package    PHP_Reflect
 * @subpackage Tests
 * @author     Laurent Laville <pear@laurent-laville.org>
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    Release: @package_version@
 * @link       http://php5.laurent-laville.org/reflect/
 */
class V504Test extends \PHPUnit_Framework_TestCase
{
    protected static $fixtures;
    protected static $dependencies;

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
            ->name('gh15.php')
            ->in(self::$fixtures);
        ;

        $pm = new ProviderManager;
        $pm->set('php_features', new SymfonyFinderProvider($finder));

        $reflect = new Reflect();
        $reflect->setProviderManager($pm);
        $reflect->parse();

        foreach ($reflect->getPackages() as $package) {
            foreach ($package->getDependencies() as $dep) {
                if ($dep->isPhpFeature()) {
                    self::$dependencies[] = $dep;
                }
            }
        }
    }

    /**
     * Checks that we have at least all dependencies
     *
     * @return void
     */
    public function testDependencyCount()
    {
        $this->assertCount(4, self::$dependencies);
    }

    /**
     * Test detection of class member access on direct instantiation.
     *
     * @depends testDependencyCount
     * @return void
     */
    public function testClassMemberAccessOnDirectInstantiation()
    {
        $d = 0;  // (new Foo)->bar();

        $this->assertEquals(
            'ClassMemberAccessOnDirectInstantiation',
            self::$dependencies[$d]->getPhpFeature(),
            self::$dependencies[$d]->getName()
            . ' is not a class member access on direct instantiation.'
        );
    }

    /**
     * Test detection of class member access on indirect instantiation.
     *
     * @depends testDependencyCount
     * @return void
     */
    public function testClassMemberAccessOnIndirectInstantiation()
    {
        $d = 2;  // (new $a())->bar();

        $this->assertEquals(
            'ClassMemberAccessOnIndirectInstantiation',
            self::$dependencies[$d]->getPhpFeature(),
            self::$dependencies[$d]->getName()
            . ' is not a class member access on indirect instantiation.'
        );
    }
}
