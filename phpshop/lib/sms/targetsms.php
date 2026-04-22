<?php

/**
 * Отправка SMS через targetsms.ru
 * @version 1.1
 * @package PHPShopLib
 */
function SendSMS($msg, $phone = false) {
    global $PHPShopSystem;

    $query_array = array(
        'login' => $PHPShopSystem->getSerilizeParam('admoption.sms_user'),
        'password' => $PHPShopSystem->getSerilizeParam('admoption.sms_pass'),
        'target' => $PHPShopSystem->getSerilizeParam('admoption.sms_phone'),
        'message' => PHPShopString::win_utf8($msg),
        'sender' => $PHPShopSystem->getSerilizeParam('admoption.sms_name')
    );

    if (!empty($phone))
        $query_array['target'] = $phone;

    $param = array(
        'security' => array('login' => $query_array['login'], 'password' => $query_array['password']), 
        'type' => 'sms',
        'message' => array( 
            array(
                'type' => 'sms',
                'sender' => $query_array['sender'],
                'text' => $query_array['message'],
                'abonent' => array(
                    array('phone' => $query_array['target'])
                ),
                'name_delivery'=>'phpshop',
            ),
    ));



    $param_json = json_encode($param, true);

    $href = 'https://sms.targetsms.ru/sendsmsjson.php';

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'charset=utf-8', 'Expect:'));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $param_json);
    curl_setopt($ch, CURLOPT_TIMEOUT, 600);
    curl_setopt($ch, CURLOPT_URL, $href);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    $res = curl_exec($ch);
    $result = json_decode($res, true);
    curl_close($ch);
    return $result;
}

?>