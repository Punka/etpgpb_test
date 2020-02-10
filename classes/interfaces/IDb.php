<?php

namespace app\classes\interfaces;

interface IDb {
    public function getDb();
    public static function getInstance();
}