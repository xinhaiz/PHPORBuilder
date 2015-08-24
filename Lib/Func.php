<?php

namespace Lib;

final class Func {

    /**
     * 文件名、类名处理
     *
     * @param  string $name
     * @return string
     */
    public static function uc($name) {
        $options   = \Lib\Options::getInstance();
        $ucwords   = (bool)$options->getUcwords();
        $underline = (bool)$options->getUnderline();
        $name      = ucfirst($name);

        if($ucwords) {
            $name = $underline ? self::ucc($name, '_', '_') : self::ucc($name);
        } else {
            $name = $underline ? ucfirst($name) : str_replace('_', '', $name);
        }

        return $name;
    }

    /**
     * @param  string $name
     * @return string
     */
    public static function ucc($name, $sep = '_', $replace = '') {
        return (str_replace(' ', $replace, (ucwords(str_replace($sep, ' ', $name)))));
    }
}