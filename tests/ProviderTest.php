<?php

namespace Bartlett\Tests\Reflect;

use Bartlett\Reflect\ProviderManager;
use Bartlett\Reflect\Provider\SymfonyFinderProvider;
use Symfony\Component\Finder\Finder;

if (!defined('TEST_FILES_PATH')) {
    define(
        'TEST_FILES_PATH',
        __DIR__ . DIRECTORY_SEPARATOR .
        '_files' . DIRECTORY_SEPARATOR
    );
}

/**
 * Unit Test Case that covers Bartlett\Reflect\ProviderManager
 *
 * @author Laurent Laville
 */

class ProviderTest extends \PHPUnit_Framework_TestCase
{
    protected static $finder;

    /**
     * Sets up the shared fixture.
     *
     * @return void
     * @link   http://phpunit.de/manual/current/en/fixtures.html#fixtures.sharing-fixture
     */
    public static function setUpBeforeClass()
    {
        self::$finder = new Finder();
        self::$finder->files()
            ->name('c*.php')
            ->in(TEST_FILES_PATH);
    }

    /**
     *  covers Bartlett\Reflect\ProviderManager::has
     *
     * @return void
     */
    public function testHasProvider()
    {
        $alias = 'SymfonyFinder';

        $pm = new ProviderManager;
        $pm->set($alias, new SymfonyFinderProvider(self::$finder));

        $this->assertTrue(
            $pm->has($alias),
            "ProviderManager has no $alias provider registered."
        );
    }

    /**
     *  covers Bartlett\Reflect\ProviderManager::get
     *
     * @return void
     */
    public function testUnknownProvider()
    {
        $pm = new ProviderManager;
        $pm->set('SymfonyFinder', new SymfonyFinderProvider(self::$finder));

        try {
            $alias = 'SF2Finder';
            $pm->get($alias);

        } catch (\OutOfRangeException $expected) {
            $this->assertEquals(
                'There is no "' . $alias . '" provider registered.',
                $expected->getMessage(),
                'Expected exception message does not match'
            );
            return;
        }
        $this->fail(
            'An expected \OutOfRangeException exception has not been raised.'
        );
    }

    /**
     *  covers Bartlett\Reflect\ProviderManager::get
     *
     * @return void
     */
    public function testRetrieveProvider()
    {
        $alias      = 'SF2Finder';
        $rmProvider = new SymfonyFinderProvider(self::$finder);

        $pm = new ProviderManager;
        $pm->set($alias, $rmProvider);

        try {
            $provider = $pm->get($alias);

        } catch (\OutOfRangeException $expected) {
            $this->fail(
                'An unexpected \OutOfRangeException exception has been raised.'
            );
        }

        $this->assertSame(
            $rmProvider,
            $provider,
            'Providers does not match.'
        );
    }

    /**
     *  covers Bartlett\Reflect\ProviderManager::all
     *
     * @return void
     */
    public function testRetrieveAllProviders()
    {
        $providers = array(
            'SF2Finder' => new SymfonyFinderProvider(self::$finder)
        );
        $pm = new ProviderManager;

        foreach ($providers as $alias => $provider) {
            $pm->set($alias, $provider);
        }

        $this->assertEquals(
            $providers,
            $pm->all(),
            'Providers list does not match.'
        );
    }

    /**
     *  covers Bartlett\Reflect\ProviderManager::set
     *
     * @return void
     */
    public function testInvalidAliasProvider()
    {
        $pm = new ProviderManager;

        try {
            $alias = '???';
            $pm->set($alias, new SymfonyFinderProvider(self::$finder));

        } catch (\InvalidArgumentException $expected) {
            $this->assertEquals(
                'The provider name "' . $alias . '" is invalid.',
                $expected->getMessage(),
                'Expected exception message does not match'
            );
            return;
        }
        $this->fail(
            'An expected \InvalidArgumentException exception has not been raised.'
        );
    }

    /**
     *  covers Bartlett\Reflect\ProviderManager::remove
     *
     * @return void
     */
    public function testRemoveProvider()
    {
        $pm = new ProviderManager;
        $pm->set('SF2Finder', new SymfonyFinderProvider(self::$finder));

        try {
            $pm->remove('SF2Finder');

        } catch (\Exception $e) {
            $this->fail(
                'An unexpected exception has been raised.'
            );
        }

        $this->assertCount(
            0,
            $pm,
            'Providers registered count does not match.'
        );
    }

    /**
     *  covers Bartlett\Reflect\ProviderManager::remove
     *
     * @return void
     */
    public function testRemoveUnknownProvider()
    {
        $pm = new ProviderManager;

        try {
            $alias = 'SF2';
            $pm->remove($alias, new SymfonyFinderProvider(self::$finder));

        } catch (\OutOfRangeException $expected) {
            $this->assertEquals(
                'There is no "' . $alias . '" provider registered.',
                $expected->getMessage(),
                'Expected exception message does not match'
            );
            return;
        }
        $this->fail(
            'An expected \OutOfRangeException exception has not been raised.'
        );
    }

    /**
     *  covers Bartlett\Reflect\ProviderManager::clear
     *
     * @return void
     */
    public function testClearProviders()
    {
        $pm = new ProviderManager;
        $pm->set('SF2Finder', new SymfonyFinderProvider(self::$finder));

        $pm->clear();

        $this->assertCount(
            0,
            $pm,
            'Providers registered count does not match.'
        );
    }
}
