<?php

$phpParserDir = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'vendor/PHP-Parser/lib';

require $phpParserDir . '/PhpParser/Autoloader.php';
PhpParser\Autoloader::register();

require_once 'Symfony/Component/ClassLoader/UniversalClassLoader.php';

use Symfony\Component\ClassLoader\UniversalClassLoader;

$loader = new UniversalClassLoader();
$loader->registerNamespaces(array(
    'Bartlett' => dirname(__DIR__) . DIRECTORY_SEPARATOR . 'src',
    'Symfony'  => dirname(__DIR__) . DIRECTORY_SEPARATOR . 'vendor',
    'Bartlett\Tests\Reflect' => __DIR__,
));
$loader->register();
