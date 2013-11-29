<?php

namespace Bartlett\Reflect\Model;

use Bartlett\Reflect\Visitor\VisitorInterface;

/**
 * Visitable
 * is an abstraction which declares the accept operation. This is the entry point
 * which enables an object to be "visited" by the visitor object. Each object
 * from a collection should implement this abstraction in order to be able
 * to be visited.
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
