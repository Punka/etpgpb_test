<?php

namespace app\classes;

use app\classes\interfaces\IDb;

class Db implements IDb
{
    private static $instance;

    private static $object;

    private static $db;

    private static $host;

    private static $port;

    private static $user;

    private static $password;

    public function getDb() {
        return self::$db;
    }

    protected function __construct() {}

    public static function getInstance() {

        // Singleton: если объект создан, то возвращаем, иначе создаем
        if(!self::$object ) {

            self::$db = DB_NAME;
            self::$host = HOST;
            self::$port = PORT;
            self::$user = USER;
            self::$password = PASSWORD;

            try {
                self::$instance = new \PDO("pgsql:dbname=" . self::$db . ";host=" . self::$host . ";port=" . self::$port, self::$user, self::$password, [\PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION, \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC, \PDO::ATTR_EMULATE_PREPARES => false]);
            } catch(\PDOException $e) {
                die('Не удалось подключиться к базе данных');
            }

            self::$object = new static;
        }



        return self::$instance;
    }
}