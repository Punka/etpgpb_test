<?php

namespace app\classes;

class Query {

    private $instance;

    public function __construct() {
        $this->instance = App::db();
    }

    // Если был вызван метод from, то создаем объект, который работает с выборкой
    public function from(string $table_name) : QuerySelect {
        return new QuerySelect($this->instance, $table_name);
    }

    // Если бы вызван метод insert, то создаем объект, который работает с добавлением
    public function insert(string $table_name) : QueryInsert {
        return new QueryInsert($this->instance, $table_name);
    }
}