<?php

namespace Lib;

class Exception extends \Exception {

    private $_message  = null;
    private $_code     = null;
    private $_previous = null;

    public function __construct($message, $code = null, $previous = null) {
        $this->_message  = (string) $message;
        $this->_code     = (int) $code;
        $this->_previous = $previous;

        \set_exception_handler(array($this, 'errorHandle'));
    }

    public function errorHandle(){
        echo '[error] ' . $this->_message . "\n";
    }
}
