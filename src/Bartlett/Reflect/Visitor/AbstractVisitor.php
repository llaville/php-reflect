<?php
/**
 * Visitor Design Pattern.
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
 * Class that holds a visitor.
 *
 * @category PHP
 * @package  PHP_Reflect
 * @author   Laurent Laville <pear@laurent-laville.org>
 * @license  http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version  Release: @package_version@
 * @link     http://php5.laurent-laville.org/reflect/
 * @since    Class available since Release 2.0.0RC1
 */
abstract class AbstractVisitor implements VisitorInterface
{
    /**
     * Polymorphic method to determine the right method to invoke at run-time.
     *
     * @param Visitable $visitable Concrete visitable
     *
     * @return void
     */
    public function visit(Visitable $visitable)
    {
        $visitable->accept($this);
    }

    /**
     * Default algorithm
     *
     * @param Visitable $visitable Concrete visitable
     *
     * @return void
     */
    public function defaultVisit(Visitable $visitable)
    {
        $visitableClass = get_class($visitable);
        $visitorClass   = get_class($this);

        throw new \Exception(
            'Visitor ' . $visitorClass . '::visit (' . $visitableClass . ')' .
            ' is not implemented.'
        );
    }
}
