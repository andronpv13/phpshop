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

// Категория Яндекс
$PHPShopOrmCat = new PHPShopOrm($PHPShopModules->getParam("base.yandexcart.yandexcart_categories"));

// Категория БД
$PHPShopCategoryArray = new PHPShopCategoryArray();
$PHPShopCategory = $PHPShopCategoryArray->getArray();

include_once dirname(__FILE__) . '/../../class/YandexMarket.php';
$YandexMarket = new YandexMarket();

// Товары
$products = $YandexMarket->getProductList('ALL', false, false, 0);
$count=0;

// Товары
$csv_array[0] = ["Артикул", "Наименование", "Большое изображение", "Подробное описание", "Склад", "Цена 1", "Вес", "Валюта", "Каталог", "Путь каталога", "Характеристики", "Старая цена", "Длина", "Ширина", "Высота", "Vendor_name", "Штрихкод", "market_sku","Яндекс.Маркет"];

if (is_array($products))
    foreach ($products['result']['offerMappings'] as $row) {

        // Картинки
        if (is_array($row['offer']['pictures']))
            foreach ($row['offer']['pictures'] as $image)
                $images_array[] = $image;

        $images = implode(",", (array) $images_array);

        // Склад
        $warehouse = $row['stocks']['stocks'][0]['present'];

        // Описание
        $description = PHPShopString::utf8_win1251(nl2br($row['offer']['description']));
        
        // Подкатегория
        $parent_to = $PHPShopOrmCat->getOne(['parent_to'], ['id' => '=' . (int)$row['mapping']['marketCategoryId']])['parent_to'];
        $category = $PHPShopOrmCat->getOne(['parent_to','name'], ['id' => '=' . (int) $parent_to]);
        $parent_category = $PHPShopOrmCat->getOne(['name'], ['id' => '=' . (int)$category['parent_to']])['name'];
       
        // Путь каталога
        $category_path = $parent_category . '/' . $category['name'] . '/' . PHPShopString::utf8_win1251($row['mapping']['marketCategoryName']);
        
        // Характеристики
        $sort = null;
        if (is_array($row['offer']['params']))
            foreach ($row['offer']['params'] as $attributes) {
                $sort .= PHPShopString::utf8_win1251($attributes['name']) . '/' . PHPShopString::utf8_win1251($attributes['value']) . '#';
            }

        $csv_array[] = [$row['offer']['vendorCode'], PHPShopString::utf8_win1251($row['offer']['name']), $images, $description, $warehouse, $row['offer']['basicPrice']['value'], $row['offer']['weightDimensions']['weight']*1000, 1, null, $category_path, $sort, $row['offer']['basicPrice']['discountBase'], $row['offer']['weightDimensions']['depth'] / 10, $row['offer']['weightDimensions']['width'] / 10, $row['offer']['weightDimensions']['height'] / 10, $row['offer']['vendor'], $row['offer']['barcodes'][0], $row['mapping']['marketSku'], 1];
        
        unset($images_array);
        unset($sort_array);
        $count++;
    }


// Сохранение
$csv_file_prod = $_classPath . 'admpanel/csv/' . $postfix . 'product.yandex.csv';
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

echo "Done ~ " . $_MEM . ", products: ".$count.", files: /phpshop/admpanel/csv/product.yandex.csv";
