<?php
// Declare the interface 'iTemplate'
interface iTemplate
{
    public function setVariable($name, $var);
    public function
        getHtml($template);
}

interface iA
{
    public function Foo();
}

interface iB extends iA
{
    public function baz(Baz $baz);
}

/** short desc for class that implement a unique interface */
class Foo implements iB
{
    public function Foo() {
    }

    private function FooBaz() {

    }

    final public function baz(Baz $baz)
    {
    }
}

// short desc
Abstract class AbstractClass {
    /** static meth: */
    public static   function lambdaMethod() {}

    /** abst meth: */
    public  abstract function abstractMethod();
}

class MyDestructableClass {
    function __construct() {
        print "In constructor\n";
        $this->name = "MyDestructableClass";
    }

    function __destruct() {
        print "Destroying " . $this->name . "\n";
    }

    function dump() {
        print "Dump content of " . $this->name . "\n";

    }
}

class Bar
{
    const ONE = "Number one";
    const TWO = "Number two";
        
    function myfunction ( stdClass $param = NULL, $otherparam = TRUE ) {
    }

    protected function otherfunction( Baz $baz, $param )
    {
    }

}

class IteratorClass implements Iterator {
    public function __construct() { }
    public function key() { }
    public function current() { }
    function next() { }
    function valid() { }
    function rewind() { }
}
class DerivedClass extends IteratorClass { }

class NotCloneable {
    public $var1;
    
    private function __clone() {
    }
}
class Cloneable {
    public $var1;
}

final class TestFinalClass { }
