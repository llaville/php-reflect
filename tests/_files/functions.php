<?php

interface iB
{
    public function baz(Baz $baz);
}

class Foo
{
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
