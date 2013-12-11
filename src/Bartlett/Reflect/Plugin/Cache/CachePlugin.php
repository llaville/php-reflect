<?php

namespace Bartlett\Reflect\Plugin\Cache;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\EventDispatcher\Event;

/**
 * Plugin to enable the caching of all data source parser.
 */
class CachePlugin implements EventSubscriberInterface
{
    /**
     * @var CacheStorageInterface $cache Object used to cache parsing results
     */
    protected $storage;

    /**
     * Initializes the cache plugin.
     *
     * @param CacheStorageInterface $cache
     */
    public function __construct(CacheStorageInterface $cache)
    {
        $this->storage = $cache;
    }

    /**
     * EventSubscriberInterface implementation
     */
    public static function getSubscribedEvents()
    {
        return array(
            'reflect.progress' => 'onReflectProgress',
            'reflect.success'  => 'onReflectSuccess',
        );
    }

    /**
     * Check if results in cache will satisfy the source before parsing
     *
     * @param Event $event
     */
    public function onReflectProgress(Event $event)
    {
        if ($response = $this->storage->fetch($event)) {
            $event['notModified'] = $response;
        }
    }

    /**
     * If possible, store results in cache after source parsing
     *
     * @param Event $event
     */
    public function onReflectSuccess(Event $event)
    {
        $this->storage->cache($event);
    }
}
