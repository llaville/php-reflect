<?php
/**
 * Unit Test Case that covers AST Node ElseIf Statement.
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
 * Unit Test Case that covers AST Node ElseIf Statement.
 *
 * @category   PHP
 * @package    PHP_Reflect
 * @subpackage Tests
 * @author     Laurent Laville <pear@laurent-laville.org>
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    Release: @package_version@
 * @link       http://php5.laurent-laville.org/reflect/
 */
class ElseIfAstTest extends \PHPUnit_Framework_TestCase
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
            ->name('IfElseStatement.php')
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
     * Tests the elseif statement without optional block.
     *
     * <code>
     *  if (expr1)
     *    ...
     *  elseif (expr2)
     *    statement;
     * </code>
     *
     * @depends testAstStorage
     * @group   ast
     * @return  void
     */
    public function testElseIfWithoutBlock()
    {
        $nodes = self::$package->getChildren();
        $n     = 8;

        $this->assertInstanceOf(
            'Bartlett\\Reflect\\Ast\\Statement',
            $nodes[$n],
            'ElseIf Statement is missing.'
        );

        $expected = array(
            'startLine'       => 26,
            'endLine'         => 27,
        );
        $this->assertEquals(
            $expected,
            $nodes[$n]->getAttributes(),
            'Attributes of ElseIf Statement does not match.'
        );
    }

    /**
     * Tests the elseif statement with optional block.
     *
     * <code>
     *  if (expr1)
     *    ...
     *  elseif (expr2) {
     *    statement;
     *  }
     * </code>
     *
     * @depends testAstStorage
     * @group   ast
     * @return  void
     */
    public function testElseIfWithBlock()
    {
        $nodes = self::$package->getChildren();
        $n     = 5;

        $this->assertInstanceOf(
            'Bartlett\\Reflect\\Ast\\Statement',
            $nodes[$n],
            'ElseIf Statement is missing.'
        );

        $expected = array(
            'startLine'       => 19,
            'endLine'         => 21,
        );
        $this->assertEquals(
            $expected,
            $nodes[$n]->getAttributes(),
            'Attributes of ElseIf Statement does not match.'
        );
    }
}
