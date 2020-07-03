<?php
/**
 * Unit Test Case that covers the Environment component.
 *
 * PHP version 7
 *
 * @category   PHP
 * @package    PHP_Reflect
 * @subpackage Tests
 * @author     Laurent Laville <pear@laurent-laville.org>
 * @license    https://opensource.org/licenses/BSD-3-Clause The 3-Clause BSD License
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
 * @license    https://opensource.org/licenses/BSD-3-Clause The 3-Clause BSD License
 */
class EnvironmentTest extends \PHPUnit\Framework\TestCase
{
    const DIST_RC = 'phpreflect.json.dist';

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
        $singleScanDir = __DIR__ . DIRECTORY_SEPARATOR . 'Environment' . DIRECTORY_SEPARATOR . 'dir1';

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
        $baseScanDir = __DIR__ . DIRECTORY_SEPARATOR . 'Environment';

        $multipleScanDir = $baseScanDir . DIRECTORY_SEPARATOR . 'dir1'
            . PATH_SEPARATOR .
            $baseScanDir . DIRECTORY_SEPARATOR . 'dir2'
        ;

        putenv("BARTLETT_SCAN_DIR=$multipleScanDir");
        putenv("BARTLETTRC=" . self::DIST_RC);

        $this->assertEquals(
            $baseScanDir . DIRECTORY_SEPARATOR . 'dir1' . DIRECTORY_SEPARATOR . self::DIST_RC,
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
        $singleScanDir = __DIR__ . DIRECTORY_SEPARATOR . 'Environment' . DIRECTORY_SEPARATOR . 'dir1';

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
        $singleScanDir = __DIR__ . DIRECTORY_SEPARATOR . 'Environment' . DIRECTORY_SEPARATOR . 'dir1';

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
