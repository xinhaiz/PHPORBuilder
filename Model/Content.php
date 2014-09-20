<?php

namespace Model;

final class Content {

    protected static $_instance = null;
    private $_columns           = null;
    private $_tableName         = null;
    private $_tab               = null;

    /**
     * 未在配置内的将默认为 string
     *
     * @var array
     */
    private $_typeArr = array(
        'int'       => 'int',
        'tinyint'   => 'int',
        'smallint'  => 'int',
        'mediumint' => 'int',
        'bigint'    => 'int',
        'double'    => 'float',
        'float'     => 'float',
        'decimal'   => 'float',
    );

    public function __construct() {
        $this->_tab = \Lib\Options::getInstance()->getTab();
    }

    /**
     * 单例
     *
     * @return \Lib\Modelcontents
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
        $this->_tableName = (string)(strtolower($tableName));

        return $this;
    }

    /**
     * @param string $columns
     * @return \Lib\Modelcontents
     */
    public function setColumns($columns) {
        $this->_columns = (array)$columns;

        return $this;
    }

    /**
     * 重置
     */
    public function reset() {
        $this->_columns = null;
    }

    public function build() {
        $columns = $this->_columns;

        if (empty($columns)) {
            throw new \Lib\Exception('not found any columns');
        }

        $buffer  = \Model\Buffer::getInstance();
        $build   = \Model\Build::getInstance();
        $options = \Lib\Options::getInstance();
        $status  = \Lib\Status::getInstance();
        $items   = array();

        $namespace = ltrim(trim($options->getNamespace(), '\\'), '_');

        if (!empty($namespace) && $options->getOnNamespace() === true) {
            $buffer->pushHeader('namespace ' . $namespace . ';');
            $buffer->pushHeader(''); // 增加一空行,处理代码格式
            $namespace = '';
        }

        $buffer->pushClass($build->toClass($namespace . ucfirst($this->_tableName)));

        foreach ($columns as $column) {
            $struct  = new \Model\Columnstruct($column);
            $colName = $struct->getColumn_name();

            if (empty($colName)) {
                continue;
            }

            $result = $this->parseColumn($struct);
            ($result === false) ? $status->warning('Parse failed') : $status->notic('Parsed [' . $colName . ']...  done');

            $items[] = $colName;
        }

        $buffer->pushToArray($build->toComment(array('Return a array of model properties', '', '@return array')));
        $buffer->pushToArray($build->toToArray($items));
    }

    /**
     * 解析 columns
     *
     * @param \Model\Columnstruct $struct
     */
    private function parseColumn(\Model\Columnstruct $struct) {
        $name = $struct->getColumn_name();

        if (\Lib\Options::getInstance()->getColunderline() === false) {
            $name = trim(str_replace('_', '', $name));
        }

        $commentArr = $this->buildCommonComments($struct);

        $this->buildPropertyContent($struct, $commentArr);
        $this->buildSetfuncContent($struct, $commentArr);
        $this->buildGetfuncContent($struct, $commentArr);

        return true;
    }

    /**
     * 转成字符串按顺序合并
     *
     * @return string
     */
    public function toString() {
        $buffer  = \Model\Buffer::getInstance();
        $status  = \Lib\Status::getInstance();
        $options = \Lib\Options::getInstance();

        $items   = array();
        $status->notic('Building php head');
        $items[] = $buffer->pullHeader();

        $namespace = ($options->getOnNamespace() === false) ? $options->getNamespace() : '';

        $status->notic('Building class [' . $namespace . sprintf($options->getModelType(), ucfirst($this->_tableName)) . ']');
        $items[] = $buffer->pullClass();

        $status->notic('Building class property');
        $items[] = $buffer->pullProperty();

        $status->notic('Building class function');
        $items[] = $buffer->pullFunc();
        $items[] = $buffer->pullToArray();

        $items[] = $buffer->pullEnd();

        $buffer->clearAll();

        return implode("\n", $items);
    }

    /**
     * 创建属性内容
     *
     * @param \Model\Columnstruct $struct
     * @param array $commentArr
     */
    protected function buildPropertyContent(\Model\Columnstruct $struct, array $commentArr) {
        $build  = \Model\Build::getInstance();
        $buffer = \Model\Buffer::getInstance();

        $commentArr[] = '@var ' . $this->getDateType($struct->getData_type());

        $buffer->pushProperty($build->toComment($commentArr));
        $buffer->pushProperty($build->toProperty('_' . $struct->getColumn_name(), $struct->getColumn_default()));
    }

    /**
     * 创建set方法内容
     *
     * @param \Model\Columnstruct $struct
     * @param array $commentArr
     */
    protected function buildSetfuncContent(\Model\Columnstruct $struct, array $commentArr) {
        $build    = \Model\Build::getInstance();
        $buffer   = \Model\Buffer::getInstance();
        $options  = \Lib\Options::getInstance();
        $name     = $struct->getColumn_name();
        $dataType = $this->getDateType($struct->getData_type());

        $commentArr[] = '@param ' . $dataType . ' $' . $name;
        $commentArr[] = '@return ' . ltrim($options->getNamespace(), '_')
                . sprintf($options->getModelType(), ucfirst($this->_tableName));

        $buffer->pushFunc($build->toComment($commentArr));
        $buffer->pushFunc($build->toSetFunc($name, array(
            str_repeat($this->_tab, 2) . '$this->_' . $name . ' = (' . $dataType . ')$' . $name . ';',
            '',
            str_repeat($this->_tab, 2) . 'return $this;'
        ), $name));
    }

    /**
     * 创建get方法内容
     *
     * @param \Model\Columnstruct $struct
     * @param array $commentArr
     */
    protected function buildGetfuncContent(\Model\Columnstruct $struct, array $commentArr) {
        $build  = \Model\Build::getInstance();
        $buffer = \Model\Buffer::getInstance();
        $name   = $struct->getColumn_name();

        $commentArr[] = '@return ' . $this->getDateType($struct->getData_type());

        $buffer->pushFunc($build->toComment($commentArr));
        $buffer->pushFunc($build->toGetFunc($name, array(str_repeat($this->_tab, 2) . 'return $this->_' . $name . ';')));
    }

    /**
     * 创建公共注释部分
     *
     * @param \Model\Columnstruct $struct
     * @return array
     */
    protected function buildCommonComments(\Model\Columnstruct $struct) {
        $extra      = $struct->getExtra();
        $key        = $struct->getColumn_key();
        $default    = $struct->getColumn_default();
        $commentArr = array();

        $commentArr[] = $struct->getColumn_comment();
        $commentArr[] = '';
        $commentArr[] = 'Column Type: ' . $struct->getColumn_type();

        if (mb_strlen($default) > 0) {
            $commentArr[] = 'Default: ' . $struct->getColumn_default();
        }

        if (!empty($extra)) {
            $commentArr[] = $extra;
        }

        if (!empty($key)) {
            $commentArr[] = $key;
        }

        $commentArr[] = '';

        return $commentArr;
    }

    /**
     * 数据类型转换
     *
     * @param string $type
     * @return string
     */
    protected function getDateType($type) {
        return (isset($this->_typeArr[$type])) ? $this->_typeArr[$type] : 'string';
    }

}
