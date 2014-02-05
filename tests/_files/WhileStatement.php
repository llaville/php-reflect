<?php

$i = 1;
while ($i < 10)
    echo $i++;

while ($i < 20) {
    echo PHP_EOL;
    echo $i++;
}

while ($i < 30) :
    echo PHP_EOL;
    printf ('* %d *', $i++);
endwhile;
