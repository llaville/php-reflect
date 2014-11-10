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
    protected static $env;

    /**
     * Sets up the shared fixture.
     *
     * @return void
     * @link   http://phpunit.de/manual/current/en/fixtures.html#fixtures.sharing-fixture
     */
    public static function setUpBeforeClass()
    {
        self::$env = new Environment();
    }

    /**
     *  covers Bartlett\Reflect\AbstractEnvironment::getJsonFilename
     *
     * @return void
     */
    public function testJsonFilenameAccessor()
    {
        $this->assertEquals(
            Environment::JSON_FILE,
            self::$env->getJsonFilename(),
            "Environment JSON file does not match."
        );
    }

    /**
     *  covers Bartlett\Reflect\AbstractEnvironment::getEnv
     *
     * @return void
     */
    public function testEnvAccessor()
    {
        $this->assertEquals(
            Environment::ENV,
            self::$env->getEnv(),
            "Environment ENV variable does not match."
        );
    }

    /**
     *  covers Bartlett\Reflect\AbstractEnvironment::validateSyntax
     *
     * @return void
     */
    public function testValidateSyntaxJsonConfigFile()
    {
        try {
            $file = dirname(__DIR__) . DIRECTORY_SEPARATOR
                . getenv(self::$env->getEnv());
            $json = self::$env->validateSyntax($file);

        } catch (\Exception $e) {
            $this->fail(
                'An unexpected ' . get_class($e) . ' exception has been raised with message. '
                . '"' . $e->getMessage() . '"'
            );
        }

        $this->assertJson(
            $json,
            "Environment JSON config is not a valid JSON string."
        );
    }

    /**
     *  covers Bartlett\Reflect\AbstractEnvironment::validateSyntax
     *
     * @return void
     */
    public function testJsonConfigFile()
    {
        try {
            $file = dirname(__DIR__) . DIRECTORY_SEPARATOR
                . getenv(self::$env->getEnv());
            $json = self::$env->validateSyntax($file);
            $var  = json_decode($json, true);

        } catch (\Exception $e) {
            $this->fail(
                'An unexpected ' . get_class($e) . ' exception has been raised with message. '
                . '"' . $e->getMessage() . '"'
            );
        }

        $config = array(
            'source-providers' => array(
                array (
                    'in'   => '. as current',
                    'name' => '/\\.(php|inc|phtml)$/',
                ),
            ),
            'plugins' => array(
                array (
                    'name'  => 'Analyser',
                    'class' => 'Bartlett\\Reflect\\Plugin\\Analyser\\AnalyserPlugin',
                ),
            ),
            'analysers' => array(
                array (
                    'name'  => 'Structure',
                    'class' => 'Bartlett\\Reflect\\Analyser\\StructureAnalyser',
                ),
            ),
        );

        $this->assertEquals(
            $config,
            $var,
            "Environment JSON config does not match."
        );
    }
}
