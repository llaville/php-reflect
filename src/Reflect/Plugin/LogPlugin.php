<?php

declare(strict_types=1);

/**
 * Plugin to log events.
 *
 * PHP version 7
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

use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\EventDispatcher\GenericEvent;

use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;

/**
 * Plugin to enable the logging of all events.
 *
 * @category PHP
 * @package  PHP_Reflect
 * @author   Laurent Laville <pear@laurent-laville.org>
 * @license  https://opensource.org/licenses/BSD-3-Clause The 3-Clause BSD License
 * @link     http://php5.laurent-laville.org/reflect/
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
            $logger = new DefaultLogger('DefaultLoggerChannel', LogLevel::INFO, null, []);
        }
        $this->logger = $logger;
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
        return array(
            Reflect\Events::PROGRESS => 'onReflectProgress',
            Reflect\Events::SUCCESS  => 'onReflectSuccess',
            Reflect\Events::ERROR    => 'onReflectError',
            Reflect\Events::COMPLETE => 'onReflectComplete',
            Reflect\Events::BUILD    => 'onAstBuild',
            Reflect\Events::SNIFF    => 'onSniff',
        );
    }

    /**
     * Logs PROGRESS event.
     *
     * @param Event $event Current event emitted by the manager (Reflect class)
     *
     * @return void
     */
    public function onReflectProgress(GenericEvent $event)
    {
        $context = $event->getArguments();
        $this->logger->info('Parsing file "{file}" in progress.', $context);
    }

    /**
     * Logs SUCCESS event.
     *
     * @param Event $event Current event emitted by the manager (Reflect class)
     *
     * @return void
     */
    public function onReflectSuccess(GenericEvent $event)
    {
        $context = $event->getArguments();
        $this->logger->info('Analyze file "{file}" successful.', $context);
    }

    /**
     * Logs ERROR event.
     *
     * @param Event $event Current event emitted by the manager (Reflect class)
     *
     * @return void
     */
    public function onReflectError(GenericEvent $event)
    {
        $context = $event->getArguments();
        $this->logger->error('Parser has detected an error on file "{file}". {error}', $context);
    }

    /**
     * Logs COMPLETE event.
     *
     * @param Event $event Current event emitted by the manager (Reflect class)
     *
     * @return void
     */
    public function onReflectComplete(GenericEvent $event)
    {
        $context = $event->getArguments();
        unset($context['extra']);
        $this->logger->notice('Parsing data source "{source}" completed.', $context);
    }

    /**
     * Logs BUILD event.
     *
     * @param Event $event Current event emitted by the builder
     *
     * @return void
     */
    public function onAstBuild(GenericEvent $event)
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
     * @param Event $event Current event emitted by the sniffer
     *
     * @return void
     */
    public function onSniff(GenericEvent $event)
    {
        $context = $event->getArguments();

        $this->logger->debug('Visiting SNIFF, process {method} {sniff}', $context);
    }
}
