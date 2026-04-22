<?php
function webhooksUpdate($data) {
    global $_classPath;
    include_once($_classPath . 'modules/webhooks/class/webhooks.class.php');
    $PHPShopWebhooks = new PHPShopWebhooks($data);
    $PHPShopWebhooks->getHooks(6);
    $PHPShopWebhooks->init();
}

function webhooksInsert($data) {
    global $_classPath;
    include_once($_classPath . 'modules/webhooks/class/webhooks.class.php');
    $PHPShopWebhooks = new PHPShopWebhooks($data);
    $PHPShopWebhooks->getHooks(5);
    $PHPShopWebhooks->init();
}

$addHandler = array(
    'actionStart' => false,
    'actionDelete' => 'webhooksUpdate',
    'actionUpdate' => 'webhooksUpdate',
    'actionInsert' => 'webhooksInsert',
);

?>