<?php

namespace YourNamespace;

use Bartlett\Reflect\Plugin\CachePlugin as BaseCachePlugin;

use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class CachePlugin extends BaseCachePlugin
{
    // all additional code you need

    public function __construct($cache)
    {
        if (is_array($cache)) {
            if (isset($cache['backend']['class'])) {
                if (stripos($cache['backend']['class'], 'SQLite3Cache')) {
                    if (isset($cache['backend']['args'])) {
                        $args = parent::replaceTokens($cache['backend']['args']);
                    } else {
                        $args = array();
                    }
                    $cache['backend']['args'][0] = new \SQLite3($args[0]);
                }
            }
        }

        parent::__construct($cache);
    }

    public function activate(EventDispatcherInterface $eventDispatcher)
    {
        echo __CLASS__ . ' version is in use', PHP_EOL;
    }
}
