<?php

$baseDir   = dirname(__DIR__);
$vendorDir = $baseDir . '/vendor';

$loader = require_once $vendorDir . '/autoload.php';
$loader->addClassMap(
    array(
        'Bartlett\Tests\Reflect\Analyser\FooAnalyser'
            => __DIR__ . '/Analyser/FooAnalyser.php',
        'Bartlett\Tests\Reflect\Analyser\BarAnalyser'
            => __DIR__ . '/Analyser/BarAnalyser.php',
        'Bartlett\Tests\Reflect\Model\GenericModelTest'
            => __DIR__ . '/Model/GenericModelTest.php',
        'Bartlett\Tests\Reflect\Environment\YourLogger'
            => __DIR__ . '/Environment/YourLogger.php',
    )
);

require __DIR__ . '/ResultPrinter.php';
require __DIR__ . '/MonologConsoleLogger.php';
