<?php

namespace glob;

interface iB
{
    public function baz(Baz $baz);
}

class Foo
{
    /**
     * @param stdClass $param
     * @param mixed    $otherparam
     */
    function myfunction ( stdClass $param = NULL, $otherparam = TRUE ) {
    }

    function otherfunction( Baz $baz, $param )
    {
    }

}

function singleFunction ( Array $someparam, stdClass $somethingelse, $lastone = NULL )
{
}

function myprocess ( $param, &$myresult )
{
    return 0;
}

namespace nemo;

function nobody() {
}

$greet = function($name) {
    printf("Hello %s%s", $name, PHP_EOL);
};

namespace A\B;

class C { }
function Bar() {}

namespace Other\Space;

class Extender extends \SplObjectStorage {}

namespace MyProject;

const CONNECT_OK = 1;
class Connection {
    public function connect() {}
}
function connect() { /* ... */  }

namespace AnotherProject;

const CONNECT_OK = 1;
class Connection { /* ... */ }
function connect() { /* ... */  }

namespace Doctrine\Common\Cache;

// caching features
use \Memcache;
use My\Full\Classname as Another;

namespace Phing;

class ComposerTask extends \Task
{
}
