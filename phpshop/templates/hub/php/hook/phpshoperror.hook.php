<?php

function index_error_hook() {

    $pathinfo = pathinfo($_SERVER['REQUEST_URI']);

    // Товары
    if ($pathinfo['extension'] == 'html') {
        $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['products']);
        $PHPShopOrm->debug = false;
        $PHPShopOrm->mysql_error = false;
        $data = $PHPShopOrm->select(array('id'), array('option1' => '="' . $_SERVER['REQUEST_URI'] . '"'), false, array('limit' => 1));
        if (is_array($data))
            header('Location: /shop/UID_' . $data['id'] . '.html', true, 301);
    }
    // Каталоги
    else {
        $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['categories']);
        $PHPShopOrm->debug = false;
        $PHPShopOrm->mysql_error = false;
        $data = $PHPShopOrm->select(array('id'), array('option6' => '="' . $_SERVER['REQUEST_URI'] . '"'), false, array('limit' => 1));
        if (is_array($data))
            header('Location: /shop/CID_' . $data['id'] . '.html', true, 301);
    }
}

$addHandler = array
    (
    'index' => 'index_error_hook'
);
?>