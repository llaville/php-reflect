<?php declare(strict_types=1);

/**
 * Cache Adapter interface that should be implemented by all adapters.
 *
 * PHP version 7
 *
 * @category PHP
 * @package  PHP_Reflect
 * @author   Laurent Laville <pear@laurent-laville.org>
 * @license  https://opensource.org/licenses/BSD-3-Clause The 3-Clause BSD License
 */

namespace Bartlett\Reflect\Plugin\Cache;

/**
 * Cache adapters allow Reflect to utilize various frameworks for caching parses results.
 *
 * Reflect doesn't try to reinvent the wheel when it comes to caching.
 * Plenty of other frameworks have excellent solutions in place that you are probably
 * already using in your applications. Reflect uses adapters for caching.
 * The cache plugin requires a cache adapter so that is can store parses results in a cache.
 *
 * @category PHP
 * @package  PHP_Reflect
 * @author   Laurent Laville <pear@laurent-laville.org>
 * @license  https://opensource.org/licenses/BSD-3-Clause The 3-Clause BSD License
 * @since    Interface available since Release 2.0.0RC1
 */
interface CacheAdapterInterface
{
    /**
     * Checks if an entry exists in the cache.
     *
     * @param string $id      The cache id of the entry to check for.
     * @param array  $options (optional) Array of cache adapter options
     *
     * @return bool TRUE if a cache entry exists for the given cache id, FALSE otherwise.
     */
    public function exists($id, array $options = null);

    /**
     * Deletes a cache entry.
     *
     * @param string $id      The cache id of the entry to delete.
     * @param array  $options (optional) Array of cache adapter options
     *
     * @return bool TRUE on success, FALSE on failure
     */
    public function delete($id, array $options = null);

    /**
     * Fetches an entry from the cache.
     *
     * @param string $id      The cache id of the entry to fetch.
     * @param array  $options (optional) Array of cache adapter options
     *
     * @return string The cached data or FALSE, if no cache entry exists for the given id.
     */
    public function fetch($id, array $options = null);

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
    public function save($id, $data, $lifeTime = false, array $options = null);
}
