<?php

if ($a > $b)
    echo "a is bigger than b", PHP_EOL;

if ($a > $b)
{
    echo "a is greater than b", PHP_EOL;
} else {
    echo "a is NOT greater than b", PHP_EOL;
}

if ($a < $b) :
    echo "a is lower than b",
        PHP_EOL;
endif;

if ($a == true)
elseif ($a == false) {
    print 'still pending';
} else
print 'other';

if ($a === true)
    print TRUE;
elseif ($a === false)
    print FALSE;
else
    print 'not boolean';
