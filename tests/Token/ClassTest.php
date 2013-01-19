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
 * Tests for the PHP_Reflect_Token_CLASS class.
 *
 * @category   PHP
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

    /**
     * Sets up the fixture.
     *
     * Parse source code to find all CLASS and FUNCTION tokens
     *
     * @return void
     */
    protected function setUp()
    {
        $reflect = new PHP_Reflect();
        $tokens  = $reflect->scan(TEST_FILES_PATH . 'source2.php');

        foreach ($tokens as $id => $token) {
            
            if ($token[0] == 'T_CLASS') {
                $this->class = new PHP_Reflect_Token_CLASS(
                    $token[1], $token[2], $id, $tokens
                );
            }
            if ($token[0] == 'T_FUNCTION') {
                $this->function = new PHP_Reflect_Token_FUNCTION(
                    $token[1], $token[2], $id, $tokens
                );
                break;
            }
        }
    }

    /**
     * Tests class with abstract keyword
     * 
     * @covers PHP_Reflect_Token_CLASS::getKeywords
     * @return void
     */
    public function testGetClassKeywords()
    {
        $this->assertEquals(
            'abstract', $this->class->getKeywords()
        );
    }

    /**
     * Tests function with abstract and static keywords
     *
     * @covers PHP_Reflect_Token_FUNCTION::getKeywords
     * @return void
     */
    public function testGetFunctionKeywords()
    {
        $this->assertEquals(
            'abstract,static', $this->function->getKeywords()
        );
    }

    /**
     * Tests function public visibility
     *
     * @covers PHP_Reflect_Token_FUNCTION::getVisibility
     * @return void
     */
    public function testGetFunctionVisibility()
    {
        $this->assertEquals(
            'public', $this->function->getVisibility()
        );
    }

}
