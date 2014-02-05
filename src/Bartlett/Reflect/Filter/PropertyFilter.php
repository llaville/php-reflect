<?php
/**
 * Filter values based on property model.
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

use Bartlett\Reflect\Model\PropertyModel;

/**
 * Filter out unwanted values, in parse results, based on property model.
 *
 * @category PHP
 * @package  PHP_Reflect
 * @author   Laurent Laville <pear@laurent-laville.org>
 * @license  http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version  Release: @package_version@
 * @link     http://php5.laurent-laville.org/reflect/
 * @since    Class available since Release 2.0.0RC2
 */
class PropertyFilter extends \FilterIterator implements \Countable
{
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

        if ($item instanceof PropertyModel) {
            $accept = true;
        }
        return $accept;
    }
}
