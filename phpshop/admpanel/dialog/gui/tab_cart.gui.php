<?php

function getCartInfo($cart) {
    global $PHPShopSystem;
    $dis = null;
    $cart = unserialize($cart);
    $currency = ' ' . $PHPShopSystem->getDefaultValutaCode();
    if (is_array($cart))
        foreach ($cart as $val) {
            $dis .= '<a href="?path=product&id=' . $val['id'] . '&return=dialog" data-toggle="tooltip" data-placement="top" title="' . $val['name'] . ' - ' . $val['price'] . $currency . '"><img src="' . $val['pic_small'] . '" class="media-object pull-left" alt="" style="padding:3px"></a>';
        }
    return $dis;
}

function getReferal($str) {
    $referal = explode(',', $str);
    $dis = null;
    if (is_array($referal)) {
        foreach ($referal as $val)
            $un_array[$val] = $val;

        foreach ($un_array as $val)
            $dis .= PHPShopText::a('http://' . $val, $val, false, false, false, '_blank') . '<br>';
    }

    if (empty($str))
        $dis = __('прямой заход');

    return $dis;
}

function tab_cart() {
    global $PHPShopModules, $PHPShopSystem;

    $base = $PHPShopModules->getParam("base.visualcart.visualcart_memory");
    $tab = null;

    if (!empty($_GET['user']))
        $id = $_GET['user'];
    else
        $id = $_GET['id'];

    if (!empty($base)) {
        $PHPShopOrm = new PHPShopOrm($PHPShopModules->getParam("base.visualcart.visualcart_memory"));
        $data = $PHPShopOrm->getOne(array('*'), array('user' => '=' . intval($id)));

        if (is_array($data)) {
            $tab = '<p class="clearfix">' . getCartInfo($data['cart']) . '</p>';
            $tab .= '<div class="clearfix text-muted">' . __('Сумма') . ': ' . number_format($data['sum'], 0, '', ' ') . $PHPShopSystem->getValutaIcon() . '</div>';
            $tab .= '<div class="clearfix text-muted">' . __('Источник') . ': ' . getReferal($data['referal']) . '</div>';
        }
    }

    return $tab;
}

?>