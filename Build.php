<?php

final class Build {

    protected $_db     = null;
    protected $_params = array();
    protected $_dbname = null;
    protected $_state  = false;

    public function __construct($argv) {
        require_once(APP_PATH . DS . 'Lib' . DS . 'Loader.php');
        \Lib\Loader::getInstance();

        $params = \Lib\Params::getInstance();
        $params->setParams($argv)->parse();
        $this->_state = $params->getState();
    }

    /**
     * 流程处理前执行
     */
    public function before() {
        if($this->_state === true){
            \Lib\Status::getInstance()->show('starting');
        }
    }

    /**
     * 流程处理后执行
     */
    public function after() {
        if($this->_state === true){
            $this->_state = false;
            \Lib\Status::getInstance()->show('ended');
        }
    }

    /**
     * 流程处理
     *
     * @return boolean
     * @throws \Lib\Exception
     */
    public function process() {
        if($this->_state === false){
            if(\Lib\Params::getInstance()->showHelp() === true){
                $this->getHelp();
            }

            return false;
        }

        $db = $this->getDbResponse();
        $st = \Lib\Status::getInstance();
        $op = \Lib\Options::getInstance();

        if(empty($this->_dbname)){
            throw new \Lib\Exception('database unset');
        }

        $st->show('reading configuration tables', 2);
        $tables  = $op->getTable();

        if(empty($tables)){
            $st->show('not found configuration tables', 2);
            $st->show('reading database ：[' . $this->_dbname . ']', 2);
            $tables = $db->findTables();
        } else {
            foreach ($tables as $table){
                if($db->isExistTable($table) === false){
                    throw new \Lib\Exception('unkown table \'' . $table . '\'');
                }
            }
        }

        if (empty($tables)) {
            throw new \Lib\Exception('not found any tables');
        }

        $st->show('found ' . sizeof($tables) . ' table(s)', 2);
        $modelFile     = \Model\File::getInstance();
        $modelContents = \Model\Content::getInstance();

        foreach ($tables as $table) {
            $st->show('processing [' . $table . ']', 2);
            $tableName = ($op->getUnderline() === false) ? str_replace('_', '', $table) : $table;
            $modelContents->setTableName($tableName)->setColumns($db->findCols($table))->build();
            $modelFile->setTableName($tableName)->build();
            $modelContents->reset();
            $modelFile->reset();
            $st->show('done', 2);
        }

        return true;
    }

    /**
     * 获取 DB 资源
     *
     * @return Db
     */
    protected function getDbResponse() {
        if (!$this->_db instanceof \Lib\Db) {
            $options  = \Lib\Options::getInstance();
            $host     = $options->getHost();
            $username = $options->getUsername();
            $dbname   = $options->getDbname();
            $passwd   = $options->getPasswd();

            if(empty($host) && empty($username) && empty($passwd)){
                $dbConfig = '\\Config\\' . ucfirst(strtolower($options->getDbConfig()));

                $config = new $dbConfig();

                if (!$config instanceof \Config\ConfigAbstract) {
                    throw new \Lib\Exception('invalid database config');
                }

                $host     = $config->get('host');
                $username = $config->get('username');

                if(empty($dbname)){
                    $dbname = $config->get('dbname');
                }

                $passwd   = $config->get('passwd');
            }

            $this->_db = new \Lib\Db($host, $dbname, $username, $passwd);
            $this->_dbname = $dbname;
        }

        return $this->_db;
    }

    /**
     * Get Help info
     *
     * @return string
     */
    protected function getHelp(){
        $this->_isHelp = true;
        $item = array();

        $item[] = ' +P  Model Class保存路径, 默认保存在work.php相应目录下的BuildResult文件夹下';
        $item[] = ' +e  Model Class父类， 默认 \Base\Model\AbstractModel (未开启命名空间，\'\\\' 以 \'_\' 代替)';
        $item[] = ' +x  Model Class文件后缀名, 默认 php';
        $item[] = ' +l  Model Class文件是否保留下划线, 默认保留(1), 值[1,0]';
        $item[] = ' +m  Model Class命名类型，1. %sModel  2. Model%s  3.%s_Model  4. Model_%s';
        $item[] = ' +N  Model Class的命名空间，默认 \\';
        $item[] = ' +o  是否开启命名空间[0, 1]， 默认 1';
        $item[] = ' +d  需读取的数据库配置，默认 db';
        $item[] = ' +T  设置N个空格替代一个TAB，为0时将以TAB出现,不替换, 默认 4';
        $item[] = ' +u  连接mysql用户名，使用此项 +d 将失效';
        $item[] = ' +h  连接mysql主机，使用此项 +d 将失效';
        $item[] = ' +p  连接mysql密码，使用此项 +d 将失效';
        $item[] = ' +n  连接mysql数据库名';
        $item[] = ' +t  指定Build的表名，多个时用 \',\' 分隔';
        $item[] = ' +v  显示详情[1-3]，默认 1';
        $item[] = ' +H  显示帮助';

        echo implode("\n", $item) . "\n";
    }

}
