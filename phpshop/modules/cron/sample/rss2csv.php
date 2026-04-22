<?php

/**
 * Парсинг RSS файла в CSV
 * @author PHPShop Software
 * @version 1.0
 * @package PHPShopParser
 */

if (empty($argv[0]))
    exit('Only PHP-CLI command line!');

if (empty($argv[1]))
    exit('Use the following command options: '.$argv[0].' "https://example.ru/file.xml" outfile');

$file = $argv[1];

if(!empty($argv[2]))
    $postfix=$argv[2];
else $postfix=null;

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


$feed = str_replace(['g:'], [''], file_get_contents($file));
$xml = simplexml_load_string($feed);

// Товары
$yml_array[] = ["Артикул", "Наименование", "Краткое описание", "Большое изображение", "Подробное описание", "Склад", "Цена 1", "ISO"];

foreach ($xml->channel[0]->item as $item) {
    
    // Склад
    if ((string) $item->availability == "in stock")
        $warehouse = 1;
    else $warehouse = 0;

    // Картинки
    if (is_array((array) $item->image_link))
        $images = implode(",", (array) $item->image_link);
    else
        $images = (string) $item->image_link;
    
    // Цена
    $price = explode(" ", (string) $item->price[0]);
    
    $yml_array[] = [(string) $item->id[0], PHPShopString::utf8_win1251((string) $item->title[0]), PHPShopString::utf8_win1251((string) $item->description[0]), $images, PHPShopString::utf8_win1251((string) $item->description[0]), $warehouse, $price[0], $price[1], (int) $item->categoryId[0]];
}

// Сохранение
$csv_file_prod = $_classPath . 'admpanel/csv/' . $postfix . 'product.rss.csv';
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

echo "Done ~ " . $seconds . " sec, " . $_MEM . ", file: /phpshop/admpanel/csv/" . $postfix . "product.rss.csv";