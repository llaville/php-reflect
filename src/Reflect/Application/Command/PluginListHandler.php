<?php

declare(strict_types=1);

namespace Bartlett\Reflect\Application\Command;

use Bartlett\Reflect\Plugin\PluginManager;

use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * PHP version 7
 *
 * @category PHP
 * @package  bartlett/php-reflect
 * @author   Laurent Laville <pear@laurent-laville.org>
 * @license  https://opensource.org/licenses/BSD-3-Clause The 3-Clause BSD License
 * @link     http://php5.laurent-laville.org/reflect/
 */
class PluginListHandler implements CommandHandlerInterface
{
    public function __invoke(PluginListCommand $command): array
    {
        $pm = new PluginManager(new EventDispatcher(), $command->getConfigFilename());
        if ($command->withPlugins()) {
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
