<?php

namespace Bartlett\Reflect\Api\V3;

use Bartlett\Reflect\Plugin\PluginManager;
use Bartlett\Reflect\Plugin\CachePlugin;

class Cache extends Common
{
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
