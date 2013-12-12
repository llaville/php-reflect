<?php
/**
 * IncludeModel represents an include definition.
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

use Bartlett\Reflect\Model\AbstractModel;
use Bartlett\Reflect\Model\Visitable;

/**
 * The IncludeModel class reports information about an include.
 *
 * @category PHP
 * @package  PHP_Reflect
 * @author   Laurent Laville <pear@laurent-laville.org>
 * @license  http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version  Release: @package_version@
 * @link     http://php5.laurent-laville.org/reflect/
 * @since    Class available since Release 2.0.0RC1
 */
class IncludeModel extends AbstractModel implements Visitable
{
    private $filepath;

    /**
     * Constructs a new IncludeModel instance.
     *
     * @param string $filepath The full path to the file to include
     */
    public function __construct($filepath)
    {
        parent::__construct();

        $this->filepath = $filepath;
    }

    /**
     * Gets the corresponding include type.
     * Either include, include_one, require, require_once.
     *
     * @return string
     */
    public function getType()
    {
        return $this->struct['type'];
    }

    /**
     * Gets file path of the include.
     *
     * @return string
     */
    public function getFilePath()
    {
        return $this->filepath;
    }

    /**
     * Get a Doc comment from an include.
     *
     * @return string
     */
    public function getDocComment()
    {
        return $this->struct['docblock'];
    }

    /**
     * Gets the starting line number of the include.
     *
     * @return int
     * @see    IncludeModel::getEndLine()
     */
    public function getStartLine()
    {
        return $this->struct['startLine'];
    }

    /**
     * Gets the ending line number of the include.
     *
     * @return int
     * @see    IncludeModel::getStartLine()
     */
    public function getEndLine()
    {
        return $this->struct['endLine'];
    }

    /**
     * Gets the file name from an include.
     *
     * @return string
     */
    public function getFileName()
    {
        return $this->struct['file'];
    }

    /**
     * Checks whether this include is a require.
     *
     * @return bool TRUE if it's a require, FALSE otherwise
     */
    public function isRequire()
    {
        return ($this->getType() === 'require');
    }

    /**
     * Checks whether this include is a require_once.
     *
     * @return bool TRUE if it's a require_once, FALSE otherwise
     */
    public function isRequireOnce()
    {
        return ($this->getType() === 'require_once');
    }

    /**
     * Checks whether this include is a include.
     *
     * @return bool TRUE if it's a include, FALSE otherwise
     */
    public function isInclude()
    {
        return ($this->getType() === 'include');
    }

    /**
     * Checks whether this include is a include_once.
     *
     * @return bool TRUE if it's a include_once, FALSE otherwise
     */
    public function isIncludeOnce()
    {
        return ($this->getType() === 'include_once');
    }

    /**
     * Returns the string representation of the IncludeModel object.
     *
     * @return string
     */
    public function __toString()
    {
        $eol = "\n";

        return sprintf(
            'Include [ %s ] { %s }%s',
            $this->getType(),
            $this->getFilePath(),
            $eol
        );
    }
}
