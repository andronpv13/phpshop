<?php

function UID_deliverywidget_product_hook($obj, $dataArray, $rout) {
    global $PHPShopSystem, $PHPShopModules, $PHPShopCache;
    if ($rout == 'MIDDLE' and !$PHPShopCache->checkBot()) {

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'http://suggestions.dadata.ru/suggestions/api/4_1/rs/iplocate/address');
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
            'ip' => $_SERVER['REMOTE_ADDR']
        ]));
        $headers = [
            'Content-Type: application/json',
            'Accept: application/json',
            'Authorization: Token ' . $PHPShopSystem->getSerilizeParam('admoption.dadata_token'),
        ];
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        $city = json_decode(curl_exec($ch), true)['location']['data']['city'];
        curl_close($ch);
        
        if(empty($city))
            $city = PHPShopString::win_utf8('Москва');
        

        PHPShopParser::set('deliveryToken', $PHPShopSystem->getSerilizeParam('admoption.dadata_token'));
        PHPShopParser::set('deliveryCity', PHPShopString::utf8_win1251($city));
        $delivery=null;
        

        if (!empty($PHPShopModules->ModValue['base']['cdekwidget'])) {
            PHPShopParser::set('delivery_name', __('СДЭК'));
            $delivery .= PHPShopParser::file($GLOBALS['SysValue']['templates']['deliverywidget']['widget_delivery'], true, false, true);
        }
        
        if (!empty($PHPShopModules->ModValue['base']['pochta'])) {
            PHPShopParser::set('delivery_name', __('Почта 1 класс'));
            $delivery .= PHPShopParser::file($GLOBALS['SysValue']['templates']['deliverywidget']['widget_delivery'], true, false, true);
        }
        
        if (!empty($PHPShopModules->ModValue['base']['yandexdelivery'])) {
            PHPShopParser::set('delivery_name', __('Яндекс.Доставка'));
            $delivery .= PHPShopParser::file($GLOBALS['SysValue']['templates']['deliverywidget']['widget_delivery'], true, false, true);
        }
        
        if (!empty($PHPShopModules->ModValue['base']['boxberrywidget'])) {
            PHPShopParser::set('delivery_name', __('Boxberry'));
            $delivery .= PHPShopParser::file($GLOBALS['SysValue']['templates']['deliverywidget']['widget_delivery'], true, false, true);
        }

        PHPShopParser::set('delivery_list', $delivery);
        $dis = PHPShopParser::file($GLOBALS['SysValue']['templates']['deliverywidget']['widget'], true, false, true);
        $obj->set('deliverywidget', $dis);
    }
}

$addHandler = array
    (
    'UID' => 'UID_deliverywidget_product_hook',
);
