<?php

if (!defined('TEST_FILES_PATH')) {
    define(
      'TEST_FILES_PATH',
      dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR .
      '_files' . DIRECTORY_SEPARATOR
    );
}

require_once dirname(dirname(dirname(__FILE__))) . DIRECTORY_SEPARATOR . 'PHP/Reflect.php';

spl_autoload_register('PHP_Reflect::autoload');

/**
 * Tests for the PHP_Reflect_Token_REQUIRE_ONCE, PHP_Reflect_Token_REQUIRE
 * PHP_Reflect_Token_INCLUDE_ONCE and PHP_Reflect_Token_INCLUDE_ONCE classes.
 *
 * @package    PHP_Reflect
 * @subpackage Tests
 * @author     Laurent Laville <pear@laurent-laville.org>
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    Release: @package_version@
 * @link       https://github.com/llaville/php-reflect
 * @since      Class available since Release 0.1.0
 */
class PHP_Reflect_Token_IncludeTest extends PHPUnit_Framework_TestCase
{
    protected $reflect;
    protected $filename;

    protected function setUp()
    {
        $this->reflect = new PHP_Reflect();
        $this->reflect->scan(
            $this->filename = TEST_FILES_PATH . 'source3.php'
        );
    }

    /**
     * @covers PHP_TokenIncludes::getName
     * @covers PHP_TokenIncludes::getType
     */
    public function testGetIncludes()
    {
        $this->assertSame(
            array('test4.php', 'test3.php', 'test2.php', 'test1.php'),
            array_keys($this->reflect->getIncludes())
        );
    }

    /**
     * @covers PHP_TokenIncludes::getName
     * @covers PHP_TokenIncludes::getType
     */
    public function testGetIncludesCategorized()
    {
        $this->assertSame(
            array(
                'include'      => array(
                    'test1.php' => array('startLine' => 5, 'endLine' => 5, 'file' => $this->filename, 'docblock' => null)
                ),
                'include_once' => array(
                    'test2.php' => array('startLine' => 6, 'endLine' => 6, 'file' => $this->filename, 'docblock' => null)
                ),
                'require'      => array(
                    'test3.php' => array('startLine' => 7, 'endLine' => 7, 'file' => $this->filename, 'docblock' => null)
                ),
                'require_once' => array(
                    'test4.php' => array('startLine' => 9, 'endLine' => 10, 'file' => $this->filename, 'docblock' => "// test four\n")
                ),
            ),
            $this->reflect->getIncludes(true)
        );
    }

    /**
     * @covers PHP_Reflect_Token_Includes::getName
     * @covers PHP_Reflect_Token_Includes::getType
     */
    public function testGetIncludesCategory()
    {
        $this->assertSame(
            array('test4.php' => 
                array('startLine' => 9, 'endLine' => 10, 'file' => $this->filename, 'docblock' => "// test four\n")
            ),
            $this->reflect->getIncludes(true, 'require_once')
        );
    }

}
