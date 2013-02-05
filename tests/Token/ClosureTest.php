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
 * Tests for the PHP_Reflect_Token_FUNCTION class.
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
class PHP_Reflect_Token_ClosureTest extends PHPUnit_Framework_TestCase
{
    protected $functions;

    /**
     * Sets up the fixture.
     *
     * Parse source code to find all FUNCTION tokens
     *
     * @return void
     */
    protected function setUp()
    {
        $reflect = new PHP_Reflect();
        $tokens  = $reflect->scan(TEST_FILES_PATH . 'closure.php');

        foreach ($tokens as $id => $token) {
            if ($token[0] == 'T_FUNCTION') {
                $this->functions[] = new PHP_Reflect_Token_FUNCTION(
                    $token[1], $token[2], $id, $tokens
                );
            }
        }
    }

    /**
     * Test closures arguments
     *
     * @covers PHP_Reflect_Token_FUNCTION::getArguments
     * @return void
     */
    public function testGetArguments()
    {
        $this->assertEquals(
            array(
                array('typeHint' => 'Exception', 'name' => '$foo'),
                array('name' => '$bar'),
            ),
            $this->functions[0]->getArguments()
        );
        $this->assertEquals(
            array(
                array('name' => '$foo'),
                array('name' => '$bar'),
                array('name' => '$baz'),
            ),
            $this->functions[1]->getArguments()
        );
        $this->assertEquals(array(), $this->functions[2]->getArguments());
        $this->assertEquals(array(), $this->functions[3]->getArguments());
    }

    /**
     * Test closures names
     *
     * @covers PHP_Reflect_Token_FUNCTION::getName
     * @return void
     */
    public function testGetName()
    {
        $this->assertEquals('anonymous function', $this->functions[0]->getName());
        $this->assertEquals('anonymous function', $this->functions[1]->getName());
        $this->assertEquals('anonymous function', $this->functions[2]->getName());
        $this->assertEquals('anonymous function', $this->functions[3]->getName());
    }

    /**
     * Test closures location in source file
     *
     * @covers PHP_Reflect_Token_FUNCTION::getLine
     * @return void
     */
    public function testGetLine()
    {
        $this->assertEquals(2, $this->functions[0]->getLine());
        $this->assertEquals(3, $this->functions[1]->getLine());
        $this->assertEquals(4, $this->functions[2]->getLine());
        $this->assertEquals(5, $this->functions[3]->getLine());
    }

    /**
     * Test closures location in source file
     *
     * @covers PHP_Reflect_Token_FUNCTION::getEndLine
     * @return void
     */
    public function testGetEndLine()
    {
        $this->assertEquals(2, $this->functions[0]->getEndLine());
        $this->assertEquals(3, $this->functions[1]->getEndLine());
        $this->assertEquals(4, $this->functions[2]->getEndLine());
        $this->assertEquals(5, $this->functions[3]->getEndLine());
    }

}
