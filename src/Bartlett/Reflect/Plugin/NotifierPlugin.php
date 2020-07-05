<?php declare(strict_types=1);

/**
 * Notifies application events via different systems.
 *
 * PHP version 7
 *
 * @category PHP
 * @package  PHP_Reflect
 * @author   Laurent Laville <pear@laurent-laville.org>
 * @license  https://opensource.org/licenses/BSD-3-Clause The 3-Clause BSD License
 */

namespace Bartlett\Reflect\Plugin;

use Bartlett\Reflect\Event\ProgressEvent;
use Bartlett\Reflect\Event\ErrorEvent;
use Bartlett\Reflect\Event\CompleteEvent;
use Bartlett\Reflect\Plugin\Notifier\NotifierInterface;
use Bartlett\Reflect\Util\Timer;

use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\EventDispatcher\GenericEvent;

use Symfony\Component\Stopwatch\Stopwatch;

use function get_class;

/**
 * Notifies application events via different systems.
 *
 * @category PHP
 * @package  PHP_Reflect
 * @author   Laurent Laville <pear@laurent-laville.org>
 * @license  https://opensource.org/licenses/BSD-3-Clause The 3-Clause BSD License
 * @since    Class available since Release 3.0.0-alpha3+1
 */
class NotifierPlugin implements PluginInterface, EventSubscriberInterface
{
    private $notifier;
    private $stopwatch;

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
    public function activate(EventDispatcherInterface $eventDispatcher): void
    {
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents(): array
    {
        return array(
            ProgressEvent::class => 'onNotification',
            ErrorEvent::class    => 'onNotification',
            CompleteEvent::class => 'onNotification',
        );
    }

    /**
     * Notifies all important application events
     *
     * @param GenericEvent $event Any event
     *
     * @return void
     */
    public function onNotification(GenericEvent $event): void
    {
        static $start = false;

        switch (get_class($event)) {
            case ProgressEvent::class:
                if (!$start) {
                    $this->stopwatch->start($event['source']);
                    $start = true;
                }
                return;
            case ErrorEvent::class:
                $message = 'Parser has detected an error on file "%filename%". %error%';
                break;
            case CompleteEvent::class:
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

        $event['eventname'] = get_class($event);
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
    protected function getPlaceholders(GenericEvent $event): array
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
