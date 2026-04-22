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

// Тип Ozon
$PHPShopOrmType = new PHPShopOrm($PHPShopModules->getParam("base.ozonseller.ozonseller_type"));

// Категория Ozon
$PHPShopOrmCat = new PHPShopOrm($PHPShopModules->getParam("base.ozonseller.ozonseller_categories"));

// Категория БД
$PHPShopCategoryArray = new PHPShopCategoryArray();
$PHPShopCategory = $PHPShopCategoryArray->getArray();

include_once dirname(__FILE__) . '/../../class/OzonSeller.php';
$OzonSeller = new OzonSeller();
$limit = 1000;
$last_id=null;

// Товары
$products = $OzonSeller->getProductList('VISIBLE', false, false, $limit, $last_id);
$count=0;

if (is_array($products['result']['items'])) {
    foreach ($products['result']['items'] as $products_list) {

        $data[$products_list['product_id']] = $OzonSeller->getProduct($products_list['product_id'])['items'][0];

        // Тип
        $type = $PHPShopOrmType->getOne(['name'], ['id' => '=' . $data[$products_list['product_id']]['type_id']]);
        $data[$products_list['product_id']]['type'] = $type['name'];

        // Подкатегория
        $category = $PHPShopOrmCat->getOne(['*'], ['id' => '=' . $data[$products_list['product_id']]['description_category_id']]);
        $data[$products_list['product_id']]['category'] = $category['name'];

        // Категория
        $parent = $PHPShopOrmCat->getOne(['name'], ['id' => '=' . $category['parent_to']]);
        $data[$products_list['product_id']]['parent'] = $parent['name'];
    }
}

// Товары
$csv_array[0] = ["Артикул", "Наименование", "Краткое описание", "Большое изображение", "Подробное описание", "Склад", "Цена 1", "Вес", "Валюта", "Каталог", "Путь каталога", "Характеристики", "Старая цена", "Длина", "Ширина", "Высота", "Barcode_ozon", "Sku_ozon", "Export_ozon_id", "Export_ozon"];

if (is_array($data))
    foreach ($data as $row) {

        if (empty($row['name']))
            continue;

        // Атрибуты
        $product_info = $OzonSeller->getProductAttribures($row['id'])['result'][0];


        // Картинки
        $images_array[] = $row['primary_image'][0];

        if (is_array($row['images']))
            foreach ($row['images'] as $image)
                $images_array[] = $image;

        $images = implode(",", (array) $images_array);

        // Склад
        $warehouse = $row['stocks']['stocks'][0]['present'];

        // Описание
        $description = PHPShopString::utf8_win1251(nl2br($OzonSeller->getProductDescription($product_info['id'])['result']['description']));

        // Путь каталога
        $category_path = $row['parent'] . '/' . $row['category'] . '/' . $row['type'];

        // Характеристики
        $sort_ozon_data = $OzonSeller->getTreeAttribute(["description_category_id" => $row['description_category_id'], "type_id" => $row['type_id']]);
        if (is_array($sort_ozon_data['result'])) {
            foreach ($sort_ozon_data['result'] as $sort_ozon_value) {
                $attribute[$sort_ozon_value['id']] = PHPShopString::utf8_win1251($sort_ozon_value['name'], true);
            }
        }

        if (is_array($product_info['attributes']))
            foreach ($product_info['attributes'] as $attributes) {
                if (!empty($attribute[$attributes['id']]) and ! empty($attributes['values'][0]['dictionary_value_id'])) {
                    $sort_array[$attribute[$attributes['id']]] = PHPShopString::utf8_win1251($attributes['values'][0]['value']);
                }
            }

        $sort = null;
        if (is_array($sort_array))
            foreach ($sort_array as $sort_name => $sort_value) {
                $sort .= $sort_name . '/' . $sort_value . '#';
            }

        $csv_array[] = [$row['offer_id'], PHPShopString::utf8_win1251($row['name']), null, $images, $description, $warehouse, $row['price'], $product_info['weight'], 1, null, $category_path, $sort, $row['old_price'], $product_info['depth'] / 10, $product_info['width'] / 10, $product_info['height'] / 10, $row['barcodes'][0], $row['sku'], $row['id'], 1];
        
        unset($images_array);
        unset($sort_ozon_data);
        unset($sort_array);
        $count++;
    }


// Сохранение
$csv_file_prod = $_classPath . 'admpanel/csv/' . $postfix . 'product.ozon.csv';
PHPShopFile::writeCsv($csv_file_prod, $csv_array);

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

echo "Done ~ " . $_MEM . ", products: ".$count.", files: /phpshop/admpanel/csv/product.ozon.csv";
