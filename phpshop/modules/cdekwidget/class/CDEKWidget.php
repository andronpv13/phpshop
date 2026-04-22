<?php

/**
 * Библиотека работы с CDEK API 2
 * @author PHPShop Software
 * @version 2.1
 * @package PHPShopModules
 * @todo https://apidoc.cdek.ru
 */
class CDEKWidget {

    const TEST_ACCOUNT = 'z9GRRu7FxmO53CQ9cFfI6qiy32wpfTkd';
    const TEST_PASSWORD = 'w24JTCv4MnAcuRTx0oHjHLDtyt3I6IBq';
    const STATUS_ORDER_PREPARED = 'prepared';
    const STATUS_ORDER_PROCESS_SENT = 'process_sent';
    const STATUS_ORDER_ERROR = 'error';
    const STATUS_ORDER_DELIVERED = 'delivered';
    const STATUS_ORDER_CANCELED = 'canceled';

    public $option;
    public $isTest = false;
    private $testDomain = 'https://api.edu.cdek.ru/';
    private $domain = 'https://api.cdek.ru/';
    private $authUrl = 'v2/oauth/token';
    private $orderUrl = 'v2/orders';
    private $citiesUrl = 'v2/location/suggest';
    private $webhooksUrl = 'v2/webhooks';
    private $token;
    private $orderId;

    public function __construct() {

        $PHPShopOrm = new PHPShopOrm('phpshop_modules_cdekwidget_system');

        /* Опции модуля */
        $this->option = $PHPShopOrm->select();

        (int) $this->option['test'] === 1 ? $this->isTest = true : $this->isTest = false;

        // Оплата в ПВЗ
        if (isset($_SESSION['cdek_delivery_paid']))
            $this->option['paid'] = (int) $_SESSION['cdek_delivery_paid'];
    }

    /**
     *  Удаление Webhook
     */
    public function delWebhook() {

        $hooks = $this->getWebhook();
        if (is_array($hooks))
            foreach ($hooks as $hook) {
                $result = $this->request($this->webhooksUrl . '/' . $hook['uuid'], false, false, true);
            }

        return $result;
    }

    /**
     *  Создание Webhook
     */
    public function getWebhook() {

        $result = $this->request($this->webhooksUrl, false, false);

        return $result;
    }

    /**
     *  Создание Webhook
     */
    public function setWebhook() {

        $host = $_SERVER['SERVER_NAME'];

        $parameters = [
            'type' => 'ORDER_STATUS',
            'url' => 'https://' . $host . '/phpshop/modules/cdekwidget/api/webhook.php'
        ];

        $result = $this->request($this->webhooksUrl, $parameters);

        return $result;
    }

    /**
     * @param array $cart
     * @param int $discount
     * @param int $paymentStatus
     * @return array
     */
    public function getProducts($cart = array(), $discount = 0, $paymentStatus = 0) {
        $products = array();
        $cartWeight = 0;
        foreach ($cart as $product) {
            if ($discount > 0 && empty($product['promo_price']))
                $price = $product['price'] - ($product['price'] * $discount / 100);
            else
                $price = $product['price'];

            if (empty($product['weight']))
                $weight = $this->option['weight'];
            else
                $weight = round($product['weight']);

            $products[] = array(
                'name' => PHPShopString::win_utf8($product['name']),
                'ware_key' => !empty($product['uid']) ? PHPShopString::win_utf8($product['uid']) : $product['id'],
                'payment' => array(
                    'value' => (int) $paymentStatus === 1 ? 0 : $price
                ),
                'cost' => $price,
                'weight' => $weight,
                'amount' => $product['num']
            );
            $cartWeight += $weight;
        }

        return array('items' => $products, 'weight' => $cartWeight);
    }

    public function getCart($cart) {
        $list = [];
        foreach ($cart as $cartItem) {
            for ($i = 1; $i <= $cartItem['num']; $i++) {

                if (empty($cartItem['parent']))
                    $cartItem['parent'] = null;

                $list[] = [
                    'length' => $this->getDimension('length', $cartItem['id'], $cartItem['parent']),
                    'width' => $this->getDimension('width', $cartItem['id'], $cartItem['parent']),
                    'height' => $this->getDimension('height', $cartItem['id'], $cartItem['parent']),
                    'weight' => (!empty((float) $cartItem['weight']) ? (float) $cartItem['weight'] / 1000 : (float) $this->option['weight'] / 1000),
                ];
            }
        }

        return $list;
    }

    /**
     * Запись лога
     * @param array $message содержание запроса в ту или иную сторону
     * @param string $order_id номер заказа
     * @param string $status статус отправки
     * @param string $type request
     */
    public function log($message, $order_id, $status, $type, $status_code = 'succes') {

        $PHPShopOrm = new PHPShopOrm('phpshop_modules_cdekwidget_log');
        $id = explode("-", $order_id);

        $log = array(
            'message_new' => serialize($message),
            'order_id_new' => $id[0],
            'status_new' => $status,
            'type_new' => $type,
            'date_new' => time(),
            'status_code_new' => $status_code
        );

        $PHPShopOrm->insert($log);
    }

    /**
     * Получение кода города
     * @param string $city город
     */
    public function getCityCode($city) {
        $result = $this->request($this->citiesUrl, ['cities?name=' . PHPShopString::win_utf8($city) . '&country_code=RU'], false);
        return $result;
    }

    public function send($order) {
        $cdek_data = unserialize($order['cdek_order_data']);
        $cart = unserialize($order['orders']);

        if ($this->option['paid'] == 1)
            $order['paid'] = 1;

        if ($this->isOrderSend($cdek_data['status'])) {
            return;
        }

        $this->orderId = $order['id'];

        if (!is_array($cdek_data)) {
            $this->log(
                    array('error' => __('Не выбран адрес доставки в виджете СДЭК')), $this->orderId, __('Ошибка передачи заказа'), __('Передача заказа службе доставки СДЭК'), 'error'
            );
        }

        if ($cdek_data['type'] === 'courier') {
            $status = unserialize($order['status']);
            $address = PHPShopString::win_utf8($status['maneger']);
        }

        if (empty($order['fio']))
            $name = $cart['Person']['name_person'];
        else
            $name = $order['fio'];

        $name = PHPShopString::win_utf8($name);
        if (empty($name))
            $name = PHPShopString::toLatin($name);

        $products = $this->getProducts($cart['Cart']['cart'], $cart['Person']['discount'], (int) $order['paid']);

        $parameters = [
            'type' => 1,
            'number' => $order['uid'],
            'tariff_code' => $cdek_data['tariff'],
            'delivery_point' => $cdek_data['type'] === 'pvz' ? $cdek_data['cdek_pvz_id'] : null,
            'delivery_recipient_cost' => [
                'value' => (int) $order['paid'] === 1 ? 0 : $cart['Cart']['dostavka']
            ],
            'recipient' => [
                'name' => $name,
                'email' => $cart['Person']['mail'],
                'phones' => [
                    ['number' => str_replace(['(', ')', ' ', '+', '-', '&#43;'], '', $order['tel'])]
                ]
            ],
            'from_location' => [
                'country_code' => 'RU',
                'code' => $this->option['city_from_code'],
                'postal_code' => $this->option['index_from'],
            ],
            'packages' => [
                [
                    'number' => $order['uid'],
                    'weight' => $products['weight'],
                    'items' => $products['items'],
                    'length' => $this->getMaxDimension($cart['Cart']['cart'], 'length'),
                    'width' => $this->getMaxDimension($cart['Cart']['cart'], 'width'),
                    'height' => $this->getMaxDimension($cart['Cart']['cart'], 'height')
                ]
            ]
        ];

        // Наценка
        if (!empty($this->option['cost']) and empty($order['paid'])) {
            $cost = (int) $cart['Cart']['dostavka'];

            $parameters['delivery_recipient_cost_adv'] = [
                [
                    'sum' => (int) $cost,
                    'threshold' => 0
                ]
            ];
        }

        if ($cdek_data['type'] !== 'pvz') {
            $parameters['to_location'] = [
                'code' => $cdek_data['city_id'],
                'country_code' => 'RU',
                'address' => $address
            ];
        }

        $result = $this->request($this->orderUrl, $parameters);

        if (!isset($result['entity']['uuid'])) {
            $this->log(
                    array('response' => $result, 'parameters' => $parameters), $this->orderId, __('Ошибка передачи заказа'), __('Передача заказа службе доставки СДЭК'), 'error'
            );
        } else {
            $orm = new PHPShopOrm('phpshop_orders');
            $cdek_data['status'] = self::STATUS_ORDER_PROCESS_SENT;
            $cdek_data['uuid'] = $result['entity']['uuid'];

            $orm->update(array('cdek_order_data_new' => serialize($cdek_data)), array('id' => "='" . $this->orderId . "'"));

            $this->log(
                    array('response' => $result, 'parameters' => $parameters), $this->orderId, __('Успешная передача заказа'), __('Передача заказа службе доставки СДЭК'), 'success'
            );
        }
    }

    /**
     * @param array $request
     * @throws Exception
     */
    public function changeAddress($request) {
        $orm = new PHPShopOrm('phpshop_orders');
        $order = $this->getOrderById($request['orderId']);

        $cart = unserialize($order['orders']);
        $cart['Cart']['dostavka'] = (float) $request['cost'];
        $sum = $cart['Cart']['sum'] + $cart['Cart']['dostavka'];

        $cdekOrderData = serialize(array(
            'type' => $request['type'],
            'city_id' => $request['city'],
            'delivery_info' => PHPShopString::utf8_win1251($request['info']),
            'cdek_pvz_id' => $request['pvz'],
            'tariff' => $request['tariff'],
            'status' => CDEKWidget::STATUS_ORDER_PREPARED,
            'status_text' => __('Ожидает отправки в СДЭК')
        ));

        $status = unserialize($order['status']);
        $status['maneger'] = PHPShopString::utf8_win1251($request['info']);

        $orm->update(['cdek_order_data_new' => $cdekOrderData, 'orders_new' => serialize($cart), 'status_new' => serialize($status), 'sum_new' => $sum], ['id' => "='" . $order['id'] . "'"]);
    }

    public function buildInfoTable($order) {
        global $PHPShopGUI, $PHPShopSystem;

        $disabledPayment = '';
        $cdek = unserialize($order['cdek_order_data']);

        if ($this->option['paid'] == 1)
            $order['paid'] == 1;

        if (!is_array($cdek)) {
            // Изменили способ доставки на СДЭК.
            $template = dirname(__DIR__) . '/templates/order_error.tpl';
        } else {
            $isSend = $this->isOrderSend($cdek['status']);
            $isClosed = $this->isOrderClosed($cdek['status']);
            if ($isSend) {
                PHPShopParser::set('cdek_hide_actions', 'display: none;');
                $disabledPayment = 'disabled="disabled"';
            }

            if ($isClosed || empty($cdek['uuid'])) {
                PHPShopParser::set('cdek_statuses_hidden', 'display: none;');
            } else {
                $statuses = $this->getOrderStatuses($cdek['uuid']);
                $statusesTable = '';
                foreach ($statuses as $status) {
                    $statusesTable .= '<tr><td>' . PHPShopString::utf8_win1251($status['name']) . '</td><td>' . date('d-m-Y h:i:s', strtotime($status['date_time'])) . '</td></tr>';
                }
                PHPShopParser::set('cdek_statuses', $statusesTable);
            }

            PHPShopParser::set('cdek_status', $cdek['status_text']);
            PHPShopParser::set('cdek_delivery_info_type', $cdek['type'] === 'pvz' ? __('Самовывоз из ПВЗ') : __('Курьерская доставка'));
            PHPShopParser::set('cdek_payment_status', $PHPShopGUI->setCheckbox("payment_status", 1, __("Заказ оплачен"), $order['paid'], $disabledPayment));
            PHPShopParser::set('cdek_delivery_info', $cdek['delivery_info']);

            if (is_array($cdek['errors'])) {
                PHPShopParser::set('cdek_errors', '<tr><td>' . __('Ошибка') . '</td><td>' . implode('<br>', $cdek['errors']) . '</td></tr>');
            } else {
                PHPShopParser::set('cdek_errors', '');
            }

            $template = dirname(__DIR__) . '/templates/order_info.tpl';
        }

        PHPShopParser::set('cdek_order_id', $order['id']);

        $cart = unserialize($order['orders']);

        $products = $this->getCart($cart['Cart']['cart']);

        if (empty($this->option['default_city']))
            $defaultCity = 'auto';
        else
            $defaultCity = $this->option['default_city'];

        // Яндекс.Карты
        $yandex_apikey = $PHPShopSystem->getSerilizeParam("admoption.yandex_apikey");
        if (empty($yandex_apikey))
            $yandex_apikey = 'cb432a8b-21b9-4444-a0c4-3475b674a958';

        PHPShopParser::set('cdek_city_from', $this->option['city_from']);
        PHPShopParser::set('cdek_default_city', $defaultCity);
        PHPShopParser::set('cdek_cart', json_encode($products));
        PHPShopParser::set('cdek_ymap_key', $yandex_apikey);
        PHPShopParser::set('cdek_admin', 1);
        PHPShopParser::set('russia_only', (int) $this->option['russia_only']);
        PHPShopParser::set('cdek_scripts', '<script type="text/javascript" src="../modules/cdekwidget/js/widjet.js" charset="utf-8"/></script><script type="text/javascript" src="../modules/cdekwidget/js/cdekwidget.js" /></script>');
        $GLOBALS['modalExternalModules'] = ParseTemplateReturn(dirname(__DIR__) . '/templates/template.tpl', true);

        return ParseTemplateReturn($template, true);
    }

    /**
     * @param $orderId
     * @throws Exception
     */
    public function getOrderById($orderId) {
        $orm = new PHPShopOrm('phpshop_orders');

        $order = $orm->getOne(array('*'), array('id' => "='" . (int) $orderId . "'"));
        if (!$order) {
            throw new \Exception('Заказ не найден');
        }

        return $order;
    }

    /**
     * @param $order
     */
    public function checkTracking($order) {
        $cdek = unserialize($order['cdek_order_data']);

        if (empty($cdek['uuid'])) {
            return;
        }

        $status = $this->request($this->orderUrl, array($cdek['uuid']), false);

        if (isset($status['entity']['cdek_number'])) {
            $orm = new PHPShopOrm('phpshop_orders');
            $orm->update(array('tracking_new' => $status['entity']['cdek_number']), array('id' => "='" . $order['id'] . "'"));
        }
    }

    public function updateOrderStatus($order) {
        $cdek = unserialize($order['cdek_order_data']);

        $isClosed = $this->isOrderClosed($cdek['status']);

        if (empty($cdek['uuid']) || $isClosed) {
            return $order;
        }

        $statuses = $this->getOrderStatuses($cdek['uuid']);

        $currentStatus = array_pop($statuses);
        if (isset($currentStatus['name'])) {
            $currentStatus['name'] = PHPShopString::utf8_win1251($currentStatus['name']);

            if ($currentStatus['code'] === 'INVALID') {
                $cdek['status'] = self::STATUS_ORDER_ERROR;
                $cdek['errors'] = $this->getOrderErrors($cdek['uuid']);
            }

            $cdek['status_text'] = $currentStatus['name'];
            $order['cdek_order_data'] = serialize($cdek);

            $orm = new PHPShopOrm('phpshop_orders');
            $orm->update(array('cdek_order_data_new' => serialize($cdek)), array('id' => "='" . $order['id'] . "'"));
        }

        return $order;
    }

    public function getOrderStatuses($uuid) {
        $status = $this->request($this->orderUrl, array($uuid), false);

        return $status['entity']['statuses'];
    }

    private function getOrderErrors($uuid) {
        $order = $this->request($this->orderUrl, array($uuid), false);

        if (is_array($order['requests'][0]['errors'])) {
            $errors = array();
            foreach ($order['requests'][0]['errors'] as $error) {
                $errors[] = PHPShopString::utf8_win1251($error['message']);
            }
        }

        return $errors;
    }

    // Заказ зарегистрирован или в процессе регистрации.
    public function isOrderSend($status) {
        return $status !== self::STATUS_ORDER_PREPARED && $status !== self::STATUS_ORDER_ERROR;
    }

    // Заказ "закрыт" если доставлен или отменен. В этих статусах не запрашиваем больше статус в СДЭК.
    private function isOrderClosed($status) {
        return $status === self::STATUS_ORDER_DELIVERED || $status === self::STATUS_ORDER_CANCELED;
    }

    /**
     * @param $method
     * @param array $params
     * @param bool $post
     * @return array
     */
    private function request($method, $params = array(), $post = true, $del = false) {
        if (empty($this->token)) {
            $this->getToken();
        }

        $domain = $this->domain;

        $ch = curl_init();
        $headers = array(
            'Authorization: Bearer ' . $this->token,
            'Content-Type: application/json'
        );


        if ($post) {
            $headers[2] = 'Content-Length: ' . strlen(json_encode($params));
            curl_setopt($ch, CURLOPT_URL, $domain . $method);
        } else {
            curl_setopt($ch, CURLOPT_URL, $domain . $method . '/' . $params[0]);
        }

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        if ($post) {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($params));
        }
        if ($del) {
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
        }

        $result = curl_exec($ch);
        curl_close($ch);

        return json_decode($result, true);
    }

    public function getToken() {
        if ($this->isTest) {
            $domain = $this->testDomain;
            $account = self::TEST_ACCOUNT;
            $password = self::TEST_PASSWORD;
        } else {
            $domain = $this->domain;
            $account = $this->option['account'];
            $password = $this->option['password'];
        }

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $domain . $this->authUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, array(
            'grant_type' => 'client_credentials',
            'client_id' => $account,
            'client_secret' => $password
        ));

        $result = curl_exec($ch);
        curl_close($ch);

        $token = json_decode($result, true);

        $this->token = $token['access_token'];
        return $this->token;
    }

    private function getDimension($field, $productId, $parent = null) {
        $product = new PHPShopProduct((int) $productId);

        if (!empty($product->getParam($field))) {
            return $product->getParam($field);
        }

        if (is_null($parent) === false) {
            $product = new PHPShopProduct((int) $parent);
            if (!empty($product->getParam($field))) {
                return $product->getParam($field);
            }
        }

        return $this->option[$field];
    }

    private function getMaxDimension($cart, $side) {
        $maxDimension = 0;
        foreach ($cart as $cartItem) {
            $productDimension = $this->getDimension($side, $cartItem['id'], $cartItem['parent']);
            if ($productDimension > $maxDimension) {
                $maxDimension = $productDimension;
            }
        }

        return $maxDimension;
    }

}
