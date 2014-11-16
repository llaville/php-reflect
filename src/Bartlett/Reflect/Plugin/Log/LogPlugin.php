<?php
/**
 * Plugin to log events.
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

namespace Bartlett\Reflect\Plugin\Log;

use Bartlett\Reflect\Events;

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
 * @license  http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version  Release: @package_version@
 * @link     http://php5.laurent-laville.org/reflect/
 * @since    Class available since Release 2.4.0
 */
class LogPlugin implements EventSubscriberInterface
{
    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var array
     */
    protected $options;

    /**
     * Initializes the log plugin.
     *
     * @param LoggerInterface $logger  PSR-3 logger used to log events.
     * @param array           $options (optional) Customize log levels
     *                                 and message templates for each event.
     *
     * @link http://www.php-fig.org/psr/psr-3/
     * @link https://github.com/php-fig/log
     */
    public function __construct(LoggerInterface $logger, array $options = null)
    {
        $default = array(
            Events::PROGRESS => array(
                'level'    => LogLevel::INFO,
                'template' => 'Parsing file "{file}" in progress.',
                'context'  => true,
            ),
            Events::SUCCESS => array(
                'level'    => LogLevel::INFO,
                'template' => 'AST built.',
                'context'  => true,
            ),
            Events::CACHE  => array(
                'level'    => LogLevel::INFO,
                'template' => 'AST built by a previous request.',
                'context'  => true,
            ),
            Events::ERROR => array(
                'level'    => LogLevel::ERROR,
                'template' => 'Parser has detected an error on file "{file}". "{error}"',
                'context'  => true,
            ),
            Events::COMPLETE => array(
                'level'    => LogLevel::NOTICE,
                'template' => 'Parsing data source "{source}" completed.',
                'context'  => true,
            ),
        );

        if (isset($options)) {
            $options = array_replace_recursive($default, $options);
        } else {
            $options = $default;
        }
        $this->options = $options;
        $this->logger  = $logger;
    }

    /**
     * Returns an array of event names this subscriber wants to listen to.
     *
     * @return array The event names to listen to
     */
    public static function getSubscribedEvents()
    {
        return array(
            Events::PROGRESS => 'onReflectProgress',
            Events::SUCCESS  => 'onReflectSuccess',
            Events::CACHE    => 'onReflectCache',
            Events::ERROR    => 'onReflectError',
            Events::COMPLETE => 'onReflectComplete',
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
        $this->log($event);
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
        $this->log($event);
    }

    /**
     * Logs CACHE event.
     *
     * @param Event $event Current event emitted by the manager (Reflect class)
     *
     * @return void
     */
    public function onReflectCache(GenericEvent $event)
    {
        $this->log($event);
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
        $this->log($event);
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
        $this->log($event);
    }

    /**
     * Logs an event as specified.
     *
     * @param Event $event   Current event
     * @param array $context (optional) Contextual event data
     *
     * @return void
     */
    protected function log($event, array $context = null)
    {
        if (!isset($this->options[$event->getName()])
            || $this->options[$event->getName()] === false
            || !isset($this->options[$event->getName()]['level'])
            || !isset($this->options[$event->getName()]['template'])
            || !isset($this->options[$event->getName()]['context'])
        ) {
            // do not log this event (as asked or if params missing)
            return;
        }

        $level   = $this->options[$event->getName()]['level'];
        $message = $this->format(
            $this->options[$event->getName()]['template'],
            $event
        );
        if ($this->options[$event->getName()]['context'] === false) {
            // do not send contextual event arguments
            $context = array();
        } elseif (!isset($context)) {
            // send all contextual event arguments
            $context = $event->getArguments();
        } else {
            // use only event argument specified by $context
        }
        $this->logger->log($level, $message, $context);
    }

    /**
     * Formats a log message.
     *
     * @param string $template Message template that uses variable substitution
     *                         for string enclosed in braces ({})
     *
     * @return string
     */
    protected function format($template, $event)
    {
        return preg_replace_callback(
            '/{\s*([A-Za-z_\-\.0-9]+)\s*}/',
            function (array $matches) use ($event) {

                switch ($matches[1]) {
                    case 'source':
                    case 'error':
                        $result = $event[$matches[1]];
                        break;
                    case 'file':
                        $result = $event[$matches[1]]->getPathname();
                        break;
                    default:
                        $result = '';
                }
                return $result;
            },
            $template
        );
    }
}
