<?php

declare(strict_types=1);

namespace Bartlett\Reflect\Plugin;

use Bartlett\Reflect\Application\Events;
use Bartlett\Reflect\Plugin\Log\DefaultLogger;
use Bartlett\Reflect\Presentation\Util\Timer;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\EventDispatcher\GenericEvent;

use Symfony\Component\Stopwatch\Stopwatch;

use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;

/**
 * Plugin to track memory and time consumption.
 *
 * PHP version 7
 *
 * @category PHP
 * @package  bartlett/php-reflect
 * @author   Laurent Laville <pear@laurent-laville.org>
 * @license  https://opensource.org/licenses/BSD-3-Clause The 3-Clause BSD License
 * @link     http://php5.laurent-laville.org/reflect/
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

        if (!isset($logger)) {
            $logger = new DefaultLogger('DefaultLoggerChannel', LogLevel::INFO, null, []);
        }
        self::$logger = $logger;
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
            Events::PROGRESS => 'onReflectProgress',
            Events::SUCCESS  => 'onReflectSuccess',
            Events::ERROR    => 'onReflectError',
            Events::COMPLETE => 'onReflectComplete',
        );
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
            ['time' => Timer::toTimeString($time), 'file' => $filename]
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
        $appEvent = $this->stopwatch->stop($filename);
        $time     = $appEvent->getDuration();

        self::$logger->error(
            'Parse error in {time} on file "{file"}: {error}',
            [
                'time'  => Timer::toTimeString($time),
                'file'  => $filename,
                'error' => $event['error']
            ]
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
            ['time' => Timer::toTimeString($time), 'source' => $event['source']]
        );
    }
}
