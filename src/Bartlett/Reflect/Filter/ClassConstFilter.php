<?php
/**
 * Filter values based on statement for class constant elements.
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

namespace Bartlett\Reflect\Filter;

use Bartlett\Reflect\Ast\Statement;

/**
 * Filter out unwanted values, in parse results, based on statement node.
 *
 * @category PHP
 * @package  PHP_Reflect
 * @author   Laurent Laville <pear@laurent-laville.org>
 * @license  http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version  Release: @package_version@
 * @link     http://php5.laurent-laville.org/reflect/
 * @since    Class available since Release 2.0.0RC2
 */
class ClassConstFilter extends \FilterIterator implements \Countable
{
    /**
     * Class Constant that will restrict scope of results
     * @var string
     */
    private $name;

    /**
     * Constructs a new FilterIterator,
     * which consists of a passed in iterator with filters applied to it.
     *
     * @param \Iterator $iterator  The iterator that is being filtered.
     * @param string    $name      The class constant that you want to restrict scope.
     *
     * @return ModelFilter
     */
    public function __construct(\Iterator $iterator, $name = null)
    {
        parent::__construct($iterator);

        $this->name = $name;
    }

    /**
     * Count the number of results after the filter is applied.
     *
     * @return int
     */
    public function count()
    {
        return iterator_count($this);
    }

    /**
     * Checks whether the current element of the iterator is acceptable
     * through this filter.
     *
     * @return bool TRUE if the current element is acceptable, otherwise FALSE.
     */
    public function accept()
    {
        $item   = $this->getInnerIterator()->current();
        $accept = false;

        if ($item instanceof Statement
            && $item->getType() == 'ClassConst'
        ) {
            $accept = true;
            if ($this->name && $this->name !== $item->getName()) {
                $accept = false;
            }
        }
        return $accept;
    }
}
