<?php declare(strict_types=1);

/**
 * Common interface to all storage.
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
 * Interface used to cache FILE parses
 *
 * @category PHP
 * @package  PHP_Reflect
 * @author   Laurent Laville <pear@laurent-laville.org>
 * @license  https://opensource.org/licenses/BSD-3-Clause The 3-Clause BSD License
 * @since    Interface available since Release 2.0.0RC1
 */
interface CacheStorageInterface
{
    /**
     * Checks if cache exists for a request.
     *
     * @param array $request Request data to check for
     *
     * @return bool TRUE if a response exists in cache, FALSE otherwise
     */
    public function exists(array $request): bool;

    /**
     * Get a response from the cache for a request.
     *
     * @param array $request Request data to read from cache
     *
     * @return mixed
     */
    public function fetch(array $request);

    /**
     * Cache a FILE parse.
     *
     * @param array $request Request being cached
     *
     * @return void
     */
    public function cache(array $request): void;

    /**
     * Deletes cache entries that match a request.
     *
     * @param array $request Request to delete from cache
     *
     * @return int number of entries deleted in cache
     */
    public function delete(array $request): int;

    /**
     * Purge all cache entries for a given data source.
     *
     * @param string $source Name that identify a data source
     *                       (see ProviderManager)
     *
     * @return int
     */
    public function purge(string $source): int;
}
