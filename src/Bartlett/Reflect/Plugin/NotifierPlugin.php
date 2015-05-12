<?php
/**
 * Notifies application events via different systems.
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

use Bartlett\Reflect\Events;
use Bartlett\Reflect\Plugin\Notifier\NotifierInterface;
use Bartlett\Reflect\Util\Timer;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\EventDispatcher\GenericEvent;

use Symfony\Component\Stopwatch\Stopwatch;

/**
 * Notifies application events via different systems.
 *
 * @category PHP
 * @package  PHP_Reflect
 * @author   Laurent Laville <pear@laurent-laville.org>
 * @license  http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version  Release: @package_version@
 * @link     http://php5.laurent-laville.org/reflect/
 * @since    Class available since Release 3.0.0-alpha3+1
 */
class NotifierPlugin implements PluginInterface, EventSubscriberInterface
{
    private $notifier;

    /**
     * Initialize the notification
     *
     * @param NotifierInterface $notifier System of notification
     */
    public function __construct(NotifierInterface $notifier)
    {
        $this->notifier  = $notifier;
        $this->stopwatch = new Stopwatch();
    }

    /**
     * {@inheritdoc}
     */
    public function activate(EventDispatcherInterface $eventDispatcher)
    {
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        $events = array(
            Events::PROGRESS => 'onNotification',
            Events::ERROR    => 'onNotification',
            Events::COMPLETE => 'onNotification',
        );
        return $events;
    }

    /**
     * Notifies all important application events
     *
     * @param GenericEvent $event     Any event
     * @param string       $eventName Name of event dispatched
     *
     * @return void
     */
    public function onNotification(GenericEvent $event, $eventName)
    {
        static $start = false;

        switch ($eventName) {
            case Events::PROGRESS:
                if (!$start) {
                    $this->stopwatch->start($event['source']);
                    $start = true;
                }
                return;
            case Events::ERROR:
                $message = 'Parser has detected an error on file "%filename%". %error%';
                break;
            case Events::COMPLETE:
                $message  = "Parsing data source \"%source%\" completed.";
                $appEvent = $this->stopwatch->stop($event['source']);
                $time     = $appEvent->getDuration();
                $memory   = $appEvent->getMemory();

                $event['profile'] = sprintf(
                    'Time: %s, Memory: %4.2fMb',
                    Timer::toTimeString($time),
                    $memory / (1024 * 1024)
                );
                break;
        }
        $format = $this->notifier->getMessageFormat();

        $event['eventname'] = $eventName;
        $event['message']   = strtr($message, $this->getPlaceholders($event));
        $event['formatted'] = strtr($format, $this->getPlaceholders($event));

        $this->notifier->notify($event);
    }

    /**
     * Gets each place holders of event's arguments
     *
     * @param GenericEvent $event Any event
     *
     * @return array
     */
    protected function getPlaceholders(GenericEvent $event)
    {
        return array(
            '%eventname%' => $event['eventname'],
            '%source%'    => $event['source'],
            '%filename%'  => $event->hasArgument('file') ? $event['file'] : '',
            '%error%'     => $event->hasArgument('error') ? $event['error'] : '',
            '%profile%'   => $event->hasArgument('profile') ? $event['profile'] : '',
            '%message%'   => $event->hasArgument('message') ? $event['message'] : '',
        );
    }
}
