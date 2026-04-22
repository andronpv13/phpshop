<?php

include_once dirname(__DIR__) . '/class/Saferoute.php';

function send_to_order_saferoutewidget_hook($obj, $row, $rout) {

    $Saferoute = new Saferoute();

    if (in_array($_POST['d'], @explode(",", $Saferoute->options['delivery_id'])) and !empty($_POST['saferouteSum'])) {

        if ($rout == 'START') {

            $obj->delivery_mod = number_format($_POST['saferouteSum'], 0, '.', ' ');

            // Token
            $_POST['saferoute_token_new'] = $_POST['saferouteToken'];
            
            $ddelivery_info = json_fix_utf(json_decode(PHPShopString::win_utf8($_POST['saferouteData']), true));
            if(is_array($ddelivery_info)) {
                
                // Город
                $_POST['city_new'] = str_replace('г. ', '', $ddelivery_info['city']['name']);
                
                // Доставка
                if(!isset($ddelivery_info['delivery']['point'])) {
                    $_POST['street_new'] = $ddelivery_info['contacts']['address']['street'];
                    $_POST['flat_new'] = $ddelivery_info['contacts']['address']['flat'];
                    $_POST['house_new'] = $ddelivery_info['contacts']['address']['house'];
                }
                // Point
                else {
                    $_POST['street_new'] = $ddelivery_info['delivery']['point']['address'];
                }
            }
            
            // Информация по доставке в комментарий заказа
            $obj->manager_comment = $_POST['saferouteReq'];
            $obj->set('deliveryInfo', $_POST['saferouteReq']);
        }


        if ($rout == 'MIDDLE' and $Saferoute->options['status'] == 0) {

            $params = array(
                'id' => $_POST['saferouteToken'],
                'cmsId' => $obj->ouid,
                'paymentMethod' => PHPShopString::win_utf8($obj->get('payment'))
            );

            $result = json_decode($Saferoute->sendOrder($params), true);
            if($result['status'] == 200)
                $_POST['saferoute_token_new'] = '';
            
        }
    }
}

$addHandler = array ('send_to_order' => 'send_to_order_saferoutewidget_hook');
?>