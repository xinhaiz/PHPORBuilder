<?php

namespace Lib;

final class DbConfig {
    protected static $_instance = null;

    private function __construct() {}
    private function __clone() {}
    private function __sleep() {}

    private $_host     = null;
    private $_username = null;
    private $_passwd   = null;
    private $_port     = null;
    private $_dbname   = null;
    private $_table    = null;
    private $_options  = array();

    /**
     * 单例
     *
     * @return \Lib\DbConfig
     */
    public static function getInstance() {
        if (!self::$_instance instanceof self) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }

     /**
     * 连接数据库用户名
     *
     * @param string $username
     * @return \Lib\DbConfig
     */
    public function setUsername($username){
        $this->_username = (string)$username;

        return $this;
    }

    /**
     * 连接数据库用户名
     *
     * @return string
     */
    public function getUsername(){
        return $this->_username;
    }

    /**
     * 连接数据库名
     *
     * @param string $dbname
     * @return \Lib\DbConfig
     */
    public function setDbname($dbname){
        $this->_dbname = (string)$dbname;

        return $this;
    }

    /**
     * 连接数据库用户名
     *
     * @return string
     */
    public function getDbname(){
        return $this->_dbname;
    }

    /**
     * 需要操作的表名
     *
     * @param string $table
     * @return \Lib\DbConfig
     */
    public function setTable($table){
        $this->_table = explode(',', $table);

        return $this;
    }

    /**
     * 需要操作的表名
     *
     * @return array()
     */
    public function getTable(){
        return $this->_table;
    }

    /**
     * 连接数据库用户密码
     *
     * @param string $passwd
     * @return \Lib\DbConfig
     */
    public function setPasswd($passwd){
        $this->_passwd = (string)$passwd;

        return $this;
    }

    /**
     * 连接数据库用户密码
     *
     * @return string
     */
    public function getPasswd(){
        return $this->_passwd;
    }

    /**
     * 连接数据库主机
     *
     * @param string $host
     * @return \Lib\DbConfig
     */
    public function setHost($host){
        $this->_host = (string)$host;

        return $this;
    }

    /**
     * 连接数据库主机
     *
     * @return string
     */
    public function getHost(){
        return $this->_host;
    }

    /**
     * 连接数据库主机端口
     *
     * @param int $port
     * @return \Lib\DbConfig
     */
    public function setPort($port){
        $this->_port = (int)$port;

        return $this;
    }

    /**
     * 连接数据库主机端口
     *
     * @return int
     */
    public function getPort(){
        return $this->_port;
    }

    /**
     * 连接数据库驱动选项
     *
     * @param int $options
     * @return \Lib\DbConfig
     */
    public function setOptions($options){
        if(!is_array($options)) {
            $options = array($options);
        }

        $this->_options = (array)$options;

        return $this;
    }

    /**
     * 连接数据库驱动选项
     *
     * @return int
     */
    public function getOptions(){
        return $this->_options;
    }

    /**
     * MySQL DSN
     *
     * @return string
     */
    public function getDsn() {
        return 'mysql:host=' . $this->_host . ':' . $this->_port . ';dbname=' . $this->_dbname;
    }
}

