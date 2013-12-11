<?php

namespace Bartlett\Reflect\Plugin\Cache;

/**
 * Default cache storage implementation
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
     * @param CacheAdapterInterface $adapter Cache adapter used to store cache data
     * @param int                   $ttl     (optional) Default cache TTL
     */
    public function __construct($adapter, $ttl = 3600)
    {
        $this->cache       = $adapter;
        $this->maxlifetime = $ttl;
    }

    /**
     * Checks if cache exists for a request
     *
     * @param RequestInterface $request
     *
     * @return bool
     */
    public function exists($request)
    {
        // Hash a request data source into a string that returns cache metadata
        $this->key = sha1($request['source']);

        if ($this->entries = $this->cache->fetch($this->key)) {
            return true;
        }
        return false;
    }

    /**
     * Get a Response from the cache for a request
     *
     * @param RequestInterface $request
     *
     * @return null|Response
     */
    public function fetch($request)
    {
        if (!$this->exists($request)) {
            return;
        }

        $manifest = null;
        $entries  = unserialize($this->entries);
        foreach ($entries as $index => $entry) {
            if ($entry['sourceFile'] === $request['filename']) {
                $manifest = $entry;
                break;  // we found entry in cache corresponding to current filename
            }
        }

        if (!isset($manifest)) {
            // no cache results for this filename
            return;
        }

        // Ensure that the response is not expired
        if ($manifest['expiration'] < time()) {
            // results have expired
            $response = null;
        } else {
            $response = $this->cache->fetch($manifest['cacheData']);
            if ($response) {
                $response = unserialize($response);
            } else {
                // The response is not valid because the body was somehow deleted
                $response = null;
            }
        }

        if ($response === null) {
            // Remove the entry from the metadata and update the cache
            unset($entries[$index]);
            if (count($entries)) {
                $this->cache->save($this->key, serialize($entries));
            } else {
                $this->cache->delete($this->key);
            }
        }

        return $response;
    }

    /**
     * Cache a FILE parse
     *
     * @param RequestInterface $request  Request being cached
     */
    public function cache($request)
    {
        $currentTime = time();
        $entries     = array();

        if ($this->exists($request)) {
            foreach (unserialize($this->entries) as $entry) {
                if ($entry['expiration'] < $currentTime) {
                    // remove expired entry from the metadata
                    continue;
                }
                if ($entry['sourceFile'] === $request['filename']) {
                    // remove old cached content
                    $this->cache->delete($entry['cacheData']);
                } else {
                    $entries[] = $entry;
                }
            }
        }

        // update the manifest
        $key = sha1_file($request['filename']);
        array_push($entries, array(
            'expiration' => $currentTime + $this->maxlifetime,
            'cacheData'  => $key,
            'sourceFile' => $request['filename']
        ));
        $this->cache->save($this->key, serialize($entries));

        // save user data
        $this->cache->save($key, serialize($request['data']));
    }

    /**
     * Deletes cache entries that match a request
     *
     * @param RequestInterface $request Request to delete from cache
     */
    public function delete($request)
    {
        if ($this->exists($request)) {
            foreach (unserialize($this->entries) as $entry) {
                if ($entry['cacheData']) {
                    // delete each results of the manifest
                    $this->cache->delete($entry['cacheData']);
                }
            }
            // delete the manifest of data source
            $this->cache->delete($this->key);
        }
    }

    /**
     * Purge all cache entries for a given data source
     *
     * @param string $source
     */
    public function purge($source)
    {
        $request = array('source' => $source);
        $this->delete($request);
    }
}
