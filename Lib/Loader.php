<?php

namespace Lib;

final class Loader {

    protected static $_instance = null;

    public function __construct() {
        spl_autoload_register(array($this, 'autoload'));
    }

    /**
     * 单例
     *
     * @return Lib\Instance
     */
    public static function getInstance() {
        if (!self::$_instance instanceof self) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }

    public function autoload($className){
        return $this->import($className);
    }

    /**
     * 引入文件
     *
     * @staticvar array $_imported
     * @param string $filename
     * @return boolean
     */
    public function import($filename) {
        static $_imported = [];

        $filename = $this->filePath($filename);
        $filekey  = str_replace(DS, '_', $filename);

        if (!isset($_imported[$filekey])) {
            if(!file_exists($filename)){
                throw new \Lib\Exception($filename . ': No such file or directory');
            }

            require($filename);
            $_imported[$filekey] = 1;

            return true;
        }

        return (isset($_imported[$filekey]) && $_imported[$filekey] === 1) ? true : false;
    }

    /**
     * 文件路径
     *
     * @param string $path
     * @return string
     */
    public function filePath($filename, $ext = '.php') {
        $prevPath = strpos($filename, APP_PATH) === 0 ? '' : APP_PATH . DS;

        return $prevPath . str_replace('\\', DS, $filename) . $ext;
    }
}
