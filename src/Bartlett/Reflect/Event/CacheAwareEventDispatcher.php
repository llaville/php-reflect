<?php
/**
 * Event-driven architecture.
 *
 * PHP version 5
 *
 * @category PHP
 * @package  PHP_Reflect
 * @author   Laurent Laville <pear@laurent-laville.org>
 * @license  https://opensource.org/licenses/BSD-3-Clause The 3-Clause BSD License
 * @link     http://php5.laurent-laville.org/reflect/
 */

namespace Bartlett\Reflect\Event;

use Bartlett\Reflect\Events;

use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\EventDispatcher\EventDispatcher;

/**
 * Class that is aware if a cache is used or not.
 *
 * @category PHP
 * @package  PHP_Reflect
 * @author   Laurent Laville <pear@laurent-laville.org>
 * @license  https://opensource.org/licenses/BSD-3-Clause The 3-Clause BSD License
 * @link     http://php5.laurent-laville.org/reflect/
 * @since    Class available since Release 3.0.0-beta2
 */
class CacheAwareEventDispatcher extends EventDispatcher
{
    /**
     * {@inheritdoc}
     */
    protected function doDispatch($listeners, $eventName, Event $event)
    {
        foreach ($listeners as $listener) {
            $evt = $this->preNotify($listener, $eventName, clone($event));
            call_user_func($listener, $evt, $eventName, $this);
            if ($evt->isPropagationStopped()) {
                break;
            }
        }
    }

    /**
     * Called before notify a listener about the event.
     *
     * @param mixed  $listener  The listener to notify with that $event
     * @param string $eventName The event name
     * @param Event  $event     The event
     *
     * @return Event
     */
    protected function preNotify($listener, $eventName, Event $event)
    {
        if (Events::SUCCESS == $eventName
            && $event instanceof GenericEvent
        ) {
            /*
             * 'ast' argument of 'reflect.success' event is used only by the cache plugin.
             *  Remove it improve performance.
             */
            if (is_array($listener)
                && !$listener[0] instanceof \Bartlett\Reflect\Plugin\CachePlugin
            ) {
                unset($event['ast']);
            }
        }
        return $event;
    }
}
