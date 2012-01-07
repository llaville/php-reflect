<?php

function foo(Exception $e, array $context = array('user'))
{
    echo 'Caught exception: ', $e->getMessage(), 'in ', $context[0], ' context', PHP_EOL;
}

try {
    $error = 'Always throw this error';
    throw new Exception($error);

} catch (Exception $e)
    foo($e, array('debug'));
}
