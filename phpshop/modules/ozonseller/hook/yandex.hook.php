<?php

function setCategories_ozonseller_hook($obj, $data) {
    if (!empty($_GET['marketplace']) and $_GET['marketplace'] == 'ozon')
        return true;
}

function setProducts_ozonseller_hook($obj, $data) {
    $add = $list = $vemdorSort = null;

    if (!empty($_GET['marketplace']) and $_GET['marketplace'] == 'ozon') {

        // price columns
        $price = $data['val']['price'];
        $oldprice = $data['val']['oldprice'];
        $fee = 0;

        if (!empty($data['val']['price_ozon'])) {
            $price = $data['val']['price_ozon'];
        } elseif (!empty($data['val']['price' . (int) $obj->ozon_options['price']])) {
            $price = $data['val']['price' . (int) $obj->ozon_options['price']];
        }
        if (isset($obj->ozon_options['fee']) && (float) $obj->ozon_options['fee'] > 0) {
            $fee = (float) $obj->ozon_options['fee'];
        }

        if ($fee > 0) {
            if ($obj->ozon_options['fee_type'] == 1) {
                $price = $price - ($price * $fee / 100);
                $oldprice = $oldprice - ($oldprice * $fee / 100);
            } else {
                $price = $price + ($price * $fee / 100);
                $oldprice = $oldprice + ($oldprice * $fee / 100);
            }
        }

        $data['xml'] = str_replace('<price>' . $data['val']['price'] . '</price>', '<price>' . $price . '</price><oldprice>' . $oldprice . '</oldprice><min_price>' . $price . '</min_price>', $data['xml']);


        $outlets = null;
        if (is_array($obj->warehouse)) {
            foreach ($obj->warehouse as $warehouse) {

                // Лимит по складу
                if (!empty($obj->ozon_options['export_items_limit']) and $data['val']['items'] >= $obj->ozon_options['export_items_limit'])
                    $data['val']['items'] = $obj->ozon_options['export_items_limit'];

                $outlets .= '<outlet instock="' . $data['val']['items'] . '" warehouse_name="' . $warehouse['name'] . '"></outlet>';
            }
        }

        $add .= '<outlets>' . $outlets . '</outlets>';

        // Ключ обновления артикул
        if ($obj->ozon_options['type'] == 2) {
            $data['xml'] = str_replace('<offer id="' . $data['val']['id'] . '"', '<offer id="' . $data['val']['uid'] . '"', $data['xml']);
        }

        if (!empty($add))
            $data['xml'] = str_replace('</offer>', $add . '</offer>', $data['xml']);

        return $data['xml'];
    }
}

function PHPShopYml_ozonseller_hook($obj) {

    if (!empty($_GET['marketplace']) and $_GET['marketplace'] == 'ozon') {
        $_GET['utf'] = true;

        // Настройки модуля
        $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['ozonseller']['ozonseller_system']);
        $obj->ozon_options = $PHPShopOrm->select();
        $obj->warehouse = unserialize($obj->ozon_options['warehouse']);

        // Пароль
        if (!empty($obj->ozon_options['password']))
            if ($_GET['pas'] != $obj->ozon_options['password'])
                exit('Login error!');
    }
}

$addHandler = [
    'setProducts' => 'setProducts_ozonseller_hook',
    '__construct' => 'PHPShopYml_ozonseller_hook',
    'setCategories' => 'setCategories_ozonseller_hook'
];
?>