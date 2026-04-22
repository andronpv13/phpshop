<?php

class NovaPay
{
    const NOVAPAY_PAYMENT_ID = 10023;
    const TEST_API_URL = 'https://api-qecom.novapay.ua/v1';
    const API_URL = 'https://api-ecom.novapay.ua/v1';
    const TEST_MERCHANT_ID = 1;

    public $options = array();

    public function __construct() {
        $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['novapay']['novapay_system']);
        $this->options = $PHPShopOrm->select();
    }

    public function createPayment($orderNumber) {
        $order = $this->getOrder($orderNumber);
        $orders = unserialize($order['orders']);

        $sessionId = $this->createSession($order);

        $parameters = array(
            'merchant_id' => $this->getMerchantId(),
            'session_id'  => $sessionId,
            'external_id' => $order['uid'],
            'amount'      => (float) $order['sum'],
            'products'    => $this->getProducts($orders['Cart']['cart'], (float) $orders['Person']['discount']),
            'use_hold'    => false
        );

        if((float) $orders['Cart']['weight'] < 100) {
            $orders['Cart']['weight'] = 100;
        }

        // Интеграция с модулем Новая Почта.
        if(isset($order['np_order_data']) && !empty($order['np_order_data'])) {
            $novaPoshta = unserialize($order['np_order_data']);
            $parameters['use_hold'] = true;
            $parameters['delivery'] = array(
                'volume_weight'       => 0.0004,
                'weight'              => $orders['Cart']['weight'] / 1000,
                'recipient_city'      => $novaPoshta['recipient_city_ref'],
                'recipient_warehouse' => $novaPoshta['recipient_warehouse_ref']
            );
        }

        $payment = $this->request('payment', $parameters);

        if(isset($payment['url']) && !empty($payment['url'])) {
            $this->log(array('request' => $parameters, 'response' => $payment), $order['uid'], 'Платеж успешно создан.', 'create_payment_success');

            return $payment['url'];
        }

        $this->log(array('request' => $parameters, 'response' => $payment), $order['uid'], 'Ошибка создания платежа.', 'create_payment_error');

        throw new \Exception('Ошибка создания сессии платежа.');
    }

    public function createSession($order) {
        $fioArr = explode(' ', $order['fio']);

        $parameters = array(
            'merchant_id'       => $this->getMerchantId(),
            'client_first_name' => isset($fioArr[1]) ? PHPShopString::win_utf8($fioArr[1]) : '',
            'client_last_name'  => PHPShopString::win_utf8($fioArr[0]),
            'client_patronymic' => isset($fioArr[2]) ? PHPShopString::win_utf8($fioArr[2]) : '',
            'client_phone'      => '+' . trim(str_replace(array('(', ')', '-', '+', '&#43;'), '', $order['tel'])),
            'callback_url'      => self::getSiteUrl() . '/phpshop/modules/novapay/payment/check.php',
            'success_url'       => self::getSiteUrl() . '/success/?status=success&novapay=1&uid=' . $order['uid'],
            'fail_url'          => self::getSiteUrl() . '/success/?status=fail'
        );

        $session = $this->request('session', $parameters);

        if(isset($session['id']) && !empty($session['id'])) {
            return $session['id'];
        }

        $this->log(array('request' => $parameters, 'response' => $session), $order['uid'], 'Ошибка создания сессии платежа.', 'create_session_error');

        throw new \Exception('Ошибка создания сессии платежа.');
    }

    public function request($method, $parameters = array()) {
        $url = self::API_URL . '/' . $method;
        if((int) $this->options['dev_mode'] === 1) {
            $url = self::TEST_API_URL . '/' . $method;
        }

        $handle = curl_init();
        $message = json_encode($parameters);
        $signature = $this->generateSignature($message);

        $httpHeader = array(
            'Content-Type: application/json',
            'x-sign: '.$signature,
        );

        curl_setopt($handle, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($handle, CURLOPT_POSTFIELDS, $message);
        curl_setopt($handle, CURLOPT_HTTPHEADER, $httpHeader);
        curl_setopt($handle, CURLOPT_URL, $url);
        curl_setopt($handle, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($handle, CURLOPT_SSL_VERIFYPEER, 0);

        $result = curl_exec($handle);

        curl_close($handle);

        return json_decode($result, true);
    }

    public function getMerchantId() {
        if((int) $this->options['dev_mode'] === 1) {
            return self::TEST_MERCHANT_ID;
        }

        return (int) $this->options['merchant_id'];
    }

    public function getOrder($orderNumber) {
        $orm = new PHPShopOrm('phpshop_orders');
        $order = $orm->getOne(array('*'), array('uid' => "='" . $orderNumber . "'"));

        if(is_array($order)) {
            return $order;
        }

        $this->log($orderNumber, $orderNumber, 'Ошибка поиска заказа.', 'get_order_error');

        throw new \Exception('Заказ ' . $orderNumber . ' не найден.');
    }

    public function getProducts($cart, $discount) {
        $products = array();
        foreach ($cart as $product) {
            if($discount > 0 && empty($product['promo_price']))
                $price = $product['price']  - ($product['price']  * $discount  / 100);
            else
                $price = $product['price'];

            $products[] = array(
                'description' => PHPShopString::win_utf8($product['name']),
                'count'       => (int) $product['num'],
                'price'       => (float) $price
            );
        }

        $products[] = array(
            'description' => PHPShopString::win_utf8('Доставка'),
            'count'       => (int) $product['num'],
            'price'       => 60
        );

        return $products;
    }

    public function log($message, $order_id, $status, $type, $statusCode = null) {

        $orderIdArray = explode('-', $order_id);

        $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['novapay']['novapay_log']);
        $log = array(
            'message_new' => serialize($message),
            'order_id_new' => $orderIdArray[0],
            'status_new' => $status,
            'type_new' => $type,
            'status_code_new' => $statusCode,
            'date_new' => time()
        );
        $PHPShopOrm->insert($log);
    }

    public static function isNovaPayPaymentMethod($paymentId) {
        return self::NOVAPAY_PAYMENT_ID === (int) $paymentId;
    }

    public static function getOrderStatuses($currentStatus) {
        $PHPShopOrderStatusArray = new PHPShopOrderStatusArray();
        $OrderStatusArray = $PHPShopOrderStatusArray->getArray();
        $order_status_value[] = array(__('Новый заказ'), 0, $currentStatus);
        if (is_array($OrderStatusArray))
            foreach ($OrderStatusArray as $order_status)
                $order_status_value[] = array($order_status['name'], $order_status['id'], $currentStatus);

        return $order_status_value;
    }

    public static function getSiteUrl()
    {
        if(!empty($_SERVER['HTTPS']) && 'off' !== strtolower($_SERVER['HTTPS'])) {
            return 'https://' . $_SERVER['SERVER_NAME'];
        }

        return  'http://' . $_SERVER['SERVER_NAME'];
    }

    public function generateSignature($message)
    {
        if (!openssl_sign($message, $signature, $this->options['private_key'])) {
            return false;
        }

        return base64_encode($signature);
    }

    public function verifySignature($message, $signature)
    {
        if (!is_string($signature)) {
            return false;
        }

        $signature = base64_decode($signature, true);
        if ($signature === false) {
            return false;
        }

        $result = openssl_verify($message, $signature, $this->options['public_key']);

        return $result === 1;
    }
}