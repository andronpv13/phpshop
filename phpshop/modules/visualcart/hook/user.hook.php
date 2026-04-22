<?php

function autorization_visyalcart_hook($obj, $row) {
    if (PHPShopSecurity::true_num($_SESSION['UsersId'])) {
        $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['visualcart']['visualcart_memory']);
        $PHPShopOrm->debug = false;
        $data = $PHPShopOrm->select(array('memory'), array('user' => "=" . $_SESSION['UsersId']), array('order' => 'date'), array('limit' => 1));
        if (is_array($data)) {

            $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['visualcart']['visualcart_memory']);
            $PHPShopOrm->debug = false;
            if (preg_match("/^[a-zA-Z0-9_]{4,35}$/", $data['memory'])) {
                $data = $PHPShopOrm->select(array('*'), array('memory' => "='" . $data['memory'] . "'"), false, array('limit' => 1));

                if (is_array($data)) {
                    $_SESSION['cart'] = unserialize($data['cart']);
                    setcookie("visualcart_memory", $data['memory'], time() + 60 * 60 * 24 * 90, "/", $_SERVER['SERVER_NAME'], 0);
                }
            }
        }
    }
}

$addHandler = array
    (
    'autorization' => 'autorization_visyalcart_hook'
);
?>
