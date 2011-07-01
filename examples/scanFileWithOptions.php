<?php
/**
 * Example that scan a simple file (PEAR core file) 
 * and return list of PEAR_Error methods (with limited details)
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
    $options = array(
        'properties' => array(
            'interface' => array(
                'keywords', 'parent', 'methods'
            ),
            'class' => array(
                'keywords', 'parent', 'methods', 'interfaces', 'package'
            ),
            'function' => array(
                'keywords', 'signature'
            ),
        )
    );
    $reflect = new PHP_Reflect($options);
    $reflect->scan($source);

    $classes = $reflect->getClasses();

    print_r($classes['\\']['PEAR_Error']['docblock']);
    print_r($classes['\\']['PEAR_Error']['methods']);

} catch (Exception $e) {
    echo 'Caught exception: ' . $e->getMessage() . PHP_EOL;
}
?>