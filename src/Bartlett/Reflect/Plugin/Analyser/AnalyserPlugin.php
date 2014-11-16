<?php
/**
 * Plugin to analyse metrics of parsing results.
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

namespace Bartlett\Reflect\Plugin\Analyser;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\EventDispatcher\Event;

use Bartlett\Reflect\Events;
use Bartlett\Reflect\Command\AnalyserListCommand;
use Bartlett\Reflect\Command\AnalyserRunCommand;

/**
 * Plugin to analyse metrics of parsing results.
 *
 * @category PHP
 * @package  PHP_Reflect
 * @author   Laurent Laville <pear@laurent-laville.org>
 * @license  http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version  Release: @package_version@
 * @link     http://php5.laurent-laville.org/reflect/
 * @since    Class available since Release 2.0.0RC2
 */
class AnalyserPlugin implements EventSubscriberInterface
{
    protected $analysers;
    protected $metrics;

    /**
     * Initializes the analyser plugin.
     *
     * @param mixed $analyser One or more analyser that will deepthly
     *                        process source code
     */
    public function __construct($analyser)
    {
        if (!is_array($analyser)) {
            $analyser = array($analyser);
        }
        $this->analysers = $analyser;
        $this->metrics   = array();
    }

    /**
     * Gets the commands available with this plugin.
     *
     * @return array An array of Command instances
     */
    public static function getCommands()
    {
        $commands   = array();
        $commands[] = new AnalyserListCommand;
        $commands[] = new AnalyserRunCommand;

        return $commands;
    }

    /**
     * Provides the metrics project summary.
     *
     * @param string $source (optional) The data source identifier.
     *                       (see ProviderManager)
     *
     * @return array
     * @throws \OutOfBoundsException if $source identifier is unknown
     */
    public function getMetrics($source = null)
    {
        if (isset($source)) {
            if (!isset($this->metrics[$source])) {
                throw new \OutOfBoundsException("Data source '$source' is unknown");
            }
            return $this->metrics[$source];
        }
        return $this->metrics;
    }

    /**
     * Returns an array of event names this subscriber wants to listen to.
     *
     * @return array The event names to listen to
     */
    public static function getSubscribedEvents()
    {
        return array(
            Events::COMPLETE => 'onReflectComplete',
        );
    }

    /**
     * Analyse metrics at end of parsing a full data source.
     *
     * @param Event $event Current event emitted by the manager (Reflect class)
     *
     * @return void
     */
    public function onReflectComplete(Event $event)
    {
        $source = $event['source'];
        foreach ($this->analysers as $analyser) {
            $metrics = $analyser->analyse($event->getSubject());
            if (!isset($this->metrics[$source])) {
                $this->metrics[$source] = array();
            }
            $this->metrics[$source] = array_merge($this->metrics[$source], $metrics);
        }
    }
}
