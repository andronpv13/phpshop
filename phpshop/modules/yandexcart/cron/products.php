<?php

session_start();

// Включение
$enabled = false;

if (empty($_SERVER['DOCUMENT_ROOT'])) {
    $_classPath = realpath(dirname(__FILE__)) . "/../../../";
    $enabled = true;
} else
    $_classPath = "../../../";

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
PHPShopObj::loadClass("valuta");

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

$PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['products']);
$count = 0;

include_once dirname(__FILE__) . '/../class/YandexMarket.php';
$Market = new YandexMarket();

// Ключ обновления 
if ($Market->options['type'] == 1)
    $type = 'id';
else
    $type = 'uid';

// Компания 1
$data = $PHPShopOrm->getList(['*'], ['yml' => "='1'"], ['order' => 'datas desc'], ['limit' => 1000]);

// Проверка уникальности ключа обновления
if (is_array($data)) {
    foreach ($data as $row) {
        $products[(int) $row[$type]] = $row;
    }
}


if (is_array($products) and count($products) > 0) {

    // Склад
    $Market->updateStocks($products, false);

    // Цены
    $Market->updatePrices($products, false);

    $count += count($products);
}

// Компания 2
$data = $PHPShopOrm->getList(['*'], ['yml_2' => "='1'"], ['order' => 'datas desc'], ['limit' => 1000]);

// Проверка уникальности ключа обновления
if (is_array($data)) {
    foreach ($data as $row) {
        $products[(int) $row[$type]] = $row;
    }
}

if (is_array($products) and count($products) > 0) {

    // Склад
    $Market->updateStocks($products, 2);

    // Цены
    $Market->updatePrices($products, 2);

    $count += count($products);
}

// Компания 3
$data = $PHPShopOrm->getList(['*'], ['yml_3' => "='1'"], ['order' => 'datas desc'], ['limit' => 1000]);

// Проверка уникальности ключа обновления
if (is_array($data)) {
    foreach ($data as $row) {
        $products[(int) $row[$type]] = $row;
    }
}

if (is_array($products) and count($products) > 0) {

    // Склад
    $Market->updateStocks($products, 3);

    // Цены
    $Market->updatePrices($products, 3);

    $count += count($products);
}

echo "Данные отправлены для " . $count . " товаров";
