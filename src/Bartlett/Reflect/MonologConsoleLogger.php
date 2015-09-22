<?php

namespace Bartlett\Reflect;

use Monolog\Logger;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Processor\PsrLogMessageProcessor;

class MonologConsoleLogger extends Logger
{
    public function __construct($name = 'YourLogger', $level = Logger::DEBUG)
    {
        $tempDir = sys_get_temp_dir() . '/bartlett/logs';

        if (!file_exists($tempDir)) {
            mkdir($tempDir, 0755, true);
        }
        $filename = sprintf('sniffer-visitors-php%s.log', PHP_VERSION_ID);

        $stream = new RotatingFileHandler("$tempDir/$filename", 30, $level);
        $stream->setFilenameFormat('{filename}-{date}', 'Ymd');

        $handlers = array($stream);

        $processors = array(
            new PsrLogMessageProcessor(),
        );

        parent::__construct($name, $handlers, $processors);
    }
}
