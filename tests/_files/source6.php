<?php
namespace MyProject {

const CONNECT_OK = 1;
class Connection {
    public function connect() {}
}
function connect() { /* ... */  }
}

namespace AnotherProject {

const CONNECT_OK = 1;
class Connection { /* ... */ }
function connect() { /* ... */  }
}
