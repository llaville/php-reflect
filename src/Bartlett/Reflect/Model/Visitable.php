<?php
/**
 * Interface that allow a class to be visitable 
 * as defined by the Visitor Design Pattern.
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

namespace Bartlett\Reflect\Model;

use Bartlett\Reflect\Visitor\VisitorInterface;

/**
 * Visitable is an abstraction which declares the accept operation. This is 
 * the entry point which enables an object to be "visited" by the visitor object. 
 * Each object from a collection should implement this abstraction in order 
 * to be able to be visited.
 *
 * @category PHP
 * @package  PHP_Reflect
 * @author   Laurent Laville <pear@laurent-laville.org>
 * @license  http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version  Release: @package_version@
 * @link     http://php5.laurent-laville.org/reflect/
 * @since    Interface available since Release 2.0.0RC1
 */
interface Visitable
{
    /**
     * Enables an object to be visited.
     *
     * @param VisitorInterface $visitor Concrete visitor
     *
     * @return void
     */
    public function accept(VisitorInterface $visitor);
}
