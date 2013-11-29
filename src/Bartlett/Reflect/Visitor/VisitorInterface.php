<?php

namespace Bartlett\Reflect\Visitor;

use Bartlett\Reflect\Model\Visitable;

/**
 * VisitorInterface
 * is an interface used to declare the visit operation for all the types
 * of visitable classes.
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
