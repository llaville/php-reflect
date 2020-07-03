<?php declare(strict_types=1);

/**
 * Common interface to all notifiers accessible through the NotifierPlugin.
 *
 * @category PHP
 * @package  PHP_Reflect
 * @author   Laurent Laville <pear@laurent-laville.org>
 * @license  https://opensource.org/licenses/BSD-3-Clause The 3-Clause BSD License
 */

namespace Bartlett\Reflect\Plugin\Notifier;

use Symfony\Component\EventDispatcher\GenericEvent;

/**
 * Interface that all notifiers must implement.
 *
 * @category PHP
 * @package  PHP_Reflect
 * @author   Laurent Laville <pear@laurent-laville.org>
 * @license  https://opensource.org/licenses/BSD-3-Clause The 3-Clause BSD License
 * @since    Class available since Release 3.0.0-alpha3+1
 */
interface NotifierInterface
{
    /**
     * Defines the new message format
     *
     * @param string $format The message format with predefined place holders
     *
     * @return self for fluent interface
     * @see Bartlett\Reflect\Plugin\NotifierPlugin::getPlaceholders() for known place holders
     */
    public function setMessageFormat($format);

    /**
     * Gets the current message format
     *
     * @return string
     */
    public function getMessageFormat();

    /**
     * Notify an important event
     *
     * @param GenericEvent $event
     *
     * @return bool TRUE on a successful notification, FALSE on failure
     */
    public function notify(GenericEvent $event);
}
