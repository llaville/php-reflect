<?php

namespace Bartlett\Reflect\Api\V3;

use Bartlett\Reflect\Plugin\PluginManager;

use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class Plugin extends Common
{
    public function __call($name, $args)
    {
        if ('invoke' == $name) {
        } elseif ('list' == $name) {
            return $this->dir();
        }
    }

    public function __invoke($arg)
    {
    }

    public function dir()
    {
        $pm = new PluginManager(new EventDispatcher());
        if ($this->registerPlugins) {
            $pm->registerPlugins();
        }

        $plugins = $pm->getPlugins();
        $rows    = array();

        foreach ($plugins as $plugin) {
            if (!$plugin instanceof EventSubscriberInterface) {
                $rows[] = array(get_class($plugin), '');
                continue;
            }
            $events = $plugin::getSubscribedEvents();
            $first  = true;
            foreach ($events as $event => $function) {
                if (!$first) {
                    $rows[] = array('', $event);
                } else {
                    $rows[] = array(get_class($plugin), $event);
                    $first  = false;
                }
            }
        }

        return $rows;
    }
}
