<?php
/**
 * Библиотека работы с ЮКасса API
 * @author PHPShop Software
 * @version 1.2
 * @package PHPShopModules
 * @todo https://yookassa.ru/developers/payment-acceptance/receipts/54fz/yoomoney/payments#process-settlement-receipt
 */
class YandexKassa {

    const YANDEX_KASSA_PAYMENT_ID = 10004;

    private $apiUrl = 'https://api.yookassa.ru/v3/';

    /** @var array */
    public $options;

    /** @var PHPShopSystem */
    public $PHPShopSystem;

    /** @var int */
    private $tax;

    /** @var int */
    private $taxDelivery;

    public function __construct() {
        $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['yandexkassa']['yandexkassa_system']);
        $this->options = $PHPShopOrm->select();

        $this->PHPShopSystem = new PHPShopSystem();
        $ndsEnabled = $this->PHPShopSystem->getParam('nds_enabled');
        $nds = $this->PHPShopSystem->getParam('nds');
        $this->tax = $this->setTax(empty($ndsEnabled) ? 2 : $nds);

        if ($this->options['payment_mode'] == 1)
            $this->payment_mode = 'full_prepayment';
        else
            $this->payment_mode = 'full_payment';
    }

    /**
     * Сборка массива товаров.
     * @param array $cart
     * @param int $discount
     * @return array
     */
    public function prepareProducts($cart, $discount) {
        $items = array();
        $total = 0;
        foreach ($cart as $product) {
            if ($discount > 0 && empty($product['promo_price']))
                $price = $product['price'] - ($product['price'] * $discount / 100);
            else
                $price = $product['price'];

            // НДС
            $PHPShopProduct = new PHPShopProduct($product['id']);
            $yandex_vat_code = $PHPShopProduct->getParam('yandex_vat_code');
            if (!empty($yandex_vat_code))
                $tax = $yandex_vat_code;
            else
                $tax = $this->tax;

            $items[] = array(
                'description' => PHPShopString::win_utf8($product['name']),
                'quantity' => $product['num'],
                'amount' => array(
                    'value' => number_format($price, 2, '.', ''),
                    'currency' => 'RUB'
                ),
                'vat_code' => $tax,
                'payment_subject' => 'commodity',
                'payment_mode' => $this->payment_mode,
            );

            $total = number_format($total + (int) $product['num'] * $price, 2, '.', '');
        }

        return array('items' => $items, 'total' => $total);
    }

    /**
     * @param int $deliveryCost
     * @param null|int $deliveryNds
     * @return array|null
     */
    public function prepareDelivery($deliveryCost = 0, $deliveryNds = null) {
        if ($deliveryCost == 0) {
            return null;
        }

        $this->taxDelivery = $this->setTax($deliveryNds);

        return array(
            'description' => PHPShopString::win_utf8('Доставка',true),
            'quantity' => 1,
            'amount' => array(
                'value' => number_format($deliveryCost, 2, '.', ''),
                'currency' => 'RUB'
            ),
            'vat_code' => $this->taxDelivery,
            'payment_subject' => 'service',
            'payment_mode' => $this->payment_mode,
        );
    }

    /**
     *  Второй чек
     */
    public function createReceipt($items, $payment_id, $order_id, $email, $delivery) {
        // Если есть доставка - добавляем в общий массив товаров
        if (is_array($delivery)) {
            $items['items'][] = $delivery;
            $items['total'] = number_format($delivery['amount']['value'] + $items['total'], 2, '.', '');
        }

        $parameters = array(
            'customer' => array(
                'email' => $email,
            ),
            'payment_id' => $payment_id,
            'type' => 'payment',
            'send' => true,
            'items' => $items['items'],
            'settlements' => array(
                array(
                    'type' => 'prepayment',
                    'amount' => array(
                        'value' => number_format($items['total'], 2, '.', ''),
                        'currency' => 'RUB',
                    )
                )
        ));


        $payment = $this->request($parameters,'receipts');

        isset($payment['type']) && $payment['type'] === 'error' ? $status = 'Ошибка регистрации чека' : $status = 'Чек успешно зарегистрирован';

        $this->log(array('request' => $parameters, 'response' => $payment), $order_id, $status, 'Второй чек', $payment['id'], isset($payment['status']) ? $payment['status'] : null);

        return $payment;
    }

    /**
     *  Создание ссылки на оплату
     */
    public function createPayment($items, $orderNumber, $email, $delivery = null) {
        // Если есть доставка - добавляем в общий массив товаров
        if (is_array($delivery)) {
            $items['items'][] = $delivery;
            $items['total'] = number_format($delivery['amount']['value'] + $items['total'], 2, '.', '');
        }

        $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['orders']);
        $PHPShopOrm->debug = false;
        $order = $PHPShopOrm->getOne(array('id'), array('uid' => "='" . $orderNumber . "'"));

        $protocol = self::isHttps() ? 'https://' : 'http://';

        $parameters = array(
            'amount' => array(
                'value' => $items['total'],
                'currency' => 'RUB'
            ),
            'description' => PHPShopString::win_utf8($this->PHPShopSystem->getName()) . ' - ' . $orderNumber,
            'receipt' => array(
                'customer' => array(
                    'email' => $email
                ),
                'items' => $items['items']
            ),
            'confirmation' => array(
                'type' => 'redirect',
                'locale' => 'ru_RU',
                'return_url' => $protocol . $_SERVER['SERVER_NAME'] . '/yandexkassa/?order=' . base64_encode($order['id'])
            ),
            'capture' => true,
            'metadata' => array(
                'cms_name' => 'api_phpshop'
            )
        );

        $payment = $this->request($parameters,'payments');

        isset($payment['type']) && $payment['type'] === 'error' ? $status = 'Ошибка регистрации платежа' : $status = 'Платеж успешно зарегистрирован';

        $this->log(array('request' => $parameters, 'response' => $payment), $order['id'], $status, 'Регистрация платежа', $payment['id'], isset($payment['status']) ? $payment['status'] : null);

        return $payment;
    }

    /**
     * @param $orderId
     * @param string $statusCode
     * @throws Exception
     */
    public function getLogDataByOrderId($orderId, $statusCode = 'pending') {
        $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['yandexkassa']['yandexkassa_log']);

        $log = $PHPShopOrm->getOne(array('*'), array('`order_id`=' => '"' . $orderId . '"', '`status_code`=' => '"' . $statusCode . '"'));

        if (!$log) {
            $this->log(array('orderId' => $orderId, 'status_code' => $statusCode), $orderId, 'Ошибка поиска зарегистрированной оплаты', 'Поиск заказа в журнале модуля');
            throw new \Exception('Запись в журнале о заказе не найдена.');
        }

        return $log;
    }

    /**
     * @param $yandexId
     * @param string $statusCode
     */
    public function findLogDataByYandexId($yandexId, $statusCode = 'pending') {
        $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['yandexkassa']['yandexkassa_log']);

        return $PHPShopOrm->getOne(array('*'), array('`yandex_id`=' => '"' . $yandexId . '"', '`status_code`=' => '"' . $statusCode . '"'));
    }

    public function isPaid() {
        
    }

    public function getOrderStatus($yandexOrderId) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->apiUrl . 'payments/' . $yandexOrderId);
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($ch, CURLOPT_USERPWD, $this->options['shop_id'] . ':' . $this->options['api_key']);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $result = json_decode(curl_exec($ch), true);
        curl_close($ch);

        return $result;
    }

    /**
     * @param int $peymentId
     * @return bool
     */
    public static function isYandexKassaPaymentMethod($peymentId) {
        return (int) $peymentId === self::YANDEX_KASSA_PAYMENT_ID;
    }

    public function request($parameters = array(),$path='payments') {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->apiUrl.$path);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Idempotence-Key: ' . uniqid('', true),
            'Content-Type: application/json',
            'Content-Length: ' . strlen(json_encode($parameters))
        ));
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($ch, CURLOPT_USERPWD, $this->options['shop_id'] . ':' . $this->options['api_key']);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($parameters));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $result = json_decode(curl_exec($ch), true);
        curl_close($ch);

        return $result;
    }

    /**
     * @param int $currentStatus
     * @return array
     */
    public static function getOrderStatuses($currentStatus) {
        $PHPShopOrderStatusArray = new PHPShopOrderStatusArray();
        $OrderStatusArray = $PHPShopOrderStatusArray->getArray();
        $order_status_value[] = array(__('Новый заказ'), 0, $currentStatus);
        if (is_array($OrderStatusArray))
            foreach ($OrderStatusArray as $order_status)
                $order_status_value[] = array($order_status['name'], $order_status['id'], $currentStatus);

        return $order_status_value;
    }

    public static function isHttps() {
        return !empty($_SERVER['HTTPS']) && 'off' !== strtolower($_SERVER['HTTPS']);
    }

    /**
     * @param $message
     * @param $order_id
     * @param $status
     * @param $type
     * @param null $yandexId
     * @param null $statusCode
     */
    public function log($message, $order_id, $status, $type, $yandexId = null, $statusCode = null) {

        $orderIdArray = explode('-', $order_id);

        $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['yandexkassa']['yandexkassa_log']);
        $log = array(
            'message_new' => serialize($message),
            'order_id_new' => $orderIdArray[0],
            'status_new' => __($status),
            'type_new' => __($type),
            'yandex_id_new' => $yandexId,
            'status_code_new' => $statusCode,
            'date_new' => time()
        );
        $PHPShopOrm->insert($log);
    }

    private function setTax($tax = null) {
        if ($tax == "") {
            return 1;
        }

        switch ($tax) {
            case 0:
                $result = 2;
                break;
            case 5:
                $result = 7;
                break;
            case 10:
                $result = 3;
                break;
            case 18:
                $result = 4;
                break;
            case 20:
                $result = 4;
                break;
             case 22:
                $result = 11;
                break;
            default: $result = 2;
        }

        return $result;
    }

}
