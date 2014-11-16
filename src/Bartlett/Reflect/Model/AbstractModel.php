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
    protected $calls;

    /**
     * Base Model class constructor
     */
    public function __construct($attributes)
    {
        $struct = array(
            'docComment' => null,
            'startLine'  => 0,
            'endLine'    => 0,
            'file'       => null,
            'extension'  => 'user',
        );
        $this->struct = array_merge($struct, $attributes);
        $this->calls  = array();
    }

    /**
     * Sets the file path where this model is defined
     *
     * @param string $path
     *
     * @return self for fluent interface
     */
    public function setFile($path)
    {
        $this->struct['file'] = $path;
        return $this;
    }

    /**
     * Increments number of element uses
     *
     * @param array $attributes Call stack (file / line)
     *
     * @return self for fluent interface
     */
    public function incCalls(array $attributes = null)
    {
        if (empty($attributes)) {
            $call = array(
                'file' => $this->struct['file'],
                'line' => $this->struct['startLine']
            );
        } else {
            $call = array(
                'file' => $attributes['file'],
                'line' => $attributes['startLine']
            );
        }
        $this->calls[] = $call;
        return $this;
    }

    /**
     * Returns number of current element uses
     *
     * @param bool $count (optional) if TRUE return only the number of call,
     *                    otherwise return the full stack (file/line) of calls.
     *
     * @return mixed
     */
    public function getCalls($count = true)
    {
        if ($count === true) {
            $calls = count($this->calls);
        } else {
            $calls = $this->calls;
        }
        return $calls;
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
