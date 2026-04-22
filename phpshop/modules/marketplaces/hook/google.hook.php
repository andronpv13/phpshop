<?php

include_once dirname(__DIR__) . '/class/Marketplaces.php';

function marketplacesRssHook($obj) {

    $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['marketplaces']['marketplaces_system']);
    $obj->marketplaces_options = $PHPShopOrm->select();

    // Пароль
    if (!empty($obj->marketplaces_options['password']))
        if ($_GET['pas'] != $obj->marketplaces_options['password'])
            exit('Login error!');

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
}

function setProducts_google_hook($obj, $data) {
    $add = null;

    // Brand из характеристики, если не задано принудительно в карточке товара.
    if (is_array($data['val']['vendor_array']) && empty($data['val']['vendor_name']))
        foreach ($data['val']['vendor_array'] as $v) {
            // Brand
            if (!empty($obj->brand_array[$v[0]])) {
                $add .= '<g:brand>' . $obj->brand_array[$v[0]] . '</g:brand>';
            }
        }

    // Brand из карточки товара
    if (!empty($data['val']['vendor_name']))
        $add .= '<g:brand>' . $data['val']['vendor_name'] . '</g:brand>';

    if (!empty($data['val']['barcode'])) {
        $add .= '<g:gtin>' . $data['val']['barcode'] . '</g:gtin>';
    }

    if (!empty($data['val']['vendor_code'])) {
        $add .= '<g:mbn>' . $data['val']['vendor_code'] . '</g:mbn>';
    }

    // condition
    switch ($data['val']['condition']) {
        case 2:
            $add .= '<g:condition>refurbished</g:condition>';
            break;
        case 3:
            $add .= '<g:condition>used</g:condition>';
            break;
        default:
            $add .= '<g:condition>new</g:condition>';
            break;
    }

    if (!empty($add))
        $data['xml'] = str_replace('</item>', $add . '</item>', $data['xml']);

    $options = unserialize($obj->marketplaces_options['options']);

    // price columns
    $price = $data['val']['price'];
    $fee = 0;
    $markup = (int) $options['price_ali_markup'];

    if (!empty($data['val']['price_google'])) {
        $price = $data['val']['price_google'];
    } elseif (isset($options['price_google']) && (int) $options['price_google'] > 1 && !empty($data['val']['price' . (int) $options['price_google']])) {
        $price = $data['val']['price' . (int) $options['price_google']];
    }
    if (isset($options['price_google_fee']) && (float) $options['price_google_fee'] > 0) {
        $fee = (float) $options['price_google_fee'];
    }

    // Наценка руб.
    $price = $price + (int) $markup;

    // Наценка %
    if ($fee > 0) {
        $price = $price + ($price * $fee / 100);
    }

    $data['xml'] = str_replace('<g:price>' . $data['val']['price'] . ' ' . $obj->defvalutaiso . '</g:price>', '<g:price>' . $price . ' ' . $obj->defvalutaiso . '</g:price>', $data['xml']);

    return $data['xml'];
}

$addHandler = [
    'setProducts' => 'setProducts_google_hook',
    '__construct' => 'marketplacesRssHook'
];
