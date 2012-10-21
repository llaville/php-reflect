<?php
/**
 * Copyright (c) 2011-2012, Laurent Laville <pear@laurent-laville.org>
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
 * Tests for the PHP_Reflect_Token_STRING class.
 *
 * @category   PHP
 * @package    PHP_Reflect
 * @subpackage Tests
 * @author     Laurent Laville <pear@laurent-laville.org>
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    Release: @package_version@
 * @link       https://github.com/llaville/php-reflect
 * @since      Class available since Release 1.2.0
 */
class PHP_Reflect_Token_StringTest extends PHPUnit_Framework_TestCase
{
    protected $reflect;
    protected $functions;

    /**
     * Sets up the fixture.
     *
     * @return void
     */
    protected function setUp()
    {
        $opts = array(
            'containers' => array(
                'string' => 'functions',
            ),
            'properties' => array(
                'string' => array(
                    'arguments'
                ),
            ),
        );

        $this->reflect = new PHP_Reflect($opts);
        $this->reflect->connect(
            'T_STRING',
            'PHP_Reflect_Token_STRING',
            array($this->reflect, 'parseToken')
        );

        $this->functions = array();
    }

    /**
     * Tests functions arguments
     * 
     * @covers PHP_Reflect_Token_STRING::getArguments
     * @return void
     */
    public function testGetArgumentsWithNestedFunctionCalls()
    {
        $tokens = $this->reflect->scan(TEST_FILES_PATH . 'source8-01.php');

        foreach ($tokens as $id => $token) {
            if ($token[0] == 'T_STRING') {
                $t_string = new PHP_Reflect_Token_STRING(
                    $token[1], $token[2], $id, $tokens
                );

                if ('jdtojewish'  === $t_string->getName()) {
                    $this->functions[] = $t_string;
                }
            }
        }

        $this->assertEquals(
            array(
                array('typeHint' => 'mixed', 'name' => 'gregoriantojd'),
                array('defaultValue' => 'false')
            ),
            $this->functions[0]->getArguments()
        );
    }

    /**
     * Tests functions arguments
     * 
     * @covers PHP_Reflect_Token_STRING::getArguments
     * @link http://www.php.net/manual/en/language.oop5.typehinting.php
     * @return void
     */
    public function testGetArgumentsWithTypeHintingNullObject()
    {
        $tokens = $this->reflect->scan(TEST_FILES_PATH . 'source8-02.php');

        foreach ($tokens as $id => $token) {
            if ($token[0] == 'T_STRING') {
                $t_string = new PHP_Reflect_Token_STRING(
                    $token[1], $token[2], $id, $tokens
                );

                if ('test'  === $t_string->getName()) {
                    $this->functions[] = $t_string;
                }
            }
        }

        $this->assertEquals(
            array(
                array('defaultValue' => 'NULL')
            ),
            $this->functions[0]->getArguments()
        );
    }

    /**
     * Tests functions arguments
     * 
     * @covers PHP_Reflect_Token_STRING::getArguments
     * @link http://www.php.net/manual/en/language.oop5.typehinting.php
     * @return void
     */
    public function testGetArgumentsWithTypeHintingNotNullObject()
    {
        $tokens = $this->reflect->scan(TEST_FILES_PATH . 'source8-03.php');

        foreach ($tokens as $id => $token) {
            if ($token[0] == 'T_STRING') {
                $t_string = new PHP_Reflect_Token_STRING(
                    $token[1], $token[2], $id, $tokens
                );

                if ('test'  === $t_string->getName()) {
                    $this->functions[] = $t_string;
                }
            }
        }

        $this->assertEquals(
            array(
                array('typeHint' => 'object', 'defaultValue' => 'stdClass'),
            ),
            $this->functions[0]->getArguments()
        );
    }

    /**
     * Tests functions arguments
     * 
     * @covers PHP_Reflect_Token_STRING::getArguments
     * @return void
     */
    public function testGetArgumentsWithDefaultValue()
    {
        $tokens = $this->reflect->scan(TEST_FILES_PATH . 'source8-04.php');

        foreach ($tokens as $id => $token) {
            if ($token[0] == 'T_STRING') {
                $t_string = new PHP_Reflect_Token_STRING(
                    $token[1], $token[2], $id, $tokens
                );

                if ('foo'  === $t_string->getName()) {
                    $this->functions[] = $t_string;
                }
            }
        }

        $this->assertEquals(
            array(
                array('name' => '$e'),
                array('defaultValue' => "array('debug')"),
            ),
            $this->functions[0]->getArguments()
        );
    }

}
