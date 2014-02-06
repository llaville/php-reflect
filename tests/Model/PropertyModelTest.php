<?php
/**
 * Unit Test Case that covers the Property Model representative.
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
 * @since      Class available since Release 2.0.0RC2
 */

namespace Bartlett\Tests\Reflect\Model;

use Bartlett\Reflect;
use Bartlett\Reflect\ProviderManager;
use Bartlett\Reflect\Provider\SymfonyFinderProvider;
use Bartlett\Reflect\Exception\ModelException;
use Symfony\Component\Finder\Finder;

if (!defined('TEST_FILES_PATH')) {
    define(
        'TEST_FILES_PATH',
        dirname(__DIR__) . DIRECTORY_SEPARATOR .
        '_files' . DIRECTORY_SEPARATOR
    );
}

/**
 * Unit Test Case that covers Bartlett\Reflect\Model\PropertyModel
 *
 * @category   PHP
 * @package    PHP_Reflect
 * @subpackage Tests
 * @author     Laurent Laville <pear@laurent-laville.org>
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    Release: @package_version@
 * @link       http://php5.laurent-laville.org/reflect/
 */
class PropertyModelTest extends \PHPUnit_Framework_TestCase
{
    protected static $classes;

    /**
     * Sets up the shared fixture.
     *
     * @return void
     * @link   http://phpunit.de/manual/current/en/fixtures.html#fixtures.sharing-fixture
     */
    public static function setUpBeforeClass()
    {
        $finder = new Finder();
        $finder->files()
            ->name('properties.php')
            ->in(TEST_FILES_PATH);

        $pm = new ProviderManager;
        $pm->set('test_files', new SymfonyFinderProvider($finder));

        $reflect = new Reflect();
        $reflect->setProviderManager($pm);
        $reflect->parse();

        foreach ($reflect->getPackages() as $package) {
            foreach ($package->getClasses() as $rc) {
                self::$classes[] = $rc;
            }
        }
    }

    /**
     * Tests doc comment accessor.
     *
     *  covers PropertyModel::getDocComment
     * @return void
     */
    public function testDocCommentAccessor()
    {
        $c = 0;  // class SimpleClass
        $p = 5;  // var6 property

        $properties = iterator_to_array(
            self::$classes[$c]->getProperties(),
            false
        );

        $this->assertEquals(
            '/** This is allowed only in PHP 5.3.0 and later. */',
            $properties[$p]->getDocComment(),
            $properties[$p]->getName()
            . ' doc comment does not match.'
        );
    }

    /**
     * Tests name of the property.
     *
     *  covers Property::getName
     * @return void
     */
    public function testNameAccessor()
    {
        $c = 0;  // class SimpleClass
        $p = 1;  // var2 property

        $properties = iterator_to_array(
            self::$classes[$c]->getProperties(),
            false
        );

        $this->assertEquals(
            'var2',
            $properties[$p]->getName(),
            $properties[$p]->getName()
            . ", property #$p name does not match."
        );
    }

    /**
     * Tests if property is defined at run-time or compile-time.
     *
     *  covers PropertyModel::isDefault
     * @return void
     */
    public function testDefaultValue()
    {
        $c = 0;  // class SimpleClass
        $p = 1;  // var2 property

        $properties = iterator_to_array(
            self::$classes[$c]->getProperties(),
            false
        );

        $this->assertTrue(
            $properties[$p]->isDefault(),
            $properties[$p]->getName()
            . ", property #$p was not declared at compile-time."
        );
    }

    /**
     * Tests default value of the property.
     *
     *  covers PropertyModel::getValue
     * @return void
     */
    public function testValueAccessor()
    {
        $c = 0;  // class SimpleClass
        $p = 5;  // var6 property

        $properties = iterator_to_array(
            self::$classes[$c]->getProperties(),
            false
        );

        $expected = 'hello world
';

        $this->assertEquals(
            $expected,
            $properties[$p]->getValue(),
            $properties[$p]->getName()
            . ", property #$p value does not match."
        );
    }

    /**
     * Tests property with private visibility.
     *
     *  covers PropertyModel::isPrivate
     * @return void
     */
    public function testPrivateProperty()
    {
        $c = 0;  // class SimpleClass
        $p = 2;  // var3 property

        $properties = iterator_to_array(
            self::$classes[$c]->getProperties(),
            false
        );

        $this->assertTrue(
            $properties[$p]->isPrivate(),
            $properties[$p]->getName()
            . ' is not a private property.'
        );
    }

    /**
     * Tests property with protected visibility.
     *
     *  covers PropertyModel::isProtected
     * @return void
     */
    public function testProtectedProperty()
    {
        $c = 0;  // class SimpleClass
        $p = 3;  // var4 property

        $properties = iterator_to_array(
            self::$classes[$c]->getProperties(),
            false
        );

        $this->assertTrue(
            $properties[$p]->isProtected(),
            $properties[$p]->getName()
            . ' is not a protected property.'
        );
    }

    /**
     * Tests property with public visibility.
     *
     *  covers PropertyModel::isPublic
     * @return void
     */
    public function testPublicProperty()
    {
        $c = 0;  // class SimpleClass
        $p = 4;  // var5 property

        $properties = iterator_to_array(
            self::$classes[$c]->getProperties(),
            false
        );

        $this->assertTrue(
            $properties[$p]->isPublic(),
            $properties[$p]->getName()
            . ' is not a public property.'
        );
    }

    /**
     * Tests property with static keyword.
     *
     *  covers PropertyModel::isStatic
     * @return void
     */
    public function testStaticProperty()
    {
        $c = 0;  // class SimpleClass
        $p = 2;  // var3 property

        $properties = iterator_to_array(
            self::$classes[$c]->getProperties(),
            false
        );

        $this->assertTrue(
            $properties[$p]->isStatic(),
            $properties[$p]->getName()
            . ' is not a static property.'
        );
    }
}
