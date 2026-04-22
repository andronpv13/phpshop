<?php

/**
 * Корзина
 * @package PHPShopAjaxElements
 */
session_start();

$_classPath = "../";
include($_classPath . "class/obj.class.php");
PHPShopObj::loadClass("base");
$PHPShopBase = new PHPShopBase($_classPath . "inc/config.ini", true, true);
PHPShopObj::loadClass("array");
PHPShopObj::loadClass("orm");
PHPShopObj::loadClass("product");
PHPShopObj::loadClass("system");
PHPShopObj::loadClass("valuta");
PHPShopObj::loadClass("string");
PHPShopObj::loadClass("cart");
PHPShopObj::loadClass("security");
PHPShopObj::loadClass("user");
PHPShopObj::loadClass("lang");
PHPShopObj::loadClass("order");

$_REQUEST['addname'] = PHPShopString::utf8_win1251($_REQUEST['addname']);

// Мультибаза
$PHPShopBase->checkMultibase("../../");

// Массив валют
$PHPShopValutaArray = new PHPShopValutaArray();

// Системные настройки
$PHPShopSystem = new PHPShopSystem();

$PHPShopLang = new PHPShopLang(array('locale' => $_SESSION['lang'], 'path' => 'shop'));

// Корзина
$PHPShopCart = new PHPShopCart();

// Модули
$PHPShopModules = new PHPShopModules($_classPath . "modules/");

// Добавлем товар
if (PHPShopSecurity::true_param($_REQUEST['xid'], $_REQUEST['num'])) {

    if (empty($_REQUEST['num']))
        $add = $PHPShopCart->del($_REQUEST['xid']);
    else
        $add = $PHPShopCart->add($_REQUEST['xid'], $_REQUEST['num'], $_REQUEST['xxid']);
}

// Дата обновления корзины
setcookie("cart_update_time", time(), 0, "/", $_SERVER['SERVER_NAME'], 0);

// Формируем результат
$_RESULT = array(
    "num" => $PHPShopCart->getNum(),
    "sum" => $PHPShopCart->getSum(true, ' '),
    "message" => $PHPShopCart->getMessage(),
    "success" => $add,
    "status" => $PHPShopCart->log['status']
);

// Перехват модуля в начале функции
$hook = $PHPShopModules->setHookHandler('cartload', 'cartload', false, array($_RESULT, $_REQUEST, $PHPShopCart));
if (is_array($hook))
    $_RESULT = $hook;

// JSON 
$_RESULT['message'] = PHPShopString::win_utf8($_RESULT['message']);
echo json_encode($_RESULT);
