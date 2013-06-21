<?php
function inverse($x) {
    if (!$x) {
        throw new Exception('Division by zero.');
    }
    return 1/$x;
}

try {
    echo inverse(5), PHP_EOL;
} catch (Exception $e) {
    echo 'Caught exception 1: ',  $e->getMessage(), PHP_EOL;
} finally {
    echo "First finally.", PHP_EOL;
}

try {
    echo inverse(0), PHP_EOL;
} catch (Exception $e) {
    echo 'Caught exception 2: ',  $e->getMessage(), PHP_EOL;
} finally {
    echo "Second finally.", PHP_EOL;
}

// Continue execution
echo "Hello World", PHP_EOL;
