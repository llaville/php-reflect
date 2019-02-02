<?php

declare(strict_types=1);

/**
 * Plugin manager.
 *
 * PHP version 7
 *
 * @category PHP
 * @package  PHP_Reflect
 * @author   Laurent Laville <pear@laurent-laville.org>
 * @license  https://opensource.org/licenses/BSD-3-Clause The 3-Clause BSD License
 * @link     http://php5.laurent-laville.org/reflect/
 */

namespace Bartlett\Reflect\Plugin;

use Bartlett\Reflect\Application\Command\ConfigValidateCommand;
use Bartlett\Reflect\Application\Command\ConfigValidateHandler;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Plugin manager
 *
 * @category PHP
 * @package  PHP_Reflect
 * @author   Laurent Laville <pear@laurent-laville.org>
 * @license  https://opensource.org/licenses/BSD-3-Clause The 3-Clause BSD License
 * @link     http://php5.laurent-laville.org/reflect/
 * @since    Class available since Release 3.0.0-alpha1
 */
class PluginManager
{
    protected $eventDispatcher;

    protected $plugins = [];
    protected $registeredPlugins = [];
    protected $configFilename;

    /**
     * Initializes plugin manager
     *
     * @param EventDispatcherInterface $eventDispatcher Instance of an event
     *        dispatcher
     * @param string                   $configFilename  Path to json configuration file
     */
    public function __construct(EventDispatcherInterface $eventDispatcher, string $configFilename)
    {
        $this->eventDispatcher = $eventDispatcher;
        $this->configFilename = $configFilename;
    }

    /**
     * Loads all plugins declared in the JSON config file.
     *
     * @return void
     */
    public function registerPlugins()
    {
        $command = new ConfigValidateCommand($this->configFilename);
        $configValidateHandler = new ConfigValidateHandler();
        $var = $configValidateHandler($command);

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
