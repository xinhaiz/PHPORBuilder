<?php

namespace Model;

final class Content {

    protected static $_instance = null;
    private $_columns           = null;
    private $_tableName         = null;
    private $_tab               = null;
    private $_viewLevel         = 3;

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
            $buffer->pushHeader('namespace ' . $namespace . ';' . "\n");
            $namespace = '';
        }

        $buffer->pushClass($build->toClass($namespace . ucfirst($this->_tableName)));

        foreach ($columns as $column) {
            $struct  = new \Model\Columnstruct($column);
            $colName = $struct->getColumn_name();

            if (empty($colName)) {
                continue;
            }

            $status->show('being parsed column [' . $colName . ']', $this->_viewLevel);
            $result  = $this->parseColumn($struct);
            $status->show($result === true ? 'ok' : 'failed', $this->_viewLevel);
            $items[] = $colName;
        }

        $buffer->pushToArray($build->toToArray($items));
    }

    /**
     * 解析 columns
     *
     * @param \Model\Columnstruct $struct
     */
    private function parseColumn(\Model\Columnstruct $struct) {
        $options = \Lib\Options::getInstance();
        $name    = $struct->getColumn_name();

        if ($options->getColunderline() === false) {
            $name = trim(str_replace('_', '', $name));
        }

        $extra      = $struct->getExtra();
        $key        = $struct->getColumn_key();
        $commentArr = array();

        $commentArr[] = $struct->getColumn_comment();
        $commentArr[] = '';
        $commentArr[] = $struct->getColumn_type();

        if (!empty($extra)) {
            $commentArr[] = $extra;
        }

        if (!empty($key)) {
            $commentArr[] = $key;
        }

        $this->buildPropertyContent($commentArr, $struct);
        $this->buildSetfuncContent($commentArr, $struct);
        $this->buildGetfuncContent($commentArr, $struct);

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
        $status->show('building php head', $this->_viewLevel);
        $items[] = $buffer->pullHeader();

        $namespace = ($options->getOnNamespace() === false) ? $options->getNamespace() : '';

        $status->show('building class [' . $namespace . sprintf($options->getModelType(), ucfirst($this->_tableName)) . ']', $this->_viewLevel);
        $items[] = $buffer->pullClass();

        $status->show('building class property', $this->_viewLevel);
        $items[] = $buffer->pullProperty();

        $status->show('building class function', $this->_viewLevel);
        $items[] = $buffer->pullFunc();
        $items[] = $buffer->pullToArray();

        $status->show('ending', $this->_viewLevel);
        $items[] = $buffer->pullEnd();

        $buffer->clearAll();

        return implode("\n", $items);
    }

    /**
     * 创建属性内容
     *
     * @param array $commentArr
     * @param \Model\Columnstruct $struct
     */
    protected function buildPropertyContent(array $commentArr, \Model\Columnstruct $struct) {
        $build = \Model\Build::getInstance();

        $commentArr[] = '';
        $commentArr[] = '@var ' . $this->getDateType($struct->getData_type());

        \Model\Buffer::getInstance()->pushProperty(implode("\n", array(
            $build->toComment($commentArr),
            $build->toProperty('_' . $struct->getColumn_name(), $struct->getColumn_default())
        )));
    }

    /**
     * 创建set方法内容
     *
     * @param array $commentArr
     * @param \Model\Columnstruct $struct
     */
    protected function buildSetfuncContent(array $commentArr, \Model\Columnstruct $struct) {
        $build    = \Model\Build::getInstance();
        $options  = \Lib\Options::getInstance();
        $name     = $struct->getColumn_name();
        $dataType = $this->getDateType($struct->getData_type());

        $commentArr[] = '';
        $commentArr[] = '@param ' . $dataType . ' $' . $name;
        $commentArr[] = '@return ' . ltrim($options->getNamespace(), '_')
                . sprintf($options->getModelType(), ucfirst($this->_tableName));

        \Model\Buffer::getInstance()->pushFunc(implode("\n", array(
            $build->toComment($commentArr),
            $build->toSetFunc($name, array(
                str_repeat($this->_tab, 2) . '$this->_' . $name . ' = (' . $dataType . ')$' . $name . ';' . "\n",
                str_repeat($this->_tab, 2) . 'return $this;'
                    ), $name)
        )));
    }

    /**
     * 创建get方法内容
     *
     * @param array $commentArr
     * @param \Model\Columnstruct $struct
     */
    protected function buildGetfuncContent(array $commentArr, \Model\Columnstruct $struct) {
        $build = \Model\Build::getInstance();
        $name  = $struct->getColumn_name();

        $commentArr[] = '';
        $commentArr[] = '@return ' . $this->getDateType($struct->getData_type());

        \Model\Buffer::getInstance()->pushFunc(implode("\n", array(
            $build->toComment($commentArr),
            $build->toGetFunc($name, array(str_repeat($this->_tab, 2) . 'return $this->_' . $name . ';'))
        )));
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
