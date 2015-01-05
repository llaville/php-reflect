<?php

namespace Bartlett\Reflect\Plugin;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Plugin interface
 *
 */
interface PluginInterface
{
    /**
     * Announce plugin activation
     *
     * @param EventDispatcherInterface $eventDispatcher Instance of the event
     *        dispatcher
     */
    public function activate(EventDispatcherInterface $eventDispatcher);
}
