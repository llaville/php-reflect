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
 * Tests for the PHP_Reflect_Token_NAMESPACE class.
 *
 * @package    PHP_Reflect
 * @subpackage Tests
 * @author     Laurent Laville <pear@laurent-laville.org>
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    Release: @package_version@
 * @link       https://github.com/llaville/php-reflect
 * @since      Class available since Release 0.1.0
 */
class PHP_Reflect_Token_NamespaceTest extends PHPUnit_Framework_TestCase
{
    protected $ns;

    protected function setUp()
    {
        $reflect = new PHP_Reflect();
        $tokens  = $reflect->scan(TEST_FILES_PATH . 'source5.php');
        $i       = 0;

        foreach ($tokens as $id => $token) {
            if (($token[0] == 'T_STRING' && $token[1] == 'namespace') 
                || ($token[0] == 'T_NAMESPACE')
            ) {
                $this->ns = new PHP_Reflect_Token_NAMESPACE($token[1], $token[2], $id, $tokens);
            }
        }
    }

    /**
     * @covers PHP_Reflect_Token_NAMESPACE::getName
     */
    public function testGetName()
    {
        if (version_compare(PHP_VERSION, '5.3.0', '<')) {
            $this->assertEquals(
                'A', $this->ns->getName()
            );
        } else {
            $this->assertEquals(
                'A\B', $this->ns->getName()
            );
        }
    }
}
