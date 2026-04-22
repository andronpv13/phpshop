<?php
function webhooksUpdate($data) {
    global $_classPath;
    include_once($_classPath . 'modules/webhooks/class/webhooks.class.php');
    $PHPShopWebhooks = new PHPShopWebhooks($data);
    $PHPShopWebhooks->getHooks(4);
    $PHPShopWebhooks->init();
}

function webhooksInsert($data) {
    global $_classPath;
    include_once($_classPath . 'modules/webhooks/class/webhooks.class.php');
    $PHPShopWebhooks = new PHPShopWebhooks($data);
    $PHPShopWebhooks->getHooks(7);
    $PHPShopWebhooks->init();
}

$addHandler = array(
    'actionStart' => false,
    'actionDelete' => 'webhooksUpdate',
    'actionUpdate' => 'webhooksUpdate',
    'actionInsert' => 'webhooksInsert'
);

?>