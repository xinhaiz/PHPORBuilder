<?php

namespace Config;

abstract class ConfigAbstract {

    private $_config = array();

    final public function __construct() {
        $this->_config = $this->init();
    }

    public function __set($name, $value){
        if((isset($this->$name))){
            $this->$name = $value;
        }
    }

    public function __get($name) {
        return (isset($this->$name)) ? $this->$name : null;
    }

    public function get($key = null, $default = null){
        return (isset($this->_config[$key])) ? $this->_config[$key] : ($default === true ? $this->_config : $default);
    }

    /**
     * 获取所有配置
     *
     * @return array
     */
    public function getConfig() {
        return $this->_config;
    }

    abstract public function init();
}

