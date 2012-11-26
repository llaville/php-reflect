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
 * Tests for the PHP_Reflect_Token_NAMESPACE class.
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
class PHP_Reflect_Token_NamespaceTest extends PHPUnit_Framework_TestCase
{
    protected $ns;
    protected $namespaces;
    protected $namespacesWithoutImportDefault;
    protected $namespacesWithoutImport;
    protected $namespacesOnlyImport;
    protected $namespacesUserAliasingImporting;
    protected $nsWarning;
    protected $classes;
    protected $functions;

    /**
     * Sets up the fixture.
     *
     * Parse source code to find all CLASS and NAMESPACE tokens
     *
     * @return void
     */
    protected function setUp()
    {
        $reflect = new PHP_Reflect();
        // 1st file
        $tokens  = $reflect->scan(TEST_FILES_PATH . 'source5.php');

        foreach ($tokens as $id => $token) {
            if (($token[0] == 'T_STRING' && $token[1] == 'namespace')
                || ($token[0] == 'T_NAMESPACE')
            ) {
                $this->ns[] = new PHP_Reflect_Token_NAMESPACE(
                    $token[1], $token[2], $id, $tokens
                );
            }

            if ($token[0] == 'T_CLASS') {
                $this->classes[] = new PHP_Reflect_Token_CLASS(
                    $token[1], $token[2], $id, $tokens
                );
            }
        }
        $this->functions = $reflect->getFunctions();

        // 2nd file
        $tokens = $reflect->scan(TEST_FILES_PATH . 'source6.php');

        foreach ($tokens as $id => $token) {
            if (($token[0] == 'T_STRING' && $token[1] == 'namespace')
                || ($token[0] == 'T_NAMESPACE')
            ) {
                $this->ns[] = new PHP_Reflect_Token_NAMESPACE(
                    $token[1], $token[2], $id, $tokens
                );
            }
        }
        $this->namespaces = $reflect->getNamespaces();

        $reflect = new PHP_Reflect();
        // 3rd file
        $reflect->scan(TEST_FILES_PATH . 'source10.php');

        $this->namespacesWithoutImportDefault
            = $reflect->getNamespaces();
        $this->namespacesWithoutImport
            = $reflect->getNamespaces(PHP_Reflect::NAMESPACES_WITHOUT_IMPORT);
        $this->namespacesOnlyImport
            = $reflect->getNamespaces(PHP_Reflect::NAMESPACES_ONLY_IMPORT);
        $this->namespacesUserAliasingImporting
            = $reflect->getNamespaces(PHP_Reflect::NAMESPACES_ALL);

        $reflect = new PHP_Reflect();
        // 4th file
        $reflect->scan(TEST_FILES_PATH . 'source11.php');

        $this->nsWarning = $reflect->isNamespaceWarning();
    }

    /**
     * Tests namespaces naming
     *
     * @covers PHP_Reflect_Token_NAMESPACE::getName
     * @return void
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
     * @return void
     */
    public function testGetMultipleNamespaceInSameFile()
    {
        if (version_compare(PHP_VERSION, '5.3.0', '<')) {
            $this->markTestSkipped(
                'NAMESPACE is fully supported only with PHP 5.3.0 or greater'
            );
        } else {
            $this->assertEquals(
                'MyProject', $this->ns[2]->getName()
            );
            $this->assertEquals(
                'AnotherProject', $this->ns[3]->getName()
            );

            $expected = array(
                'A\B' => array(
                    'startLine' => 2,
                    'endLine'   => 5,
                    'file'      => TEST_FILES_PATH . 'source5.php',
                    'docblock'  => null,
                    'alias'     => 'B',
                    'import'    => false
                ),
                'Other\Space' => array(
                    'startLine' => 7,
                    'endLine'   => 9,
                    'file'      => TEST_FILES_PATH . 'source5.php',
                    'docblock'  => null,
                    'alias'     => 'Space',
                    'import'    => false
                ),
                'MyProject' => array(
                    'startLine' => 2,
                    'endLine'   => 9,
                    'file'      => TEST_FILES_PATH . 'source6.php',
                    'docblock'  => null,
                    'alias'     => 'MyProject',
                    'import'    => false
                ),
                'AnotherProject' => array(
                    'startLine' => 11,
                    'endLine'   => 16,
                    'file'      => TEST_FILES_PATH . 'source6.php',
                    'docblock'  => null,
                    'alias'     => 'AnotherProject',
                    'import'    => false
                ),
            );

            $this->assertSame(
                $expected, $this->namespaces
            );
        }
    }

    /**
     * Retrieves functions declared on a user namespace
     *
     * @covers PHP_Reflect_Token_NAMESPACE::getName
     * @return void
     */
    public function testGetFunctionsFromUserNamespace()
    {
        if (version_compare(PHP_VERSION, '5.3.0', '<')) {
            $this->markTestSkipped(
                'NAMESPACE is fully supported only with PHP 5.3.0 or greater'
            );
        } else {
            $expected = array(
                'Bar' => array(
                    'startLine' => 5,
                    'endLine'   => 5,
                    'file'      => TEST_FILES_PATH . 'source5.php',
                    'namespace' => 'A\B',
                    'docblock'  => null,
                    'keywords'  => '',
                    'signature' => 'Bar()',
                    'arguments' => array(),
                    'ccn'       => 1
                ),
            );

            $this->assertSame(
                $expected, $this->functions['A\B']
            );
        }
    }

    /**
     * Retrieves functions declared on global namespace
     *
     * @covers PHP_Reflect_Token_NAMESPACE::getName
     * @return void
     */
    public function testGetFunctionsFromGlobalNamespace()
    {
        if (version_compare(PHP_VERSION, '5.3.0', '<')) {
            $this->markTestSkipped(
                'NAMESPACE is fully supported only with PHP 5.3.0 or greater'
            );
        } else {
            $this->assertNotContains('\\', array_keys($this->functions));
        }
    }

    /**
     * Retrieves namespace of class or interface declaration
     *
     * @covers PHP_Reflect_Token_INTERFACE::getPackage
     * @return void
     */
    public function testGetPackageNamespaceWhenExtendingFromNamespaceClass()
    {
        if (version_compare(PHP_VERSION, '5.3.0', '<')) {
            $this->markTestSkipped(
                'NAMESPACE is fully supported only with PHP 5.3.0 or greater'
            );
        } else {
            $expected = array(
                'namespace'   => 'Other\Space',
                'fullPackage' => '',
                'category'    => '',
                'package'     => '',
                'subpackage'  => ''
            );

            $this->assertSame(
                $expected, $this->classes[1]->getPackage()
            );
        }
    }

    /**
     * Retrieves only user namespaces without imports (see use keyword)
     *
     * @link   http://www.php.net/manual/en/language.namespaces.importing.php
     * @return void
     */
    public function testGetOnlyUserNamespacesWithoutImports()
    {
        if (version_compare(PHP_VERSION, '5.3.0', '<')) {
            $this->markTestSkipped(
                'NAMESPACE is fully supported only with PHP 5.3.0 or greater'
            );
        } else {
            $expected = array(
                'Doctrine\Common\Cache' => array(
                    'startLine' => 2,
                    'endLine'   => 5,
                    'file'      => TEST_FILES_PATH . 'source10.php',
                    'docblock'  => null,
                    'alias'     => 'Cache',
                    'import'    => false,
                ),
            );

            $this->assertEquals(
                $expected,
                $this->namespacesWithoutImportDefault
            );

            $this->assertEquals(
                $expected,
                $this->namespacesWithoutImport
            );
        }
    }

    /**
     * Retrieves only imported namespaces (see use keyword)
     *
     * @link   http://www.php.net/manual/en/language.namespaces.importing.php
     * @return void
     */
    public function testGetOnlyImportNamespaces()
    {
        if (version_compare(PHP_VERSION, '5.3.0', '<')) {
            $this->markTestSkipped(
                'NAMESPACE is fully supported only with PHP 5.3.0 or greater'
            );
        } else {
            $expected = array(
                '\Memcache' => array(
                    'startLine' => 4,
                    'endLine'   => 4,
                    'file'      => TEST_FILES_PATH . 'source10.php',
                    'docblock'  => null,
                    'alias'     => 'Memcache',
                    'import'    => true,
                ),
                'My\Full\Classname' => array(
                    'startLine' => 5,
                    'endLine'   => 5,
                    'file'      => TEST_FILES_PATH . 'source10.php',
                    'docblock'  => null,
                    'alias'     => 'Another',
                    'import'    => true,
                ),
            );

            $this->assertEquals(
                $expected,
                $this->namespacesOnlyImport
            );
        }
    }

    /**
     * Retrieves only imported namespaces (see use keyword)
     *
     * @link   http://www.php.net/manual/en/language.namespaces.importing.php
     * @return void
     */
    public function testGetUserAndImportNamespaces()
    {
        if (version_compare(PHP_VERSION, '5.3.0', '<')) {
            $this->markTestSkipped(
                'NAMESPACE is fully supported only with PHP 5.3.0 or greater'
            );
        } else {
            $expected = array(
                'Doctrine\Common\Cache' => array(
                    'startLine' => 2,
                    'endLine'   => 5,
                    'file'      => TEST_FILES_PATH . 'source10.php',
                    'docblock'  => null,
                    'alias'     => 'Cache',
                    'import'    => false,
                ),
                '\Memcache' => array(
                    'startLine' => 4,
                    'endLine'   => 4,
                    'file'      => TEST_FILES_PATH . 'source10.php',
                    'docblock'  => null,
                    'alias'     => 'Memcache',
                    'import'    => true,
                ),
                'My\Full\Classname' => array(
                    'startLine' => 5,
                    'endLine'   => 5,
                    'file'      => TEST_FILES_PATH . 'source10.php',
                    'docblock'  => null,
                    'alias'     => 'Another',
                    'import'    => true,
                ),
            );

            $this->assertEquals(
                $expected,
                $this->namespacesUserAliasingImporting
            );
        }
    }

    /**
     * Tests namespaces uses that should raise warning with PHP 5.2
     *
     * @return void
     */
    public function testIsNamespaceWarning()
    {
        if (version_compare(PHP_VERSION, '5.3.0', '>=')) {
            $this->markTestSkipped(
                'NAMESPACE is fully supported with PHP 5.3.0 or greater'
            );
        } else {
            $this->assertTrue($this->nsWarning);
        }
    }

}
