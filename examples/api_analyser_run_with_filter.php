<?php
/**
 * Examples of Structure and Loc analyser's run with filter applied on final results.
 *
 * @category PHP
 * @package  PHP_Reflect
 * @author   Laurent Laville <pear@laurent-laville.org>
 * @license  http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version  Release: @package_version@
 * @link     http://php5.laurent-laville.org/reflect/
 * @since    Example available since Release 3.1.0
 */

require_once dirname(__DIR__) . '/vendor/autoload.php';

use Bartlett\Reflect\Client;

// creates an instance of client
$client = new Client();

// request for a Bartlett\Reflect\Api\Analyser
$api = $client->api('analyser');

// perform request, on a data source with two analysers (structure, loc)
$dataSource = dirname(__DIR__) . '/src';
$analysers  = array('structure', 'loc');

// filter rules on final results
$closure = function ($data) {
    $filterOnKeys = array(
        'classes', 'abstractClasses', 'concreteClasses',
        'classConstants', 'globalConstants', 'magicConstants',
    );

    foreach ($data as $title => &$keys) {
        if (strpos($title, 'StructureAnalyser') === false) {
            continue;
        }
        // looking into Structure Analyser metrics and keep classes and constants info
        foreach ($keys as $key => $val) {
            if (!in_array($key, $filterOnKeys)) {
                unset($keys[$key]);  // "removed" unsolicited values
                continue;
            }
        }
    }
    return $data;
};

// equivalent to CLI command `phpreflect analyser:run --filter=YourFilters.php ../src structure loc`
//$metrics = $api->run($dataSource, $analysers, null, false, $closure = 'YourFilters.php');

// OR with embeded $closure code
$metrics = $api->run($dataSource, $analysers, null, false, $closure);

var_export($metrics);
