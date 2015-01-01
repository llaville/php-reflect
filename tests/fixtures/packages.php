<?php
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
