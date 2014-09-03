<?php

namespace Lib;

class Db {

    protected $_db     = null;
    protected $_dbname = null;

    public function __construct(\Lib\DbConfig $dbConfig) {
        $status = \Lib\Status::getInstance();
        $maxTry = 5;
        $index  = 1;

        $status->show('connecting to the database', 2);

        while($index <= $maxTry){
            $status->show('try ' . $index . '...', 3);
            $index++;

            try {
                $pdo = new \PDO($dbConfig->getDsn(), $dbConfig->getUsername(), $dbConfig->getPasswd());
            } catch (\Exception $e){
                $status->show($e->getMessage(), 3);
                continue;
            }

            break;
        }

        if (!isset($pdo) || !$pdo instanceof \PDO) {
            throw new \Lib\Exception('connection failed');
        }

        $options = $dbConfig->getOptions();

        if(!empty($options)) {
            foreach ($options as $option) {
                $status->show('querying driver option [' . $option . ']', 3);
                $pdo->query($option);
            }
        }

        $status->show('connect successed', 2);
        $this->_db     = $pdo;
        $this->_dbname = $dbConfig->getDbname();
    }

    /**
     * 获取所有表名
     *
     * @return boolean
     */
    public function findTables() {
        $query = $this->_db->query('show tables');

        if (!$query instanceof \PDOStatement) {
            return array();
        }

        $tables   = $query->fetchAll();
        $items    = array();
        $tkeyName = 'Tables_in_' . $this->_dbname;

        foreach ($tables as $table) {
            if (isset($table[$tkeyName])) {
                $items[] = $table[$tkeyName];
            }
        }

        return $items;
    }

    /**
     * 检测表是否存在
     *
     * @param string $table
     * @return boolean
     */
    public function isExistTable($table){
         $query = $this->_db->query("select count(*) as total from `information_schema`.`TABLES` "
                . "where `TABLE_SCHEMA` = '" . addslashes($this->_dbname) . "'"
                 . " and `TABLE_NAME` = '" . addslashes($table) . "'");

         if (!$query instanceof \PDOStatement) {
            return array();
        }

        $count  = $query->fetch();

        return (isset($count['total']) && $count['total'] > 0) ? true : false;
    }

        /**
     * 列出表结构相关信息
     *
     * @param string $table
     * @return array
     */
    public function findCols($table) {
        $query = $this->_db->query("select * from `information_schema`.`COLUMNS` "
                . "where `TABLE_SCHEMA` = '" . addslashes($this->_dbname) . "' "
                . "and `TABLE_NAME` = '" . addslashes($table) . "'");

        if (!$query instanceof \PDOStatement) {
            return array();
        }

        $cols  = $query->fetchAll();
        $items = array();

        foreach ($cols as $index=>$col) {
            foreach ($col as $key => $val) {
                if ($key === (int)$key) {
                    continue;
                }

                $items[$index][strtolower($key)] = $val;
            }
        }

        return $items;
    }

}
