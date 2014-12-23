<?php
/**
 * Class member access on instantiation.
 *
 * @since PHP 5.4.0RC1
 */
class Foo
{
    function __construct($name = null)
    {
        $this->name = isset($name) ? $name : 'World';
    }

    function bar()
    {
        echo 'Hello ' . $this->name . PHP_EOL;
    }
}

(new Foo)->bar();

(new Foo ('Baz') )->bar();

$a = 'Foo';
(new $a)->bar();

(new $a ('Baz'))->bar();
