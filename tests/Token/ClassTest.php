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
 * Tests for the PHP_Reflect_Token_CLASS class.
 *
 * @package    PHP_Reflect
 * @subpackage Tests
 * @author     Laurent Laville <pear@laurent-laville.org>
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    Release: @package_version@
 * @link       https://github.com/llaville/php-reflect
 * @since      Class available since Release 0.1.0
 */
class PHP_Reflect_Token_ClassTest extends PHPUnit_Framework_TestCase
{
    protected $class;
    protected $function;

    protected function setUp()
    {
        $reflect = new PHP_Reflect();
        $tokens  = $reflect->scan(TEST_FILES_PATH . 'source2.php');

        foreach ($tokens as $id => $token) {
            
            if ($token[0] == 'T_CLASS') {
                $this->class = new PHP_Reflect_Token_CLASS($token[1], $token[2], $id, $tokens);
            }
            if ($token[0] == 'T_FUNCTION') {
                $this->function = new PHP_Reflect_Token_FUNCTION($token[1], $token[2], $id, $tokens);
                break;
            }
        }
    }

    /**
     * @covers PHP_Reflect_Token_CLASS::getKeywords
     */
    public function testGetClassKeywords()
    {
        $this->assertEquals(
            'abstract', $this->class->getKeywords()
        );
    }

    /**
     * @covers PHP_Reflect_Token_FUNCTION::getKeywords
     */
    public function testGetFunctionKeywords()
    {
        $this->assertEquals(
            'abstract,static', $this->function->getKeywords()
        );
    }

    /**
     * @covers PHP_Reflect_Token_FUNCTION::getVisibility
     */
    public function testGetFunctionVisibility()
    {
        $this->assertEquals(
            'public', $this->function->getVisibility()
        );
    }

}
