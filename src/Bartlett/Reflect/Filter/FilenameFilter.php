<?php

namespace Bartlett\Reflect\Filter;

class FilenameFilter extends \FilterIterator
{
    private $filenameFilter;

    public function __construct(\Iterator $iterator , $filename)
    {
        parent::__construct($iterator);

        $this->filenameFilter = $filename;
    }

    public function accept()
    {
        $item = $this->getInnerIterator()->current();

        if (strcasecmp($item->getFileName(), $this->filenameFilter) == 0) {
            return true;
        }
        return false;
    }

}