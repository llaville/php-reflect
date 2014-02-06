<?php

$arr = array(1, 2, 3, 4);
foreach ($arr as &$value) {
    $value = $value * 2;
    echo $value, PHP_EOL;
}

foreach ($arr as $key => $value) {
    echo "Key: $key; Value: $value \n";
}

foreach ($arr as $k => $v) :
    echo "Key: $key; Value: $value \n",
        PHP_EOL;
endforeach;
