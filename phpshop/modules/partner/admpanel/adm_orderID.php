<?php

function partner_addBonus($data) {
    global $_classPath;

    include_once($_classPath . 'modules/partner/class/partner.class.php');
    $PHPShopPartnerOrder = new PHPShopPartnerOrder();
    $PHPShopPartnerOrder->option = $PHPShopPartnerOrder->option();

    // Модуль включен
    if ($PHPShopPartnerOrder->option['enabled'] == 1) {

        // Если заказ выполнен, заносим эти данные в лог партнера, начисляем % партнеру
        if ($_POST['statusi_new'] == $PHPShopPartnerOrder->option['order_status']) {
            
            // Начисляем бонус партнеру
            $PHPShopPartnerOrder->addBonus($data['id'],$data['sum']);

            // Смена статуса
            $PHPShopPartnerOrder->updateLog($data['id']);
        }
    }
}

$addHandler = array(
    'actionStart' => false,
    'actionDelete' => false,
    'actionUpdate' => 'partner_addBonus'
);
