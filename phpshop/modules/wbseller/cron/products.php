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
$options = (new PHPShopOrm($PHPShopModules->getParam("base.wbseller.wbseller_system")))->getOne(['*']);

$PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['products']);
$data = $PHPShopOrm->getList(['*'], ['export_wb' => "='1'", 'export_wb_id' => '>0'], ['order' => 'datas desc'], ['limit' => 1000]);

// Проверка уникальности ключа обновления
if(is_array($data)){
    foreach($data as $row){
        $data_uniq[(int) $row['export_wb_id']] = $row;
    }
}

if (is_array($data_uniq)) {

    include_once dirname(__FILE__) . '/../class/WbSeller.php';
    $WbSeller = new WbSeller();

    foreach ($data_uniq as $prod) {

        // price columns
        $price = $prod['price'];

        if (!empty($prod['price_wb'])) {
            $price = $prod['price_wb'];
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

        // Снять скидки
        if ($WbSeller->discount == 1) {
            $prices[] = [
                'nmID' => (int) $prod['export_wb_id'],
                'price' => (int) $WbSeller->price($price, $prod['baseinputvaluta']),
                'discount' => (int) 0
            ];
        } else {
            $prices[] = [
                'nmID' => (int) $prod['export_wb_id'],
                'price' => (int) $WbSeller->price($price, $prod['baseinputvaluta'])
            ];
        }

        if (empty($prod['barcode_wb']))
            $prod['barcode_wb'] = $prod['uid'];

        if ($prod['items'] < 0)
            $prod['items'] = 0;

        $stocks[] = [
            'barcode_wb' => (string) $prod['barcode_wb'],
            'uid' => (string) PHPShopString::win_utf8($prod['uid']),
            'enabled' => (int) $prod['enabled'],
            'items' => (int) $prod['items']
        ];
    }


    // Цены
    if (is_array($prices))
        $result = $WbSeller->sendPrices(['data' => $prices]);

    // Остатки
    if (is_array($stocks))
        $WbSeller->setProductStock($stocks);

    if (!empty($result['uploadId']))
        echo "Цены и остатки успешно отправлены для " . count($prices) . " товаров";
}
?>