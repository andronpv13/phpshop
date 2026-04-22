<?php

function index_error_hook() {
    //$pathinfo = pathinfo($_SERVER['REQUEST_URI']);
    $pathinfo = parse_url($_SERVER['REQUEST_URI']);

    // Каталоги
    $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['categories']);
    $PHPShopOrm->debug = false;
    $PHPShopOrm->mysql_error = false;
    $data = $PHPShopOrm->select(array('id'), array('cat_seo_name_old' => '="' . $pathinfo['path'] . '"'), false, array('limit' => 1));
    if (is_array($data)) {

        // Убираем окончание .html
        if ($GLOBALS['PHPShopSeoPro']->getSettings()['html_enabled'] == 1)
            $html = '.html';
        else
            $html = null;

        header('Location: /shop/CID_' . $data['id'] . $html, true, 301);
        return true;
    }
    // Товары
    else {
        $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['products']);
        $PHPShopOrm->debug = false;
        $PHPShopOrm->mysql_error = false;
        $data = $PHPShopOrm->select(array('id'), array('prod_seo_name_old' => '="' . $pathinfo['path'] . '"'), false, array('limit' => 1));
        if (is_array($data)) {

            // Убираем окончание .html
            if ($GLOBALS['PHPShopSeoPro']->getSettings()['html_enabled'] == 1)
                $html = '.html';
            else
                $html = null;

            header('Location: /shop/UID_' . $data['id'] . $html, true, 301);
            return true;
        }
    }
}

$addHandler = array
    (
    'index' => 'index_error_hook'
);
