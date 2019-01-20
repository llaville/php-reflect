<?php

declare(strict_types=1);

class SimpleClass
{
    var $debug = false;

    public $var2 = 7;

    private static $var3 = 'foo';

    protected $var4 = myConstant;
    
    public $var5 = array();

    /** This is allowed only in PHP 5.3.0 and later. */
    public $var6 = <<<'EOD'
hello world
EOD;
}
