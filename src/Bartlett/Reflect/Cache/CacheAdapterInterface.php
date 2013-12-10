<?php

namespace Bartlett\Reflect\Cache;

/**
 * Cache adapters allow Reflect to utilize various frameworks for caching parses results.
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
