<?php

$_classPath = $_SERVER['DOCUMENT_ROOT'] . "/phpshop/";
include($_classPath . "class/obj.class.php");

PHPShopObj::loadClass("base");
$PHPShopBase = new PHPShopBase($_classPath . "inc/config.ini", true, false);

PHPShopObj::loadClass("orm");
PHPShopObj::loadClass("system");
PHPShopObj::loadClass("text");
PHPShopObj::loadClass("string");
PHPShopObj::loadClass("array");
PHPShopObj::loadClass("valuta");
PHPShopObj::loadClass("product");

require "../class/Dolyame.php";
$Dolyame = new Dolyame();

// Статус для оплаты
if ($Dolyame->order_status == 0) {
    
    $PHPShopSystem = new PHPShopSystem();
    $PHPShopValutaArray = new PHPShopValutaArray();

    $PHPShopProduct = new PHPShopProduct($_POST['id']);
    $price = $PHPShopProduct->getPrice();

    if ((int) $price <= (int) $Dolyame->max_sum and ! empty($PHPShopProduct->getParam('dolyame_enabled'))) {

        $products[] = [
            'name' => iconv("windows-1251", "utf-8", htmlspecialchars($PHPShopProduct->getName(), ENT_COMPAT, 'cp1251', true)),
            'quantity' => 1,
            'price' => number_format($price, 2, '.', ''),
            'sku' => $v['uid'],
        ];
    }

    // Авторизованный пользователь
    if (!empty($_SESSION['UsersId']))
        $client_info = [
            'first_name' => iconv("windows-1251", "utf-8", htmlspecialchars($_SESSION['UsersName'], ENT_COMPAT, 'cp1251', true)),
            'email' => $_SESSION['UsersLogin'],
        ];
    else $client_info=null;

    $orderId = "click_".time();

    // Новая заявка
    $result = $Dolyame->create_click($products, $client_info, $orderId);

    if (!empty($result['link'])) {

        $_RESULT['link']=$result['link'];
        $_RESULT['success']=1;
        
        echo json_encode($_RESULT);

    }
    
}