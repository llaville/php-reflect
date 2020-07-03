<?php
/**
 * Unit Test Case that covers each Model representative.
 *
 * PHP version 7
 *
 * @category   PHP
 * @package    PHP_Reflect
 * @subpackage Tests
 * @author     Laurent Laville <pear@laurent-laville.org>
 * @license    https://opensource.org/licenses/BSD-3-Clause The 3-Clause BSD License
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
 * @license    https://opensource.org/licenses/BSD-3-Clause The 3-Clause BSD License
 */
abstract class GenericModelTest extends \PHPUnit\Framework\TestCase
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
    public static function setUpBeforeClass(): void
    {
        self::$fixtures = dirname(__DIR__) . DIRECTORY_SEPARATOR
            . 'fixtures' . DIRECTORY_SEPARATOR;

        self::$fixture = self::$fixtures . self::$fixture;

        $client = new Client();

        // request for a Bartlett\Reflect\Api\Analyser
        self::$api = $client->api('analyser');

        $analyserId   = 'Bartlett\Reflect\Analyser\ReflectionAnalyser';
        $dataSource   = self::$fixture;
        $analysers    = array('reflection');
        $metrics      = self::$api->run($dataSource, $analysers);
        self::$models = $metrics[$analyserId];
    }
}
