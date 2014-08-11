<?php

namespace Lib;

final class Status {
    protected static $_instance = null;

    private $_view = 1;

    public function __construct() {
        $this->_view = (int)(\Lib\Options::getInstance()->getView());
    }

    /**
     * 单例
     *
     * @return \Lib\Status
     */
    public static function getInstance() {
        if (!self::$_instance instanceof self) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }

    /**
     * @param string $message
     * @param int $level
     */
    public function show($message, $level = 1){
        if($this->_view >= $level){
            echo  '[' . date('Y-m-d H:i:s') . '] ' . (string)$message . "\n";
        }
    }

}
