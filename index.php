<?php

namespace app;

//error_reporting(E_ALL);
//ini_set('display_startup_errors', 1);
//ini_set('display_errors', '1');

// автоматическое подключение файлов используемых классов
require_once "classes/AutoLoader.php";

use app\classes\{App, Tree, Item, Parser};

// регистрируем конфиг
App::registerConfig();

// загружаем в объект Parse путь до файла с входными данными
$parser = new Parser("runtime/items.csv");

// перебираем упорядоченные данные для дальнейшей записи в БД
foreach($parser->getResult() as $row) {
    // создаем объект Item (который описивает свойства полей таблицы item)
    $model = new Item();

    // загружаем данные в определенном порядке (иначе не будет работать), можно было загрузить через сеттеры
    $model->load($row);

    // сохраняем запись в БД
    $model->save();
}

// создаем объект дерево с переданной названием таблицы (Можно было бы это сделать иначе, но решил именно так...)
$tree = new Tree(Item::getTableName());

// рендерим результат с переданным контентом построенного дерева
App::render('index', [
    'content' => $tree->getTree(),
]);

?>

