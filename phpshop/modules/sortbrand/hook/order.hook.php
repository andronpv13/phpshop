<?php

/**
 * Адрес
 */
function order_hook_full_adres() {
    $Arg=func_get_args();
    $str=null;
    foreach($Arg as $val) {
        if(!empty($val)) $str.=$val.', ';
    }
    return substr($str,0,strlen($str)-2);
}


/**
 * Добавление кнопки быстрого заказа
 */
function order_hook($obj,$row,$rout) {

    if($rout =='END') {
        $callback=urlencode('http://'.$_SERVER['SERVER_NAME'].$obj->getValue('dir.dir').'/order/');

        $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['yandexorder']['yandexorder_system']);
        $data = $PHPShopOrm->select();

        if(empty($data['button']))
        $button_img='http://cards2.yandex.net/hlp-get/4412/png/4.png';
        else $button_img=$data['button'];

        $button='<a href="http://market.yandex.ru/addresses.xml?callback='.$callback.'"><img src="'.$button_img.'" border="0" /></a>';

        // Форма личной информации по заказу
        $cart_min=$obj->PHPShopSystem->getSerilizeParam('admoption.cart_minimum');
        if($cart_min <= $obj->PHPShopCart->getSum(false)) {
            $obj->set('yandexorder',$button);

            // Заполнеям данными из Яндекса
            if(isset($_POST['operation_id'])) {
                $adres=order_hook_full_adres($_POST['city'],$_POST['street'],'д.'.$_POST['building'],'корпус '.$_POST['suite'],'подъезд '.$_POST['entrance'],'квартира '.$_POST['flat'],'этаж '.$_POST['floor'],'метро '. $_POST['metro'],$_POST['comment']);
                $obj->set('UserTel',$_POST['phone']);
                $obj->set('UserTelCode',$_POST['phone-extra']);
                $obj->set('UserName',PHPShopString::utf8_win1251($_POST['firstname'].' '.$_POST['lastname']));
                $obj->set('UserMail',$_POST['email']);
                $obj->set('UserAdres',PHPShopString::utf8_win1251($adres));
            }

            $obj->set('orderContent',parseTemplateReturn('phpshop/modules/yandexorder/templates/main_order_forma.tpl',true));
        }
        else {

            $obj->set('orderContent',$obj->message($obj->lang('cart_minimum').' '.$cart_min,$obj->lang('bad_order_mesage_2')));
        }

    }
}

$addHandler=array
        (
        'order'=>'order_hook'
);
?>