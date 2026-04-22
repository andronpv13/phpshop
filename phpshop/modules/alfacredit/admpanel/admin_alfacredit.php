<?php

// Настройки модуля
include_once dirname(__FILE__) . '/../hook/mod_option.hook.php'; 
$PHPShopAlfaCredit = new PHPShopAlfaCreditArray(false);

function getCartInfo($cart) {
    global $PHPShopSystem;
    $dis = null;
    $cart = unserialize($cart);
    $currency = ' ' . $PHPShopSystem->getDefaultValutaCode();
    if (is_array($cart))
        foreach ($cart as $val) {
            $dis.='<a href="?path=product&id=' . $val['id'] . '" data-toggle="tooltip" data-placement="top" title="' . $val['name'] . ' - ' . $val['price'] . $currency . '">' . $val['id'] . '</a>, ';
        }
    return substr($dis, 0, strlen($dis) - 2);
}

function getStatus($status) {
    global $PHPShopAlfaCredit;

    $status = unserialize($status);
    if (is_array($status))
        return $PHPShopAlfaCredit->statuses[$status['currentStatus']];
    else
        return '';
}

function actionStart() {
    global $PHPShopInterface, $PHPShopModules, $TitlePage;
    
    $PHPShopInterface->checkbox_action = false;
    $PHPShopInterface->setCaption(array("Идентификатор", "25%"), array("Дата", "15%"), array("Товары", "25%"), array("Статус", "25%"));

    // SQL
    $PHPShopOrm = new PHPShopOrm($PHPShopModules->getParam("base.alfacredit.alfacredit_log"));
    $data = $PHPShopOrm->select(array('*'), false, array('order' => 'id DESC'), array("limit" => 1000));
    if (is_array($data))
        foreach ($data as $row) {
            $PHPShopInterface->setRow($row['reference'], array('name'=>PHPShopDate::get($row['date'], true),'order'=>$row['date']), getCartInfo($row['cart']), getStatus($row['status']));
        }
    $PHPShopInterface->Compile();
}

?>