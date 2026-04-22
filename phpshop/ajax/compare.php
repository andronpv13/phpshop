<?php

/**
 * Сравнение товаров
 * @package PHPShopAjaxElements
 */
session_start();

$_classPath = "../";
include($_classPath . "class/obj.class.php");
PHPShopObj::loadClass("base");
$PHPShopBase = new PHPShopBase($_classPath . "inc/config.ini");
PHPShopObj::loadClass("array");
PHPShopObj::loadClass("product");
PHPShopObj::loadClass("system");
PHPShopObj::loadClass("security");
PHPShopObj::loadClass("compare");
PHPShopObj::loadClass("lang");

// Модули
$PHPShopModules = new PHPShopModules($_classPath . "modules/");

// Получаем запрос.
$xid = intval($_REQUEST['xid']);

// Получаем входящее количество товаров для сравнения
if (is_array($_SESSION['compare']))
    $compar = count($_SESSION['compare']);
else
    $compar = 0;

$PHPShopLang = new PHPShopLang(array('locale' => $_SESSION['lang'], 'path' => 'shop'));

// Класс сравнения
$PHPShopCompare = new PHPShopCompare();

switch ($_REQUEST['action']) {
    
    case 'add':
        $PHPShopCompare->add($xid);
        break;

    case 'remove':
        $PHPShopCompare->del($xid);
        break;

    default: $PHPShopCompare->add($xid);
}

if ($compar == $PHPShopCompare->getNum()) {
    $same = '1';
} else {
    $same = '0';
}

// Формируем результат
$_RESULT = array(
    "num" => $PHPShopCompare->getNum(),
    "same" => $same,
    "message" => $PHPShopCompare->getMessage(),
    "success" => 1
);


$_RESULT['message'] = PHPShopString::win_utf8($_RESULT['message']);
echo json_encode($_RESULT);
?>