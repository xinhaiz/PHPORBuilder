<?php

namespace Lib;

final class State {
    
    const NOTIC = 36;
    const WARNING = 33;
    const ERROR = 31;

    /**
     * @param string $message
     * @param int $newline
     */
    public static function notice($message, $newline = true) {
        $notice = self::output($message, self::NOTIC);
        echo ($newline === true ? $notice : trim($notice));
    }

    /**
     * @param string $message
     * @param int $level
     */
    public static function warning($message) {
        echo self::output($message, self::WARNING);
    }

    /**
     * @param string $message
     * @param int $level
     */
    public static function error($message) {
        throw new \Lib\Exception(self::output($message, self::ERROR));
    }
    
    /**
     * 输出信息
     * 
     * @param string $message
     * @return string
     */
    public static function output($message, $type = self::NOTIC) {
        switch (strtolower(PHP_OS)) {
            case 'linux':
                $message = shell_exec('echo -e "\033[0;' . (int)$type . 'm[Error] ' . $message . '\033[0m"');
            break;
            case 'darwin':
                $message = shell_exec('echo "\033[0;' . (int)$type . 'm[Error] ' . $message . '\033[0m"');
            break;
            default:
            break;
        }
        
        return $message;
    }
}