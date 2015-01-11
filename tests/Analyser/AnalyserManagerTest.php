<?php
/**
 * Unit tests for PHP_Reflect package, Analyser Manager Component
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
 * @since      Class available since Release 3.0.0-alpha2+1
 */

namespace Bartlett\Tests\Reflect\Analyser;

use Bartlett\Reflect\Analyser\AnalyserManager;

/**
 * Tests for PHP_CompatInfo, handling analysers.
 *
 * @category   PHP
 * @package    PHP_Reflect
 * @subpackage Tests
 * @author     Laurent Laville <pear@laurent-laville.org>
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    Release: @package_version@
 * @link       http://php5.laurent-laville.org/reflect/
 */
class AnalyserManagerTest extends \PHPUnit_Framework_TestCase
{
    protected $am;

    /**
     * Sets up the fixture.
     *
     * @return void
     */
    public function setUp()
    {
        putenv("BARTLETT_SCAN_DIR=" . __DIR__);
        putenv("BARTLETTRC=phpreflect.json");

        $this->am = new AnalyserManager();
    }

    /**
     * Clean-up test environment
     *
     * @return void
     */
    public function tearDown()
    {
        putenv("BARTLETT_SCAN_DIR=");
        putenv("BARTLETTRC=");
    }


    /**
     * Analyser Manager can handle only instance of AnalyserInterface
     *
     * @group analyser
     * @return void
     */
    public function testContainsOnlyAnalyserInterfaces()
    {
        $this->assertContainsOnlyInstancesOf(
            'Bartlett\Reflect\Analyser\AnalyserInterface',
            $this->am->getAnalysers()
        );
    }

    /**
     * Tests the analysers accessor.
     *
     *  covers Bartlett\Reflect\Analyser\AnalyserManager::getAnalysers
     *
     * @depends testContainsOnlyAnalyserInterfaces
     * @group   analyser
     * @return  void
     */
    public function testDefaultAnalysers()
    {
        $this->assertCount(
            3,
            $this->am->getAnalysers(),
            'Default Reflect 3.0 distribution contains only 3 analysers'
        );
    }

    /**
     * Tests the analysers register process.
     *
     *  covers Bartlett\Reflect\Analyser\AnalyserManager::registerAnalysers
     *
     * @depends testContainsOnlyAnalyserInterfaces
     * @group   analyser
     * @return  void
     */
    public function testRegisterAnalysers()
    {
        $this->am->registerAnalysers();

        $analysers = $this->am->getAnalysers();

        $this->assertCount(
            4,
            $analysers
        );
    }

    /**
     * Tests to add direclty a new analyser.
     *
     *  covers Bartlett\Reflect\Analyser\AnalyserManager::registerAnalysers
     *
     * @group   analyser
     * @return  void
     */
    public function testAddAnalyser()
    {
        $analyser = new FooAnalyser();
        $this->am->addAnalyser($analyser);

        $analysers = $this->am->getAnalysers();

        $this->assertCount(
            4,
            $analysers
        );
    }

    /**
     * Tests Name accessor.
     *
     *  covers Bartlett\Reflect\Analyser\AbstractAnalyser::getName
     *
     * @group   analyser
     * @return  void
     */
    public function testName()
    {
        $analyser = new FooAnalyser();

        $this->assertEquals('FooAnalyser', $analyser->getName());
    }

    /**
     * Tests Short Name accessor.
     *
     *  covers Bartlett\Reflect\Analyser\AbstractAnalyser::getName
     *
     * @group   analyser
     * @return  void
     */
    public function testShortName()
    {
        $analyser = new FooAnalyser();

        $this->assertEquals('foo', $analyser->getShortName());
    }
}
