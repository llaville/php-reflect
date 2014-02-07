<?php

if (isset($var)) {
    echo "This var is set so I will print.";
}

$a = "test";
$b = "anothertest";

var_dump(isset($a));      // TRUE
var_dump(
    isset(
        $a, 
        $b
    )
);  // TRUE
