<?php
/**
 * A base class for all Model objects.
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

use Bartlett\Reflect\Visitor\VisitorInterface;

/**
 * AbstractModel is used to declare base accept operation
 * if no element implementation is provided.
 *
 * @category PHP
 * @package  PHP_Reflect
 * @author   Laurent Laville <pear@laurent-laville.org>
 * @license  http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version  Release: @package_version@
 * @link     http://php5.laurent-laville.org/reflect/
 * @since    Class available since Release 2.0.0RC1
 */
abstract class AbstractModel
{
    protected $name;
    protected $struct;

    /**
     * Model class constructor
     */
    public function __construct()
    {
        $this->struct = array(
            'extension' => 'user',
            'uses'      => 0,
        );
    }

    /**
     * Returns number of current element uses
     *
     * @return int
     */
    public function getUses()
    {
        return $this->struct['uses'];
    }

    /**
     * Implement Visitor Design Pattern.
     *
     * @param VisitorInterface $visitor Concrete visitor
     *
     * @return void
     */
    public function accept(VisitorInterface $visitor)
    {
        $modelClass = explode('\\', get_class($this));
        $method     = 'visit' . array_pop($modelClass);

        if (method_exists($visitor, $method)) {
            // visit the method and exit
            $visitor->{$method}($this);
            return;
        }

        // if not visit operations is defined, call a default algorithm
        $visitor->defaultVisit($this);
    }
}
