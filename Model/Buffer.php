<?php

namespace Model;

final class Buffer {

    protected static $_instance = null;
    private $_header   = array("<?php\n");
    private $_class    = null;
    private $_property = null;
    private $_func     = null;
    private $_toArray  = null;
    private $_end      = "}\n";

    public function __construct() {}

    /**
     * 单例
     *
     * @return \Model\Buffer
     */
    public static function getInstance() {
        if (!self::$_instance instanceof self) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }

    /**
     * 清除重置
     *
     * @return boolean
     */
    public function clearAll(){
        $this->_header   = array("<?php\n");
        $this->_class    = null;
        $this->_property = null;
        $this->_func     = null;
        $this->_toArray  = null;

        return true;
    }

    /**
     * Header
     *
     * @param string $header
     * @return \Model\Buffer
     */
    public function pushHeader($header){
        $this->_header[] = (string)$header;

        return $this;
    }

    /**
     * Header
     *
     * @return string
     */
    public function pullHeader(){
        return implode("\n", $this->_header);
    }

    /**
     * 类行（only one）
     *
     * @param string $class
     * @return \Model\Buffer
     */
    public function pushClass($class){
        $this->_class = (string)$class;

        return $this;
    }

    /**
     * 类行（only one）
     *
     * @return string
     */
    public function pullClass(){
        return $this->_class;
    }

    /**
     * 属性
     *
     * @param string $property
     * @return \Model\Buffer
     */
    public function pushProperty($property){
        $this->_property[] = (string)$property;

        return $this;
    }

    /**
     * 属性
     *
     * @return string
     */
    public function pullProperty(){
        return implode("\n", $this->_property);
    }

    /**
     * 方法
     *
     * @param string $func
     * @return \Model\Buffer
     */
    public function pushFunc($func){
        $this->_func[] = (string)$func;

        return $this;
    }

    /**
     * 方法
     *
     * @return string
     */
    public function pullFunc(){
        return implode("\n", $this->_func);
    }

    /**
     * toArray (only one)
     *
     * @param string $toArray
     * @return \Model\Buffer
     */
    public function pushToArray($toArray){
        $this->_toArray = (string)$toArray;

        return $this;
    }

    /**
     * toArray (only one)
     *
     * @return string
     */
    public function pullToArray(){
        return $this->_toArray;
    }

    /**
     * PHP file end
     *
     * @return string
     */
    public function pullEnd(){
        return $this->_end;
    }

}
