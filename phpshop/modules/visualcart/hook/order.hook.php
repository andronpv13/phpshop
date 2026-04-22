<?php

/**
 * Очистка памяти корзины при удалении товара из заказа
 */
function id_delete_visyalcart_hook($obj, $row) {
    if (PHPShopSecurity::true_search($_COOKIE['visualcart_memory'])) {
        $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['visualcart']['visualcart_memory']);
        $PHPShopOrm->delete(array('memory' => "='" . $_COOKIE['visualcart_memory'] . "'"));
    }
}

/**
 * Сохраненные личные данные
 */
function index_visyalcart_hook($obj, $row, $rout) {
    if ($rout == 'START' and preg_match("/^[a-zA-Z0-9_]{4,35}$/", $_COOKIE['visualcart_memory'])) {
        $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['visualcart']['visualcart_memory']);
        $data = $PHPShopOrm->select(array('*'), array('memory' => "='" . $_COOKIE['visualcart_memory'] . "'"), false, array('limit' => 1));
        if (is_array($data)) {

            if (!empty($data['mail']))
                $_POST['mail'] = $data['mail'];

            if (!empty($data['name']))
                $_POST['name_new'] = $data['name'];

            if (!empty($data['tel']))
                $_POST['tel_new'] = $data['tel'];
        }
    }
}

$addHandler = array
    (
    '#id_delete' => 'id_delete_visyalcart_hook',
    'cart' => 'id_delete_visyalcart_hook',
    'index' => 'index_visyalcart_hook'
);
?>