<?php
/**
 * Filter values based on method model.
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

use Bartlett\Reflect\Model\MethodModel;

/**
 * Filter out unwanted values, in parse results, based on method model.
 *
 * @category PHP
 * @package  PHP_Reflect
 * @author   Laurent Laville <pear@laurent-laville.org>
 * @license  http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version  Release: @package_version@
 * @link     http://php5.laurent-laville.org/reflect/
 * @since    Class available since Release 2.0.0RC2
 */
class MethodFilter extends \FilterIterator implements \Countable
{
    /**
     * Method Modifiers that will restrict perimeter of results
     * @var array
     */
    private $modifiers;

    /**
     * Method name
     * @var string
     */
    private $name;

    /**
     * Constructs a new FilterIterator,
     * which consists of a passed in iterator with filters applied to it.
     *
     * @param \Iterator $iterator  The iterator that is being filtered.
     * @param array     $modifiers The method modifiers that you want to restrict perimeter.
     *
     * @return MethodFilter
     */
    public function __construct(\Iterator $iterator, array $modifiers = null, $name = null)
    {
        parent::__construct($iterator);

        if ($modifiers) {
            $modifiers = array_map('strtolower', $modifiers);
        }

        $this->modifiers = $modifiers;
        $this->name      = $name;
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

        if ($item instanceof MethodModel) {
            $accept = true;
            if ($this->name) {
                if ($this->name !== $item->getShortName()) {
                    $accept = false;
                }
            }
            if ($this->modifiers
                && !array_intersect($item->getAttribute('modifiers'), $this->modifiers)
            ) {
                $accept = false;
            }
        }
        return $accept;
    }
}
