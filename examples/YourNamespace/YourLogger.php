<?php

namespace YourNamespace;

use Psr\Log\AbstractLogger;

class YourLogger extends AbstractLogger
{
    public function log($level, $message, array $context = array())
    {
        printf('%s : %s%s', $level, $message, PHP_EOL);
    }
}
