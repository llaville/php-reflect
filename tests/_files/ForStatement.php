<?php

for ($i = 1; $i < 10; $i++)
    echo $i;

echo PHP_EOL;

for ($i = 1; $i < 10; $i++) {
    echo $i,
        PHP_EOL;
}

for ($i = 1; $i < 10; $i++) :
    printf('* %d *%s', $i, PHP_EOL);
endfor;
