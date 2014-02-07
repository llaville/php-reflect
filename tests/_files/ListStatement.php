<?php

$info = array('coffee', 'brown', 'caffeine');

list (
    $drink, 
    $color, 
    $power
) = $info;
echo "$drink is $color and $power makes it special.", PHP_EOL;

list ($drink, , $power) 
    = $info;
echo "$drink has $power.", PHP_EOL;
