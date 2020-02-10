<?php

namespace app\classes;

class QueryInsert {

    private $instance;

    private $table;

    private $columns = [];

    private $values;

    public $params = [];

    public function __construct(\PDO $instance, $table) {
        $this->instance = $instance;
        $this->table = $table;
    }

    public function columns(array $items) : QueryInsert {
        $this->columns = $items;

        return $this;
    }

    public function values(array $items) : QueryInsert {
        $this->values = $items;

        // приводим данные в нормальный вид (склеиваем, если массивов несколько)
        if(is_array($items[0])) {
            foreach($items as $row) {

                $this->params = array_merge($this->params, array_values($row));
            }
        } else {
            $this->params = $items;
        }

        return $this;
    }

    private function getColumns() {

        $db = $this->instance;

        // Получаем информация по полям в таблице, без учета ID
        $query = $db->query("SELECT * FROM information_schema.columns WHERE table_schema = 'public' AND table_name = 'item' AND column_name <> 'id'");

        $arr = [];

        // собираем массив с названиями полей
        foreach ($query->fetchAll() as $row) {
            $arr[] = $row['column_name'];
        }

        return $arr;
    }

    public function save() : bool {

        $db = $this->instance;

        // собираем SQL команду по кускам
        $sql = $this->createSQL();

        // загружаем запрос
        $prepare = $db->prepare($sql);

        try {
            // выполняем запрос с переданными данными, которые необходимо добавить
            $prepare->execute($this->params);
        } catch (\Exception $e) {
            die('Error: ' . $e->getMessage());
        } finally {
            $prepare->closeCursor();
        }

        return true;
    }

    public function createSQL() {
        $sql = "INSERT INTO ";

        $arr = [];

        $arr[] = $this->table;

        // если поля были указаны, то используем их, иначе получаем все поля из метода $this->getColumns()
        if($this->columns) {
            $arr[] = "(" . implode(', ', $this->columns) . ")";
        } else {
            $arr[] = "(" . implode(', ', $this->getColumns()) . ")";
        }

        // если нужно загрузить сразу несколько записей, то перебираем этот самый массив
        if(is_array($this->values[0])) {
            $values = [];
            foreach ($this->values as $row) {
                // заменяем завания полей на ?, так как используется метод prepare()
                $values[] = "(" . implode(', ', array_fill(0, count($row), '?')) . ")";
            }
            $arr[] = "VALUES " . implode(', ', $values);
        } else {
            // заменяем завания полей на ?, так как используется метод prepare()
            $arr[] = "VALUES (" . implode(', ', array_fill(0, count($this->values), '?')) . ")";
        }

        return $sql . implode(' ', $arr) . ';';
    }
}