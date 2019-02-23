<?php

final class Build {

    protected $_db     = null;
    protected $_params = array();
    protected $_dbname = null;
    protected $_state  = false;

    public function __construct($argv) {
        require_once(APP_PATH . DS . 'Lib' . DS . 'Loader.php');
        \Lib\Loader::getInstance();

        $params       = \Lib\Params::getInstance();
        $params->setParams($argv)->parse();
        $this->_state = $params->getState();
    }

    /**
     * 流程处理前执行
     */
    public function before() {
        if ($this->_state === false && \Lib\Params::getInstance()->showHelp() === false) {
            \Lib\State::error('Invalid process');
        }
    }

    /**
     * 流程处理后执行
     */
    public function after() {
        $this->_state = ($this->_state === true ? false : true);
    }

    /**
     * 流程处理
     *
     * @return boolean
     * @throws \Lib\Exception
     */
    public function process() {
        if ($this->_state === false) {
            if (\Lib\Params::getInstance()->showHelp() === true) {
                $this->getHelp();
            }

            return false;
        }

        $db = $this->getDbResponse();
        $op = \Lib\Options::getInstance();

        if (empty($this->_dbname)) {
            \Lib\State::error('The database is not specified');
        }

        \Lib\State::notice('Scanning the database table...');
        $tables = $op->getTable();

        if (empty($tables)) {
            $tables = $db->findTables();
        } else {
            foreach ($tables as $table) {
                if ($db->isExistTable($table) === false) {
                    \Lib\State::warning('Unkown table \'' . $table . '\'');
                }
            }
        }

        if (empty($tables)) {
            \Lib\State::warning('Not found any tables');
        }

        \Lib\State::notice('Found ' . sizeof($tables) . ' table(s)');
        $modelFile     = \Model\File::getInstance();
        $modelContents = \Model\Content::getInstance();
        $replaceArr    = $op->getReplace() ?: [];

        foreach ($tables as $table) {
            $tableName = \Lib\Func::uc($table);
            $className = $tableName;

            if(!empty($replaceArr['source']) && !empty($replaceArr['target'])) {
                $className = str_ireplace($replaceArr['source'], ucfirst($replaceArr['target']), $className);
            }

            if (preg_match('/^[0-9]+/', $tableName)) {
                $tableName = ltrim(preg_replace('/^[0-9]+/', '', $tableName), '_');
            }

            \Lib\State::notice('-----------------');
            \Lib\State::notice('Processing [' . $table . ']');
            $modelContents->setTableInfo($db->findTableInfo($table));
            $modelContents->setClassName($className);
            $modelContents->setTableName($tableName);
            $modelContents->setColumns($db->findCols($table));
            $modelContents->build();
            \Lib\State::notice('Done');

            $modelFile->setFileName($className)->build();
            $modelContents->reset();
            $modelFile->reset();
        }

        return true;
    }

    /**
     * 流程处理
     *
     * @return boolean
     * @throws \Lib\Exception
     */
    public function testData() {
        $db = $this->getDbResponse();
        $op = \Lib\Options::getInstance();

        $tables = $op->getTable();

        if (empty($tables)) {
            $tables = $db->findTables();
        } else {
            foreach ($tables as $table) {
                if ($db->isExistTable($table) === false) {
                    \Lib\State::warning('Unkown table \'' . $table . '\'');
                }
            }
        }

        $modelFile     = \Model\File::getInstance();
        $modelContents = \Model\Content::getInstance();
        $replaceArr    = $op->getReplace() ?: [];

        foreach ($tables as $table) {
            $tableName = \Lib\Func::uc($table);
            $className = $tableName;

            if(!empty($replaceArr['source']) && !empty($replaceArr['target'])) {
                $className = str_ireplace($replaceArr['source'], ucfirst($replaceArr['target']), $className);
            }

            if (preg_match('/^[0-9]+/', $tableName)) {
                $tableName = ltrim(preg_replace('/^[0-9]+/', '', $tableName), '_');
            }

            $fields = $db->findCols($table);
            $cols   = [];
            $values = [];
            $num    = 10000*100;

            foreach ($fields as $index => $field) {
                $cols[$index] = $field['column_name'] ?? '';
            }

            $sql = sprintf('INSERT INTO `%s` (`%s`) VALUES ', $table, implode('`, `', $cols));

            for($idx = 0; $idx < $num; ++$idx) {
                foreach ($fields as $index => $field) {
                    $type   = $field['column_type'] ?? 'varchar(8)';
                    $matchs = [];
                    preg_match('/([a-z]+)(\(([0-9]+)\))?/i', $type, $matchs);
                    $typeName = strtolower($matchs[1] ?? 'varchar');
                    $valueLen = (int)($matchs[3] ?? 8);

                    switch ($typeName) {
                        case 'date':
                            $value = "'" . sprintf('2018-%02s-%02s', 4, mt_rand(1, 30)) . "'";
                        break;
                        case 'bigint':
                            $value = mt_rand(0, 999999999999);
                        break;
                        case 'int':
                            $value = mt_rand(0, PHP_INT_MAX % 0xFFFFFFFF);
                        break;
                        case 'tinyint':
                            $value = mt_rand(0, 0x7F);
                        break;
                        case 'char':
                        case 'varchar':
                        default:
                            $value = "'" . $this->rand($valueLen) . "'";
                        break;
                    }

                    $values[$idx][$index] = $value;
                }

                if($idx > 0 && (($idx + 1) % 3000 == 0)) {
                    $valSql = [];

                    foreach ($values as $items) {
                        $valSql[] = '(' . implode(',', $items) . ')';
                    }

                    $db->query($sql . implode(',', $valSql));

                    $values = [];
                }
            }
        }

        return true;
    }

    /**
     * 生成一串随机码
     *
     * @param int $length
     * @param boolean $case
     * @return string
     */
    public function rand($length = 12, $case = true) {
        $str = 'abcdefghijklnmopqsrtvuwxyz123456879';

        if($case === true) {
            $str .= 'ABCDFEGHIJKLMNOPRQSTUVWXYZ';
        }

        $slen = strlen($str);
        $nstr = [];

        while ($length > 0) {
            $index = mt_rand(0, $slen);

            if(isset($str[$index])) {
                $nstr[] = $str[$index];
                --$length;
            }
        }

        return implode($nstr);
    }

    /**
     * 获取 DB 资源
     *
     * @return Db
     */
    protected function getDbResponse() {
        if (!$this->_db instanceof \Lib\Db) {
            $options  = \Lib\Options::getInstance();
            $dbConfig = \Lib\DbConfig::getInstance();
            $userName = $options->getUsername();
            $passwd   = $options->getPasswd();
            $confName = (empty($userName) || empty($passwd)) ? $options->getDbConfig() : false;
            $params   = array('host', 'dbname', 'port', 'options');

            if (!empty($confName)) {
                $predefined = '\\Config\\' . ucfirst(strtolower($confName));
                $preConfig  = new $predefined();

                if ($preConfig instanceof \Config\ConfigAbstract) {
                    $dbConfig->setHost($preConfig->get('host'));
                    $dbConfig->setPort($preConfig->get('port'));
                    $dbConfig->setDbname($preConfig->get('dbname'));
                    $dbConfig->setOptions($preConfig->get('options'));
                    $dbConfig->setUsername($preConfig->get('username'));
                    $dbConfig->setPasswd($preConfig->get('passwd'));
                }
            } else {
                array_push($params, 'username');
                array_push($params, 'passwd');
            }

            foreach ($params as $name) {
                $get = 'get' . ucfirst(strtolower($name));

                if (method_exists($options, $get)) {
                    $val = $options->{$get}();
                    $set = 'set' . ucfirst(strtolower($name));

                    if (!empty($val) && method_exists($dbConfig, $set)) {
                        $dbConfig->{$set}($val);
                    }
                }
            }

            $this->_db     = new \Lib\Db($dbConfig);
            $this->_dbname = $dbConfig->getDbname();
        }

        return $this->_db;
    }

    /**
     * Get Help info
     *
     * @return string
     */
    protected function getHelp() {
        $this->_isHelp = true;
        $item          = array();

        $item[] = 'f  Model Class保存路径, 默认保存在work.php相应目录下的BuildResult文件夹下';
        $item[] = ' e  Model Class父类 (未开启命名空间，\'\\\' 以 \'_\' 代替)';
        $item[] = ' i  Model Class类所需接口类 (未开启命名空间，\'\\\' 以 \'_\' 代替)';
        $item[] = ' x  Model Class文件后缀名, 默认 php';
        $item[] = ' l  Model Class文件名/类名是否保留下划线, 默认 false';
        $item[] = ' L  Model Class方法名是否保留下划线, 默认 true [弃用]';
        $item[] = ' m  Model Class命名类型, 默认 1，1. %sModel  2. Model%s  3.%s_Model  4. Model_%s';
        $item[] = ' R  自定义替换部份类名及文件名，格式 source:target, target相应字符首字母将被自动大写';
        $item[] = ' N  Model Class的命名空间，默认 \ ';
        $item[] = ' F  Model Class能支持写 final 关键字, 默认 false';
        $item[] = ' U  文件名/类名/列名所有 _ 分隔单词首字母大写，否则仅第一单词首字母大写, 默认 false';
        $item[] = ' o  是否开启命名空间， 默认 true';
        $item[] = ' d  从Config中读取的数据库配置，默认 false';
        $item[] = ' T  设置N个空格替代一个TAB，为0时将以TAB出现不替换, 默认 4';
        $item[] = ' u  连接mysql用户名，使用此项 +d 将失效';
        $item[] = ' p  连接mysql密码，使用此项 +d 将失效, 不建议直接在命令行输入密码';
        $item[] = ' h  连接mysql主机, 默认 127.0.0.1';
        $item[] = ' P  连接mysql主机端口, 默认 3306';
        $item[] = ' n  连接mysql数据库名';
        $item[] = ' O  数据库驱动选项处理, 多个时用 \',\' 分隔';
        $item[] = ' t  指定Build的表名，多个时用 \',\' 分隔';
        $item[] = ' H  显示帮助';

        \Lib\State::notice(implode("\n", $item));
    }

}
