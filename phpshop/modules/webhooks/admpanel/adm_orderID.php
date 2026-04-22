<?php

function webhooksUpdate($data) {
    global $_classPath;
    include_once($_classPath . 'modules/webhooks/class/webhooks.class.php');
    $PHPShopWebhooks = new PHPShopWebhooks($data);
    $PHPShopWebhooks->getHooks(2);
    $PHPShopWebhooks->init();
}

function webhooksStore($data) {
    global $_classPath;
    include_once($_classPath . 'modules/webhooks/class/webhooks.class.php');
    $PHPShopWebhooks = new PHPShopWebhooks($data);
    $PHPShopWebhooks->getHooks(3);
    $PHPShopWebhooks->init();
}

$addHandler = array(
    'actionStart' => false,
    'actionDelete' => 'webhooksUpdate',
    'actionUpdate' => 'webhooksUpdate',
    'updateStore' => 'webhooksStore',
);
?>