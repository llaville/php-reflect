<?php

declare(strict_types=1);

/**
 * Manage plugins
 *
 * PHP version 7
 *
 * @category PHP
 * @package  PHP_Reflect
 * @author   Laurent Laville <pear@laurent-laville.org>
 * @license  https://opensource.org/licenses/BSD-3-Clause The 3-Clause BSD License
 * @link     http://php5.laurent-laville.org/reflect/
 */

namespace Bartlett\Reflect\Api\V3;

use Bartlett\Reflect\Plugin\PluginManager;

use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Identify all plugins available
 *
 * @category PHP
 * @package  PHP_Reflect
 * @author   Laurent Laville <pear@laurent-laville.org>
 * @license  https://opensource.org/licenses/BSD-3-Clause The 3-Clause BSD License
 * @link     http://php5.laurent-laville.org/reflect/
 * @since    Class available since Release 3.0.0-alpha1
 */
class Plugin extends Common
{
    public function __call($name, $args)
    {
        if ('list' == $name) {
            return $this->dir();
        }
    }

    /**
     * List all plugins installed.
     *
     * @return array
     */
    public function dir()
    {
        $pm = new PluginManager(new EventDispatcher());
        if ($this->registerPlugins) {
            $pm->registerPlugins();
        }

        $plugins = $pm->getPlugins();
        $rows    = [];

        foreach ($plugins as $plugin) {
            if (!$plugin instanceof EventSubscriberInterface) {
                $events = [];
            } else {
                $events = $plugin::getSubscribedEvents();
            }
            $rows[get_class($plugin)] = array_keys($events);
        }

        return $rows;
    }
}
