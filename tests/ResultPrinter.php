<?php

declare(strict_types=1);

/**
 * Prints the result of a TestRunner run using a PSR-3 logger.
 *
 * PHP version 7
 *
 * @category   PHP
 * @package    PHP_Reflect
 * @subpackage Tests
 * @author     Laurent Laville <pear@laurent-laville.org>
 * @license    https://opensource.org/licenses/BSD-3-Clause The 3-Clause BSD License
 * @link       http://php5.laurent-laville.org/reflect/
 * @since      Class available since Release 3.1.0
 */

namespace Bartlett\Tests\Reflect;

use Bartlett\LoggerTestListenerTrait;

use Psr\Log\LoggerAwareTrait;
use Psr\Log\LogLevel;

/**
 * Prints the result of a TestRunner run using a PSR-3 logger.
 *
 * Use with `--printer` switch on command line
 * or `printerClass` attribute in phpunit.xml config file.
 *
 * @category   PHP
 * @package    PHP_Reflect
 * @subpackage Tests
 * @author     Laurent Laville <pear@laurent-laville.org>
 * @license    https://opensource.org/licenses/BSD-3-Clause The 3-Clause BSD License
 * @link       http://php5.laurent-laville.org/reflect/
 */
class ResultPrinter extends \PHPUnit_TextUI_ResultPrinter
{
    use LoggerTestListenerTrait, LoggerAwareTrait;

    /**
     * {@inheritDoc}
     */
    public function __construct($out = null, $verbose = false, $colors = self::COLOR_DEFAULT, $debug = false, $numberOfColumns = 80)
    {
        parent::__construct($out, $verbose, $colors, $debug, $numberOfColumns);

        if ($this->debug) {
            $minLevelOrList = LogLevel::INFO;
        } elseif ($this->verbose) {
            $minLevelOrList = LogLevel::NOTICE;
        } else {
            $minLevelOrList = array(LogLevel::NOTICE, LogLevel::ERROR);
        }

        $console = new MonologConsoleLogger('ResultPrinter');
        $console->setAcceptedLevels($minLevelOrList);

        $handlers = $console->getHandlers();
        foreach ($handlers as &$handler) {
            // attachs processors only to console handler
            if ($handler instanceof \Monolog\Handler\FilterHandler) {
                // new results presentation when color is supported or not
                $handler->pushProcessor(array($this, 'messageProcessor'));
            }
        }
        $this->setLogger($console);
    }

    /**
     * {@inheritDoc}
     */
    public function printResult(\PHPUnit_Framework_TestResult $result)
    {
        $this->printHeader();
        $this->printFooter($result);
    }

    protected function printHeader()
    {
        $this->logger->notice(
            \PHP_Timer::resourceUsage() .
            "\n",
            array('operation' => __FUNCTION__)
        );
    }

    public function messageProcessor(array $record)
    {
        $self  = $this;
        $debug = $this->debug;

        $context = $record['context'];

        if (!array_key_exists('operation', $context)) {
            return $record;
        }

        if ('printHeader' == $context['operation']) {
            $color  = 'fg-yellow';
            $record['message'] = $self->formatWithColor($color, $record['message']);

        } elseif ('printFooter' == $context['operation']) {
            if ($context['testCount'] === 0) {
                $color = 'fg-black, bg-yellow';
            } else {
                $color = ($context['status'] == 'OK')
                    ? 'fg-black, bg-green' : 'fg-white, bg-red';
            }
            $record['message'] = $self->formatWithColor($color, $record['message']);

        } elseif ('startTestSuite' == $context['operation']) {
            $record['message'] =
                $self->formatWithColor('fg-yellow', $context['suiteName'].':') .
                "\n\n    " .
                $self->formatWithColor(
                    'fg-cyan',
                    sprintf('Test suite started with %d tests', $context['testCount'])
                ) .
                "\n"
            ;

        } elseif ('endTestSuite' == $context['operation']) {
            $resultStatus  = ($context['errorCount'] + $context['failureCount']) ? 'KO' : 'OK';
            $resultMessage = sprintf('Results %s. ', $resultStatus) .
                $self->formatCounters(
                    $context['testCount'],
                    $context['assertionCount'],
                    $context['failureCount'],
                    $context['errorCount'],
                    $context['incompleteCount'],
                    $context['skipCount'],
                    $context['riskyCount']
                )
            ;
            if ($resultStatus == 'OK') {
                if ($context['testCount'] === 0) {
                    $color = 'fg-black, bg-yellow';
                } else {
                    $color = 'fg-yellow';
                }
            } else {
                $color = 'fg-red';
            }

            $record['message'] =
                $self->formatWithColor('fg-yellow', $context['suiteName'].':') .
                "\n\n    " .
                $self->formatWithColor(
                    'fg-cyan',
                    'Test suite ended. '
                ) .
                $self->formatWithColor(
                    $color,
                    $resultMessage
                ) .
                "\n"
            ;

        } elseif (in_array(strtolower($record['level_name']), array(LogLevel::INFO, LogLevel::WARNING, LogLevel::ERROR))) {
            // indent messages
            $indent = str_repeat(' ', 4);

            $shortLabel = $context['testName'];
            $longLabel  = str_replace($context['testDescriptionArr'][0].'::', '', $context['testDescriptionStr']);

            if ('startTest' == $context['operation']) {
                $record['message'] = sprintf("%sTest '%s' started.", $indent, ($debug ? $longLabel : $shortLabel));

            } elseif ('endTest' == $context['operation']) {
                $record['message'] = sprintf("%sTest '%s' ended.", $indent, $shortLabel);

            } elseif ('addError' == $context['operation']) {
                $record['message'] = sprintf("%sError while running test '%s'. %s", $indent, $shortLabel, $context['reason']);

            } elseif ('addFailure' == $context['operation']) {
                $record['message'] = sprintf("%sTest '%s' failed. %s", $indent, $shortLabel, $context['reason']);

            } elseif ('addIncompleteTest' == $context['operation']) {
                $record['message'] = sprintf("%sTest '%s' is incomplete. %s", $indent, $shortLabel, $context['reason']);

            } elseif ('addRiskyTest' == $context['operation']) {
                $record['message'] = sprintf("%sTest '%s' is risky. %s", $indent, $shortLabel, $context['reason']);

            } elseif ('addSkippedTest' == $context['operation']) {
                $record['message'] = sprintf("%sTest '%s' has been skipped. %s", $indent, $shortLabel, $context['reason']);
            }
        }

        return $record;
    }
}
