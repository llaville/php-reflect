<?php
/**
 * Unit tests for PHP_Reflect package, issues reported
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
 * Tests for PHP_Reflect, retrieving reference elements,
 * and versioning information.
 *
 * @category   PHP
 * @package    PHP_Reflect
 * @subpackage Tests
 * @author     Laurent Laville <pear@laurent-laville.org>
 * @license    https://opensource.org/licenses/BSD-3-Clause The 3-Clause BSD License
 */
class IssueTest extends \PHPUnit\Framework\TestCase
{
    const GH4 = 'packages.php';

    protected static $fixtures;
    protected static $analyserId;
    protected static $api;

    /**
     * Sets up the shared fixture.
     *
     * @return void
     */
    public static function setUpBeforeClass(): void
    {
        self::$fixtures = dirname(__DIR__) . DIRECTORY_SEPARATOR
            . 'fixtures' . DIRECTORY_SEPARATOR;

        self::$analyserId = 'Bartlett\Reflect\Analyser\ReflectionAnalyser';

        $client = new Client();

        // request for a Bartlett\Reflect\Api\Analyser
        self::$api = $client->api('analyser');
    }

    /**
     * Regression test for bug GH#4
     *
     * @link https://github.com/llaville/php-reflect/issues/
     *       "Handle namespaces without name"
     * @link https://github.com/llaville/php-reflect/pull/4 by Eric Colinet
     * @group regression
     * @return void
     */
    public function testBugGH4()
    {
        $dataSource = self::$fixtures . self::GH4;
        $analysers  = array('reflection');
        $metrics    = self::$api->run($dataSource, $analysers);
        $models     = $metrics[self::$analyserId];

        $c = 0;    // empty namespace, class MyGlobalClass

        $this->assertInstanceOf(
            'Bartlett\Reflect\Model\ClassModel',
            $models[$c]
        );
        $this->assertEquals('MyGlobalClass', $models[$c]->getName());
    }
}
