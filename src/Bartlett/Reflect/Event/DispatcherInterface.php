<?php

namespace Bartlett\Reflect\Event;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Holds an event dispatcher
 */
interface DispatcherInterface
{
    /**
     * Set the EventDispatcher of the request
     *
     * @param EventDispatcherInterface $eventDispatcher
     *
     * @return self
     */
    public function setEventDispatcher(EventDispatcherInterface $eventDispatcher);

    /**
     * Get the EventDispatcher of the request
     *
     * @return EventDispatcherInterface
     */
    public function getEventDispatcher();

    /**
     * Helper to dispatch Reflect events
     *
     * @param string $eventName Name of the event to dispatch
     * @param array  $context   Context of the event
     *
     * @return Event The created event object
     */
    public function dispatch($eventName, array $context = array());

    /**
     * Add an event subscriber to the dispatcher
     *
     * @param EventSubscriberInterface $subscriber The subscriber
     *
     * @return self
     */
    public function addSubscriber(EventSubscriberInterface $subscriber);
}
