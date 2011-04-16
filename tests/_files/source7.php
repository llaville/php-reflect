<?php
$key = 'vname';

$GLOBALS['_PEAR_PACKAGEUPDATE_ERRORS'] = 1;

// 2 user globals
global 
    $init, $log;

global $HTTP_POST_VARS;
    
echo $HTTP_POST_VARS
    ['vname'];
echo $HTTP_POST_VARS[$key];
    
echo $_POST['vname'];

function Bar()
{
    // init Bar function
    global $init;
}
