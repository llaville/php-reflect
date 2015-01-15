<?php
/**
 * Plugin to track memory and time consumption.
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

use Bartlett\Reflect;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\EventDispatcher\GenericEvent;

use Symfony\Component\Stopwatch\Stopwatch;

use Psr\Log\LoggerInterface;

/**
 * Plugin to track memory and time consumption.
 *
 * @category PHP
 * @package  PHP_Reflect
 * @author   Laurent Laville <pear@laurent-laville.org>
 * @license  http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version  Release: @package_version@
 * @link     http://php5.laurent-laville.org/reflect/
 * @since    Class available since Release 3.0.0-alpha1
 */
class ProfilerPlugin implements PluginInterface, EventSubscriberInterface
{
    /**
     * @var Stopwatch
     */
    protected $stopwatch;

    /**
     * @var LoggerInterface
     */
    protected static $logger;

    /**
     * Initialize the profiler
     *
     * @param LoggerInterface $logger Compatible PSR-3 logger
     */
    public function __construct(LoggerInterface $logger = null)
    {
        $this->stopwatch = new Stopwatch();
        self::$logger    = $logger;
    }

    public function activate(EventDispatcherInterface $eventDispatcher)
    {
    }

    /**
     * Returns an array of event names this subscriber wants to listen to.
     *
     * @return array The event names to listen to
     */
    public static function getSubscribedEvents()
    {
        if (self::$logger) {
            $events = array(
                Reflect\Events::PROGRESS => 'onReflectProgress',
                Reflect\Events::SUCCESS  => 'onReflectSuccess',
                Reflect\Events::ERROR    => 'onReflectError',
                Reflect\Events::COMPLETE => 'onReflectComplete',
            );
        } else {
            $events = array();
        }
        return $events;
    }

    /**
     * Just before parsing a new file of the data source.
     *
     * @param GenericEvent $event A 'reflect.progress' event
     *
     * @return void
     */
    public function onReflectProgress(GenericEvent $event)
    {
        static $start = false;

        if (!$start) {
            $this->stopwatch->start($event['source']);
            $start = true;
        }
        $this->stopwatch->start($event['file']->getPathname());
    }

    /**
     * After parsing a file of the data source.
     *
     * @param GenericEvent $event A 'reflect.success' event
     *
     * @return void
     */
    public function onReflectSuccess($event)
    {
        $filename = $event['file']->getPathname();
        $appEvent = $this->stopwatch->stop($filename);
        $time     = $appEvent->getDuration();

        self::$logger->info(
            'AST built in {time} on file "{file}"',
            array('time' => $this->toTimeString($time), 'file' => $filename)
        );
    }

    /**
     * PHP-Parser raised an error.
     *
     * @param GenericEvent $event A 'reflect.error' event
     *
     * @return void
     */
    public function onReflectError($event)
    {
        $filename = $event['file']->getPathname();
        $this->stopwatch->stop($filename);

        self::$logger->error(
            'Parse error in {time} on file "{file"}: {error}',
            array(
                'time'  => $this->toTimeString($time),
                'file'  => $filename,
                'error' => $event['error']
            )
        );
    }

    /**
     * A parse request is over.
     *
     * @param GenericEvent $event A 'reflect.complete' event
     *
     * @return void
     */
    public function onReflectComplete($event)
    {
        $appEvent = $this->stopwatch->stop($event['source']);
        $time     = $appEvent->getDuration();

        self::$logger->notice(
            'Parsing data source {source} completed in {time}',
            array('time' => $this->toTimeString($time), 'source' => $event['source'])
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
