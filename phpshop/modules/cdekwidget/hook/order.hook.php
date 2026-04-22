<?php

/**
 * Добавление js
 * param obj $obj
 * param array $row
 * param string $rout
 */
function order_cdek_hook($obj, $row, $rout) {
    global $PHPShopSystem;

    if ($rout == 'MIDDLE') {

        include_once 'phpshop/modules/cdekwidget/class/CDEKWidget.php';
        $CDEKWidget = new CDEKWidget();

        $PHPShopCart = new PHPShopCart();

        $cart = $CDEKWidget->getCart($PHPShopCart->getArray());

        if (empty($CDEKWidget->option['default_city']))
            $defaultCity = 'auto';
        else
            $defaultCity = $CDEKWidget->option['default_city'];

        // Яндекс.Карты
        $yandex_apikey = $PHPShopSystem->getSerilizeParam("admoption.yandex_apikey");
        if (empty($yandex_apikey))
            $yandex_apikey = 'cb432a8b-21b9-4444-a0c4-3475b674a958';

        PHPShopParser::set('cdek_city_from', $CDEKWidget->option['city_from']);
        PHPShopParser::set('russia_only', (int) $CDEKWidget->option['russia_only']);
        PHPShopParser::set('cdek_default_city', $defaultCity);
        PHPShopParser::set('cdek_cart', json_encode($cart));
        PHPShopParser::set('cdek_ymap_key', $yandex_apikey);
        PHPShopParser::set('cdek_admin', 0);
        PHPShopParser::set('cdek_scripts', '<script type="text/javascript" src="phpshop/modules/cdekwidget/js/widjet.js" charset="utf-8"/></script><script type="text/javascript" src="phpshop/modules/cdekwidget/js/cdekwidget.js" /></script>');

        $obj->set('order_action_add', ParseTemplateReturn($GLOBALS['SysValue']['templates']['cdekwidget']['cdek_template'], true) , true);
    }
}

$addHandler = array
    (
    'order' => 'order_cdek_hook'
);
?>