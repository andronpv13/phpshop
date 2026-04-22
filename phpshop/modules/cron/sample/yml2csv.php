<?php

/**
 * Парсинг YML файла в CSV
 * @author PHPShop Software
 * @version 1.2
 * @package PHPShopParser
 */
if (empty($argv[0]))
    exit('Only PHP-CLI command line!');

if (empty($argv[1]))
    exit('Use the following command options: ' . $argv[0] . ' "https://example.ru/file.xml" outfile');

$file = $argv[1];

if (!empty($argv[2]))
    $postfix = $argv[2];
else
    $postfix = null;

if (empty($_SERVER['DOCUMENT_ROOT'])) {
    $_classPath = realpath(dirname(__FILE__)) . "/../../../";
} else
    $_classPath = "../../../";

include($_classPath . "class/obj.class.php");
PHPShopObj::loadClass(array("base", "system", "file", "string"));
$PHPShopBase = new PHPShopBase($_classPath . "inc/config.ini", false, true);

// Включаем таймер
$time = explode(' ', microtime());
$start_time = $time[1] + $time[0];

$xml = simplexml_load_file($file);

// Товары
$yml_array[0] = ["Артикул", "Наименование", "Краткое описание", "Большое изображение", "Подробное описание", "Склад", "Цена 1", "Вес", "ISO", "Каталог", "Путь каталога", "Характеристики", "Штрихкод", "Подтип", "Подчиненные товары", "Цвет", "Старая цена", "Длина", "Ширина", "Высота"];

// Каталоги
foreach ($xml->shop[0]->categories[0]->category as $item) {
    $category_array[(string) $item->attributes()->id] = [PHPShopString::utf8_win1251((string) $item[0]), (string) $item->attributes()->parentId];
}

foreach ($xml->shop[0]->offers[0]->offer as $item) {

    $warehouse = 0;
    $parent2 = $parent = '';

    // Путь каталога
    $category_path = createCategoryPath($category_array, (string) $item->categoryId[0]);
    $category_path = substr($category_path, 1, strlen($category_path) - 1);
    $category_path_array = explode("/", $category_path);
    $category_path = implode("/", array_reverse($category_path_array));

    // Склад
    if (isset($item->count[0]))
        $warehouse = (int) $item->count[0];

    // Склад
    if (isset($item->amount[0]))
        $warehouse = (int) $item->count[0];

    // Склад
    if ((string) $item->attributes()->available == "true" and empty($warehouse))
        $warehouse = 1;

    // Картинки
    if (is_array((array) $item->picture))
        $images = implode(",", (array) $item->picture);
    else
        $images = (string) $item->picture;

    // Старая цена
    if (isset($item->oldprice[0]))
        $oldprice = (string) $item->oldprice[0];

    // Габариты
    if (isset($item->dimensions[0])) {
        $dimensions = explode("/", (string) $item->dimensions[0]);
        $length = $dimensions[0];
        $width = $dimensions[1];
        $height = $dimensions[2];
    }

    // Характеристики
    $sort = null;
    $i = 0;

    if (is_array((array) $item->param)) {
        while ($i < (count((array) $item->param) - 1)) {

            $sort_name = PHPShopString::utf8_win1251((string) $item->param[$i]->attributes()->name);
            $sort_value = PHPShopString::utf8_win1251((string) $item->param[$i]);
            $i++;

            $sort .= $sort_name . '/' . $sort_value . '#';
        }
    } else
        $sort = (string) $item->param[0];

    // Бренд
    if (isset($item->vendor[0]))
        $sort .= 'Бренд/' . (string) $item->vendor[0];

    // Штрихкод
    if (!empty((string) $item->barcode[0]))
        $barcode = (string) $item->barcode[0];
    else
        $barcode = null;

    // Подтипы
    if (!empty((string) $item->attributes()->group_id)) {

        $parent_enabled = 1;
        $sort = null;

        if (!empty((string) $item->param[0]))
            $parent = PHPShopString::utf8_win1251((string) $item->param[0]);

        if (!empty((string) $item->param[1]))
            $parent2 = PHPShopString::utf8_win1251((string) $item->param[1]);

        // Главный товар
        if (!is_array($yml_array[(string) $item->attributes()->group_id])) {

            // Название
            $name = ucfirst(trim(str_replace([$parent, $parent2], ['', ''], PHPShopString::utf8_win1251((string) $item->name[0]))));

            $yml_array[(string) $item->attributes()->group_id] = [(string) $item->attributes()->group_id, $name, PHPShopString::utf8_win1251((string) $item->description[0]), $images, PHPShopString::utf8_win1251((string) $item->description[0]), $warehouse, (string) $item->price[0], ($item->weight[0] * 100), (string) $item->currencyId[0], (string) $item->categoryId[0], $sort, $barcode, 0, (string) $item->attributes()->id, '', $oldprice, $length, $width, $height];
        } else {

            // Список подтипов
            $yml_array[(string) $item->attributes()->group_id][13] .= ',' . (string) $item->attributes()->id;

            // Картинка
            $yml_array[(string) $item->attributes()->group_id][3] .= ',' . $images;

            // Минимальная цена
            if ($yml_array[(string) $item->attributes()->group_id][6] > (string) $item->price[0])
                $yml_array[(string) $item->attributes()->group_id][6] = (string) $item->price[0];
        }
    }
    else {
        $parent_enabled = 0;
        $parent = $parent2 = '';
    }


    $yml_array[(string) $item->attributes()->id] = [(string) $item->attributes()->id, PHPShopString::utf8_win1251((string) $item->name[0]), PHPShopString::utf8_win1251((string) $item->description[0]), $images, PHPShopString::utf8_win1251((string) $item->description[0]), $warehouse, (string) $item->price[0], ($item->weight[0] * 100), (string) $item->currencyId[0], (int) $item->categoryId[0], $category_path, $sort, $barcode, $parent_enabled, $parent, $parent2, $oldprice, $length, $width, $height];
}

// Построение пути каталогов
function createCategoryPath($category_array, $id, $path = null) {

    if (isset($category_array[$id])) {
        $path .= '/' . $category_array[$id][0];

        if (isset($category_array[$category_array[$id][1]])) {
            $path .= '/' . $category_array[$category_array[$id][1]][0];

            $path .= createCategoryPath($category_array, $category_array[$category_array[$id][1]][0], $path);
            return $path;
        }

        return $path;
    }
}

// Сохранение
$csv_file_prod = $_classPath . 'admpanel/csv/' . $postfix . 'product.yml.csv';
PHPShopFile::writeCsv($csv_file_prod, $yml_array);

// Расход памяти
if (function_exists('memory_get_usage')) {
    $mem = memory_get_usage();
    $_MEM = round($mem / 1024000, 2) . " Mb";
} else
    $_MEM = null;

// Выключаем таймер
$time = explode(' ', microtime());
$seconds = ($time[1] + $time[0] - $start_time);
$seconds = substr($seconds, 0, 6);

echo "Done ~ " . $seconds . " sec, " . $_MEM . ", files: /phpshop/admpanel/csv/" . $postfix . "product.yml.csv";
