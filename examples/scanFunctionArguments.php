<?php
/**
 * Example that scan a simple file
 * (PHPUnit 3.5.15 - PHPUnit_Framework_TestCase abstract class)
 * and return list of methods (with signature and arguments details)
 *
 * PHP version 5
 *
 * @category PHP
 * @package  PHP_Reflect
 * @author   Laurent Laville <pear@laurent-laville.org>
 * @license  http://www.opensource.org/licenses/bsd-license.php  BSD
 * @version  GIT: $Id$
 * @link     http://php5.laurent-laville.org/reflect/
 * @since    File available since Release 1.1.0
 */

require_once 'Bartlett/PHP/Reflect/Autoload.php';

$source = '/path/to/PHPUnit/Framework/TestCase.php';

try {
    $options = array(
        'properties' => array(
            'function' => array(
                'signature', 'arguments'
            ),
        )
    );
    $reflect = new PHP_Reflect($options);
    $reflect->scan($source);

    $classes = $reflect->getClasses();

    print_r($classes['\\']['PHPUnit_Framework_TestCase']['methods']);

} catch (Exception $e) {
    echo 'Caught exception: ' . $e->getMessage() . PHP_EOL;
}
?>