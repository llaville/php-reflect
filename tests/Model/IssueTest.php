<?php
/**
 * Unit tests for PHP_Reflect package, issues reported
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
 * Tests for PHP_CompatInfo, retrieving reference elements,
 * and versioning information.
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
    const GH4 = 'packages.php';

    protected static $fixtures;
    protected static $api;

    /**
     * Sets up the shared fixture.
     *
     * @return void
     */
    public static function setUpBeforeClass()
    {
        self::$fixtures = dirname(__DIR__) . DIRECTORY_SEPARATOR
            . 'fixtures' . DIRECTORY_SEPARATOR;

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
        $models     = $metrics['ReflectionAnalyser'];

        $c = 0;    // empty namespace, class MyGlobalClass

        $this->assertInstanceOf(
            'Bartlett\Reflect\Model\ClassModel',
            $models[$c]
        );
        $this->assertEquals('MyGlobalClass', $models[$c]->getName());
    }
}
