<?php

namespace Model;

final class Build {

    protected static $_instance = null;
    private $_tab               = null;

    private function __construct() {
        $this->_tab = \Lib\Options::getInstance()->getTab();
    }

    /**
     * 单例
     *
     * @return \Model\Build
     */
    public static function getInstance() {
        if (!self::$_instance instanceof self) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }

    /**
     * 创建类名
     *
     * @param string $name
     * @return string
     */
    public function toClass($name) {
        $options    = \Lib\Options::getInstance();
        $finalCls   = $options->getFinal() === true ? 'final ' : '';
        $items      = array($finalCls . 'class ' . sprintf($options->getModelType(), $name));
        $extendName = $options->getExtendName();
        $implements = $options->getImplements();

        if (!empty($extendName)) {
            $items[] = ' extends ' . $extendName;
        }

        if (!empty($implements)) {
            $items[] = ' implements ' . $implements;
        }

        $items[] = " {\n";

        return implode('', $items);
    }

    /**
     * 创建注释
     *
     * @param array|string $comments
     * @param boolean $indentation
     * @return array
     */
    public function toComment($comments, $indentation = true) {
        $comments = (is_array($comments)) ? $comments : array($comments);
        $tabBlock = ($indentation === false) ? '' : $this->_tab;
        $items    = array($tabBlock . '/**');

        foreach ($comments as $comment) {
            $items[] = $tabBlock . ' * ' . $comment;
        }

        $items[] = $tabBlock . ' */';

        return implode("\n", $items);
    }

    /**
     * 创建属性
     *
     * @param string $name
     * @param string $type
     * @param string $value
     * @return string
     */
    public function toProperty($name, $type, $value, $permissions = 'protected') {
        switch($type) {
            case 'int':
                $value = (int)$value;
            break;
            case 'float':
                $value = (float)$value;
            break;
            default:
                $value = $value === null ? 'null' : ("'" . $value . "'");
            break;
        }

        return $this->_tab . $permissions . ' $' . $name . ' = ' . $value . ';' . "\n";
    }

    /**
     * 创建set方法
     *
     * @param string $name
     * @param array $code
     * @param string $params
     * @return string
     */
    public function toSetFunc($name, array $code, $params) {
        return $this->toFunc('set' . \Lib\Func::ucc($name), $code, $params, 'public');
    }

    /**
     * 创建get方法
     *
     * @param string $name
     * @param string $code
     * @return string
     */
    public function toGetFunc($name, $code) {
        return $this->toFunc('get' . \Lib\Func::ucc($name), $code, null, 'public');
    }

    /**
     * 创建方法
     *
     * @param string $name
     * @param array $code
     * @param string|array $params
     * @param string $permissions [public|private|protected]
     * @return array
     */
    public function toFunc($name, array $code, $params = null, $permissions = 'public') {
        $params = (is_array($params)) ? $params : array($params);
        $argvs  = (empty($params) || empty($params[0])) ? '' : ('$' . implode(', $', $params));

        $items   = array($this->_tab . $permissions . ' function ' . $name . '(' . $argvs . ') {');
        $items[] = implode("\n", $code);
        $items[] = $this->_tab . "}\n";

        return implode("\n", $items);
    }

    /**
     * 创建toArray方法
     *
     * @param array $sets
     * @return string
     */
    public function toToArray(array $sets) {
        $items  = array(str_repeat($this->_tab, 2) . 'return [');
        $citem  = array();
        $lenArr = array_map(function($name){
            return mb_strlen($name);
        }, $sets);

        sort($lenArr);
        $maxLen = array_pop($lenArr) + 1;

        foreach ($sets as $name) {
            $len = $maxLen - mb_strlen($name);
            $citem[] = str_repeat($this->_tab, 3) . '\'' . $name . '\'' . str_repeat(' ', $len) . '=> $this->' . lcfirst(\Lib\Func::ucc($name));
        }

        $items[] = implode(',' . "\n", $citem);
        unset($citem);

        $items[] = str_repeat($this->_tab, 2) . "];";

        return $this->toFunc('toArray', $items);
    }

}
