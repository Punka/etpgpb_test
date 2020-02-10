<?php

namespace app\classes;

class Parser {

    private $handle = null;

    public function __construct(string $path)
    {
        // пытаемся получить ресурс файла
        if($handle = fopen($path, "r")) {
            $this->handle = $handle;
        }
    }

    public function __destruct()
    {
        // закрываем ресурс файла
        fclose($this->handle);
    }

    public function getResult() {
        $cnt = 0;

        $result = [];

        // если ресурс получен, то перебираем его, при помощи базовой функции fgetcsv()
        if($this->handle) {
            while (($data = fgetcsv($this->handle, 1000, ",")) !== FALSE) {

                if($cnt) $result[$data[0]] = $data;

                $cnt++;
            }
        }

        // сортируем массив по ключу, для удобного занесения в БД
        ksort($result);

        return $result;
    }
}