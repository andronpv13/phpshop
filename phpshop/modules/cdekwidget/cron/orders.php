<?php

session_start();

// Включение
$enabled = true;

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
PHPShopObj::loadClass("product");
PHPShopObj::loadClass("valuta");

$PHPShopBase = new PHPShopBase($_classPath . "inc/config.ini", true, true);
$PHPShopSystem = new PHPShopSystem();
$_SESSION['lang'] = $PHPShopSystem->getSerilizeParam("admoption.lang_adm");
$PHPShopLang = new PHPShopLang(array('locale' => $_SESSION['lang'], 'path' => 'admin'));

// Авторизация
if ($_GET['s'] == md5($PHPShopBase->SysValue['connect']['host'] . $PHPShopBase->SysValue['connect']['dbase'] . $PHPShopBase->SysValue['connect']['user_db'] . $PHPShopBase->SysValue['connect']['pass_db']))
    $enabled = true;

if (empty($enabled))
    exit("Ошибка авторизации!");

// Настройки модуля
include_once dirname(__FILE__) . '/../class/CDEKWidget.php';
$CDEKWidget = new CDEKWidget();

// Заказы
$data = (new PHPShopOrm($GLOBALS['SysValue']['base']['orders']))->getList(['*'], ['statusi' => '=' . $CDEKWidget->option['status'], 'cdek_order_data' => '!=""'], ['order' => 'id DESC'], ['limit' => 2]);
$count = 0;
if (is_array($data))
    foreach ($data as $row) {

        $cdek = unserialize($row['cdek_order_data']);
        if (is_array($cdek)) {

            // Еще не отправлен
            if (empty($CDEKWidget->isOrderSend($cdek['status']))) {

                // Отправить
                $CDEKWidget->send($row);
                $count++;
            }
        }
    }
echo "Отправлено " . (int) $count . " заказов в CDEK";