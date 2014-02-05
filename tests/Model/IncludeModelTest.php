<?php
/**
 * Unit Test Case that covers the Include Model representative.
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

if (!defined('TEST_FILES_PATH')) {
    define(
        'TEST_FILES_PATH',
        dirname(__DIR__) . DIRECTORY_SEPARATOR .
        '_files' . DIRECTORY_SEPARATOR
    );
}

/**
 * Unit Test Case that covers Bartlett\Reflect\Model\IncludeModel
 *
 * @category   PHP
 * @package    PHP_Reflect
 * @subpackage Tests
 * @author     Laurent Laville <pear@laurent-laville.org>
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    Release: @package_version@
 * @link       http://php5.laurent-laville.org/reflect/
 */
class IncludeModelTest extends \PHPUnit_Framework_TestCase
{
    protected static $includes;

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
            ->name('includes.php')
            ->in(TEST_FILES_PATH);

        $pm = new ProviderManager;
        $pm->set('test_files', new SymfonyFinderProvider($finder));

        $reflect = new Reflect();
        $reflect->setProviderManager($pm);
        $reflect->parse();

        foreach ($reflect->getPackages() as $package) {
            foreach ($package->getIncludes() as $rn) {
                self::$includes[] = $rn;
            }
        }
    }

    /**
     * Tests the Doc comment accessor.
     *
     *  covers IncludeModel::getDocComment
     * @return void
     */
    public function testDocCommentAccessor()
    {
        $n = 3;  // require_once

        $expected = '/** test four */';

        $this->assertEquals(
            $expected,
            self::$includes[$n]->getDocComment(),
            self::$includes[$n]->getType() . ' doc comment does not match.'
        );
    }

    /**
     * Tests starting line number accessor.
     *
     *  covers IncludeModel::getStartLine
     * @return void
     */
    public function testStartLineAccessor()
    {
        $n = 3;  // require_once

        $this->assertEquals(
            9,
            self::$includes[$n]->getStartLine(),
            self::$includes[$n]->getType() . ' starting line does not match.'
        );
    }

    /**
     * Tests ending line number accessor.
     *
     *  covers IncludeModel::getEndLine
     * @return void
     */
    public function testEndLineAccessor()
    {
        $n = 3;  // require_once

        $this->assertEquals(
            10,
            self::$includes[$n]->getEndLine(),
            self::$includes[$n]->getType() . ' ending line does not match.'
        );
    }

    /**
     * Tests file name accessor.
     *
     *  covers IncludeModel::getFileName
     * @return void
     */
    public function testFileNameAccessor()
    {
        $n = 2;  // require

        $this->assertEquals(
            TEST_FILES_PATH . 'includes.php',
            self::$includes[$n]->getFileName(),
            self::$includes[$n]->getType() . ' file name does not match.'
        );
    }

    /**
     * Tests file name accessor.
     *
     *  covers IncludeModel::getFilePath
     * @return void
     */
    public function testFilePathAccessor()
    {
        $n = 0;  // include

        $this->assertEquals(
            array('__DIR__', '/test1.php'),
            self::$includes[$n]->getFilePath(),
            self::$includes[$n]->getType() . ' file path does not match.'
        );
    }

    /**
     * Tests whether the include is a require.
     *
     *  covers IncludeModel::isRequire
     * @return void
     */
    public function testRequire()
    {
        $n = 2;  // require

        $this->assertTrue(
            self::$includes[$n]->isRequire(),
            self::$includes[$n]->getType() . ' type does not match.'
        );
    }

    /**
     * Tests whether the include is a require_once.
     *
     *  covers IncludeModel::isRequireOnce
     * @return void
     */
    public function testRequireOnce()
    {
        $n = 3;  // require_once

        $this->assertTrue(
            self::$includes[$n]->isRequireOnce(),
            self::$includes[$n]->getType() . ' type does not match.'
        );
    }

    /**
     * Tests whether the include is a include.
     *
     *  covers IncludeModel::isInclude
     * @return void
     */
    public function testInclude()
    {
        $n = 0;  // include

        $this->assertTrue(
            self::$includes[$n]->isInclude(),
            self::$includes[$n]->getType() . ' type does not match.'
        );
    }

    /**
     * Tests whether the include is a include.
     *
     *  covers IncludeModel::isIncludeOnce
     * @return void
     */
    public function testIncludeOnce()
    {
        $n = 1;  // include_once

        $this->assertTrue(
            self::$includes[$n]->isIncludeOnce(),
            self::$includes[$n]->getType() . ' type does not match.'
        );
    }

    /**
     * Tests string representation of the IncludeModel object
     *
     *  covers IncludeModel::__toString
     * @return void
     */
    public function testToString()
    {
        $n = 0;  // include

        $expected = <<<EOS
Include [ include ] { __DIR__ . /test1.php }

EOS;
        $this->expectOutputString($expected);

        print(
            self::$includes[$n]->__toString()
        );
    }
}
