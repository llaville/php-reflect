<?php

namespace Bartlett\Reflect\Plugin\Log;

use Psr\Log\AbstractLogger;
use Psr\Log\LogLevel;

class DefaultLogger extends AbstractLogger
{
    private static $levels = array(
        100 => 'debug',
        200 => 'info',
        250 => 'notice',
        300 => 'warning',
        400 => 'error',
        500 => 'critical',
        550 => 'alert',
        600 => 'emergency',
    );

    private $channel;
    private $level;
    private $processors;

    public function __construct(
        $name = 'DefaultLoggerChannel',
        $level = LogLevel::INFO,
        $handler = null,
        array $processors = array()
    ) {
        $this->channel = $name;
        $this->level   = array_search($level, self::$levels);

        if (isset($handler)
            && is_object($handler)
            && method_exists($handler, 'handle')
            && is_callable(array($handler, 'handle'))
        ) {
            $this->handler = $handler;
        } else {
            $this->handler = $this;
            $processors[] = array($this, 'interpolate');
        }
        $this->processors = $processors;
    }

    public function isHandling(array $record)
    {
        $level = array_search($record['level'], self::$levels);
        return $level >= $this->level;
    }

    public function log($level, $message, array $context = array())
    {
        $record = array(
            'channel'  => $this->channel,
            'level'    => $level,
            'message'  => $message,
            'context'  => $context,
            'extra'    => array(),
            'datetime' => new \DateTime(),
        );

        if ($this->isHandling($record)) {
            foreach ($this->processors as $processor) {
                $record = call_user_func($processor, $record);
            }
            $this->handler->handle($record);
        }
    }

    public function handle(array $record)
    {
        error_log(
            sprintf(
                '%s.%s: %s',
                $this->channel,
                strtoupper($record['level']),
                $record['message']
            )
        );
    }

    /**
     * Processes a record's message according to PSR-3 rules
     *
     * It replaces {foo} with the value from $context['foo']
     *
     * This code was copied from Monolog\Processor\PsrLogMessageProcessor
     *
     * @author Jordi Boggiano <j.boggiano@seld.be>
     */
    public function interpolate(array $record)
    {
        if (false === strpos($record['message'], '{')) {
            return $record;
        }

        $replacements = array();
        foreach ($record['context'] as $key => $val) {
            if (is_null($val)
                || is_scalar($val)
                || (is_object($val) && method_exists($val, "__toString"))
            ) {
                $replacements['{'.$key.'}'] = $val;
            } elseif (is_object($val)) {
                $replacements['{'.$key.'}'] = '[object '.get_class($val).']';
            } else {
                $replacements['{'.$key.'}'] = '['.gettype($val).']';
            }
        }

        $record['message'] = strtr($record['message'], $replacements);

        return $record;
    }
}
