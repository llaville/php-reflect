<?php
/**
 * DependencyModel represents all external dependencies.
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

/**
 * The DependencyModel class reports information about internal/extension
 * functions, constants, globals.
 *
 * @category PHP
 * @package  PHP_Reflect
 * @author   Laurent Laville <pear@laurent-laville.org>
 * @license  http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version  Release: @package_version@
 * @link     http://php5.laurent-laville.org/reflect/
 * @since    Class available since Release 2.0.0RC2
 */
class DependencyModel extends AbstractModel implements Visitable
{
    /**
     * Constructs a new DependencyModel instance.
     *
     * @param string $name Name of the package or namespace
     */
    public function __construct($qualifiedName, $attributes)
    {
        parent::__construct($attributes);

        $this->name = $qualifiedName;
    }

    public function getName()
    {
        return $this->name;
    }

    /**
     * Gets the file name from a user-defined namespace.
     *
     * @return string
     */
    public function getFileName()
    {
        return $this->struct['file'];
    }

    /**
     * Returns the string representation of the DependencyModel object.
     *
     * @return string
     */
    public function __toString()
    {
        $eol = "\n";
        $str = '';
        // TODO
        return $str;
    }
}
