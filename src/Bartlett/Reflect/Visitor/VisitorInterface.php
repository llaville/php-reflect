<?php
/**
 * Visitor Design Pattern.
 * All visitors should implement this interface.
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

namespace Bartlett\Reflect\Visitor;

use Bartlett\Reflect\Model\Visitable;

/**
 * Holds a visitor.
 *
 * This interface is used to declare the visit operation for all the types
 * of visitable classes.
 *
 * @category PHP
 * @package  PHP_Reflect
 * @author   Laurent Laville <pear@laurent-laville.org>
 * @license  http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version  Release: @package_version@
 * @link     http://php5.laurent-laville.org/reflect/
 * @since    Interface available since Release 2.0.0RC1
 */

interface VisitorInterface
{
    /**
     * Perform an operation on object to be visited.
     *
     * @param Visitable $visitable Concrete visitable
     *
     * @return void
     */
    public function visit(Visitable $visitable);
}
