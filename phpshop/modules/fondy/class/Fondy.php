<?php

class Fondy
{
    public static $FORM_ACTION = 'https://api.fondy.eu/api/checkout/redirect/';
    public $SIGNATURE_SEPARATOR = '|';
    public $option;

    function __construct()
    {
        $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['fondy']['fondy_system']);
        $this->option = $PHPShopOrm->select();
        $this->option['response_url'] = $this->urlSite() . '/done/';
        $this->option['server_callback_url'] = $this->urlSite() . '/success/';
    }

    public function getPaymentLink($data = false)
    {
        $data = ($data) ? $data : $this->option;
        $params = array(
            'merchant_id' => $data['merchant_id'],
            'order_id' => $data['order_id'],
            'order_desc' => $data['order_id'],
            'currency' => $data['currency'],
            'amount' => $data['amount'],
            'response_url' => $data['response_url'],
            'server_callback_url' => $data['server_callback_url'],
        );
        $params['signature'] = $this->getSignature($params, $data['password']);
        return $this->sendQuery($params);
    }

    public function getSignature($data, $password, $encoded = true)
    {
        $data = array_filter($data, function ($var) {
            return $var !== '' && $var !== null;
        });
        ksort($data);

        $str = $password;
        foreach ($data as $k => $v) {
            $str .= $this->SIGNATURE_SEPARATOR . $v;
        }

        if ($encoded) {
            return sha1($str);
        } else {
            return $str;
        }
    }

    public function sendQuery($params)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://api.fondy.eu/api/checkout/url/');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-type: application/json'));
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(array('request' => $params)));
        return json_decode(curl_exec($ch), true);
    }

    public function log($message, $order_id, $status, $type)
    {
        $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['fondy']['fondy_log']);
        $PHPShopOrm->insert(array(
            'message_new' => serialize($message),
            'order_id_new' => $order_id,
            'status_new' => $status,
            'type_new' => $type,
            'date_new' => time()
        ));
    }

    public function isLinkPayment()
    {
//        $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['fondy']['fondy_log']);
//        $data = $PHPShopOrm->select(array('*'), array('order_id' => '="' . $this->option['order_id'] . '"', 'type' => '="link"'), array('order' => 'id DESC'), array('limit' => 1));
//        if (!empty($data['status'])) {
//            return $data['status'];
//        }
        $hash = md5($this->option['order_id'] . $this->option['amount'] . $this->option['merchant_id']);
        if (isset($_SESSION[$hash])) {
            return $_SESSION[$hash];
        }
        return false;
    }

    public function getForm()
    {
        $ammount = ($this->option['amount'] * 100);
        $payment_forma = PHPShopText::setInput('hidden', 'merchant_id', $this->option['merchant_id'], false);
        $payment_forma .= PHPShopText::setInput('hidden', 'order_id', $this->option['order_id'], false);
        $payment_forma .= PHPShopText::setInput('hidden', 'order_desc', $this->option['order_desc'], false);
        $payment_forma .= PHPShopText::setInput('hidden', 'currency', $this->option['currency'], false);
        $payment_forma .= PHPShopText::setInput('hidden', 'amount', $ammount, false);
        $payment_forma .= PHPShopText::setInput('hidden', 'response_url', $this->option['response_url'], false);
        $payment_forma .= PHPShopText::setInput('hidden', 'server_callback_url', $this->option['server_callback_url'], false);
        $payment_forma .= PHPShopText::setInput('hidden', 'signature', $this->getSignature(array(
            'merchant_id' => $this->option['merchant_id'],
            'order_id' => $this->option['order_id'],
            'order_desc' => $this->option['order_desc'],
            'currency' => $this->option['currency'],
            'amount' => $ammount,
            'response_url' => $this->option['response_url'],
            'server_callback_url' => $this->option['server_callback_url']
        ), $this->option['password']), false);
        $payment_forma .= PHPShopText::setInput('submit', 'send', 'Fondy', $float = "left; margin-left:10px;", 250);

        return $payment_forma;
    }

    function urlSite()
    {
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
        $domainName = $_SERVER['HTTP_HOST'];
        return trim($protocol . $domainName, '/');
    }
}