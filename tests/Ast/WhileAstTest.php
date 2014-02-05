<?php
/**
 * Unit Test Case that covers AST Node While Statement.
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
 * @since      Class available since Release 2.0.0RC2
 */

namespace Bartlett\Tests\Reflect\Ast;

use Bartlett\Reflect\Parser\DefaultParser;
use Bartlett\Reflect\Tokenizer\DefaultTokenizer;

use Symfony\Component\Finder\Finder;

if (!defined('TEST_FILES_PATH')) {
    define(
        'TEST_FILES_PATH',
        dirname(__DIR__) . DIRECTORY_SEPARATOR .
        '_files' . DIRECTORY_SEPARATOR
    );
}

/**
 * Unit Test Case that covers AST Node While Statement.
 *
 * @category   PHP
 * @package    PHP_Reflect
 * @subpackage Tests
 * @author     Laurent Laville <pear@laurent-laville.org>
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    Release: @package_version@
 * @link       http://php5.laurent-laville.org/reflect/
 */
class WhileAstTest extends \PHPUnit_Framework_TestCase
{
    protected static $parser;
    protected static $ast;
    protected static $package;

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
            ->name('WhileStatement.php')
            ->in(TEST_FILES_PATH);

        $iter = $finder->getIterator();
        $iter->rewind();
        $file = $iter->current();

        self::$parser = new DefaultParser(
            new DefaultTokenizer
        );
        self::$ast = self::$parser->parseFile($file);
    }

    /**
     * Test the AST (Abstract Syntax Tree) storage.
     *
     * @return void
     */
    public function testAstStorage()
    {
        $this->assertInstanceOf(
            '\SplObjectStorage',
            self::$ast,
            'Invalid AST storage.'
        );

        $this->assertCount(
            1,
            self::$ast,
            'Should have only a single package object in AST storage.'
        );

        self::$ast->rewind();
        self::$package = self::$ast->current();
        $this->assertInstanceOf(
            'Bartlett\\Reflect\\Model\\PackageModel',
            self::$package,
            'There is no PackageModel in AST storage.'
        );
    }

    /**
     * Tests the while statement without block.
     *
     * <code>
     *  while (expression)
     *    commands;
     * </code>
     *
     * @depends testAstStorage
     * @group   ast
     * @return  void
     */
    public function testWhileWithoutBlock()
    {
        $nodes = self::$package->getChildren();

        $this->assertInstanceOf(
            'Bartlett\\Reflect\\Ast\\Statement',
            $nodes[1],
            'While Statement is missing.'
        );

        $expected = array(
            'startLine'       => 4,
            'endLine'         => 5,
        );
        $this->assertEquals(
            $expected,
            $nodes[1]->getAttributes(),
            'Attributes of While Statement does not match.'
        );
    }

    /**
     * Tests the while statement with optional block.
     *
     * <code>
     *  while (expression) {
     *    commands;
     *    ...
     *  }
     * </code>
     *
     * @depends testAstStorage
     * @group   ast
     * @return  void
     */
    public function testWhileWithBlock()
    {
        $nodes = self::$package->getChildren();

        $this->assertInstanceOf(
            'Bartlett\\Reflect\\Ast\\Statement',
            $nodes[2],
            'While Statement is missing.'
        );

        $expected = array(
            'startLine'       => 7,
            'endLine'         => 10,
        );
        $this->assertEquals(
            $expected,
            $nodes[2]->getAttributes(),
            'Attributes of While Statement does not match.'
        );
    }

    /**
     * Tests the while statement with alternative syntax.
     *
     * <code>
     *  while (expression):
     *    commands
     *    ...
     *  endwhile;
     * </code>
     *
     * @depends testAstStorage
     * @group   ast
     * @return  void
     */
    public function testWhileWithAlternativeSyntax()
    {
        $nodes = self::$package->getChildren();

        $this->assertInstanceOf(
            'Bartlett\\Reflect\\Ast\\Statement',
            $nodes[3],
            'Label Statement is missing.'
        );

        $expected = array(
            'startLine'       => 12,
            'endLine'         => 15,
        );
        $this->assertEquals(
            $expected,
            $nodes[3]->getAttributes(),
            'Attributes of Label Statement does not match.'
        );
    }
}
