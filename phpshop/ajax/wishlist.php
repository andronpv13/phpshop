<?php

/**
 * Отложенные товары
 * @package PHPShopAjaxElements
 */
session_start();

$_classPath = "../";
include($_classPath . "class/obj.class.php");
PHPShopObj::loadClass("base");
$PHPShopBase = new PHPShopBase($_classPath . "inc/config.ini");
PHPShopObj::loadClass("array");
PHPShopObj::loadClass("orm");
PHPShopObj::loadClass("product");
PHPShopObj::loadClass("system");
PHPShopObj::loadClass("valuta");
PHPShopObj::loadClass("string");
PHPShopObj::loadClass("cart");
PHPShopObj::loadClass("security");
PHPShopObj::loadClass("user");
PHPShopObj::loadClass("parser");
PHPShopObj::loadClass("lang");

$product_id = intval($_REQUEST['product_id']);

$PHPShopLang = new PHPShopLang(array('locale' => $_SESSION['lang'], 'path' => 'shop'));

// Данные по товару
$objProduct = new PHPShopProduct($product_id);

// Имя товара
$name = PHPShopSecurity::CleanStr($objProduct->getParam("name"));

// готовим метки для шаблонов сообщений.
$GLOBALS['SysValue']['other']['prodId'] = $product_id;
$GLOBALS['SysValue']['other']['prodName'] = $name;
$GLOBALS['SysValue']['other']['ShopDir'] = $GLOBALS['SysValue']['dir']['dir'];

// проверяем авторизован ли пользователь
if (PHPShopSecurity::true_num($_SESSION['UsersId'])) {
    $UsersId = $_SESSION['UsersId'];
    $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['shopusers']);
    $PHPShopOrm->debug = false;
    $data = $PHPShopOrm->select(array('wishlist'), array('id' => "=" . intval($UsersId)), false, array("limit" => "1"));
    $wishlist = unserialize($data['wishlist']);
    $wishlist[$product_id] = intval($_REQUEST['parent_id']);
    $count = $_SESSION['wishlistCount'] = count($wishlist);
    $wishlist = serialize($wishlist);
    $PHPShopOrm->update(array("wishlist" => $wishlist), array('id' => "=" . intval($UsersId)), '');
} else {
    
    switch($_REQUEST['action']){
        case "remove":
            unset($_SESSION['wishlist'][$product_id]);
            break;
        case "add":
            $_SESSION['wishlist'][$product_id] = intval($_REQUEST['parent_id']);
             break;
        default: $_SESSION['wishlist'][$product_id] = intval($_REQUEST['parent_id']);
    }

    $count = count($_SESSION['wishlist']);
}

if (PHPShopParser::checkFile('../../' . $GLOBALS['SysValue']['dir']['templates'] . chr(47) . $_SESSION['skin'] . "/users/wishlist/wishlist_add_alert_done.tpl", true))
    $message = PHPShopParser::file('../../' . $GLOBALS['SysValue']['dir']['templates'] . chr(47) . $_SESSION['skin'] . "/users/wishlist/wishlist_add_alert_done.tpl", true);
else
    $message = PHPShopParser::file('../lib/templates/wishlist/wishlist_add_alert_done.tpl', true);

$message = PHPShopString::win_utf8($message);

// Формируем результат
$_RESULT = array(
    "success" => 1,
    "message" => $message,
    "count" => $count,
);

echo json_encode($_RESULT);
?>