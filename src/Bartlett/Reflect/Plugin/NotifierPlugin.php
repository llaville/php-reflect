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
            Events::SUCCESS  => 'onNotification',
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
                $message = 'Parsing file "%filename%" in progress.';
                if (!$start) {
                    $this->stopwatch->start($event['source']);
                    $start = true;
                }
                break;
            case Events::SUCCESS:
                $message = 'Analyze file "%filename%" successful.';
                break;
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
                    $this->toTimeString($time),
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

    /**
     * Formats the elapsed time as a string.
     *
     * This code has been copied and adapted from phpunit/php-timer
     *
     * @param int $time The period duration (in milliseconds)
     *
     * @return string
     */
    protected function toTimeString($time)
    {
        $times = array(
            'hour'   => 3600000,
            'minute' => 60000,
            'second' => 1000
        );

        $ms = $time;

        foreach ($times as $unit => $value) {
            if ($ms >= $value) {
                $time = floor($ms / $value * 100.0) / 100.0;
                return $time . ' ' . ($time == 1 ? $unit : $unit . 's');
            }
        }
        return $ms . ' ms';
    }
}
