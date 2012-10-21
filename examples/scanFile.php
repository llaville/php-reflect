<?php
/**
 * Example that scan a simple file and return list of classes used
 *
 * PHP version 5
 *
 * @category PHP
 * @package  PHP_Reflect
 * @author   Laurent Laville <pear@laurent-laville.org>
 * @license  http://www.opensource.org/licenses/bsd-license.php  BSD
 * @version  GIT: $Id$
 * @link     http://php5.laurent-laville.org/reflect/
 * @since    File available since Release 1.0.0
 */

require_once 'Bartlett/PHP/Reflect/Autoload.php';

$source = '/path/to/PEAR-1.9.2/PEAR.php';

$reflect = new PHP_Reflect();
$reflect->scan($source);

$classes = $reflect->getClasses();
print_r(array_keys($classes));
?>