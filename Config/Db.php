<?php

namespace Config;

class Db extends \Config\ConfigAbstract {

    // 不提供 options 配置， 如：SET NAMES ‘utf8’
    // 程序默认处理了 SET NAMES ‘utf8’
    public function init() {
        return array(
            'host'     => '127.0.0.1',
            'dbname'   => null,
            'username' => 'test',
            'passwd'   => 'test'
        );
    }

}
