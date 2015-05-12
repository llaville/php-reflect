<?php
/**
 * Examples of Structure analyser's run with a custom cache plugin.
 *
 * @category PHP
 * @package  PHP_Reflect
 * @author   Laurent Laville <pear@laurent-laville.org>
 * @license  http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version  Release: @package_version@
 * @link     http://php5.laurent-laville.org/reflect/
 * @since    Example available since Release 3.0.0-beta2
 */

$loader = require_once dirname(__DIR__) . '/vendor/autoload.php';
$loader->addClassMap(
    array(
        'YourNamespace\CachePlugin'
            =>  __DIR__ . '/YourNamespace/CachePlugin.php',
    )
);

use Bartlett\Reflect\Client;

// set our own location of JSON config file
putenv("BARTLETT_SCAN_DIR=" . __DIR__ . '/YourNamespace');

// set our own JSON config file
putenv("BARTLETTRC=yournamespace.json");

// creates an instance of client
$client = new Client();

// request for a Bartlett\Reflect\Api\Analyser
$api = $client->api('analyser');

// perform request, on a data source with default analyser (structure)
$dataSource = dirname(__DIR__) . '/src';
$analysers  = array('structure');

// equivalent to CLI command `phpreflect analyser:run ../src`
$metrics = $api->run($dataSource, $analysers);

var_export($metrics);
