<?php declare(strict_types=1);

/**
 * Plugin to log events.
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
use Bartlett\Reflect\Event\SuccessEvent;
use Bartlett\Reflect\Event\ErrorEvent;
use Bartlett\Reflect\Event\CompleteEvent;
use Bartlett\Reflect\Event\BuildEvent;
use Bartlett\Reflect\Event\SniffEvent;
use Bartlett\Reflect\Plugin\Log\DefaultLogger;

use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;

/**
 * Plugin to enable the logging of all events.
 *
 * @category PHP
 * @package  PHP_Reflect
 * @author   Laurent Laville <pear@laurent-laville.org>
 * @license  https://opensource.org/licenses/BSD-3-Clause The 3-Clause BSD License
 * @since    Class available since Release 2.4.0
 */
class LogPlugin implements PluginInterface, EventSubscriberInterface
{
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * Initializes the log plugin.
     *
     * @param LoggerInterface $logger PSR-3 logger used to log events.
     *
     * @link http://www.php-fig.org/psr/psr-3/
     * @link https://github.com/php-fig/log
     */
    public function __construct(LoggerInterface $logger = null)
    {
        if (!isset($logger)) {
            $logger = new DefaultLogger('DefaultLoggerChannel', LogLevel::INFO, null, array());
        }
        $this->logger = $logger;
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
            BuildEvent::class    => 'onAstBuild',
            SniffEvent::class    => 'onSniff',
        );
    }

    /**
     * Logs PROGRESS event.
     *
     * @param ProgressEvent $event Current event emitted by the manager (Reflect class)
     *
     * @return void
     */
    public function onReflectProgress(ProgressEvent $event): void
    {
        $context = $event->getArguments();
        $this->logger->info('Parsing file "{file}" in progress.', $context);
    }

    /**
     * Logs SUCCESS event.
     *
     * @param SuccessEvent $event Current event emitted by the manager (Reflect class)
     *
     * @return void
     */
    public function onReflectSuccess(SuccessEvent $event): void
    {
        $context = $event->getArguments();
        $this->logger->info('Analyze file "{file}" successful.', $context);
    }

    /**
     * Logs ERROR event.
     *
     * @param ErrorEvent $event Current event emitted by the manager (Reflect class)
     *
     * @return void
     */
    public function onReflectError(ErrorEvent $event): void
    {
        $context = $event->getArguments();
        $this->logger->error('Parser has detected an error on file "{file}". {error}', $context);
    }

    /**
     * Logs COMPLETE event.
     *
     * @param CompleteEvent $event Current event emitted by the manager (Reflect class)
     *
     * @return void
     */
    public function onReflectComplete(CompleteEvent $event): void
    {
        $context = $event->getArguments();
        unset($context['extra']);
        $this->logger->notice('Parsing data source "{source}" completed.', $context);
    }

    /**
     * Logs BUILD event.
     *
     * @param BuildEvent $event Current event emitted by the builder
     *
     * @return void
     */
    public function onAstBuild(BuildEvent $event): void
    {
        $context = $event->getArguments();

        if (isset($context['node'])) {
            $context['node'] = sprintf(
                '%s with attributes %s',
                $context['node']->getType(),
                json_encode($context['node']->getAttributes())
            );
        }
        $this->logger->debug('Building AST, process {method} {node}', $context);
    }

    /**
     * Logs SNIFF event.
     *
     * @param SniffEvent $event Current event emitted by the sniffer
     *
     * @return void
     */
    public function onSniff(SniffEvent $event): void
    {
        $context = $event->getArguments();

        $this->logger->debug('Visiting SNIFF, process {method} {sniff}', $context);
    }
}
