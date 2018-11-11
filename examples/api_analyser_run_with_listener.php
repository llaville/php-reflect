<?php
/**
 * Examples of Structure analyser's run with a listener attached.
 *
 * @category PHP
 * @package  PHP_Reflect
 * @author   Laurent Laville <pear@laurent-laville.org>
 * @license  https://opensource.org/licenses/BSD-3-Clause The 3-Clause BSD License
 * @link     http://php5.laurent-laville.org/reflect/
 * @since    Example available since Release 3.0.0-beta2
 */

require_once dirname(__DIR__) . '/vendor/autoload.php';

use Bartlett\Reflect\Client;

use Symfony\Component\EventDispatcher\GenericEvent;

// creates an instance of client
$client = new Client();

// request for a Bartlett\Reflect\Api\Analyser
$api = $client->api('analyser');

$dispatcher = $api->getEventDispatcher();

$dispatcher->addListener(
    'reflect.progress',
    function (GenericEvent $e) {
        printf(
            'Parsing Data source "%s" in progress ... File "%s"' . PHP_EOL,
            $e['source'],
            $e['file']->getPathname()
        );
    }
);

// perform request, on a data source with default analyser (structure)
$dataSource = dirname(__DIR__) . '/src';
$analysers  = array('structure');

// equivalent to CLI command `phpreflect analyser:run ../src`
$metrics = $api->run($dataSource, $analysers);

var_export($metrics);
