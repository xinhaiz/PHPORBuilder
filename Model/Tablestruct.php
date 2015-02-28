<?php

namespace Model;

final class Tablestruct {

    protected $_table_schema    = null;
    protected $_table_name      = null;
    protected $_table_type      = null;
    protected $_table_comment   = null;
    protected $_table_collation = null;
    protected $_engine          = null;

    public function __construct(array $table) {
        foreach ($table as $key => $value) {
            $method = 'set' . ucfirst(strtolower($key));

            if (method_exists($this, $method)) {
                $this->{$method}($value);
            }
        }
    }

    /**
     * 列名
     *
     * @param string $table_name
     * @return \Model\Tables
     */
    public function setTable_name($table_name) {
        $this->_table_name = $table_name;

        return $this;
    }

    /**
     * 列名
     *
     * @return string
     */
    public function getTable_name() {
        return $this->_table_name;
    }

    /**
     * 列属性类型
     *
     * @param string $table_type
     * @return \Model\Tables
     */
    public function setTable_type($table_type) {
        $this->_table_type = $table_type;

        return $this;
    }

    /**
     * 列属性类型
     *
     * @return string
     */
    public function getTable_type() {
        return $this->_table_type;
    }

    /**
     * 所属库
     *
     * @param string $table_schema
     * @return \Model\Tables
     */
    public function setTable_schema($table_schema) {
        $this->_table_schema = $table_schema;

        return $this;
    }

    /**
     * 所属库
     *
     * @return string
     */
    public function getTable_schema() {
        return $this->_table_schema;
    }

    /**
     * 注释
     *
     * @param string $table_comment
     * @return \Model\Tables
     */
    public function setTable_comment($table_comment) {
        $this->_table_comment = $table_comment;

        return $this;
    }

    /**
     * 注释
     *
     * @return string
     */
    public function getTable_comment() {
        $comment = $this->_table_comment;

        if(empty($comment)){
            $comment = ucfirst($this->_table_name);
        }

        return $comment;
    }

    /**
     * 编码
     *
     * @param string $table_collation
     * @return \Model\Tables
     */
    public function setTable_collation($table_collation) {
        $this->_table_collation = $table_collation;

        return $this;
    }

    /**
     * 编码
     *
     * @return string
     */
    public function getTable_collation() {
        return $this->_table_collation;
    }

    /**
     * 数据引擎
     *
     * @param string $engine
     * @return \Model\Tables
     */
    public function setEngine($engine) {
        $this->_engine = $engine;

        return $this;
    }

    /**
     * 数据引擎
     *
     * @return string
     */
    public function getEngine() {
        return $this->_engine;
    }
}
