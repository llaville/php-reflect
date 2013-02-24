<?php
/**
 * Copyright (c) 2011-2013, Laurent Laville <pear@laurent-laville.org>
 *
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions
 * are met:
 *
 *     * Redistributions of source code must retain the above copyright
 *       notice, this list of conditions and the following disclaimer.
 *     * Redistributions in binary form must reproduce the above copyright
 *       notice, this list of conditions and the following disclaimer in the
 *       documentation and/or other materials provided with the distribution.
 *     * Neither the name of the authors nor the names of its contributors
 *       may be used to endorse or promote products derived from this software
 *       without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS"
 * AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE
 * IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE
 * ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR CONTRIBUTORS
 * BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR
 * CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF
 * SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS
 * INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN
 * CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE)
 * ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 *
 * PHP version 5
 *
 * @category PHP
 * @package  PHP_Reflect
 * @author   Laurent Laville <pear@laurent-laville.org>
 * @license  http://www.opensource.org/licenses/bsd-license.php  BSD
 * @version  GIT: $Id$
 * @link     http://php5.laurent-laville.org/reflect/
 */

if (!defined('TEST_FILES_PATH')) {
    define(
        'TEST_FILES_PATH',
        dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR .
        '_files' . DIRECTORY_SEPARATOR
    );
}

/**
 * Tests for
 * the PHP_Reflect_Token_MagicConstant and PHP_Reflect_Token_CONST classes
 *
 * @category   PHP
 * @package    PHP_Reflect
 * @subpackage Tests
 * @author     Laurent Laville <pear@laurent-laville.org>
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    Release: @package_version@
 * @link       https://github.com/llaville/php-reflect
 * @since      Class available since Release 1.6.0
 */
class PHP_Reflect_Token_ConstantTest extends PHPUnit_Framework_TestCase
{
    protected $constants;
    protected $obj;

    /**
     * Sets up the fixture.
     *
     * Parse source code to find all CONSTANT_ENCAPSED_STRING
     * and magic constants familiy tokens
     *
     * @return void
     */
    protected function setUp()
    {
        $this->obj = new PHP_Reflect();
        $tokens    = $this->obj->scan(TEST_FILES_PATH . 'magic_constant.php');

        $constants = array(
            // user constants
            'T_CONST',
            // magic constants
            'T_LINE', 'T_FILE', 'T_DIR',
            'T_FUNC_C', 'T_CLASS_C', 'T_TRAIT_C', 'T_METHOD_C', 'T_NS_C'
        );

        foreach ($tokens as $id => $token) {
            if (in_array($token[0], $constants)) {
                $class = 'PHP_Reflect_Token_' . substr($token[0], 2);

                $this->constants[] = new $class(
                    $token[1], $token[2], $id, $tokens
                );
            }
        }
    }

    /**
     * Test magic and user constants names
     *
     * @covers PHP_Reflect_Token_MagicConstant::getName
     * @covers PHP_Reflect_Token_CONST::getName
     * @return void
     */
    public function testGetName()
    {
        $this->assertEquals('CONNECT_OK',    $this->constants[0]->getName());
        $this->assertEquals('__FILE__',      $this->constants[1]->getName());
        $this->assertEquals('DSN',           $this->constants[2]->getName());
        $this->assertEquals('__CLASS__',     $this->constants[3]->getName());
        $this->assertEquals('__METHOD__',    $this->constants[4]->getName());
        $this->assertEquals('__LINE__',      $this->constants[5]->getName());
        $this->assertEquals('__FUNCTION__',  $this->constants[6]->getName());
        $this->assertEquals('__LINE__',      $this->constants[7]->getName());

        if (version_compare(PHP_VERSION, '5.3.0') < 0) {
            return;
        }
        $this->assertEquals('__NAMESPACE__', $this->constants[8]->getName());
        $this->assertEquals('__DIR__',       $this->constants[9]->getName());

        if (version_compare(PHP_VERSION, '5.4.0') < 0) {
            return;
        }
        $this->assertEquals('__TRAIT__',     $this->constants[10]->getName());
    }

    /**
     * Test magic and user constants location in source file
     *
     * @covers PHP_Reflect_Token_MagicConstant::getLine
     * @covers PHP_Reflect_Token_CONST::getLine
     * @return void
     */
    public function testGetLine()
    {
        $this->assertEquals(2,  $this->constants[0]->getLine());
        $this->assertEquals(4,  $this->constants[1]->getLine());
        $this->assertEquals(7,  $this->constants[2]->getLine());
        $this->assertEquals(10, $this->constants[3]->getLine());
        $this->assertEquals(10, $this->constants[4]->getLine());
        $this->assertEquals(10, $this->constants[5]->getLine());
        $this->assertEquals(15, $this->constants[6]->getLine());
        $this->assertEquals(15, $this->constants[7]->getLine());

        if (version_compare(PHP_VERSION, '5.3.0') < 0) {
            return;
        }
        $this->assertEquals(19, $this->constants[8]->getLine());
        $this->assertEquals(19, $this->constants[9]->getLine());

        if (version_compare(PHP_VERSION, '5.4.0') < 0) {
            return;
        }
        $this->assertEquals(22, $this->constants[10]->getLine());
    }

    /**
     * Test getting all constants (user, class, magic) at-once
     *
     * @covers PHP_Reflect::getConstants
     * @return void
     */
    public function testGetAllConstants()
    {
        $expected = array(
            'CONNECT_OK' => array(
                array(
                    'file' => TEST_FILES_PATH . 'magic_constant.php',
                    'namespace' => '',
                    'line' => 2,
                    'value' => '1',
                    'uses' => array(2),
                    'class' => false,
                    'trait' => false,
                    'docblock' => null,
                )
            ),
            'DSN' => array(
                array(
                    'file' => TEST_FILES_PATH . 'magic_constant.php',
                    'namespace' => '',
                    'line' => 7,
                    'value' => 'protocol://',
                    'uses' => array(7),
                    'class' => 'Connection',
                    'trait' => false,
                    'docblock' => null,
                )
            ),
            '__FILE__' => array(
                array(
                    'file' => TEST_FILES_PATH . 'magic_constant.php',
                    'namespace' => '',
                    'uses' => array(4),
                    'class' => false,
                    'trait' => false,
                    'docblock' => null,
                )
            ),
            '__CLASS__' => array(
                array(
                    'file' => TEST_FILES_PATH . 'magic_constant.php',
                    'namespace' => '',
                    'uses' => array(10),
                    'class' => false,
                    'trait' => false,
                    'docblock' => null,
                )
            ),
            '__METHOD__' => array(
                array(
                    'file' => TEST_FILES_PATH . 'magic_constant.php',
                    'namespace' => '',
                    'uses' => array(10),
                    'class' => false,
                    'trait' => false,
                    'docblock' => null,
                )
            ),
            '__LINE__' => array(
                array(
                    'file' => TEST_FILES_PATH . 'magic_constant.php',
                    'namespace' => '',
                    'uses' => array(10),
                    'class' => false,
                    'trait' => false,
                    'docblock' => null,
                ),
                array(
                    'file' => TEST_FILES_PATH . 'magic_constant.php',
                    'namespace' => '',
                    'uses' => array(15),
                    'class' => false,
                    'trait' => false,
                    'docblock' => '/* ... */',
                ),
            ),
            '__FUNCTION__' => array(
                array(
                    'file' => TEST_FILES_PATH . 'magic_constant.php',
                    'namespace' => '',
                    'uses' => array(15),
                    'class' => false,
                    'trait' => false,
                    'docblock' => '/* ... */',
                )
            ),
            '__NAMESPACE__' => array(
                array(
                    'file' => TEST_FILES_PATH . 'magic_constant.php',
                    'namespace' => '',
                    'uses' => array(19),
                    'class' => false,
                    'trait' => false,
                    'docblock' => null,
                )
            ),
            '__DIR__' => array(
                array(
                    'file' => TEST_FILES_PATH . 'magic_constant.php',
                    'namespace' => '',
                    'uses' => array(19),
                    'class' => false,
                    'trait' => false,
                    'docblock' => null,
                )
            ),
            '__TRAIT__' => array(
                array(
                    'file' => TEST_FILES_PATH . 'magic_constant.php',
                    'namespace' => '',
                    'uses' => array(22),
                    'class' => false,
                    'trait' => false,
                    'docblock' => null,
                )
            ),
        );

        $this->assertEquals($expected, $this->obj->getConstants());
    }

    /**
     * Test categorized feature
     *
     * @covers PHP_Reflect::getConstants
     * @return void
     */
    public function testGetCategorizedConstants()
    {
        $expected = array(
            'user' => array(
                'CONNECT_OK' => array(
                    'file' => TEST_FILES_PATH . 'magic_constant.php',
                    'namespace' => '',
                    'line' => 2,
                    'value' => '1',
                    'uses' => array(2),
                    'docblock' => null,
                ),
            ),
            'class' => array(
                'Connection' => array(
                    'DSN' => array(
                        'file' => TEST_FILES_PATH . 'magic_constant.php',
                        'namespace' => '',
                        'line' => 7,
                        'value' => 'protocol://',
                        'uses' => array(7),
                        'docblock' => null,
                    ),
                ),
            ),
            'magic' => array(
                '__FILE__' => array(
                    array(
                        'file' => TEST_FILES_PATH . 'magic_constant.php',
                        'namespace' => '',
                        'uses' => array(4),
                        'class' => false,
                        'trait' => false,
                        'docblock' => null,
                    )
                ),
                '__CLASS__' => array(
                    array(
                        'file' => TEST_FILES_PATH . 'magic_constant.php',
                        'namespace' => '',
                        'uses' => array(10),
                        'class' => false,
                        'trait' => false,
                        'docblock' => null,
                    )
                ),
                '__METHOD__' => array(
                    array(
                        'file' => TEST_FILES_PATH . 'magic_constant.php',
                        'namespace' => '',
                        'uses' => array(10),
                        'class' => false,
                        'trait' => false,
                        'docblock' => null,
                    )
                ),
                '__LINE__' => array(
                    array(
                        'file' => TEST_FILES_PATH . 'magic_constant.php',
                        'namespace' => '',
                        'uses' => array(10),
                        'class' => false,
                        'trait' => false,
                        'docblock' => null,
                    ),
                    array(
                        'file' => TEST_FILES_PATH . 'magic_constant.php',
                        'namespace' => '',
                        'uses' => array(15),
                        'class' => false,
                        'trait' => false,
                        'docblock' => '/* ... */',
                    ),
                ),
                '__FUNCTION__' => array(
                    array(
                        'file' => TEST_FILES_PATH . 'magic_constant.php',
                        'namespace' => '',
                        'uses' => array(15),
                        'class' => false,
                        'trait' => false,
                        'docblock' => '/* ... */',
                    )
                ),
                '__NAMESPACE__' => array(
                    array(
                        'file' => TEST_FILES_PATH . 'magic_constant.php',
                        'namespace' => '',
                        'uses' => array(19),
                        'class' => false,
                        'trait' => false,
                        'docblock' => null,
                    )
                ),
                '__DIR__' => array(
                    array(
                        'file' => TEST_FILES_PATH . 'magic_constant.php',
                        'namespace' => '',
                        'uses' => array(19),
                        'class' => false,
                        'trait' => false,
                        'docblock' => null,
                    )
                ),
                '__TRAIT__' => array(
                    array(
                        'file' => TEST_FILES_PATH . 'magic_constant.php',
                        'namespace' => '',
                        'uses' => array(22),
                        'class' => false,
                        'trait' => false,
                        'docblock' => null,
                    )
                ),
            ),
            'ext' => array(),
        );

        $this->assertEquals($expected, $this->obj->getConstants(true));
    }

    /**
     * Test categorized feature : user constants only
     *
     * @covers PHP_Reflect::getConstants
     * @return void
     */
    public function testGetUserConstants()
    {
        $expected = array(
            'CONNECT_OK' => array(
                'file' => TEST_FILES_PATH . 'magic_constant.php',
                'namespace' => '',
                'line' => 2,
                'value' => '1',
                'uses' => array(2),
                'docblock' => null,
            ),
        );

        $this->assertEquals($expected, $this->obj->getConstants(false, 'user'));
    }

    /**
     * Test categorized feature : class constants only
     *
     * @covers PHP_Reflect::getConstants
     * @return void
     */
    public function testGetClassConstants()
    {
        $expected = array(
            'Connection' => array(
                'DSN' => array(
                    'file' => TEST_FILES_PATH . 'magic_constant.php',
                    'namespace' => '',
                    'line' => 7,
                    'value' => 'protocol://',
                    'uses' => array(7),
                    'docblock' => null,
                ),
            ),
        );

        $this->assertEquals($expected, $this->obj->getConstants(false, 'class'));
    }

    /**
     * Test categorized feature : magic constants only
     *
     * @covers PHP_Reflect::getConstants
     * @return void
     */
    public function testGetMagicConstants()
    {
        $expected = array(
            '__FILE__' => array(
                array(
                    'file' => TEST_FILES_PATH . 'magic_constant.php',
                    'namespace' => '',
                    'uses' => array(4),
                    'class' => false,
                    'trait' => false,
                    'docblock' => null,
                )
            ),
            '__CLASS__' => array(
                array(
                    'file' => TEST_FILES_PATH . 'magic_constant.php',
                    'namespace' => '',
                    'uses' => array(10),
                    'class' => false,
                    'trait' => false,
                    'docblock' => null,
                )
            ),
            '__METHOD__' => array(
                array(
                    'file' => TEST_FILES_PATH . 'magic_constant.php',
                    'namespace' => '',
                    'uses' => array(10),
                    'class' => false,
                    'trait' => false,
                    'docblock' => null,
                )
            ),
            '__LINE__' => array(
                array(
                    'file' => TEST_FILES_PATH . 'magic_constant.php',
                    'namespace' => '',
                    'uses' => array(10),
                    'class' => false,
                    'trait' => false,
                    'docblock' => null,
                ),
                array(
                    'file' => TEST_FILES_PATH . 'magic_constant.php',
                    'namespace' => '',
                    'uses' => array(15),
                    'class' => false,
                    'trait' => false,
                    'docblock' => '/* ... */',
                ),
            ),
            '__FUNCTION__' => array(
                array(
                    'file' => TEST_FILES_PATH . 'magic_constant.php',
                    'namespace' => '',
                    'uses' => array(15),
                    'class' => false,
                    'trait' => false,
                    'docblock' => '/* ... */',
                )
            ),
            '__NAMESPACE__' => array(
                array(
                    'file' => TEST_FILES_PATH . 'magic_constant.php',
                    'namespace' => '',
                    'uses' => array(19),
                    'class' => false,
                    'trait' => false,
                    'docblock' => null,
                )
            ),
            '__DIR__' => array(
                array(
                    'file' => TEST_FILES_PATH . 'magic_constant.php',
                    'namespace' => '',
                    'uses' => array(19),
                    'class' => false,
                    'trait' => false,
                    'docblock' => null,
                )
            ),
            '__TRAIT__' => array(
                array(
                    'file' => TEST_FILES_PATH . 'magic_constant.php',
                    'namespace' => '',
                    'uses' => array(22),
                    'class' => false,
                    'trait' => false,
                    'docblock' => null,
                )
            ),
        );

        $this->assertEquals($expected, $this->obj->getConstants(false, 'magic'));
    }

}
