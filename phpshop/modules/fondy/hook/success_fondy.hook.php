<?php

/**
 * Функция хук, обратотка результата выполнения платежа
 * @param object $obj объект функции
 * @param array $value данные о заказе
 */
function success_mod_fondy_hook($obj, $value)
{
    $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['fondy']['fondy_system']);
    $option = $PHPShopOrm->select();

    include_once 'phpshop/modules/fondy/class/Fondy.php';
    include_once 'phpshop/modules/fondy/class/Signature.php';
    $fondy = new Fondy();

    Signature::merchant($option['merchant_id']);
    Signature::password($option['password']);
    $post = json_decode(file_get_contents("php://input"), true);

    if (Signature::check($post)) {
        $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['orders']);
        $orderId = explode("_", $post['order_id']);
        $row = $PHPShopOrm->select(array('*'), array('uid' => '="' . $orderId[1] . '"'));

        if (!empty($row['uid'])) {
            if ($post['order_status'] == 'approved') {
                $PHPShopOrm->query("UPDATE `phpshop_orders` SET `statusi`='101' WHERE `id`=" . $row['id']);
                $fondy->log('Заказ ' . $post['order_id'] . ' оплачен', $post['order_id'], $post['order_status'], 'Уведомление о платеже');
            } else {
                $fondy->log('response_code: ' . $post['response_code'], $post['order_id'], $post['order_status'], 'Уведомление о платеже');
            }
        }
    } else {
        $fondy->log('response_code: ' . $post['response_code'], $post['order_id'], $post['order_status'], 'Уведомление о платеже');
    }
    exit();
}

$addHandler = array('index' => 'success_mod_fondy_hook');