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
$options = (new PHPShopOrm($PHPShopModules->getParam("base.megamarket.megamarket_system")))->getOne(['*']);

$PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['products']);
$data = $PHPShopOrm->getList(['*'], ['export_megamarket' => '>0'], ['order' => 'datas desc'], ['limit' => 300]);

if (is_array($data)) {

    include_once dirname(__FILE__) . '/../class/Megamarket.php';
    $Megamarket = new Megamarket();

    foreach ($data as $prod) {

        // price columns
        $price = $prod['price'];

        if (!empty($prod['price_megamarket'])) {
            $price = $prod['price_megamarket'];
        } elseif (!empty($prod['price' . (int) $options['price']])) {
            $price = $prod['price' . (int) $options['price']];
        }

        if ($options['fee'] > 0) {
            if ($options['fee_type'] == 1) {
                $price = $price - ($price * $options['fee'] / 100);
            } else {
                $price = $price + ($price * $options['fee'] / 100);
            }
        }

        if ($Megamarket->type == 2) {
            $offerId = $prod['uid'];
        } else {
            $offerId = $prod['id'];
        }

        if ($prod['items'] < 0)
            $prod['items'] = 0;

        $stocks[] = [
            'offerId' => (string) $offerId,
            'quantity' => (int) $prod['items'],
        ];
        
        $prices[] = [
            'offerId' => (string) $offerId,
            'price' => (int) $Megamarket->price($price, $prod['baseinputvaluta']),
            'isDeleted' => (bool) false
        ];

    }


    // Цены
    if (is_array($prices))
        $result = $Megamarket->setProductPrice($prices,'cron');

    // Остатки
    if (is_array($stocks))
       $Megamarket->setProductStock($stocks,'cron');

    if (!empty($result['success']))
        echo "Цены и остатки успешно отправлены для " . count($prices) . " товаров";
}
?>