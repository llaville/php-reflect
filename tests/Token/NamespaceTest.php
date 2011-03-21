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
    protected $namespaces;

    protected function setUp()
    {
        $reflect = new PHP_Reflect();
        $tokens  = $reflect->scan(TEST_FILES_PATH . 'source5.php');

        foreach ($tokens as $id => $token) {
            if (($token[0] == 'T_STRING' && $token[1] == 'namespace')
                || ($token[0] == 'T_NAMESPACE')
            ) {
                $this->ns[] = new PHP_Reflect_Token_NAMESPACE($token[1], $token[2], $id, $tokens);
            }
        }

        $tokens  = $reflect->scan(TEST_FILES_PATH . 'source6.php');

        foreach ($tokens as $id => $token) {
            if (($token[0] == 'T_STRING' && $token[1] == 'namespace')
                || ($token[0] == 'T_NAMESPACE')
            ) {
                $this->ns[] = new PHP_Reflect_Token_NAMESPACE($token[1], $token[2], $id, $tokens);
            }
        }

        $this->namespaces = $reflect->getNamespaces();
    }

    /**
     * @covers PHP_Reflect_Token_NAMESPACE::getName
     */
    public function testGetName()
    {
        if (version_compare(PHP_VERSION, '5.3.0', '<')) {
            $this->assertEquals(
                'A', $this->ns[0]->getName()
            );
        } else {
            $this->assertEquals(
                'A\B', $this->ns[0]->getName()
            );
        }
    }

    /**
     * Defining multiple namespaces in the same file
     *
     * @link   http://www.php.net/manual/en/language.namespaces.definitionmultiple.php
     * @covers PHP_Reflect_Token_NAMESPACE::getName
     */
    public function testGetMultipleNamespaceInSameFile()
    {
        if (version_compare(PHP_VERSION, '5.3.0', '<')) {
            $this->markTestSkipped(
                'NAMESPACE is fully supported only with PHP 5.3.0 or greater'
            );
        } else {
            $this->assertEquals(
                'MyProject', $this->ns[1]->getName()
            );
            $this->assertEquals(
                'AnotherProject', $this->ns[2]->getName()
            );

            $expected = array(
                'A\B' => array(
                    'startLine' => 2,
                    'endLine'   => 5,
                    'file'      => TEST_FILES_PATH . 'source5.php',
                    'docblock'  => null
                ),
                'MyProject' => array(
                    'startLine' => 2,
                    'endLine'   => 9,
                    'file'      => TEST_FILES_PATH . 'source6.php',
                    'docblock'  => null
                ),
                'AnotherProject' => array(
                    'startLine' => 11,
                    'endLine'   => 16,
                    'file'      => TEST_FILES_PATH . 'source6.php',
                    'docblock'  => null
                ),
            );

            $this->assertSame(
                $expected, $this->namespaces
            );
        }
    }
}
