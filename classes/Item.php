<?php

namespace app\classes;

class Item {

    public $id;
    public $position;
    public $title;
    public $value;
    public $parent_id = 0;

    public function __construct() {}

    public function load(array $item) {
        $this->setPosition($item[0]);
        $this->setTitle($item[1]);
        $this->setValue($item[2]);
    }

    public static function getTableName() : string {
        return 'item';
    }

    public function getPosition() : string {
        return $this->position;
    }

    public function setPosition(string $val) {
        // разбиваем позицию примера 1.1.1 через разделитель "."
        $arr = explode('.', $val);

        if(count($arr) > 1) {
            // извлекаем последний элемент (текущая позиция), чтоб получить позицию родителя
            array_pop($arr);

            $parent = implode('.', $arr);

            // находим родителя
            if($query = (new Query())->from('item')->where("position = :position", [':position' => $parent])->one()) {
                $this->parent_id = $query['id'];
            }
        }

        $this->position = $val;
    }

    public function getTitle() : string {
        return $this->title;
    }

    public function setTitle(string $val) {
        $this->title = $val;
    }

    public function getValue() : int {
        return $this->value;
    }

    public function setValue(string $val) {
        $this->value = doubleval($val);
    }

    public function getParentId() : int {
        return $this->parent_id;
    }

    public static function getItems(string $table) : array {
        $result = [];
        $result = (new Query())->from($table)->all();

        return $result;
    }

    public function save() {
        $query = (new Query())
            ->from($this->getTableName())
            ->where("position = :position AND title = :title AND value = :value", [
                ':position' => $this->getPosition(),
                ':title' => $this->getTitle(),
                ':value' => $this->getValue()
            ]);

        // проверяем есть запись с данными уже в таблице, если нет, то добавляем
        if(!$query->exists()) {
            (new Query())
                ->insert($this->getTableName())
                ->columns(['position', 'title', 'value', 'parent_id'])
                ->values([$this->getPosition(), $this->getTitle(), $this->getValue(), $this->getParentId()])
                ->save();
        }
    }
}