<?php
trait T1
{
    public function doSomething() 
    {
        $this->var = 42;
        echo $this->var;
    }
}

trait T2
{
    public function doSomethingElseButDoItWell() 
    {
        $this->setVar(43);
        echo $this->getVar();
    }
}

class C
{
    use T1, T2;
  
    private $var;
  
    private function getVar() 
    {
        return $this->var;
    }
    
    private function setVar($value) 
    {
        $this->var = $value;
    }
}

$c = new C;
$c->doSomething();
$c->doSomethingElseButDoItWell();
