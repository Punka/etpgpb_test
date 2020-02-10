<?php

namespace app\classes;

class AutoLoader {
    public static function load(string $file) {

        // Извлекаем из namespace: app (так как физически его не существует)
        $arr = explode('\\', $file);
        unset($arr[0]);

        // собраем путь до файла, по namespace без учета app
        $path = implode('/', $arr) . '.php';

        if(file_exists($path)) {
            require_once $path;
        }
    }
}

// Регистрируем метод который подключает используемые классы
\spl_autoload_register('app\classes\AutoLoader::load');