<?php
/**
 * Unit Test Case that covers the Function Model representative.
 *
 * PHP version 5
 *
 * @category   PHP
 * @package    PHP_Reflect
 * @subpackage Tests
 * @author     Laurent Laville <pear@laurent-laville.org>
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    GIT: $Id$
 * @link       http://php5.laurent-laville.org/reflect/
 * @since      Class available since Release 2.0.0RC1
 */

namespace Bartlett\Tests\Reflect\Model;

/**
 * Unit Test Case that covers Bartlett\Reflect\Model\FunctionModel
 *
 * @category   PHP
 * @package    PHP_Reflect
 * @subpackage Tests
 * @author     Laurent Laville <pear@laurent-laville.org>
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    Release: @package_version@
 * @link       http://php5.laurent-laville.org/reflect/
 */
class FunctionModelTest extends GenericModelTest
{
    /**
     * Sets up the shared fixture.
     *
     * @return void
     */
    public static function setUpBeforeClass()
    {
        self::$fixture = 'namespaces.php';
        parent::setUpBeforeClass();
    }

    /**
     * Tests starting line number accessor.
     *
     *  covers Bartlett\Reflect\Model\AbstractModel::getStartLine
     * @group  reflection
     * @return void
     */
    public function testStartLineAccessor()
    {
        $f = 3;  // function glob\myprocess

        $this->assertEquals(
            29,
            self::$models[$f]->getStartLine(),
            self::$models[$f]->getName() . ' starting line does not match.'
        );
    }

    /**
     * Tests ending line number accessor.
     *
     *  covers Bartlett\Reflect\Model\AbstractModel::getEndLine
     * @group  reflection
     * @return void
     */
    public function testEndLineAccessor()
    {
        $f = 3;  // function glob\myprocess

        $this->assertEquals(
            32,
            self::$models[$f]->getEndLine(),
            self::$models[$f]->getName() . ' ending line does not match.'
        );
    }

    /**
     * Tests file name accessor.
     *
     *  covers Bartlett\Reflect\Model\AbstractFunctionModel::getFileName
     * @group  reflection
     * @return void
     */
    public function testFileNameAccessor()
    {
        $f = 3;  // function glob\myprocess

        $this->assertEquals(
            self::$fixtures . 'namespaces.php',
            self::$models[$f]->getFileName(),
            self::$models[$f]->getName() . ' file name does not match.'
        );
    }

    /**
     * Tests method name accessor.
     *
     *  covers Bartlett\Reflect\Model\AbstractFunctionModel::getName
     * @group  reflection
     * @return void
     */
    public function testNameAccessor()
    {
        $f = 3;  // function glob\myprocess

        $this->assertEquals(
            'glob\myprocess',
            self::$models[$f]->getName(),
            self::$models[$f]->getName() . ' function name does not match.'
        );
    }

    /**
     * Tests method extension name acessor.
     *
     *  covers Bartlett\Reflect\Model\AbstractModel::getExtensionName
     * @group  reflection
     * @return void
     */
    public function testExtensionNameAccessor()
    {
        $f = 3;  // function glob\myprocess

        $this->assertEquals(
            'user',
            self::$models[$f]->getExtensionName(),
            self::$models[$f]->getName() . ' extension name does not match.'
        );
    }

    /**
     * Tests the namespace name accessor.
     *
     *  covers Bartlett\Reflect\Model\AbstractFunctionModel::getNamespaceName
     * @group  reflection
     * @return void
     */
    public function testNamespaceNameAccessor()
    {
        $f = 4;  // function nemo\nobody

        $this->assertEquals(
            'nemo',
            self::$models[$f]->getNamespaceName(),
            self::$models[$f]->getName() . ' namespace does not match.'
        );
    }

    /**
     * Tests function short name accessor.
     *
     *  covers Bartlett\Reflect\Model\AbstractFunctionModel::getShortName
     * @group  reflection
     * @return void
     */
    public function testShortNameAccessor()
    {
        $f = 2;  // function glob\singleFunction

        $this->assertEquals(
            'singleFunction',
            self::$models[$f]->getShortName(),
            self::$models[$f]->getName() . ' short name does not match.'
        );
    }

    /**
     * Tests the number of parameters that a function defines.
     *
     *  covers Bartlett\Reflect\Model\AbstractFunctionModel::getNumberOfParameters
     * @group  reflection
     * @return void
     */
    public function testNumberOfParametersAccessor()
    {
        $f = 2;  // function glob\singleFunction

        $this->assertEquals(
            3,
            self::$models[$f]->getNumberOfParameters(),
            self::$models[$f]->getName() . ' number of parameters does not match.'
        );
    }

    /**
     * Tests the number of required parameters that a function defines.
     *
     *  covers Bartlett\Reflect\Model\AbstractFunctionModel::getNumberOfRequiredParameters
     * @group  reflection
     * @return void
     */
    public function testNumberOfRequiredParametersAccessor()
    {
        $f = 2;  // function glob\singleFunction

        $this->assertEquals(
            2,
            self::$models[$f]->getNumberOfRequiredParameters(),
            self::$models[$f]->getName() . ' number of required parameters does not match.'
        );
    }

    /**
     * Tests parameters of the class method.
     *
     *  covers Bartlett\Reflect\Model\AbstractFunctionModel::getParameters
     * @group  reflection
     * @return void
     */
    public function testParametersAccessor()
    {
        $f = 2;  // function glob\singleFunction

        $this->assertCount(
            3,
            self::$models[$f]->getParameters(),
            self::$models[$f]->getName() . ' parameters number does not match.'
        );
    }

    /**
     * Tests whether a function is defined in a namespace.
     *
     *  covers Bartlett\Reflect\Model\AbstractFunctionModel::inNamespace
     * @group  reflection
     * @return void
     */
    public function testInNamespace()
    {
        $f = 2;  // function glob\singleFunction

        $this->assertTrue(
            self::$models[$f]->inNamespace(),
            self::$models[$f]->getName() . ' is defined in a namespace.'
        );
    }

    /**
     * Tests whether it's an anonymous function (closure).
     *
     *  covers Bartlett\Reflect\Model\AbstractFunctionModel::isClosure
     * @group  reflection
     * @return void
     */
    public function testAnonymousFunction()
    {
        $f = 2;  // function glob\singleFunction

        $this->assertFalse(
            self::$models[$f]->isClosure(),
            self::$models[$f]->getName() . ' is not an anonymous function.'
        );
    }

    /**
     * Tests whether it's a closure in a user namespace.
     *
     *  covers Bartlett\Reflect\Model\AbstractFunctionModel::isClosure
     * @group  reflection
     * @return void
     */
    public function testClosureInNamespace()
    {
        $f = 5;  // closure in nemo namespace

        $this->assertTrue(
            self::$models[$f]->isClosure(),
            self::$models[$f]->getName() . ' is a closure.'
        );
    }

    /**
     * Tests whether it's an internal function.
     *
     *  covers Bartlett\Reflect\Model\AbstractFunctionModel::isInternal
     * @group  reflection
     * @return void
     */
    public function testInternalFunction()
    {
        $f = 2;  // function glob\singleFunction

        $this->assertFalse(
            self::$models[$f]->isInternal(),
            self::$models[$f]->getName() . ' is a user-defined function.'
        );
    }

    /**
     * Tests string representation of the FunctionModel object
     *
     *  covers Bartlett\Reflect\Model\FunctionModel::__toString
     * @group  reflection
     * @return void
     */
    public function testToString()
    {
        $f = 2;  // function glob\singleFunction

        $expected = <<<EOS
Function [ <user> function glob\\singleFunction ] {
  @@ %filename% 25 - 27

  - Parameters [3] {
    Parameter #0 [ <required> array \$someparam ]
    Parameter #1 [ <required> glob\stdClass \$somethingelse ]
    Parameter #2 [ <optional> \$lastone = NULL ]
  }
}

EOS;
        $this->expectOutputString(
            str_replace('%filename%', self::$fixture, $expected)
        );

        print(
            self::$models[$f]->__toString()
        );
    }
}
