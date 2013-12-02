<?php

require_once 'Symfony/Component/ClassLoader/UniversalClassLoader.php';

use Symfony\Component\ClassLoader\UniversalClassLoader;

$loader = new UniversalClassLoader();
$loader->registerNamespaces(array(
    'Bartlett' => dirname(__DIR__) . DIRECTORY_SEPARATOR . 'src',
    'Symfony'  => dirname(__DIR__) . DIRECTORY_SEPARATOR . 'vendor',
));
$loader->register();
