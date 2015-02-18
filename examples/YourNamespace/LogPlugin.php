<?php

namespace YourNamespace;

use Bartlett\Reflect\Plugin\LogPlugin as BaseLogPlugin;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;

use Psr\Log\LoggerInterface;

class LogPlugin extends BaseLogPlugin
{
    // all additional code you need

    public function __construct()
    {
        parent::__construct(new YourLogger());
    }

    public function activate(EventDispatcherInterface $eventDispatcher)
    {
        echo __CLASS__ . ' version is in use', PHP_EOL;
    }
}
