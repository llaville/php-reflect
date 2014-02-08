<?php

function exception_error_handler($errno, $errstr, $errfile, $errline ) {
    throw new ErrorException($errstr, $errno, 0, $errfile, $errline);
}
set_error_handler("exception_error_handler");

try {
    strpos();
} 
catch (Exception $e) {
    print $e->getMessage(), 
        PHP_EOL;
}
finally {
    print 'After try/catch blocks, and before to continue on next statement', 
        PHP_EOL;
}
