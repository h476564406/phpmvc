<?php

/**
 * Class MYSQLDB
 *
 * Connect with mysql db and query sql, execute sql.
 * Document here: http://php.net/manual/en/book.mysqli.php
 */
class MYSQLDB
{

    private $host;
    private $port;
    private $user;
    private $pwd;
    private $charset;
    private $dbname;

    // Store mysql link
    private $link;

    public function __construct(array $params)
    {

        if (empty($params)) {
            die('Please input correct db params！');
        }

        $this->host = isset($params['host']) ? $params['host'] : '127.0.0.1';
        $this->port = isset($params['port']) ? $params['port'] : '3306';
        $this->user = isset($params['user']) ? $params['user'] : 'root';
        $this->pwd = isset($params['pwd']) ? $params['pwd'] : '';
        $this->charset = isset($params['charset']) ? $params['charset'] : 'utf8';
        $this->dbname = isset($params['dbname']) ? $params['dbname'] : 'itcast';
        $this->connect();

    }

    private function connect()
    {
        $link = new mysqli($this->host, $this->user, $this->pwd, $this->dbname, $this->port);

        if (!$link) {
            die('Error! Please check mysql config!' . $link->connect_error);
        }

        $this->link = $link;
        $this->link->set_charset($this->charset);
    }

    /**
     * Query sql. e.g. 'select from ...'
     *
     * @param $sql
     * @return mixed
     */
    public function query($sql)
    {

        $result = $this->link->query($sql);
        if ($this->link->errno) {
            $this->_error($sql);
        }

        $rows = array();
        while ($row = $result->fetch_assoc()) {
            $rows[] = $row;
        }

        $result->free();

        if ($rows) {
            return $rows;
        } else {
            return false;
        }
    }

    /**
     * Execute sql. e.g. 'Update...'
     *
     * @param $sql
     * @return int
     */
    public function exe($sql)
    {

        $this->link->query($sql);

        if ($this->link->errno) {
            $this->_error($sql);
        }

        return $this->link->insert_id ? $this->link->insert_id : $this->link->affected_rows;
    }

    /**
     * Echo error, stop running code.
     */
    private function _error()
    {
        if ($this->link->errno) {
            echo "Execute sql error! Error info:" . $this->link->error;
            die();
        }
    }
}
