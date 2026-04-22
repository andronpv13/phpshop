<?php

/**
 * Отправка SMS через terasms.ru (бывший smsmm.ru)
 * @author terasms.ru
 * @version 2.1
 * @package PHPShopLib
 */
function SendSMS($msg, $phone = false) {
    global $PHPShopSystem;

    $query_array = array(
        'login' => $PHPShopSystem->getSerilizeParam('admoption.sms_user'),
        'password' => $PHPShopSystem->getSerilizeParam('admoption.sms_pass'),
        'target' => $PHPShopSystem->getSerilizeParam('admoption.sms_phone'),
        'message' => PHPShopString::win_utf8($msg),
        'phpshop' => 1,
        'sender' => $PHPShopSystem->getSerilizeParam('admoption.sms_name')
    );

    if (!empty($phone))
        $query_array['target'] = $phone;

    $get_string = http_build_query($query_array);

    $fp = fsockopen("auth.terasms.ru", 80, $errno, $errstr, 30);
    if (!$fp) {
        $api_uri = 'http://auth.terasms.ru/outbox/send/';
        $get_string = http_build_query($query_array);
        $res = file_get_contents($api_uri . '?' . $get_string);
    } else {

        $out = "GET /outbox/send/send/?$get_string    HTTP/1.1\r\n";
        $out .= "Host: auth.terasms.ru\r\n";
        $out .= "Connection: Close\r\n\r\n";

        fwrite($fp, $out);
        $res = null;
        while (!feof($fp)) {
            $res.=fgets($fp, 128);
        }
        fclose($fp);
    }
}

?>