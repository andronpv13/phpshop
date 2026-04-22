<?php

session_start();

// Включение
$enabled = false;

if (empty($_SERVER['DOCUMENT_ROOT'])) {
    $_classPath = realpath(dirname(__FILE__)) . "/../../../../";
    $enabled = true;
} else
    $_classPath = "../../../../";

include($_classPath . "class/obj.class.php");
PHPShopObj::loadClass("base");
PHPShopObj::loadClass("system");
PHPShopObj::loadClass("orm");
PHPShopObj::loadClass("date");
PHPShopObj::loadClass("order");
PHPShopObj::loadClass("cart");
PHPShopObj::loadClass("parser");
PHPShopObj::loadClass("text");
PHPShopObj::loadClass("lang");
PHPShopObj::loadClass("security");
PHPShopObj::loadClass("array");
PHPShopObj::loadClass("product");
PHPShopObj::loadClass("category");

$PHPShopBase = new PHPShopBase($_classPath . "inc/config.ini", true, true);
$PHPShopSystem = new PHPShopSystem();

// Авторизация
if ($_GET['s'] == md5($PHPShopBase->SysValue['connect']['host'] . $PHPShopBase->SysValue['connect']['dbase'] . $PHPShopBase->SysValue['connect']['user_db'] . $PHPShopBase->SysValue['connect']['pass_db']))
    $enabled = true;

if (empty($enabled))
    exit("Ошибка авторизации!");


// Настройки модуля
PHPShopObj::loadClass("modules");
$PHPShopModules = new PHPShopModules($_classPath . "modules/");


include_once dirname(__FILE__) . '/../../class/WbSeller.php';
$WbSeller = new WbSeller();
$limit = 100;
$result = [];

function getProductList($updatedAt, $nmID) {
    global $WbSeller, $limit;

    // Товары
    $products_add = $WbSeller->getProductList("", $limit, $updatedAt, $nmID);
    if (is_array($products_add['cursor'])) {
        $GLOBALS['result'][] = $products_add;
        if ($products_add['cursor']['total'] >= $limit) {
            getProductList($products_add['cursor']['updatedAt'], $products_add['cursor']['nmID']);
        }
    }
}

getProductList(false, false);
$count = 0;



// Товары
$csv_array[0] = ["Артикул", "Наименование", "Краткое описание", "Большое изображение", "Подробное описание", "Склад", "Цена 1", "Вес", "Валюта", "Каталог", "Путь каталога", "Характеристики", "Длина", "Ширина", "Высота", "Barcode_wb", "Export_wb_id", "Export_wb"];


if (is_array($GLOBALS['result']))
    foreach ($GLOBALS['result'] as $products_array) {
         foreach($products_array['cards'] as $product)
           $products['cards'][] = $product;
    }

if (is_array($products['cards']))
    foreach ($products['cards'] as $row) {

        // Картинки
        if (is_array($row['photos']))
            foreach ($row['photos'] as $image)
                if (!empty($image) and ! stristr($image['big'], '.mp4'))
                    $images_array[] = $image['big'];

        $images = implode(",", (array) $images_array);

        // Цена
        //$price = $WbSeller->getProductPrice($row['nmID'])['data']['listGoods'][0]['sizes'][0]['price'];
        // Склад
        $warehouse = 1;

        // Описание
        $description = PHPShopString::utf8_win1251(nl2br($row['description']));

        // Путь каталога
        $category_path = PHPShopString::utf8_win1251($row['subjectName']);

        // Характеристики
        $sort = null;
        if (is_array($row['characteristics']))
            foreach ($row['characteristics'] as $attributes) {
                $sort .= PHPShopString::utf8_win1251($attributes['name']) . '/' . PHPShopString::utf8_win1251($attributes['value'][0]) . '#';
            }

        $csv_array[] = [$row['vendorCode'], PHPShopString::utf8_win1251($row['title']), null, $images, $description, $warehouse, $price, $row['dimensions']['weightBrutto'] * 100, 1, null, $category_path, $sort, $row['dimensions']['depth'], $row['dimensions']['width'], $row['dimensions']['height'], $row['sizes'][0]['skus'][0], $row['nmID'], 1];

        unset($images_array);
        $count++;
    }


// Сохранение
$csv_file_prod = $_classPath . 'admpanel/csv/' . $postfix . 'product.wb.csv';
PHPShopFile::writeCsv($csv_file_prod, $csv_array);

// Расход памяти
if (function_exists('memory_get_usage')) {
    $mem = memory_get_usage();
    $_MEM = round($mem / 1024000, 2) . " Mb";
} else
    $_MEM = null;

echo "Done ~ " . $_MEM . ", products: " . $count . ", files: /phpshop/admpanel/csv/product.wb.csv";
