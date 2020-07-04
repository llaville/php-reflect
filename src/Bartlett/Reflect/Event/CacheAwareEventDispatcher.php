<?php declare(strict_types=1);

/**
 * Event-driven architecture.
 *
 * PHP version 7
 *
 * @category PHP
 * @package  PHP_Reflect
 * @author   Laurent Laville <pear@laurent-laville.org>
 * @license  https://opensource.org/licenses/BSD-3-Clause The 3-Clause BSD License
 */

namespace Bartlett\Reflect\Event;

use Symfony\Contracts\EventDispatcher\Event;
use Symfony\Component\EventDispatcher\EventDispatcher;

/**
 * Class that is aware if a cache is used or not.
 *
 * @category PHP
 * @package  PHP_Reflect
 * @author   Laurent Laville <pear@laurent-laville.org>
 * @license  https://opensource.org/licenses/BSD-3-Clause The 3-Clause BSD License
 * @since    Class available since Release 3.0.0-beta2
 */
class CacheAwareEventDispatcher extends EventDispatcher
{
    /**
     * {@inheritdoc}
     *
     * @return void
     */
    protected function doDispatch($listeners, $eventName, Event $event): void
    {
        foreach ($listeners as $listener) {
            $evt = $this->preNotify($listener, clone($event));
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
     * @param Event  $event     The event
     *
     * @return Event
     */
    protected function preNotify($listener, Event $event)
    {
        if ($event instanceof SuccessEvent) {
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
