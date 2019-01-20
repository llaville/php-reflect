<?php

declare(strict_types=1);

/**
 * Plugin to track memory and time consumption.
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

use Bartlett\Reflect;
use Bartlett\Reflect\Plugin\Log\DefaultLogger;
use Bartlett\Reflect\Util\Timer;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\EventDispatcher\GenericEvent;

use Symfony\Component\Stopwatch\Stopwatch;

use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;

/**
 * Plugin to track memory and time consumption.
 *
 * @category PHP
 * @package  PHP_Reflect
 * @author   Laurent Laville <pear@laurent-laville.org>
 * @license  https://opensource.org/licenses/BSD-3-Clause The 3-Clause BSD License
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

        if (!isset($logger)) {
            $logger = new DefaultLogger('DefaultLoggerChannel', LogLevel::INFO, null, array());
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
            Reflect\Events::PROGRESS => 'onReflectProgress',
            Reflect\Events::SUCCESS  => 'onReflectSuccess',
            Reflect\Events::ERROR    => 'onReflectError',
            Reflect\Events::COMPLETE => 'onReflectComplete',
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
            array('time' => Timer::toTimeString($time), 'file' => $filename)
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
                'time'  => Timer::toTimeString($time),
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
            array('time' => Timer::toTimeString($time), 'source' => $event['source'])
        );
    }
}
