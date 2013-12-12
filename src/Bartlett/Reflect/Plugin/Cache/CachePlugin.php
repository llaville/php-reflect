<?php
/**
 * Plugin to cache parsing results.
 *
 * PHP version 5
 *
 * @category PHP
 * @package  PHP_Reflect
 * @author   Laurent Laville <pear@laurent-laville.org>
 * @license  http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version  GIT: $Id$
 * @link     http://php5.laurent-laville.org/reflect/
 */

namespace Bartlett\Reflect\Plugin\Cache;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\EventDispatcher\Event;

/**
 * Plugin to enable the caching of all data source parser.
 *
 * @category PHP
 * @package  PHP_Reflect
 * @author   Laurent Laville <pear@laurent-laville.org>
 * @license  http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version  Release: @package_version@
 * @link     http://php5.laurent-laville.org/reflect/
 * @since    Class available since Release 2.0.0RC1
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
     * @param CacheStorageInterface $cache Object used to cache parsing results
     */
    public function __construct(CacheStorageInterface $cache)
    {
        $this->storage = $cache;
    }

    /**
     * Returns an array of event names this subscriber wants to listen to.
     *
     * @return array The event names to listen to
     */
    public static function getSubscribedEvents()
    {
        return array(
            'reflect.progress' => 'onReflectProgress',
            'reflect.success'  => 'onReflectSuccess',
        );
    }

    /**
     * Checks if results in cache will satisfy the source before parsing.
     *
     * @param Event $event Current event emitted by the manager (Reflect class)
     *
     * @return void
     */
    public function onReflectProgress(Event $event)
    {
        if ($response = $this->storage->fetch($event)) {
            $event['notModified'] = $response;
        }
    }

    /**
     * If possible, store results in cache after source parsing.
     *
     * @param Event $event Current event emitted by the manager (Reflect class)
     *
     * @return void
     */
    public function onReflectSuccess(Event $event)
    {
        $this->storage->cache($event);
    }
}
