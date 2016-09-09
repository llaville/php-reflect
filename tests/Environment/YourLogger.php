<?php

namespace Bartlett\Tests\Reflect\Environment;

use Monolog\Logger;
use Monolog\Handler\TestHandler;

class YourLogger extends Logger
{
    public function __construct($name = 'SUT')
    {
        $stream = new TestHandler();

        $handlers = array($stream);

        parent::__construct($name, $handlers);
    }
}
