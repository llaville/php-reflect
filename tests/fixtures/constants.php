<?php
namespace sandbox;
/** connection semaphore */
const CONNECT_OK = 1;

error_log('Magic constants in ' . __FILE__);

class Connection {
    const DSN = 'protocol://';
    public function connect()
    {
        error_log(__CLASS__ . ' / ' . __METHOD__ . ' @' . __LINE__);
    }
}
function connect() {
    /* ... */
    error_log(__FUNCTION__ . '@' . __LINE__);

}

error_log(__NAMESPACE__ . ' in ' . __DIR__);

trait PeanutButter {
    function traitName() {echo __TRAIT__;}
}

const TWO = ONE +1;

define ('FOO', 'something');
