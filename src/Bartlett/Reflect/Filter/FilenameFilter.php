<?php
/**
 * Filter values based on the file name.
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

/**
 * Filter out unwanted values, in parse results, based on file names.
 *
 * @category PHP
 * @package  PHP_Reflect
 * @author   Laurent Laville <pear@laurent-laville.org>
 * @license  http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version  Release: @package_version@
 * @link     http://php5.laurent-laville.org/reflect/
 * @since    Class available since Release 2.0.0RC1
 */
class FilenameFilter extends \FilterIterator
{
    /**
     * Filename that will restrict perimeter of results
     * @var string
     */
    private $filenameFilter;

    /**
     * Constructs a new FilterIterator, which consists of a passed in iterator with filters applied to it.
     *
     * @param \Iterator $iterator The iterator that is being filtered.
     * @param string    $filename The filename that you want to restrict perimeter.
     *
     * @return FilenameFilter
     */
    public function __construct(\Iterator $iterator, $filename)
    {
        parent::__construct($iterator);

        $this->filenameFilter = $filename;
    }

    /**
     * Checks whether the current element of the iterator is acceptable
     * through this filter.
     *
     * @return bool TRUE if the current element is acceptable, otherwise FALSE.
     */
    public function accept()
    {
        $item = $this->getInnerIterator()->current();

        if (strcasecmp($item->getFileName(), $this->filenameFilter) == 0) {
            return true;
        }
        return false;
    }
}
