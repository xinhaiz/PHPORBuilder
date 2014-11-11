<?php

namespace Model;

final class File {

    protected static $_instance = null;
    private $_tableName = null;
    private $_file      = null;

    public function __construct() {}

    /**
     * 单例
     *
     * @return \Lib\Modelfile
     */
    public static function getInstance() {
        if (!self::$_instance instanceof self) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }

    /**
     * @param string $tableName
     * @return \Lib\Modelfile
     */
    public function setTableName($tableName) {
        $this->_tableName = (string) (strtolower($tableName));
        $this->_file = null;

        return $this;
    }

    /**
     * 重置
     */
    public function reset(){
        $this->_tableName = null;
        $this->_file      = null;
    }

    /**
     * 当前文件名
     *
     * @return string
     */
    public function getFile() {
        return $this->_file;
    }

    /**
     * model  contents build
     *
     * @return boolean
     */
    public function build() {
        $this->touchFile();
        file_put_contents($this->getFile(), \Model\Content::getInstance()->toString());
        \Lib\State::notice('File [' . $this->_file . '], create successed');
    }

    /**
     * model file create
     *
     * @return boolean
     */
    public function touchFile(){
        $options = \Lib\Options::getInstance();
        $dir     = $options->getFilepath();

        if (!file_exists($dir)) {
            mkdir($dir, 0755, true);
        }

        $this->_file = $dir . DS . ucfirst($this->_tableName) . $options->getExt();

        return true;
    }

}
