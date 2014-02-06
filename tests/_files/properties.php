<?php
class SimpleClass
{
    $var1 = 'PHP4 public property';

    public $var2 = 7;

    private static $var3 = 'foo';

    protected $var4 = myConstant;
    
    public $var5 = array();

    /** This is allowed only in PHP 5.3.0 and later. */
    public $var6 = <<<'EOD'
hello world
EOD;
}
