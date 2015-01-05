<?php
/**
 * Unit Test Case that covers each Model representative.
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
 * @since      Class available since Release 3.0.0-alpha2
 */

namespace Bartlett\Tests\Reflect\Model;

use Bartlett\Reflect\Client;

/**
 * Unit Test Case that covers Bartlett\Reflect\Model\*Model
 *
 * @category   PHP
 * @package    PHP_Reflect
 * @subpackage Tests
 * @author     Laurent Laville <pear@laurent-laville.org>
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    Release: @package_version@
 * @link       http://php5.laurent-laville.org/reflect/
 */
abstract class GenericModelTest extends \PHPUnit_Framework_TestCase
{
    protected static $fixtures;
    protected static $fixture;
    protected static $models;
    protected static $api;

    /**
     * Sets up the shared fixture.
     *
     * @return void
     * @link   http://phpunit.de/manual/current/en/fixtures.html#fixtures.sharing-fixture
     */
    public static function setUpBeforeClass()
    {
        self::$fixtures = dirname(__DIR__) . DIRECTORY_SEPARATOR
            . 'fixtures' . DIRECTORY_SEPARATOR;

        self::$fixture = self::$fixtures . self::$fixture;

        $client = new Client();

        // request for a Bartlett\Reflect\Api\Analyser
        self::$api = $client->api('analyser');

        $dataSource   = self::$fixture;
        $analysers    = array('reflection');
        $metrics      = self::$api->run($dataSource, $analysers);
        self::$models = $metrics['ReflectionAnalyser'];
    }
}
