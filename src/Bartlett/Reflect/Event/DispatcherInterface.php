<?php declare(strict_types=1);

/**
 * Event-driven architecture.
 * All dispatchers should implement this interface.
 *
 * PHP version 7
 *
 * @category PHP
 * @package  PHP_Reflect
 * @author   Laurent Laville <pear@laurent-laville.org>
 * @license  https://opensource.org/licenses/BSD-3-Clause The 3-Clause BSD License
 */

namespace Bartlett\Reflect\Event;

use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Holds an event dispatcher.
 *
 * @category PHP
 * @package  PHP_Reflect
 * @author   Laurent Laville <pear@laurent-laville.org>
 * @license  https://opensource.org/licenses/BSD-3-Clause The 3-Clause BSD License
 * @since    Interface available since Release 2.0.0RC1
 */
interface DispatcherInterface
{
    /**
     * Set the EventDispatcher of the request
     *
     * @param EventDispatcherInterface $eventDispatcher Instance of the event
     *        dispatcher
     *
     * @return self for a fuent interface
     */
    public function setEventDispatcher(EventDispatcherInterface $eventDispatcher);

    /**
     * Get the EventDispatcher of the request
     *
     * @return EventDispatcherInterface
     */
    public function getEventDispatcher(): EventDispatcherInterface;

    /**
     * Dispatches an event to all registered listeners.
     *
     * @param object $event The event to dispatch
     *
     * @return object
     */
    public function dispatch(object $event): object;

    /**
     * Adds an event subscriber.
     *
     * @param EventSubscriberInterface $subscriber The subscriber which is
     *        interested by events
     *
     * @return self for a fuent interface
     */
    public function addSubscriber(EventSubscriberInterface $subscriber);
}
