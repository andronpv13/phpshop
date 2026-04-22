<?php

/**
 *  Журнал добавления в корзину
 */
function cartload_visualcart_hook($obj, $data) {

    $_RESULT = $data[0];
    $_REQUEST = $data[1];
    $PHPShopCart = $data[2];

    $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['visualcart']['visualcart_log']);
    $insert['date_new'] = time();
    
    if(!empty($_SESSION['UsersId']))
    $insert['user_new'] = $_SESSION['UsersId'];

    // Успешное добавлени в корзину
    if (!empty($PHPShopCart->log['status'])){
        $insert['status_new'] = 1;
        
    }
    else {
        $insert['status_new'] = 2;
    }

    $insert['content_new'] = $PHPShopCart->log['name'];
    $insert['ip_new'] = $_SERVER["REMOTE_ADDR"];
    $insert['num_new'] = $_REQUEST['num'];
    $insert['price_new'] = $PHPShopCart->log['price'];
    $insert['product_id_new'] = $_REQUEST['xid'];
    $PHPShopOrm->insert($insert);
}

$addHandler = array
    (
    'cartload' => 'cartload_visualcart_hook'
);
?>