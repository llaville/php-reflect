<?php
/**
 * Copyright (c) 2012, Laurent Laville <pear@laurent-laville.org>
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
 * Tests for the PHP_Reflect_Token_TRAIT class.
 *
 * @package    PHP_Reflect
 * @subpackage Tests
 * @author     Laurent Laville <pear@laurent-laville.org>
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    Release: @package_version@
 * @link       https://github.com/llaville/php-reflect
 * @since      Class available since Release 1.2.0
 */
class PHP_Reflect_Token_TraitTest extends PHPUnit_Framework_TestCase
{
    protected $class;
    protected $traits;

    protected function setUp()
    {
        $reflect = new PHP_Reflect();
        $tokens  = $reflect->scan(TEST_FILES_PATH . 'source9.php');
        $i       = 0;

        foreach ($tokens as $id => $token) {
            if ($token[0] == 'T_CLASS') {
                $this->class = new PHP_Reflect_Token_CLASS(
                    $token[1], $token[2], $id, $tokens
                );
            }
            elseif ($token[0] == 'T_TRAIT') {
                $this->traits[$i] = new PHP_Reflect_Token_TRAIT(
                    $token[1], $token[2], $id, $tokens
                );
                $i++;
            }
        }
    }

    /**
     * @covers PHP_Reflect_Token_TRAIT::getName
     */
    public function testGetName()
    {
        if (version_compare(PHP_VERSION, '5.4.0RC1', '<')) {
            $this->markTestSkipped();
        }
        
        $this->assertEquals(
            'T1', $this->traits[0]->getName()
        );
    }

    /**
     * @covers PHP_Reflect_Token_TRAIT::getParent
     */
    public function testGetParentNotExists()
    {
        if (version_compare(PHP_VERSION, '5.4.0RC1', '<')) {
            $this->markTestSkipped();
        }
        
        $this->assertFalse(
            $this->traits[0]->getParent()
        );
    }

    /**
     * @covers PHP_Reflect_Token_TRAIT::hasParent
     */
    public function testHasParentNotExists()
    {
        if (version_compare(PHP_VERSION, '5.4.0RC1', '<')) {
            $this->markTestSkipped();
        }
        
        $this->assertFalse(
            $this->traits[0]->hasParent()
        );
    }

}
