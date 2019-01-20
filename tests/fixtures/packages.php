<?php

declare(strict_types=1);

/** Global (empty) namespace */
namespace {
    class MyGlobalClass
    {
        public function foo($field)
        {
            $this->$field = new stdClass;
        }
    }
}
