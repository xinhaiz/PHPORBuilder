<?php
/**
 * +------------------------------------------------------+
 * | 一个简单的ORM 类生成工具                              |
 * +------------------------------------------------------+
 * | License : Apache License Version 2.0                 |
 * | http://www.apache.org/licenses/LICENSE-2.0.html      |
 * +------------------------------------------------------+
 * |    Author : Gsinhi(xinhai.z@gsinhi.com)              |
 * +------------------------------------------------------+
 */

if(!isset($argv[0])){
    echo 'invalid request';
    return false;
}

unset($argv[0]);

define('APP_PATH', realpath(dirname(__FILE__)));
define('DS', DIRECTORY_SEPARATOR);

require_once('Build.php');
$build = new Build($argv);
$build->before();
$build->process();
$build->after();
unset($build);