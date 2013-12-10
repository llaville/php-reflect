<?php

namespace Bartlett\Reflect\Model;

class PackageModel
    extends AbstractModel
    implements Visitable, \IteratorAggregate
{
    protected $elements = array();

    /**
     * Constructs a new PackageModel instance.
     */
    public function __construct($name)
    {
        $this->name = $name;

        $this->struct = array(
            'docblock'  => '',
            'startLine' => 0,
            'endLine'   => 0,
            'file'      => '',
        );
    }

    public function getIterator()
    {
        return new \ArrayIterator($this->elements);
    }

    public function addElement($element)
    {
        $this->elements[] = $element;
    }

    /**
     * Get a Doc comment from a namespace.
     *
     * @return string
     */
    public function getDocComment()
    {
        return $this->struct['docblock'];
    }

    /**
     * Gets the starting line number of the namespace.
     *
     * @return int
     * @see    PackageModel::getEndLine()
     */
    public function getStartLine()
    {
        return $this->struct['startLine'];
    }

    /**
     * Gets the ending line number of the namespace.
     *
     * @return int
     * @see    PackageModel::getStartLine()
     */
    public function getEndLine()
    {
        return $this->struct['endLine'];
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
     * Gets the full name of the namespace.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Gets the short name (alias) of the namespace.
     *
     * @return string
     */
    public function getShortName()
    {
        return $this->name;
    }

    /**
     * Returns the string representation of the PackageModel object.
     *
     * @return string
     */
    public function __toString()
    {
        $eol = "\n";
        $str = '';
        $str .= sprintf(
            'Package [ %s ] {%s',
            $this->getName(),
            $eol
        );

        $str .= sprintf(
            '  @@ %s %d - %d%s',
            $this->getFileName(),
            $this->getStartLine(),
            $this->getEndLine(),
            $eol
        );

        $str .= '}' . $eol;

        return $str;
    }

}
