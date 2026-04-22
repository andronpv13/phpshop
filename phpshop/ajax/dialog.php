<?php

/**
 * Сравнение товаров
 * @package PHPShopAjaxElements
 */
session_start();

$_classPath = "../";
include($_classPath . "class/obj.class.php");
PHPShopObj::loadClass("base");
$PHPShopBase = new PHPShopBase($_classPath . "inc/config.ini", true, true);
PHPShopObj::loadClass("array");
PHPShopObj::loadClass("system");
PHPShopObj::loadClass("security");
PHPShopObj::loadClass("lang");
PHPShopObj::loadClass("elements");
PHPShopObj::loadClass("shopelements");
PHPShopObj::loadClass("parser");
PHPShopObj::loadClass("nav");
include('../.' . $SysValue['file']['elements']);
include('../.' . $SysValue['file']['shopelements']);

// Системные настройки
$PHPShopSystem = new PHPShopSystem();
$PHPShopNav = new PHPShopNav();
$PHPShopRecaptchaElement = new PHPShopRecaptchaElement();

// Модули
$PHPShopModules = new PHPShopModules($_classPath . "modules/");
$PHPShopLang = new PHPShopLang(array('locale' => $_SESSION['lang'], 'path' => 'shop'));

$PHPShopDialogElement = new PHPShopDialogElement();

if (!empty($_POST['reg']))
    $result = $PHPShopDialogElement->add_user($_POST['mail'], PHPShopString::utf8_win1251($_POST['name']),$_POST['pas'],$_POST['tel']);
elseif(isset($_POST['answer'])){
    $result = $PHPShopDialogElement->answer($_POST['answer']);
}
else{
    if(!empty($_SESSION['UsersId']))
        $UsersId = $_SESSION['UsersId'];
    else $UsersId=null;
    $result = $PHPShopDialogElement->message($UsersId,$_POST['new'],false,$_POST['path']);
}

if(empty($result['bot']))
    $result['bot']=null;
if(empty($result['status']))
    $result['status']=null;
if(empty($result['animation']))
    $result['animation']=null;
if(empty($result['count']))
    $result['count']=0;

echo json_encode(array('success' => 1, 'num' => $result['count'], 'bot' => $result['bot'], 'status' => $result['status'],'animation'=>$result['animation'],'message' => PHPShopString::win_utf8($result['message'])));
?>