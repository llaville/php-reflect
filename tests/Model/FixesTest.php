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
 * @since      Class available since Release 2.0.0RC1
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
 * Unit Test Case that covers Bartlett\Reflect\Model\FunctionModel
 *
 * @category   PHP
 * @package    PHP_Reflect
 * @subpackage Tests
 * @author     Laurent Laville <pear@laurent-laville.org>
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    Release: @package_version@
 * @link       http://php5.laurent-laville.org/reflect/
 */
class FixesTest extends \PHPUnit_Framework_TestCase
{
    protected static $interfaces;
    protected static $classes;
    protected static $functions;

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
            ->name('fixes.php')
            ->in(TEST_FILES_PATH);

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

    public function testFix()
    {
        $this->assertInstanceOf('Bartlett\Reflect\Model\DependencyModel', self::$classes[0][0]);
        $this->assertSame('stdClass', self::$classes[0][0]->getName());
        $this->assertInstanceOf('Bartlett\Reflect\Model\ClassModel', self::$classes[0][1]);
        $this->assertSame('MyGlobalClass', self::$classes[0][1]->getName());        
    }
}
