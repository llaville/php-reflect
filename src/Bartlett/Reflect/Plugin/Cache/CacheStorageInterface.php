<?php

namespace Bartlett\Reflect\Plugin\Cache;

/**
 * Interface used to cache FILE parses
 */
interface CacheStorageInterface
{
    /**
     * Get a response from the cache for a request
     *
     * @param array $request Request data to read from cache
     *
     * @return mixed
     */
    public function fetch($request);

    /**
     * Cache a FILE parse
     *
     * @param array $request Request being cached
     */
    public function cache($request);

    /**
     * Deletes cache entries that match a request
     *
     * @param array $request Request to delete from cache
     */
    public function delete($request);

    /**
     * Purge all cache entries for a given data source
     *
     * @param string $dataSource
     */
    public function purge($dataSource);
}
