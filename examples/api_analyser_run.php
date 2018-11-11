<?php
/**
 * Examples of Structure and Loc analyser's run.
 *
 * <code>
 * array (
 *  'files' =>
 *  array (
 *    // ...
 *  ),
 *  'Bartlett\\Reflect\\Analyser\\StructureAnalyser' =>
 *  array (
 *    'namespaces' => 19,
 *    'interfaces' => 7,
 *    'traits' => 0,
 *    'classes' => 56,
 *    'abstractClasses' => 6,
 *    'concreteClasses' => 50,
 *    'functions' => 6,
 *    'namedFunctions' => 0,
 *    'anonymousFunctions' => 6,
 *    'methods' => 280,
 *    'publicMethods' => 241,
 *    'protectedMethods' => 29,
 *    'privateMethods' => 10,
 *    'nonStaticMethods' => 273,
 *    'staticMethods' => 7,
 *    'constants' => 0,
 *    'classConstants' => 17,
 *    'globalConstants' => 0,
 *    'magicConstants' => 3,
 *    'testClasses' => 0,
 *    'testMethods' => 0,
 *  ),
 *  'Bartlett\\Reflect\\Analyser\\LocAnalyser' =>
 *  array (
 *    'llocClasses' => 995,
 *    'llocByNoc' => 0,
 *    'llocByNom' => 0,
 *    'llocFunctions' => 48,
 *    'llocByNof' => 0,
 *    'llocGlobal' => 0,
 *    'classes' => 56,
 *    'functions' => 6,
 *    'methods' => 303,
 *    'cloc' => 117,
 *    'eloc' => 2700,
 *    'lloc' => 1043,
 *    'wloc' => 329,
 *    'loc' => 3146,
 *    'ccn' => 475,
 *    'ccnMethods' => 451,
 *  ),
 * )
 * </code>
 *
 * @category PHP
 * @package  PHP_Reflect
 * @author   Laurent Laville <pear@laurent-laville.org>
 * @license  https://opensource.org/licenses/BSD-3-Clause The 3-Clause BSD License
 * @link     http://php5.laurent-laville.org/reflect/
 * @since    Example available since Release 3.0.0-alpha3
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

// equivalent to CLI command `phpreflect analyser:run ../src structure loc`
$metrics = $api->run($dataSource, $analysers);

var_export($metrics);
