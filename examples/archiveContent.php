<?php
/**
 * Reflect v2 can parse archives data source
 *
 * @author Laurent Laville
 */

require_once __DIR__ . DIRECTORY_SEPARATOR . 'autoload.php';

use Bartlett\Reflect;
use Bartlett\Reflect\ProviderManager;
use Bartlett\Reflect\Provider\SymfonyFinderProvider;
use Symfony\Component\Finder\Finder;
use Symfony\Component\EventDispatcher\GenericEvent;


// -- source 1: ZIP archive (PHP_CompatInfo 2.0.0)
$zip = 'phar://' . __DIR__ . '/archives/PHP_CompatInfo-2.0.0.zip';

$zipFinder = new Finder();
$zipFinder->files()
    ->name('*.php')
    ->in($zip);

// -- source 2: TAR archive (PHP_Reflect 1.0.0)
$tar = 'phar://' . __DIR__ . '/archives/PHP_Reflect-1.0.0.tar';

$tarFinder = new Finder();
$tarFinder->files()
    ->name('*.php')
    ->in($tar);

// -- source 3: TAR gzipped archive (Phing 2.6.1)
$tgz = 'phar://' . __DIR__ . '/archives/phing-2.6.1.tgz';

$tgzFinder = new Finder();
$tgzFinder->files()
    ->name('*.php')
    ->in($tgz);

// -- source 4: PHAR archive (PHPUnit 3.7.28)
$phr = 'phar://' . __DIR__ . '/archives/phpunit.phar';

$phrFinder = new Finder();
$phrFinder->files()
    ->name('*.php')
    ->in($phr);

$pm = new ProviderManager;
$pm->set('ZipSource', new SymfonyFinderProvider($zipFinder));
$pm->set('TarSource', new SymfonyFinderProvider($tarFinder));
$pm->set('TgzSource', new SymfonyFinderProvider($tgzFinder));
$pm->set('PhrSource', new SymfonyFinderProvider($phrFinder));

$reflect = new Reflect;
$reflect->setProviderManager($pm);

// Add a listener that will echo out files when they are parsed
$reflect->getEventDispatcher()->addListener('reflect.progress', function (GenericEvent $e) {
    printf(
        'Parsing Data source "%s" in progress ... File "%s"' . PHP_EOL,
        $e['source'],
        $e['filename']
    );
});

$reflect->parse(array('ZipSource'));

$results = array();
foreach($reflect->getClasses() as $class) {
    $results[] = $class->getName();
}
echo 'Classes found on data source :' . PHP_EOL;
print_r( $results );
