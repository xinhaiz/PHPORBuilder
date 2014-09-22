<?php

namespace Lib;

final class Status {

    protected static $_instance = null;

    public function __construct() {}

    /**
     * 单例
     *
     * @return \Lib\Status
     */
    public static function getInstance() {
        if (!self::$_instance instanceof self) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }

    /**
     * @param string $message
     * @param int $newline
     */
    public function notic($message, $newline = true) {
        $notice = (strcasecmp(PHP_OS, 'linux') === 0 ? shell_exec('echo -e "\033[0;36m' . $message . '\033[0m"') : $message);
        echo ($newline === true ? $notice : trim($notice));
    }

    /**
     * @param string $message
     * @param int $level
     */
    public function warning($message) {
        echo (strcasecmp(PHP_OS, 'linux') === 0 ? shell_exec('echo -e "\033[0;33m[Warning] ' . $message . '\033[0m"') : $message);
    }

    /**
     * @param string $message
     * @param int $level
     */
    public function error($message) {
        $message = (strcasecmp(PHP_OS, 'linux') === 0 ? shell_exec('echo -e "\033[0;31m[Error] ' . $message . '\033[0m"') : $message);
        throw new \Lib\Exception($message);
    }
}
