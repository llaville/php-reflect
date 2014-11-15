<?php
/**
 * Unit Test Case that covers the Fixes.
 *
 * PHP version 5
 *
 * @category   PHP
 * @package    PHP_Reflect
 * @subpackage Tests
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    GIT: $Id$
 * @link       http://php5.laurent-laville.org/reflect/
 * @since      Class available since Release 2.4.0
 */

namespace Bartlett\Tests\Reflect\Model;

use Bartlett\Reflect;
use Bartlett\Reflect\ProviderManager;
use Bartlett\Reflect\Provider\SymfonyFinderProvider;
use Symfony\Component\Finder\Finder;

/**
 * Unit Test Case that covers namespace and instance of variable attribute
 *
 * @category   PHP
 * @package    PHP_Reflect
 * @subpackage Tests
 * @author     Eric Colinet
 * @author     Laurent Laville <pear@laurent-laville.org>
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    Release: @package_version@
 * @link       http://php5.laurent-laville.org/reflect/
 */
class FixesTest extends \PHPUnit_Framework_TestCase
{
    protected static $fixtures;
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

        $finder = new Finder();
        $finder->files()
            ->name('fixes.php')
            ->in(self::$fixtures);

        $pm = new ProviderManager;
        $pm->set('test_files', new SymfonyFinderProvider($finder));

        $reflect = new Reflect();
        $reflect->setProviderManager($pm);
        $reflect->parse();

        $n = 0;

        foreach ($reflect->getPackages() as $package) {
            foreach ($package->getClasses() as $rc) {
                self::$classes[$n][] = $rc;
            }
            $n++;
        }
    }

    /**
     * Handle namespaces without name
     *
     * @return void
     * @link   https://github.com/llaville/php-reflect/pull/4
     */
    public function testFix()
    {
        $this->assertInstanceOf(
            'Bartlett\Reflect\Model\ClassModel',
            self::$classes[0][0]
        );
        $this->assertSame('MyGlobalClass', self::$classes[0][0]->getName());
    }
}
