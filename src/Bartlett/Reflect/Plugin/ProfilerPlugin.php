<?php declare(strict_types=1);

/**
 * Plugin to track memory and time consumption.
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
use Bartlett\Reflect\Event\SuccessEvent;
use Bartlett\Reflect\Event\CompleteEvent;
use Bartlett\Reflect\Plugin\Log\DefaultLogger;
use Bartlett\Reflect\Util\Timer;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

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
    public function activate(EventDispatcherInterface $eventDispatcher): void
    {
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents(): array
    {
        return array(
            ProgressEvent::class => 'onReflectProgress',
            SuccessEvent::class  => 'onReflectSuccess',
            ErrorEvent::class    => 'onReflectError',
            CompleteEvent::class => 'onReflectComplete',
        );
    }

    /**
     * Just before parsing a new file of the data source.
     *
     * @param ProgressEvent $event A 'reflect.progress' event
     *
     * @return void
     */
    public function onReflectProgress(ProgressEvent $event): void
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
     * @param SuccessEvent $event A 'reflect.success' event
     *
     * @return void
     */
    public function onReflectSuccess(SuccessEvent $event): void
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
     * @param ErrorEvent $event A 'reflect.error' event
     *
     * @return void
     */
    public function onReflectError(ErrorEvent $event): void
    {
        $filename = $event['file']->getPathname();
        $appEvent = $this->stopwatch->stop($filename);
        $time     = $appEvent->getDuration();

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
     * @param CompleteEvent $event A 'reflect.complete' event
     *
     * @return void
     */
    public function onReflectComplete(CompleteEvent $event): void
    {
        $appEvent = $this->stopwatch->stop($event['source']);
        $time     = $appEvent->getDuration();

        self::$logger->notice(
            'Parsing data source {source} completed in {time}',
            array('time' => Timer::toTimeString($time), 'source' => $event['source'])
        );
    }
}
