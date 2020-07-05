<?php declare(strict_types=1);

/**
 * Default cache storage implementation.
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
 * Default cache storage implementation.
 *
 * @category PHP
 * @package  PHP_Reflect
 * @author   Laurent Laville <pear@laurent-laville.org>
 * @license  https://opensource.org/licenses/BSD-3-Clause The 3-Clause BSD License
 * @since    Class available since Release 2.0.0RC1
 */
class DefaultCacheStorage implements CacheStorageInterface
{
    /**
     * Default cache TTL (Time To Live)
     * @var int
     */
    protected $maxlifetime;

    /**
     * Cache used to store cache data
     * @var CacheAdapterInterface
     */
    protected $cache;

    /**
     * Current manifest
     * @var array
     */
    private $entries;

    /**
     * Key that identify the manifest for data source in cache
     * @var string
     */
    private $key;

    /**
     * Constructs a default cache storage.
     *
     * @param CacheAdapterInterface $adapter Cache adapter used to store cache data
     * @param int                   $ttl     (optional) Default cache TTL
     */
    public function __construct(CacheAdapterInterface $adapter, int $ttl = 3600)
    {
        $this->cache       = $adapter;
        $this->maxlifetime = $ttl;
    }

    /**
     * {@inheritDoc}
     */
    public function exists(array $request): bool
    {
        // Hash a request data source into a string that returns cache metadata
        $this->key = $request['source'];

        if ($this->entries = $this->cache->fetch($this->key)) {
            return true;
        }
        return false;
    }

    /**
     * {@inheritDoc}
     */
    public function fetch(array $request)
    {
        if (!$this->exists($request)) {
            return;
        }

        $manifest = null;
        foreach ($this->entries as $index => $entry) {
            if ($entry['sourceFile'] === $request['file']->getPathname()) {
                $manifest = $entry;
                break;  // we found entry in cache corresponding to current filename
            }
        }

        if (!isset($manifest)) {
            // no cache results for this filename
            return;
        }

        // Ensure that the response is not expired
        if ($manifest['expiration'] < time()
            || $manifest['cacheData'] !== sha1_file($request['file']->getPathname())
        ) {
            // results have expired
            $response = false;
        } else {
            $response = $this->cache->fetch($manifest['cacheData']);
        }

        if ($response === false) {
            // Remove the entry from the metadata and update the cache
            unset($this->entries[$index]);
            if (count($this->entries)) {
                $this->cache->save($this->key, $this->entries);
            } else {
                $this->cache->delete($this->key);
            }
        }

        return $response;
    }

    /**
     * {@inheritDoc}
     */
    public function cache(array $request): void
    {
        $currentTime = time();
        $entries     = array();

        if ($this->exists($request)) {
            foreach ($this->entries as $entry) {
                if ($entry['expiration'] < $currentTime) {
                    // remove expired entry from the metadata
                    continue;
                }
                if ($entry['sourceFile'] === $request['file']->getPathname()) {
                    // remove old cached content
                    $this->cache->delete($entry['cacheData']);
                } else {
                    $entries[] = $entry;
                }
            }
        }

        // update the manifest
        $key = sha1_file($request['file']->getPathname());
        array_push(
            $entries,
            array(
                'expiration' => $currentTime + $this->maxlifetime,
                'cacheData'  => $key,
                'sourceFile' => $request['file']->getPathname()
            )
        );
        $this->cache->save($this->key, $entries);

        // save user data
        $this->cache->save($key, $request['ast']);
    }

    /**
     * {@inheritDoc}
     */
    public function delete(array $request): int
    {
        $entriesCleared = 0;

        if ($this->exists($request)) {
            foreach ($this->entries as $entry) {
                if ($entry['cacheData']) {
                    // delete each results of the manifest
                    if ($this->cache->delete($entry['cacheData'])) {
                        $entriesCleared++;
                    }
                }
            }
            // delete the manifest of data source
            $this->cache->delete($this->key);
        }
        return $entriesCleared;
    }

    /**
     * {@inheritDoc}
     */
    public function purge(string $source): int
    {
        $request = array('source' => $source);
        return $this->delete($request);
    }
}
