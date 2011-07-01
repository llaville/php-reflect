<?php
/**
 * Example that scan a simple file (PEAR core file) 
 * and return list internal PHP function used. 
 *
 * PHP version 5
 *
 * @category PHP
 * @package  PHP_Reflect
 * @author   Laurent Laville <pear@laurent-laville.org>
 * @license  http://www.opensource.org/licenses/bsd-license.php  BSD
 * @version  SVN: $Id$
 * @link     http://php5.laurent-laville.org/reflect/
 * @since    File available since Release 1.0.0
 */

require_once 'Bartlett/PHP/Reflect/Autoload.php';

$source = '/path/to/PEAR-1.9.2/PEAR.php';

try {
    $options = array('containers' => array('core' => 'internalFunctions'));

    $reflect = new PHP_Reflect($options);
    $reflect->connect(
        'T_STRING', 'PHP_CompatInfo_Token_STRING',
        array('PHP_CompatInfo_TokenParser', 'parseTokenString')
    );
    $reflect->scan($source);

    $functions = $reflect->getInternalFunctions();
    print_r($functions);

} catch (Exception $e) {
    echo 'Caught exception: ' . $e->getMessage() . PHP_EOL;
}
?>