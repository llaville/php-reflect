<?php

namespace Bartlett\Reflect\Event;

use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class that holds an event dispatcher
 */
class AbstractDispatcher implements DispatcherInterface
{
    /**
     * @var EventDispatcherInterface
     */
    protected $eventDispatcher;

    /**
     * {@inheritdoc}
     */
    public function setEventDispatcher(EventDispatcherInterface $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getEventDispatcher()
    {
        if (!$this->eventDispatcher) {
            $this->eventDispatcher = new EventDispatcher();
        }
        return $this->eventDispatcher;
    }

    /**
     * {@inheritdoc}
     */
    public function dispatch($eventName, array $context = array())
    {
        return $this->getEventDispatcher()->dispatch($eventName, new GenericEvent($this, $context));
    }

    /**
     * {@inheritdoc}
     */
    public function addSubscriber(EventSubscriberInterface $subscriber)
    {
        $this->getEventDispatcher()->addSubscriber($subscriber);
        return $this;
    }
}
