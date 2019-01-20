<?php

declare(strict_types=1);

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

function myprocess (callable $param, &$myresult, ...$opt  )
{
    return 0;
}
