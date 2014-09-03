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
        $this->_tableName = (string) (strtolower($tableName));

        return $this;
    }

    /**
     * @param string $columns
     * @return \Lib\Modelcontents
     */
    public function setColumns($columns) {
        $this->_columns = (array) $columns;

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

        if(!empty($namespace) && $options->getOnNamespace() === true){
            $buffer->pushHeader('namespace ' . $namespace . ';' . "\n");
            $namespace = '';
        }

        $buffer->pushClass($build->toClass($namespace . ucfirst($this->_tableName)));

        foreach ($columns as $column) {
            if (empty($column['column_name'])) {
                continue;
            }

            $status->show('being parsed column [' . $column['column_name'] . ']', $this->_viewLevel);
            $result  = $this->parseColumn($column);
            $status->show($result === true ? 'ok' : 'failed', $this->_viewLevel);
            $items[] = $column['column_name'];
        }

        $buffer->pushToArray($build->toToArray($items));
    }

    /**
     * 解析 columns
     *
     * @param array $column
     */
    private function parseColumn(array $column) {
        $build   = \Model\Build::getInstance();
        $buffer  = \Model\Buffer::getInstance();
        $options = \Lib\Options::getInstance();
        $name    = (isset($column['column_name']) ? strtolower($column['column_name']) : null);

        if(empty($name)){
            return false;
        }

        if($options->getColunderline() === false){
            $name = trim(str_replace('_', '', $name));
        }

        $type    = (isset($column['column_type'])) ? $column['column_type'] : null;
        $default = (isset($column['column_default'])) ? $column['column_default'] : null;
        $comment = (!empty($column['column_comment'])) ? $column['column_comment'] : ucfirst($name);
        $extra   = (isset($column['extra'])) ? $column['extra'] : null;
        $key     = (isset($column['column_key'])) ? $column['column_key'] : null;
        $rType   = (isset($this->_typeArr[$column['data_type']])) ? $this->_typeArr[$column['data_type']] : 'string';

        $commentArr = array($comment, '', $type);

        if (!empty($extra)) {
            $commentArr[10] = $extra;
        }

        if (!empty($extra)) {
            $commentArr[20] = $key;
        }

        $commentArr[50]  = '';
        $commentArr[100] = '@var ' . $rType;

        $property = array(
            $build->toComment($commentArr),
            $build->toProperty('_' . $name, $default)
        );

        $buffer->pushProperty(implode("\n", $property));

        $code = array(
            str_repeat($this->_tab, 2) . '$this->_' . $name . ' = (' . $rType . ')$' . $name . ';' . "\n",
            str_repeat($this->_tab, 2) . 'return $this;'
        );

        $commentArr[99]  = '@param ' . $rType . ' $' . $name;
        $commentArr[100] = '@return ' . ltrim($options->getNamespace(), '_')
                            . sprintf($options->getModelType(), ucfirst($this->_tableName));

        ksort($commentArr);

        $setFunc = array(
            $build->toComment($commentArr),
            $build->toSetFunc($name, $code, $name)
        );

        $buffer->pushFunc(implode("\n", $setFunc));

        unset($commentArr[100]);
        $commentArr[99] = '@return ' . $rType;

        $getFunc = array(
            $build->toComment($commentArr),
            $build->toGetFunc($name, array(str_repeat($this->_tab, 2) . 'return $this->_' . $name . ';'))
        );

        $buffer->pushFunc(implode("\n", $getFunc));

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

}
