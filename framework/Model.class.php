<?php

class Model
{
    protected $db;
    protected $fields;
    private $opt;
    private $table;

    public function __construct()
    {
        $this->table();
        $this->initLink();
        $this->getFields();
    }

    private function initLink()
    {
        $this->db = new MYSQLDB($GLOBALS['config']['database']);
    }

    private function opt()
    {
        $this->opt = array(
            'field' => '*',
            'where' => '',
        );
    }

    public function getFields()
    {
        $sql = "desc {$this->table}";
        $fields_rows = $this->query($sql);
        $this->fields = array();
        foreach ($fields_rows as $key => $row) {
            $this->fields[] = $row['Field'];
            if ($row['Key'] == 'PRI') {
                $this->fields['pk'] = $row['Field'];
            }

        }
        return $this->fields;
    }

    public function autoDelete($pk_value)
    {
        return $this->table()->where("{$this->fields['pk']}={$pk_value}")->del();
    }

    public function autoInsert(array $data)
    {
        $fields = '';
        $values = '';
        foreach ($data as $key => $value) {
            $fields .= $key . ',';
            $values .= "'" . $value . "'" . ',';
        }

        $fields = rtrim($fields, ',');
        $values = rtrim($values, ',');
        $sql = "insert into {$this->table} ($fields) values ($values)";
        return $this->db->exe($sql);
    }

    protected function table($table = '')
    {
        if (empty($table)) {
            $this->table = '`' . $this->table_name . '`';
        } else {
            $this->table = '`' . $table . '`';
        }

        return $this;
    }

    public function field($field)
    {

        $this->opt['field'] = $field;

        return $this;
    }

    public function where($where)
    {

        $this->opt['where'] = 'where ' . $where;
        return $this;
    }

    public function select()
    {
        $sql = "select {$this->opt['field']} from {$this->table} {$this->opt['where']}";
        return $this->query($sql);
    }

    public function find()
    {
        if ($rows = $this->select()) {
            return current($rows);
        } else {
            return false;
        }

    }

    public function update(array $data)
    {
        $subject = '';
        foreach ($data as $key => $value) {
            $subject .= $key . "='" . $value . "',";
        }
        $subject = rtrim($subject, ',');

        if (empty($this->opt['where'])) {
            die('Lack of where condition!');
        }

        $sql = "update {$this->table} set $subject {$this->opt['where']}";

        return $this->exe($sql);

    }

    public function del()
    {
        if (empty($this->opt['where'])) {
            die('Lack of where condition!');
        }

        $sql = "delete from {$this->table} {$this->opt['where']}";

        return $this->exe($sql);
    }

    public function query($sql)
    {
        $this->opt();
        return $this->db->query($sql);

    }

    public function exe($sql)
    {
        $this->opt();
        return $this->db->exe($sql);

    }
}
