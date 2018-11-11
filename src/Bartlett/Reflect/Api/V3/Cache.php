<?php
/**
 * Manage cache of parsing results
 *
 * PHP version 5
 *
 * @category PHP
 * @package  PHP_Reflect
 * @author   Laurent Laville <pear@laurent-laville.org>
 * @license  https://opensource.org/licenses/BSD-3-Clause The 3-Clause BSD License
 * @link     http://php5.laurent-laville.org/reflect/
 */

namespace Bartlett\Reflect\Api\V3;

use Bartlett\Reflect\Plugin\PluginManager;
use Bartlett\Reflect\Plugin\CachePlugin;

/**
 * Manage cache of parsing results
 *
 * @category PHP
 * @package  PHP_Reflect
 * @author   Laurent Laville <pear@laurent-laville.org>
 * @license  https://opensource.org/licenses/BSD-3-Clause The 3-Clause BSD License
 * @link     http://php5.laurent-laville.org/reflect/
 * @since    Class available since Release 3.0.0-alpha1
 */
class Cache extends Common
{
    /**
     * Clear cache (any adapter and backend).
     *
     * @param string $source Path to the data source or its alias.
     * @param string $alias  If set, the source refers to its alias.
     *
     * @return int Number of entries cleared in cache
     * @throws \Exception        if data source provider is unknown
     * @throws \RuntimeException if cache plugin is not installed
     */
    public function clear($source, $alias = null)
    {
        $source = trim($source);
        if ($alias) {
            $alias = $source;
        } else {
            $alias = false;
        }

        $finder = $this->findProvider($source, $alias);

        if ($finder === false) {
            throw new \RuntimeException(
                'None data source matching'
            );
        }

        $pm = new PluginManager($this->eventDispatcher);
        if (!$this->registerPlugins) {
            return 0;
        }
        $pm->registerPlugins();

        foreach ($pm->getPlugins() as $plugin) {
            if ($plugin instanceof CachePlugin) {
                $cache = $plugin->getCacheStorage();
                break;
            }
        }

        $entriesCleared = $cache->purge($this->dataSourceId);
        return $entriesCleared;
    }
}
