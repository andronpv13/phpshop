<?php

include_once dirname(__DIR__) . '/class/Megamarket.php';

function setProducts_megamarket_hook($obj, $data) {
    $add = $list = $vemdorSort = null;

    if ($_GET['marketplace'] != 'megamarket') {
        return;
    }

    // Характеристики
    if (is_array($data['val']['vendor_array']))
        foreach ($data['val']['vendor_array'] as $v) {
            // Vendor
            if ($obj->brand_array[$v[0]] != "") {
                $add .= '<vendor>' . str_replace('&', '&amp;', $obj->brand_array[$v[0]]) . '</vendor>';
                $vemdorSort = true;
            }
        }



    // Vendor из карточки товара
    if (empty($vemdorSort) and ! empty($data['val']['vendor_name']))
        $add .= '<vendor>' . str_replace('&', '&amp;', $data['val']['vendor_name']) . '</vendor>';

    // Подтип
    if (!empty($data['val']['group_id'])) {

        // Размер
        if (!empty($data['val']['size']))
            $add .= '<param name="Размер" unit="RU">' . $data['val']['size'] . '</param>';

        // Цвет
        if (!empty($data['val']['color']))
            $add .= '<param name="Цвет">' . $data['val']['color'] . '</param>';
    }

    // Oldprice
    if (!empty($data['val']['oldprice']))
        $data['xml'] = str_replace('<price>' . $data['val']['price'] . '</price>', '<price>' . $data['val']['price'] . '</price><oldprice>' . $data['val']['oldprice'] . '</oldprice>', $data['xml']);

    $options = $obj->megamarket_options;

    // price columns
    $price = $data['val']['price'];
    $fee = 0;
    $markup = 0;

    // Цена 
    if (!empty($data['val']['price_megamarket'])) {
        $price = $data['val']['price_megamarket'];
    } elseif (isset($options['price']) && (int) $options['price'] > 1 && !empty($data['val']['price' . (int) $options['price']])) {
        $price = $data['val']['price' . (int) $options['price']];
    }

    if (isset($options['fee']) && (float) $options['fee'] > 0) {
        $fee = (float) $options['fee'];
        $markup = (int) $options['markup'];
    }

    // Наценка руб.
    $price = $price + (int) $markup;

    // Наценка %
    if ($fee > 0) {
        $price = $price + ($price * $fee / 100);
    }

    $data['xml'] = str_replace('<price>' . $data['val']['price'] . '</price>', '<price>' . $price . '</price>', $data['xml']);

    // Модель
    if (!empty($data['val']['model']))
        $add .= '<model>' . $data['val']['model'] . '</model>';

    // Штрихкод
    if (!empty($data['val']['barcode']))
        $add .= '<barcode>' . $data['val']['barcode'] . '</barcode>';

    // vendorCode
    if (!empty($data['val']['vendor_code']))
        $add .= '<vendorCode>' . $data['val']['vendor_code'] . '</vendorCode>';

    $add .= '<outlets><outlet id="1" instock="' . $data['val']['items'] . '"></outlet></outlets>';

    // Ключ обновления артикул
    if ($options['type'] == 2) {
        $data['xml'] = str_replace('<offer id="' . $data['val']['id'] . '"', '<offer id="' . $data['val']['uid'] . '"', $data['xml']);
    }

    if (!empty($add))
        $data['xml'] = str_replace('</offer>', $add . '</offer>', $data['xml']);

    return $data['xml'];
}

function setDelivery_megamarket_hook($obj, $data) {

    // Бренды
    $PHPShopOrm = new PHPShopOrm();
    $PHPShopOrm->sql = 'SELECT b.id, b.name FROM ' . $GLOBALS['SysValue']['base']['sort_categories'] . ' AS a LEFT JOIN ' . $GLOBALS['SysValue']['base']['sort'] . ' AS b ON a.id = b.category where a.brand="1" limit 1000';
    $vendor = $PHPShopOrm->select();
    if (is_array($vendor)) {
        $obj->vendor = true;
        foreach ($vendor as $brand) {
            $obj->brand_array[$brand['id']] = $brand['name'];
        }
    }

    return $data['xml'];
}

function PHPShopYml_megamarket_hook($obj) {

    // Настройки модуля
    $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['megamarket']['megamarket_system']);
    $obj->megamarket_options = $PHPShopOrm->select();

    // Пароль
    if (!empty($obj->megamarket_options['password']))
        if ($_GET['pas'] != $obj->megamarket_options['password'])
            exit('Login error!');
}

$addHandler = [
    'setProducts' => 'setProducts_megamarket_hook',
    'setDelivery' => 'setDelivery_megamarket_hook',
    '__construct' => 'PHPShopYml_megamarket_hook'
];
