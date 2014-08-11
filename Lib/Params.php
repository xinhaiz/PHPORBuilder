<?php

namespace Lib;

final class Params {

    protected static $_instance = null;
    protected $_argv     = null;
    protected $_empty    = array('null', '0', 'false', 'true');
    protected $_state    = false;
    protected $_showHelp = false;

    public function __construct() {

    }

    /**
     * 单例
     *
     * @return Lib\Params
     */
    public static function getInstance() {
        if (!self::$_instance instanceof self) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }

    /**
     * @param array $argv
     * @return \Lib\Params
     */
    public function setParams($argv) {
        $this->_argv  = (array) $argv;
        $this->_state = false;

        return $this;
    }

    /**
     * 解析状态
     *
     * @return boolean
     */
    public function getState() {
        return (bool)($this->_state);
    }

    /**
     * 是否需要显示帮助
     *
     * @return boolean
     */
    public function showHelp() {
        return (bool)($this->_showHelp);
    }

    /**
     * 参数解析
     *
     * @return boolean
     * @throws \Lib\Exception
     */
    public function parse() {
        if (empty($this->_argv)) {
            $this->_state = true;

            return false;
        }

        if (strpos(implode(' ', $this->_argv), '+H') !== false) {
            $this->_state    = false;
            $this->_showHelp = true;

            return false;
        }

        $option         = null;
        $optionInstance = \Lib\Options::getInstance();

        foreach ($this->_argv as $val) {
            if ($option === null) {
                $option = trim($val);
                continue;
            }

            if (mb_strlen($option) !== 2 || strpos($option, '+') !== 0) {
                throw new \Lib\Exception('unknown option \'' . $option . '\'');
            }

            $optionName = $optionInstance->getOptionsName(ord(trim($option, '+')));

            if (empty($optionName)) {
                throw new \Lib\Exception('unknown option \'' . $option . '\'');
            }

            $funcName = 'set' . ucfirst(strtolower($optionName));

            if (method_exists($optionInstance, $funcName)) {
                if (in_array($val, $this->_empty)) {
                    $val = null;
                }

                $optionInstance->{$funcName}($val);
            }

            $option = null;
        }

        $this->_state = true;
    }

}
