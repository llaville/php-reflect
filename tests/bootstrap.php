<?php

$baseDir   = dirname(__DIR__);
$vendorDir = $baseDir . '/vendor';

$loader = require_once $vendorDir . '/autoload.php';
$loader->addClassMap(
    array(
        'Bartlett\Tests\Reflect\Model\GenericModelTest'
            => __DIR__ . '/Model/GenericModelTest.php',
    )
);
