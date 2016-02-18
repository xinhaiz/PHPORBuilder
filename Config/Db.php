<?php

namespace Config;

class Db extends \Config\ConfigAbstract {

    public function init() {
        return array(
            'host'     => 'db.gitxm.com',
            'dbname'   => 'test',
            'username' => 'root',
            'passwd'   => 'root',
            'port'     => '3306',
            'options'  => array("SET NAMES 'utf8'")
        );
    }

}
