<?php
namespace { // Builder::enterNode : empty namespace
    class MyGlobalClass
    {
        public function foo($field)
        {
            $this->$field = new stdClass; // Builder::enterNode & Builder::parseMethodCall : $this->$field = new
        }
    }
}
