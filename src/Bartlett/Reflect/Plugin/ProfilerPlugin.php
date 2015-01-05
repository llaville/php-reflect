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
use Symfony\Component\Console\ConsoleEvents;
use Symfony\Component\Console\Event\ConsoleCommandEvent;
use Symfony\Component\Console\Event\ConsoleTerminateEvent;
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
     * @param LoggerInterface $logger    Compatible PSR-3 logger
     * @param Stopwatch       $stopwatch (optional) Instance of a Stopwatch component
     */
    public function __construct(LoggerInterface $logger = null, Stopwatch $stopwatch = null)
    {
        if (!isset($stopwatch)) {
            $stopwatch = new Stopwatch();
        }
        $this->stopwatch = $stopwatch;
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
        $events = array(
            ConsoleEvents::COMMAND   => 'onCommandStart',
            ConsoleEvents::TERMINATE => 'onCommandComplete',
        );

        if (self::$logger) {
            $events = array_merge(
                $events,
                array(
                    Reflect\Events::PROGRESS => 'onReflectProgress',
                    Reflect\Events::SUCCESS  => 'onReflectSuccess',
                    Reflect\Events::ERROR    => 'onReflectError',
                    Reflect\Events::COMPLETE => 'onReflectComplete',
                )
            );
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
     * Just before executing any command, the ConsoleEvents::COMMAND event
     * is dispatched.
     *
     * @param ConsoleCommandEvent $event A console command started
     *
     * @return void
     */
    public function onCommandStart(ConsoleCommandEvent $event)
    {
        $this->stopwatch->start($event->getCommand()->getName());
    }

    /**
     * Just before executing any command, the ConsoleEvents::TERMINATE event
     * is dispatched.
     *
     * @param ConsoleTerminateEvent $event A console command ended
     *
     * @return void
     */
    public function onCommandComplete(ConsoleTerminateEvent $event)
    {
        $command = $event->getCommand();

        $consoleEvent = $this->stopwatch->stop($command->getName());

        $input  = $event->getInput();
        $output = $event->getOutput();

        if (false === $input->hasParameterOption('--profile')) {
            return;
        }

        $time   = $consoleEvent->getDuration();
        $memory = $consoleEvent->getMemory();

        $text = sprintf(
            '%s<comment>Time: %s, Memory: %4.2fMb</comment>',
            PHP_EOL,
            $this->toTimeString($time),
            $memory / (1024 * 1024)
        );
        $output->writeln($text);
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
