<?php

namespace Bartlett\Reflect\Visitor;

use Bartlett\Reflect\Model\Visitable;

/**
 * AbstractVisitor
 * is used to declare base accept operations.
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
