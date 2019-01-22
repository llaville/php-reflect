<?php

declare(strict_types=1);

/**
 * Cache Adapter for Doctrine 2.
 *
 * PHP version 7
 *
 * @category PHP
 * @package  PHP_Reflect
 * @author   Laurent Laville <pear@laurent-laville.org>
 * @license  https://opensource.org/licenses/BSD-3-Clause The 3-Clause BSD License
 * @link     http://php5.laurent-laville.org/reflect/
 * @link     http://www.doctrine-project.org/
 */

namespace Bartlett\Reflect\Plugin\Cache;

use Doctrine\Common\Cache\Cache;

/**
 * Doctrine 2 cache adapter.
 *
 * @category PHP
 * @package  PHP_Reflect
 * @author   Laurent Laville <pear@laurent-laville.org>
 * @license  https://opensource.org/licenses/BSD-3-Clause The 3-Clause BSD License
 * @link     http://php5.laurent-laville.org/reflect/
 * @since    Class available since Release 2.0.0RC1
 */
class DoctrineCacheAdapter implements CacheAdapterInterface
{
    /**
     * Instance of a Doctrine cache
     * @var Cache
     */
    protected $cache;

    /**
     * Doctrine 2 cache adapter constructor.
     *
     * @param Cache $cache Doctrine cache object
     *
     * @return DoctrineCacheAdapter
     */
    public function __construct(Cache $cache)
    {
        $this->cache = $cache;
    }

    /**
     * Checks if an entry exists in the cache.
     *
     * @param string $id      The cache id of the entry to check for.
     * @param array  $options (optional) Array of cache adapter options
     *
     * @return bool TRUE if a cache entry exists for the given cache id, FALSE otherwise.
     */
    public function exists($id, array $options = null)
    {
        return $this->cache->contains($id);
    }

    /**
     * Deletes a cache entry.
     *
     * @param string $id      The cache id of the entry to delete.
     * @param array  $options (optional) Array of cache adapter options
     *
     * @return bool TRUE on success, FALSE on failure
     */
    public function delete($id, array $options = null)
    {
        return $this->cache->delete($id);
    }

    /**
     * Fetches an entry from the cache.
     *
     * @param string $id      The cache id of the entry to fetch.
     * @param array  $options (optional) Array of cache adapter options
     *
     * @return string The cached data or FALSE, if no cache entry exists for the given id.
     */
    public function fetch($id, array $options = null)
    {
        return $this->cache->fetch($id);
    }

    /**
     * Puts data into the cache.
     *
     * @param string $id       The cache id of the new entry to store.
     * @param string $data     The cache entry/data
     * @param mixed  $lifeTime (optional) Sets a specific lifetime for this cache entry
     * @param array  $options  (optional) Array of cache adapter options
     *
     * @return bool TRUE if the entry was successfully stored in the cache, FALSE otherwise.
     */
    public function save($id, $data, $lifeTime = false, array $options = null)
    {
        return $this->cache->save($id, $data, $lifeTime);
    }
}
