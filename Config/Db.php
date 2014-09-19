<?php

namespace Config;

class Db extends \Config\ConfigAbstract {

    public function init() {
        return array(
            'host'     => 'db.gitxm.com',
            'dbname'   => 'test',
            'username' => 'root',
            'passwd'   => '103188',
            'port'     => '3306',
            'options'  => array("SET NAMES 'utf8'")
        );
    }

}
