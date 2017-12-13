<?php
/**
 * Plugin manager.
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

namespace Bartlett\Reflect\Plugin;

use Bartlett\Reflect\Environment;
use Bartlett\Reflect\Api\V3\Config;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Plugin manager
 *
 * @category PHP
 * @package  PHP_Reflect
 * @author   Laurent Laville <pear@laurent-laville.org>
 * @license  http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version  Release: @package_version@
 * @link     http://php5.laurent-laville.org/reflect/
 * @since    Class available since Release 3.0.0-alpha1
 */
class PluginManager
{
    protected $eventDispatcher;

    protected $plugins = array();
    protected $registeredPlugins = array();

    /**
     * Initializes plugin manager
     *
     * @param EventDispatcherInterface $eventDispatcher Instance of an event
     *        dispatcher
     */
    public function __construct(EventDispatcherInterface $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * Loads all plugins declared in the JSON config file.
     *
     * @return void
     */
    public function registerPlugins()
    {
        $jsonFile = Environment::getJsonConfigFilename();
        if (!$jsonFile) {
            return;
        }

        $config = new Config;
        $var    = $config->validate($jsonFile);

        foreach ($var['plugins'] as $plugin) {
            if (class_exists($plugin['class'])) {
                if (isset($plugin['options'])) {
                    $options = $plugin['options'];
                    if (is_string($options)) {
                        if (class_exists($options)) {
                            $options = new $options;
                        } else {
                            $options = null;
                        }
                    }
                } else {
                    $options = null;
                }
                $plugin = new $plugin['class']($options);

                if ($plugin instanceof PluginInterface) {
                    $this->addPlugin($plugin);
                }
            }
        }
    }

    /**
     * Adds a plugin, activates it and registers it with the event dispatcher
     *
     * @param PluginInterface $plugin Plugin instance
     *
     * @return void
     */
    public function addPlugin(PluginInterface $plugin)
    {
        $this->plugins[] = $plugin;

        $plugin->activate($this->eventDispatcher);

        if ($plugin instanceof EventSubscriberInterface) {
            $this->eventDispatcher->addSubscriber($plugin);
        }
    }

    /**
     * Gets all currently active plugin instances
     *
     * @return array plugins
     */
    public function getPlugins()
    {
        return $this->plugins;
    }
}
