<?php

/*
 * Создание пользователя
 */

function webhooks_add($obj, $data) {

    include_once('./phpshop/modules/webhooks/class/webhooks.class.php');

    $PHPShopWebhooks = new PHPShopWebhooks($data);
    $PHPShopWebhooks->getHooks(5);
    $PHPShopWebhooks->init();
}

$addHandler = array('add' => 'webhooks_add');
