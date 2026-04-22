<?php

// Настройки модуля
PHPShopObj::loadClass("array");

class PHPShopMandarinHostedArray extends PHPShopArray {

    function __construct() {
        $this->objType = 3;
        $this->objBase = $GLOBALS['SysValue']['base']['mandarinhosted']['mandarinhosted_system'];
        parent::__construct('merchant_key', 'merchant_skey', 'status', 'title', 'title_sub');
        $this->option = $this->getArray();
        $this->merchant_id = $this->option['merchant_key'];
        $this->secret_key = $this->option['merchant_skey'];
    }

    function gen_auth($merchantId, $secret) {
        $reqid = time() . "_" . microtime(true) . "_" . rand();
        $hash = hash("sha256", $merchantId . "-" . $reqid . "-" . $secret);
        return $merchantId . "-" . $hash . "-" . $reqid;
    }

    function siteURL() {
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
        $domainName = $_SERVER['HTTP_HOST'] . '/';
        return $protocol . $domainName;
    }

    function send($content) {
        $xauth = $this->gen_auth($this->merchant_id, $this->secret_key);
        $url = 'https://secure.mandarinpay.com/api/transactions';
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array('X-Auth: ' . $xauth, 'Content-type: application/json'));
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($content));
        
        $result = curl_exec($curl);
        curl_close($curl);
        return $result;
    }

}