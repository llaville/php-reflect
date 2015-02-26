<?php
/**
 * Diagram processor interface
 *
 * PHP version 5
 *
 * @category PHP
 * @package  PHP_Reflect
 * @author   Laurent Laville <pear@laurent-laville.org>
 * @license  http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version  GIT: $Id$
 * @link     http://php5.laurent-laville.org/reflect/
 */

namespace Bartlett\Reflect\Api\V3\Diagram;

/**
 * Diagram processor interface
 *
 * @category PHP
 * @package  PHP_Reflect
 * @author   Laurent Laville <pear@laurent-laville.org>
 * @license  http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version  Release: @package_version@
 * @link     http://php5.laurent-laville.org/reflect/
 * @since    Class available since Release 3.0.0-beta3
 */
interface ProcessorInterface
{
    /**
     * Parse data source, and return string formatted in processor format.
     *
     * @param array  $models Collection of Bartlett\Reflect\Model
     * @param object $class  (optional) Instance of Bartlett\Reflect\Model\ClassModel
     *
     * @return string
     */
    public function render($models, $class);
}
