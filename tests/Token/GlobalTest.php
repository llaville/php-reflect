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

/**
 * Tests for the PHP_Reflect_Token_VARIABLE class
 *
 * @category   PHP
 * @package    PHP_Reflect
 * @subpackage Tests
 * @author     Laurent Laville <pear@laurent-laville.org>
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    Release: @package_version@
 * @link       https://github.com/llaville/php-reflect
 * @since      Class available since Release 0.7.0
 */
class PHP_Reflect_Token_GlobalTest extends PHPUnit_Framework_TestCase
{
    protected $reflect;
    protected $filename;

    /**
     * Sets up the fixture.
     *
     * @return void
     */
    protected function setUp()
    {
        $this->reflect = new PHP_Reflect();
        $this->reflect->scan(
            $this->filename = TEST_FILES_PATH . 'source7.php'
        );
    }

    /**
     * Tests GLOBALS variables
     * 
     * @covers PHP_Reflect_Token_Globals::getName
     * @covers PHP_Reflect_Token_Globals::getType
     * @return void
     */
    public function testGetGlobals()
    {
        $this->assertSame(
            array(
                '', '$init', '$key', '$log', '_PEAR_PACKAGEUPDATE_ERRORS', 'vname'
            ),
            array_keys($this->reflect->getGlobals())
        );
    }

    /**
     * Tests GLOBALS variables categorized
     *
     * @covers PHP_Reflect_Token_Globals::getName
     * @covers PHP_Reflect_Token_Globals::getType
     * @return void
     */
    public function testGetGlobalsCategorized()
    {
        $this->assertSame(
            array(
                '$GLOBALS' => array(
                    '_PEAR_PACKAGEUPDATE_ERRORS' => array(
                        'startLine' => 4, 'endLine' => 4, 
                        'file' => $this->filename,
                        'namespace' => '',
                        'docblock' => null
                    )
                ),
                '$HTTP_COOKIE_VARS' => array(),
                '$HTTP_ENV_VARS' => array(),
                '$HTTP_GET_VARS' => array(),
                '$HTTP_POST_FILES' => array(),
                '$HTTP_POST_VARS' => array(
                    '' => array(
                        'startLine' => 10, 'endLine' => 10, 
                        'file' => $this->filename,
                        'namespace' => '',
                        'docblock' => null
                    ),
                    'vname' => array(
                        'startLine' => 12, 'endLine' => 13, 
                        'file' => $this->filename,
                        'namespace' => '',
                        'docblock' => null
                    ),
                    '$key' => array(
                        'startLine' => 14, 'endLine' => 14, 
                        'file' => $this->filename,
                        'namespace' => '',
                        'docblock' => null
                    )
                ),
                '$HTTP_SERVER_VARS' => array(),
                '$HTTP_SESSION_VARS' => array(),
                '$_COOKIE' => array(),
                '$_ENV' => array(),
                '$_GET' => array(),
                '$_POST' => array(
                    'vname' => array(
                        'startLine' => 16, 'endLine' => 16, 
                        'file' => $this->filename,
                        'namespace' => '',
                        'docblock' => null
                    )
                ),
                '$_SERVER' => array(),
                '$_SESSION' => array(),
                'global' => array(
                    '$init' => array(
                        'startLine' => 21, 'endLine' => 21, 
                        'file' => $this->filename,
                        'namespace' => '',
                        'docblock' => "// init Bar function\n"
                    ),
                    '$log' => array(
                        'startLine' => 8, 'endLine' => 8, 
                        'file' => $this->filename,
                        'namespace' => '',
                        'docblock' => null
                    )
                ),
            ),
            $this->reflect->getGlobals(true)
        );
    }

    /**
     * Tests GLOBALS variables
     * 
     * @covers PHP_Reflect_Token_Globals::getName
     * @covers PHP_Reflect_Token_Globals::getType
     * @return void
     */
    public function testGetGlobalsCategory()
    {
        $this->assertSame(
            array(
                '$init' => array(
                    'startLine' => 21, 'endLine' => 21, 'file' => $this->filename,
                    'namespace' => '',
                    'docblock' => "// init Bar function\n"
                ),
                '$log' => array(
                    'startLine' => 8, 'endLine' => 8, 'file' => $this->filename,
                    'namespace' => '',
                    'docblock' => null
                )
            ),
            $this->reflect->getGlobals(true, 'global')
        );
    }

}
