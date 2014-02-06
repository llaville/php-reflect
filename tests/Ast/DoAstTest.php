<?php
/**
 * Unit Test Case that covers AST Node Do Statement.
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
 * Unit Test Case that covers AST Node Do Statement.
 *
 * @category   PHP
 * @package    PHP_Reflect
 * @subpackage Tests
 * @author     Laurent Laville <pear@laurent-laville.org>
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    Release: @package_version@
 * @link       http://php5.laurent-laville.org/reflect/
 */
class DoAstTest extends \PHPUnit_Framework_TestCase
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
            ->name('DoStatement.php')
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
     * Tests the do-while statement with block.
     *
     * <code>
     *  do {
     *    commands;
     *    ...
     *  } while (expression);
     * </code>
     *
     * @depends testAstStorage
     * @group   ast
     * @return  void
     */
    public function testDoWhileWithBlock()
    {
        $nodes = self::$package->getChildren();

        $this->assertInstanceOf(
            'Bartlett\\Reflect\\Ast\\Statement',
            $nodes[1],
            'Do Statement is missing.'
        );

        $expected = array(
            'startLine'       => 4,
            'endLine'         => 8,
        );
        $this->assertEquals(
            $expected,
            $nodes[1]->getAttributes(),
            'Attributes of Do Statement does not match.'
        );
    }
}
