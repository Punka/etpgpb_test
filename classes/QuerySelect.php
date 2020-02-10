<?php

namespace app\classes;

class QuerySelect {

    private $instance;

    private $table;
    private $select = ['*'];
    private $order = ["id ASC"];
    private $limit = 10;
    private $offset = 0;
    private $where = "";

    public $params = [];

    public function __construct(\PDO $instance, string $table) {
        $this->instance = $instance;
        $this->table = $table;
    }

    public function select(array $items) : QuerySelect {
        $this->select = $items;

        return $this;
    }

    public function orderBy(array $items) : QuerySelect {
        $this->order = $items;

        return $this;
    }

    public function limit(int $limit, int $offset = 0) : QuerySelect {
        $this->limit = $limit;
        $this->offset = $offset;

        return $this;
    }

    public function where(string $sql, array $params = []) : QuerySelect {
        $this->where = $sql;
        $this->params = $params;

        return $this;
    }

    public function exists() {
        return count($this->all());
    }

    public function one() : array {
        return $this->all()[0];
    }

    public function all() {
        $db = $this->instance;

        $sql = $this->createSQL();

        $prepare = $db->prepare($sql);

        $result = [];

        try {
            // используем бинды для передачи значенией в подготовленный запрос
            if($this->params) {
                foreach ($this->params as $key => $value) {
                    $prepare->bindValue($key, $value, \PDO::PARAM_STR);
                }
            }

            $prepare->execute();

            // если записи есть, то получаем их
            if($prepare->rowCount()) {
                $result = $prepare->fetchAll();
            }

        } catch (\Exception $e) {
            die("Error: " . $e->getMessage());
        } finally {
            $prepare->closeCursor();
        }

        return $result;
    }

    public function createSQL() {
        $sql = "SELECT ";

        $arr = [];
        $arr[] = implode(', ', $this->select);
        $arr[] = "FROM " . $this->table;
        if($this->where) {
            $arr[] = "WHERE " . $this->where;
        }
        $arr[] = "ORDER BY " . implode(', ', $this->order);
        $arr[] = "LIMIT " . $this->limit;
        if($this->offset) {
            $arr[] = "OFFSET " . $this->offset;
        }

        return $sql . implode(' ', $arr) . ';';
    }
}