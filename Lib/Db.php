<?php

namespace Lib;

class Db {

    protected $_db     = null;
    protected $_dbname = null;

    public function __construct(\Lib\DbConfig $dbConfig) {
        $maxTry = 3;
        $index  = 1;

        \Lib\State::notice('Connecting to MySQL...');

        while($index <= $maxTry){
            \Lib\State::notice('Try ' . $index . '...');
            $index++;

            try {
                $pdo = new \PDO($dbConfig->getDsn(), $dbConfig->getUsername(), $dbConfig->getPasswd());
            } catch (\Exception $e){
                \Lib\State::warning($e->getMessage());

                if($index <= $maxTry) {
                    \Lib\State::notice('Try to connect again after 3 seconds');
                    sleep(3);
                }
                continue;
            }

            break;
        }

        if (!isset($pdo) || !$pdo instanceof \PDO) {
            \Lib\State::notice($dbConfig->toString());
            \Lib\State::error('MySQL connection failed.');
        }

        $options = $dbConfig->getOptions();

        if(!empty($options)) {
            foreach ($options as $option) {
                $pdo->query($option);
            }
        }

        \Lib\State::notice('connect successed');
        $this->_db     = $pdo;
        $this->_dbname = $dbConfig->getDbname();
    }

    /**
     * 执行SQL
     *
     * @param string $sql
     */
    public function query($sql) {
        return $this->_db->query($sql);
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
     * 获取表信息
     *
     * @param string $tableName
     * @return array
     */
    public function findTableInfo($tableName){
         $query = $this->_db->query("select * from `information_schema`.`TABLES` "
                . "where `TABLE_SCHEMA` = '" . addslashes($this->_dbname) . "'"
                 . " and `TABLE_NAME` = '" . addslashes($tableName) . "'");

         if (!$query instanceof \PDOStatement) {
            return array();
        }

        $table = $query->fetch();
        $items = array();

        foreach ($table as $key => $val) {
            if ($key === (int)$key) {
                continue;
            }

            $items[strtolower($key)] = $val;
        }

        return $items;
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
