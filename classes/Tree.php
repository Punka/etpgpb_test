<?php

namespace app\classes;

class Tree {

    public $table;
    public $instance;
    public $items;

    public function __construct(string $table) {

        $this->instance = App::db();
        $this->table = $table;
        $this->items = $this->generateTree();
    }

    // рекурсивно перебираем данные, и формируем массив в массиве, для дальнейшего построения дерева
    public function generateTree(array $result = [], int $parent_id = 0) {

        // получаем записи от данного родителя
        $items = (new Query())->from($this->table)->where("parent_id = :parent_id", [':parent_id' => $parent_id])->all();

        foreach($items as $row) {
            $result[$row['id']] = $row;
            $result[$row['id']]['child'] = [];
            // получаем дочерние элементы
            $result[$row['id']]['child'] = $this->generateTree($result[$row['id']]['child'], $row['id']);
        }

        return $result;
    }

    // рекурсивное построение результата дерева записей
    public function buildTree(array $items = []) {

        // первый прогон
        if(empty($items)) {
            $items = $this->items;
        }

        echo "<ul>";
        foreach ($items as $item) {
            echo '<li>';
            echo $item['position'] . ' - ' . $item['title'] . ' - ' . $item['value'] . ' ₽';

            // если есть дочерние эелементы то перебираем дочерние элементы
            if(count($item['child']) > 0) {
                $this->buildTree($item['child']);
            }
            echo '</li>';
        }
        echo "</ul>";
    }

    public function getTree() {
        // строим дерево внутри буфера
        ob_start();
        $this->buildTree();
        $data = ob_get_contents();
        ob_end_clean();

        return $data;
    }

}