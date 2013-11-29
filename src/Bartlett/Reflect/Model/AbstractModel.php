<?php

namespace Bartlett\Reflect\Model;

use Bartlett\Reflect\Visitor\VisitorInterface;

/**
 * AbstractModel
 * is used to declare base accept operation if no element implementation
 * is provided.
 */
abstract class AbstractModel
{
    protected $name;
    protected $struct;

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
     * Updates the content of a Model object.
     *
     * @param array $data New data to merge with a previous content.
     *
     * @return void
     */
    public function update($data)
    {
        if (isset($data['uses'])) {
            $data['uses'] += $this->getUses();
        }
        $this->struct = array_merge($this->struct, $data);
    }

    /**
     *
     * @param  $visitor Concrete visitor
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
