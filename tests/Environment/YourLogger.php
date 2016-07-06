<?php

namespace Bartlett\Tests\Reflect\Environment;

use Monolog\Logger;
use Monolog\Handler\RotatingFileHandler;

class YourLogger extends Logger
{
    public function __construct($name = 'DEV')
    {
        $tempDir = sys_get_temp_dir() . '/bartlett/logs';

        if (!file_exists($tempDir)) {
            mkdir($tempDir, 0755, true);
        }
        $filename = sprintf('phpreflect-%s.log', date('Ymd'));

        $stream = new RotatingFileHandler("$tempDir/$filename", 30);
        $stream->setFilenameFormat('{filename}-{date}', 'Y-m-d');

        $handlers = array($stream);

        parent::__construct($name, $handlers);
    }
}
