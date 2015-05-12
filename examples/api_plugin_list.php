<?php
/**
 * Example of API Plugin list
 *
 * @category PHP
 * @package  PHP_Reflect
 * @author   Laurent Laville <pear@laurent-laville.org>
 * @license  http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version  Release: @package_version@
 * @link     http://php5.laurent-laville.org/reflect/
 * @since    Example available since Release 3.0.0-alpha3+1
 */
require_once dirname(__DIR__) . '/vendor/autoload.php';

use Bartlett\Reflect\Client;
use Bartlett\Reflect\Environment;

// defines environment where to find the JSON config file
if (!getenv("BARTLETTRC")) {
    putenv("BARTLETTRC=phpreflect.json");
}
Environment::setScanDir();

// creates an instance of client
$client = new Client();

// request for a Bartlett\Reflect\Api\Plugin
$api = $client->api('plugin');

$plugins = $api->dir();

print_r($plugins);
