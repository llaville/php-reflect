<?php
/**
 * Copyright (c) 2011, Laurent Laville <pear@laurent-laville.org>
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
 * @version  SVN: $Id$
 * @link     http://php5.laurent-laville.org/reflect/
 */

if (!defined('TEST_FILES_PATH')) {
    define(
      'TEST_FILES_PATH',
      dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR .
      '_files' . DIRECTORY_SEPARATOR
    );
}

$dir = dirname(dirname(dirname(__FILE__)));

if (file_exists($dir . DIRECTORY_SEPARATOR . 'PHP/Reflect.php')) {
    // running from repository 
    include_once $dir . DIRECTORY_SEPARATOR . 'PHP/Reflect.php';
} else {
    // package installed
    include_once 'Bartlett/PHP/Reflect.php';
}

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
