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
 * Tests for the PHP_Reflect_Token_INTERFACE class.
 *
 * @package    PHP_Reflect
 * @subpackage Tests
 * @author     Laurent Laville <pear@laurent-laville.org>
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    Release: @package_version@
 * @link       https://github.com/llaville/php-reflect
 * @since      Class available since Release 0.1.0
 */
class PHP_Reflect_Token_InterfaceTest extends PHPUnit_Framework_TestCase
{
    protected $class;
    protected $interfaces;

    protected function setUp()
    {
        $reflect = new PHP_Reflect();
        $tokens  = $reflect->scan(TEST_FILES_PATH . 'source4.php');
        $i       = 0;

        foreach ($tokens as $id => $token) {
            if ($token[0] == 'T_CLASS') {
                $this->class = new PHP_Reflect_Token_CLASS($token[1], $token[2], $id, $tokens);
            }
            elseif ($token[0] == 'T_INTERFACE') {
                $this->interfaces[$i] = new PHP_Reflect_Token_INTERFACE($token[1], $token[2], $id, $tokens);
                $i++;
            }
        }
    }

    /**
     * @covers PHP_Reflect_Token_INTERFACE::getName
     */
    public function testGetName()
    {
        $this->assertEquals(
            'iTemplate', $this->interfaces[0]->getName()
        );
    }

    /**
     * @covers PHP_Reflect_Token_INTERFACE::getParent
     */
    public function testGetParentNotExists()
    {
        $this->assertFalse(
            $this->interfaces[0]->getParent()
        );
    }

    /**
     * @covers PHP_Reflect_Token_INTERFACE::hasParent
     */
    public function testHasParentNotExists()
    {
        $this->assertFalse(
            $this->interfaces[0]->hasParent()
        );
    }

    /**
     * @covers PHP_Reflect_Token_INTERFACE::getParent
     */
    public function testGetParentExists()
    {
        $this->assertEquals(
            'a', $this->interfaces[2]->getParent()
        );
    }

    /**
     * @covers PHP_Reflect_Token_INTERFACE::hasParent
     */
    public function testHasParentExists()
    {
        $this->assertTrue(
            $this->interfaces[2]->hasParent()
        );
    }

    /**
     * @covers PHP_Reflect_Token_INTERFACE::getInterfaces
     */
    public function testGetInterfacesExists()
    {
        $this->assertEquals(
            array('b'),
            $this->class->getInterfaces()
        );
    }

    /**
     * @covers PHP_Reflect_Token_INTERFACE::hasInterfaces
     */
    public function testHasInterfacesExists()
    {
        $this->assertTrue(
            $this->class->hasInterfaces()
        );
    }

}
