<?php

function setCategories_vkseller_hook($obj, $data) {
    if (!empty($_GET['marketplace']) and $_GET['marketplace'] == 'vk')
        return true;
}

function setProducts_vkseller_hook($obj, $data) {
    $add = $list = $vemdorSort = null;

    if (!empty($_GET['marketplace']) and $_GET['marketplace'] == 'vk') {

        // price columns
        $price = $data['val']['price'];
        $fee = 0;

        if (!empty($data['val']['price_vk'])) {
            $price = $data['val']['price_vk'];
        } elseif (!empty($data['val']['price' . (int) $obj->vk_options['price']])) {
            $price = $data['val']['price' . (int) $obj->vk_options['price']];
        }
        if (isset($obj->vk_options['fee']) && (float) $obj->vk_options['fee'] > 0) {
            $fee = (float) $obj->vk_options['fee'];
        }

        if ($fee > 0) {

            if ($obj->vk_options['fee_type'] == 1) {
                $price = $price - ($price * $fee / 100);
            } else {
                $price = $price + ($price * $fee / 100);
            }
        }

        $data['xml'] = str_replace('<price>' . $data['val']['price'] . '</price>', '<price>' . $price . '</price>', $data['xml']);
        
        // Ключ обновления артикул
        if ($obj->vk_options['type'] == 2) {
            $data['xml'] = str_replace('<offer id="' . $data['val']['id'] . '"', '<offer id="' . $data['val']['uid'] . '"', $data['xml']);
        }

        $add = '<count>' . $data['val']['items'] . '</count>';

        if (!empty($add))
            $data['xml'] = str_replace('</offer>', $add . '</offer>', $data['xml']);

        return $data['xml'];
    }
}

function PHPShopYml_vkseller_hook($obj) {

    if (!empty($_GET['marketplace']) and $_GET['marketplace'] == 'vk') {
        $_GET['utf'] = true;
        $_GET['striptag'] = true;
        $_GET['allimage'] = true;

        // Настройки модуля
        $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['vkseller']['vkseller_system']);
        $obj->vk_options = $PHPShopOrm->select();

        // Пароль
        if (!empty($obj->vk_options['password']))
            if ($_GET['pas'] != $obj->vk_options['password'])
                exit('Login error!');
    }
}

$addHandler = [
    'setProducts' => 'setProducts_vkseller_hook',
    '__construct' => 'PHPShopYml_vkseller_hook',
    '#setCategories' => 'setCategories_vkseller_hook'
];
?>