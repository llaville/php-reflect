<?php declare(strict_types=1);

/**
 * Plugin manager.
 *
 * PHP version 7
 *
 * @category PHP
 * @package  PHP_Reflect
 * @author   Laurent Laville <pear@laurent-laville.org>
 * @license  https://opensource.org/licenses/BSD-3-Clause The 3-Clause BSD License
 */

namespace Bartlett\Reflect\Plugin;

use Bartlett\Reflect\Environment;
use Bartlett\Reflect\Api\V3\Config;

use Seld\JsonLint\ParsingException;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Plugin manager
 *
 * @category PHP
 * @package  PHP_Reflect
 * @author   Laurent Laville <pear@laurent-laville.org>
 * @license  https://opensource.org/licenses/BSD-3-Clause The 3-Clause BSD License
 * @since    Class available since Release 3.0.0-alpha1
 */
class PluginManager
{
    protected $eventDispatcher;

    protected $plugins = array();

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
     * @throws ParsingException
     */
    public function registerPlugins(): void
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
    public function addPlugin(PluginInterface $plugin): void
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
    public function getPlugins(): array
    {
        return $this->plugins;
    }
}
