<?php
/**
 * Unit Test Case that covers the method call signature issues.
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
 * @since      Class available since Release 2.6.1
 */

namespace Bartlett\Tests\Reflect;

use Bartlett\Reflect;
use Bartlett\Reflect\ProviderManager;
use Bartlett\Reflect\Provider\SymfonyFinderProvider;
use Symfony\Component\Finder\Finder;

/**
 * Unit Test Case that covers the method call signature issues.
 *
 * @category   PHP
 * @package    PHP_Reflect
 * @subpackage Tests
 * @author     Laurent Laville <pear@laurent-laville.org>
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    Release: @package_version@
 * @link       http://php5.laurent-laville.org/reflect/
 */
class IssueTest extends \PHPUnit_Framework_TestCase
{
    protected static $fixtures;
    protected static $fixture;
    protected static $dependencies;

    /**
     * Sets up the shared fixture.
     *
     * @return void
     * @link   http://phpunit.de/manual/current/en/fixtures.html#fixtures.sharing-fixture
     */
    public static function setUpBeforeClass()
    {
        self::$fixtures = __DIR__ . DIRECTORY_SEPARATOR
            . '_files' . DIRECTORY_SEPARATOR;

        self::$fixture = self::$fixtures . 'method_call.php';

        $finder = new Finder();
        $finder->files()
            ->name(basename(self::$fixture))
            ->in(self::$fixtures);

        $pm = new ProviderManager;
        $pm->set('test_files', new SymfonyFinderProvider($finder));

        $reflect = new Reflect();
        $reflect->setProviderManager($pm);
        $reflect->parse();

        foreach ($reflect->getPackages() as $package) {
            foreach ($package->getDependencies() as $dep) {
                self::$dependencies[] = $dep;
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
     * Tests the property fetch method call signature.
     *
     * @depends testDependencyCount
     * @return void
     */
    public function testPropertyFetchMethodCall()
    {
        $d = 2;  // $vobject->app->getValue();

        $this->assertEquals(
            'Abc::getValue',
            self::$dependencies[$d]->getName(),
            self::$dependencies[$d]->getName() . ' class methode does not match.'
        );
    }

    /**
     * Tests the variable method call signature.
     *
     * @depends testDependencyCount
     * @return void
     */
    public function testVariableMethodCall()
    {
        $d = 3;  // $v->getName();

        $this->assertEquals(
            'stdClass::getName',
            self::$dependencies[$d]->getName(),
            self::$dependencies[$d]->getName() . ' class methode does not match.'
        );
    }
}
