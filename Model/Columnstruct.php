<?php

namespace Model;

final class Columnstruct {

    protected $_column_name    = null;
    protected $_column_type    = null;
    protected $_column_default = null;
    protected $_column_comment = null;
    protected $_column_key     = null;
    protected $_extra          = null;
    protected $_data_type      = 'string';

    public function __construct(array $column) {
        foreach ($column as $key => $value) {
            $method = 'set' . ucfirst(strtolower($key));

            if (method_exists($this, $method)) {
                $this->{$method}($value);
            }
        }
    }

    /**
     * 列名
     *
     * @param string $column_name
     * @return \Model\Columns
     */
    public function setColumn_name($column_name) {
        $this->_column_name = (string)$column_name;

        return $this;
    }

    /**
     * 列名
     *
     * @return string
     */
    public function getColumn_name() {
        return $this->_column_name;
    }

    /**
     * 列属性类型
     *
     * @param string $column_type
     * @return \Model\Columns
     */
    public function setColumn_type($column_type) {
        $this->_column_type = strtolower($column_type);

        return $this;
    }

    /**
     * 列属性类型
     *
     * @return string
     */
    public function getColumn_type() {
        return $this->_column_type;
    }

    /**
     * 默认值
     *
     * @param string $column_default
     * @return \Model\Columns
     */
    public function setColumn_default($column_default) {
        $this->_column_default = $column_default;

        return $this;
    }

    /**
     * 默认值
     *
     * @return string
     */
    public function getColumn_default() {
        return $this->_column_default;
    }

    /**
     * 注释
     *
     * @param string $column_comment
     * @return \Model\Columns
     */
    public function setColumn_comment($column_comment) {
        $this->_column_comment = $column_comment;

        return $this;
    }

    /**
     * 注释
     *
     * @return string
     */
    public function getColumn_comment() {
        $comment = $this->_column_comment;

        if(empty($comment)){
            $comment = ucfirst($this->_column_name);
        }

        return $comment;
    }

    /**
     * 主键
     *
     * @param string $column_key
     * @return \Model\Columns
     */
    public function setColumn_key($column_key) {
        $this->_column_key = $column_key;

        return $this;
    }

    /**
     * 主键
     *
     * @return string
     */
    public function getColumn_key() {
        return $this->_column_key;
    }

    /**
     * 递增类型
     *
     * @param string $extra
     * @return \Model\Columns
     */
    public function setExtra($extra) {
        $this->_extra = $extra;

        return $this;
    }

    /**
     * 递增类型
     *
     * @return string
     */
    public function getExtra() {
        return $this->_extra;
    }

    /**
     * 数据类型
     *
     * @param string $data_type
     * @return \Model\Columns
     */
    public function setData_type($data_type) {
        if(!empty($data_type)) {
            $this->_data_type = $data_type;
        }

        return $this;
    }

    /**
     * 数据类型
     *
     * @return string
     */
    public function getData_type() {
        return $this->_data_type;
    }
}
