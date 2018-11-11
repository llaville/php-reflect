<?php
/**
 * Common interface to all plugins accessible through the PluginManager.
 *
 * PHP version 5
 *
 * @category PHP
 * @package  PHP_Reflect
 * @author   Laurent Laville <pear@laurent-laville.org>
 * @license  https://opensource.org/licenses/BSD-3-Clause The 3-Clause BSD License
 * @link     http://php5.laurent-laville.org/reflect/
 */

namespace Bartlett\Reflect\Plugin;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Interface that all plugins must implement.
 *
 * @category PHP
 * @package  PHP_Reflect
 * @author   Laurent Laville <pear@laurent-laville.org>
 * @license  https://opensource.org/licenses/BSD-3-Clause The 3-Clause BSD License
 * @link     http://php5.laurent-laville.org/reflect/
 * @since    Class available since Release 3.0.0-alpha1
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
