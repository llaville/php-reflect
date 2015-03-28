<?php
/**
 * Example of API Class Reflection.
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

// creates an instance of client
$client = new Client();

// request for a Bartlett\Reflect\Api\Reflection
$api = $client->api('reflection');

// perform request, on a data source
$dataSource = dirname(__DIR__) . '/src';

// equivalent to CLI command `phpreflect reflection:class Bartlett\Reflect ../src`
$model = $api->class('Bartlett\\Reflect', $dataSource);

echo $model;
