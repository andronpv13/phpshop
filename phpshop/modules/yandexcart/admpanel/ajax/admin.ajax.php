<?php

session_start();

$_classPath = "../../../../";
include_once($_classPath . "class/obj.class.php");
include_once($_classPath . "modules/yandexcart/class/YandexMarket.php");
PHPShopObj::loadClass("base");
$PHPShopBase = new PHPShopBase($_classPath . "inc/config.ini");
PHPShopObj::loadClass('modules');
PHPShopObj::loadClass('orm');
PHPShopObj::loadClass('system');
PHPShopObj::loadClass('security');
PHPShopObj::loadClass('order');
PHPShopObj::loadClass("valuta");
PHPShopObj::loadClass("string");
PHPShopObj::loadClass("cart");
PHPShopObj::loadClass("promotions");

$PHPShopBase->chekAdmin();

// Массив валют
$PHPShopValutaArray = new PHPShopValutaArray();

// Системные настройки
$PHPShopSystem = new PHPShopSystem();

$PHPShopLang = new PHPShopLang(array('locale' => $_SESSION['lang'], 'path' => 'shop'));

// Корзина
$PHPShopCart = new PHPShopCart();

$Market = new YandexMarket();
$result = ['success' => true];

try {
    $result['imported'] = (int) $_REQUEST['imported'];
    $result['total_products'] = (int) $_REQUEST['total_products'];

    if ((int) $_REQUEST['initial'] === 1) {
        $result['total_products'] = $Market->getProductsCount();

        if ($result['total_products'] > 5000) {
            $result['total_products'] = 5000;
        }
    }

    $imported = $Market->importProducts((int) $_REQUEST['from'], $result['imported']);

    $result['from'] = (int) $_REQUEST['from'] + $imported;
    $result['imported'] += $imported;

    if (!empty($result['total_products']))
        $result['percent'] = round($result['imported'] * 100 / $result['total_products'], 2);

    if ($result['imported'] >= 5000) {
        $result['finished'] = true;
        $result['percent'] = 100;
        $result['message'] = PHPShopString::win_utf8('Товары успешно экспортированы.');
    }

    // Завершаем выполнение
    if ($result['from'] >= $result['total_products']) {
        $result['finished'] = true;
        $result['percent'] = 100;
        $result['message'] = PHPShopString::win_utf8('Товары успешно экспортированы.');
    }
} catch (\Exception $exception) {
    $result['finished'] = true; // завершаем выполнение при ошибке.
    $result['success'] = false;
    $result['message'] = PHPShopString::win_utf8($exception->getMessage());
}

echo (json_encode($result));
exit;
