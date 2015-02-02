<?php
/**
 * Unit Test Case that covers the Environment component.
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

namespace Bartlett\Tests\Reflect;

use Bartlett\Reflect\Environment;

/**
 * Unit Test Case that covers Bartlett\Reflect\Environment
 *
 * @category   PHP
 * @package    PHP_Reflect
 * @subpackage Tests
 * @author     Laurent Laville <pear@laurent-laville.org>
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    Release: @package_version@
 * @link       http://php5.laurent-laville.org/reflect/
 */
class EnvironmentTest extends \PHPUnit_Framework_TestCase
{
    const DIST_RC = 'phpreflect.json.dist';

    /**
     * Sets up the shared fixture.
     *
     * @return void
     * @link   http://phpunit.de/manual/current/en/fixtures.html#fixtures.sharing-fixture
     */
    public static function setUpBeforeClass()
    {
        copy(dirname(__DIR__) . '/bin/' . self::DIST_RC, __DIR__ . '/' . self::DIST_RC);
    }

    /**
     * Clean-up the shared fixture environment.
     *
     * @return void
     * @link   http://phpunit.de/manual/current/en/fixtures.html#fixtures.sharing-fixture
     */
    public static function tearDownAfterClass()
    {
        @unlink(__DIR__ . '/' . self::DIST_RC);
    }

    /**
     * Clean-up single test environment
     *
     * @return void
     */
    public function tearDown()
    {
        putenv("BARTLETT_SCAN_DIR=");
        putenv("BARTLETTRC=");
    }

    /**
     * @covers Bartlett\Reflect\Environment::getJsonConfigFilename
     *
     * @return void
     */
    public function testUndefinedScanDir()
    {
        $this->assertFalse(
            Environment::getJsonConfigFilename(),
            "Environment variable BARTLETT_SCAN_DIR is not defined."
        );
    }

    /**
     * @covers Bartlett\Reflect\Environment::getJsonConfigFilename
     *
     * @return void
     */
    public function testUndefinedConfigFilename()
    {
        putenv("BARTLETT_SCAN_DIR=.");

        $this->assertFalse(
            Environment::getJsonConfigFilename(),
            "Environment variable BARTLETTRC is not defined."
        );
    }

    /**
     * @covers Bartlett\Reflect\Environment::getJsonConfigFilename
     *
     * @return void
     */
    public function testGetConfigFilenameInSingleScanDirEnvironment()
    {
        $singleScanDir = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'bin';

        putenv("BARTLETT_SCAN_DIR=$singleScanDir");
        putenv("BARTLETTRC=" . self::DIST_RC);

        $this->assertEquals(
            $singleScanDir . DIRECTORY_SEPARATOR . self::DIST_RC,
            Environment::getJsonConfigFilename(),
            "Config filename does not match."
        );
    }

    /**
     * @covers Bartlett\Reflect\Environment::getJsonConfigFilename
     *
     * @return void
     */
    public function testGetConfigFilenameInMultipleScanDirEnvironment()
    {
        $multipleScanDir = __DIR__ . DIRECTORY_SEPARATOR . PATH_SEPARATOR .
            dirname(__DIR__) . DIRECTORY_SEPARATOR . 'bin';

        putenv("BARTLETT_SCAN_DIR=$multipleScanDir");
        putenv("BARTLETTRC=" . self::DIST_RC);

        $this->assertEquals(
            __DIR__ . DIRECTORY_SEPARATOR . self::DIST_RC,
            Environment::getJsonConfigFilename(),
            "Config filename does not match."
        );
    }

    /**
     * @covers Bartlett\Reflect\Environment::setScanDir
     *
     * @return void
     */
    public function testSetDefaultScanDir()
    {
        $home = defined('PHP_WINDOWS_VERSION_BUILD') ? 'USERPROFILE' : 'HOME';
        $dirs = array(
            realpath('.'),
            getenv($home) . DIRECTORY_SEPARATOR . '.config',
            DIRECTORY_SEPARATOR . 'etc'
        );
        $multipleScanDir = implode(PATH_SEPARATOR, $dirs);

        Environment::setScanDir();

        $this->assertEquals(
            $multipleScanDir,
            getenv("BARTLETT_SCAN_DIR"),
            "Environment variable BARTLETT_SCAN_DIR does not match."
        );
    }

    /**
     * @covers Bartlett\Reflect\Environment::getLogger
     * @runInSeparateProcess
     *
     * @return void
     */
    public function testDefaultLoggerAccessor()
    {
        $singleScanDir = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'bin';

        putenv("BARTLETT_SCAN_DIR=$singleScanDir");
        putenv("BARTLETTRC=" . self::DIST_RC);

        $logger = Environment::getLogger();

        $this->assertInstanceOf(
            'Psr\Log\LoggerInterface',
            $logger,
            'This is not a compatible PSR-3 logger'
        );

        $this->assertEquals(
            'Bartlett\Reflect\Plugin\Log\DefaultLogger',
            get_class($logger),
            'Default logger does not match.'
        );
    }

    /**
     * @covers Bartlett\Reflect\Environment::getLogger
     * @runInSeparateProcess
     *
     * @return void
     */
    public function testCustomLoggerAccessor()
    {
        $singleScanDir = __DIR__ . DIRECTORY_SEPARATOR . 'Environment';

        putenv("BARTLETT_SCAN_DIR=$singleScanDir");
        putenv("BARTLETTRC=phpreflect.json");

        $logger = Environment::getLogger();

        $this->assertInstanceOf(
            'Psr\Log\LoggerInterface',
            $logger,
            'This is not a compatible PSR-3 logger'
        );

        $this->assertEquals(
            'Bartlett\Tests\Reflect\Environment\YourLogger',
            get_class($logger),
            'Custom logger does not match.'
        );
    }

    /**
     * @covers Bartlett\Reflect\Environment::getClient
     * @runInSeparateProcess
     *
     * @return void
     */
    public function testDefaultClientAccessor()
    {
        $singleScanDir = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'bin';

        putenv("BARTLETT_SCAN_DIR=$singleScanDir");
        putenv("BARTLETTRC=" . self::DIST_RC);

        $client = Environment::getClient();

        $this->assertInstanceOf(
            'Bartlett\Reflect\Client\ClientInterface',
            $client,
            'This is not a compatible Reflect API client'
        );

        $this->assertEquals(
            'Bartlett\Reflect\Client\LocalClient',
            get_class($client),
            'Default client does not match.'
        );
    }
}
