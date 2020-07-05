<?php declare(strict_types=1);

/**
 * Cache Adapter for Doctrine 2.
 *
 * PHP version 7
 *
 * @category PHP
 * @package  PHP_Reflect
 * @author   Laurent Laville <pear@laurent-laville.org>
 * @license  https://opensource.org/licenses/BSD-3-Clause The 3-Clause BSD License
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
     */
    public function __construct(Cache $cache)
    {
        $this->cache = $cache;
    }

    /**
     * {@inheritDoc}
     */
    public function exists(string $id, array $options = null): bool
    {
        return $this->cache->contains($id);
    }

    /**
     * {@inheritDoc}
     */
    public function delete(string $id, array $options = null): bool
    {
        return $this->cache->delete($id);
    }

    /**
     * {@inheritDoc}
     */
    public function fetch(string $id, array $options = null)
    {
        return $this->cache->fetch($id);
    }

    /**
     * {@inheritDoc}
     */
    public function save(string $id, $data, $lifeTime = false, array $options = null): bool
    {
        return $this->cache->save($id, $data, $lifeTime);
    }
}
