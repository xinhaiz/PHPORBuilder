<?php

namespace Config;

class Db extends \Config\ConfigAbstract {

    public function init() {
        return array(
            'host'     => '127.0.0.1',
            'dbname'   => 'test',
            'username' => 'test',
            'passwd'   => 'test',
            'port'     => '3306',
            'options'  => array("SET NAMES 'utf8'")
        );
    }

}
