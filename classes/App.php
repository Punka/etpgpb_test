<?php

namespace app\classes;

class App {

    public static $db;

    protected function __construct() {}

    public static function registerConfig() {

        $config = include "config/config.php";

        // Регистрируем глобальные переменные
        define('HOST', $config['db']['host']);
        define('PORT', $config['db']['port']);
        define('DB_NAME', $config['db']['db_name']);
        define('USER', $config['db']['user']);
        define('PASSWORD', $config['db']['password']);
    }

    public static function db() {

        self::$db = Db::getInstance();

        return self::$db;
    }

    public static function render(string $file, array $params = []) {

        $path_file = 'view/' . $file . '.php';

        if(file_exists($path_file)) {
            $data = file_get_contents($path_file);

            // Заменяем метки в шаблоне на значения (если они были переданы)
            if($params) {
                foreach($params as $key => $value) {
                    $data = str_replace('{{' . $key . '}}', $value, $data);
                }
            }

            echo $data;
        } else {
            // TODO: throw enything
        }
    }
}