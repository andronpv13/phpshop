<?php

/**
 * Библиотека работы с CDEK Fulfillment API
 * @author PHPShop Software
 * @version 1.0
 * @package PHPShopModules
 * @todo https://cdekff.freshdesk.com/support/solutions/folders/69000518136/
 * @todo https://seller.ffcdek.ru
 */
class CDEKFulfillment {

    public $option;

    const API_URL = 'https://cdek.orderadmin.ru';
    const PRODUCT = '/api/products/offer';
    const ORDER = '/api/products/order';

    public function __construct() {
        $PHPShopOrm = new PHPShopOrm('phpshop_modules_cdekfulfillment_system');
        $this->option = $PHPShopOrm->select();
    }

    /*
     *  Данные о заказе
     */

    public function buildInfoTable($order) {
        global $PHPShopGUI, $PHPShopInterface;

        // Библиотека заказа
        $PHPShopOrder = new PHPShopOrderFunction($order['id']);

        $cdek = unserialize($order['cdekfulfillment_order_data']);

        // Дополнительный склад
        $PHPShopOrmWarehouse = new PHPShopOrm($GLOBALS['SysValue']['base']['warehouses']);
        $Warehouses = $PHPShopOrmWarehouse->select(array('*'), array('enabled' => "='1'"), array('order' => 'num DESC'), array('limit' => 100));

        foreach ($Warehouses as $warehouse) {
            $dataWarehouse[$warehouse['id']] = $warehouse;
        }

        // Знак рубля
        if ($PHPShopOrder->default_valuta_iso == 'RUB')
            $currency = ' <span class="rubznak">p</span>';
        else
            $currency = $PHPShopOrder->default_valuta_iso;

        if (is_array($cdek) and ! empty($cdek['id'])) {

            $info .= $PHPShopGUI->setField('№ Заказа', $PHPShopGUI->setText($cdek['id']));
            $info .= $PHPShopGUI->setField('Доставка', $PHPShopGUI->setText($order['cdekfulfillment_delivery_price'] . $currency));
            $info .= $PHPShopGUI->setField('Отправлен', $PHPShopGUI->setText(explode(".", $cdek['date']['date'])[0]));

            // Корзина локальный склад
            $PHPShopInterface = new PHPShopInterface();
            $PHPShopInterface->checkbox_action = false;
            $PHPShopInterface->setCaption(array("Наименование на складе " . $dataWarehouse[$this->option['warehouse_main']]['name'], "50%"), array("Цена", "15%"), array('<span class="hidden-xs">' . $dataWarehouse[$this->option['warehouse_main']]['name'] . '</span><span class="visible-xs">' . $dataWarehouse[$this->option['warehouse_main']]['name'] . '</span>', "10%", array('align' => 'center')), array('Сумма', '15%', array('align' => 'right')));

            // Корзина
            $cart = unserialize($order['orders'])['Cart']['cart'];

            if (is_array($cart) and sizeof($cart) != 0)
                foreach ($cart as $val) {

                    if (!empty($val['id'])) {

                        // Проверка сдека
                        $export_cdek_id = (new PHPShopProduct($val['id']))->getParam('export_cdek_id');

                        $code = null;
                        if (is_array($cdek['raw']['orderProducts'])) {
                            foreach ($cdek['raw']['orderProducts'] as $product) {

                                if ($export_cdek_id == $product['productOffer']['id']) {

                                    // Кол-во совпало со СДЕК
                                    if ($product['count'] == $val['num']) {
                                        $val['cdek'] = $product['count'];
                                        $val['main'] = 0;
                                    }
                                    // Не хватает в СДЕК
                                    elseif ($product['count'] < $val['num']) {

                                        $val['cdek'] = $product['count'];
                                        $val['main'] = $val['num'] - $product['count'];
                                    }
                                }
                            }
                        }

                        if (empty($val['cdek'])) {
                            $val['main'] = $val['num'];
                            $val['cdek'] = 0;
                        }

                        // Пропуск
                        if (empty($val['main'])) {
                            continue;
                        }


                        // Проверка подтипа товара
                        if (!empty($val['parent']))
                            $val['id'] = $val['parent'];
                        if (!empty($val['parent_uid']))
                            $val['uid'] = $val['parent_uid'];

                        // Артикул
                        if (!empty($val['uid']))
                            $code .= __('Артикул') . ': ' . $val['uid'];
                        else
                            $code .= __('Код') . ': ' . $val['id'];


                        if (!empty($val['pic_small']))
                            $icon = '<img src="' . $val['pic_small'] . '" onerror="this.onerror = null;this.src = \'./images/no_photo.gif\'" class="media-object">';
                        else
                            $icon = '<img class="media-object" src="./images/no_photo.gif">';

                        $name = '
<div class="media">
  <div class="media-left">
    <a href="?path=product&id=' . $val['id'] . '" >
      ' . $icon . '
    </a>
  </div>
   <div class="media-body">
    <div class="media-heading"><a href="?path=product&id=' . $val['id'] . '&return=order.' . $order['id'] . '" >' . $val['name'] . '</a></div>
    ' . $code . '
  </div>
</div>
';
                        // Цена
                        $price = $PHPShopOrder->ReturnSumma($val['price']) . $currency;
                        if (!empty((int) $val['price_n'])) {
                            $price .= '<br><s class="text-muted">' . $PHPShopOrder->ReturnSumma($val['price_n']) . '</s>' . $currency;
                        }

                        $PHPShopInterface->setRow(array('name' => $name, 'align' => 'left'), $price, array('name' => $val['main'], 'align' => 'center'), array('name' => $PHPShopOrder->ReturnSumma($val['price'] * $val['num']) . $currency, 'align' => 'right'));
                    }
                }

            $cart1 = $PHPShopInterface->getContent();

            // Корзина удаленный склад
            $PHPShopInterface = new PHPShopInterface();
            $PHPShopInterface->checkbox_action = false;
            $PHPShopInterface->setCaption(array("Наименование на складе " . $dataWarehouse[$this->option['warehouse_cdek']]['name'], "50%"), array("Цена", "15%"), array('<span class="hidden-xs">' . $dataWarehouse[$this->option['warehouse_cdek']]['name'] . '</span><span class="visible-xs">' . $dataWarehouse[$this->option['warehouse_cdek']]['name'] . '</span>', "10%", array('align' => 'center')), array('Сумма', '15%', array('align' => 'right')));

            // Корзина
            $cart = unserialize($order['orders'])['Cart']['cart'];

            if (is_array($cart) and sizeof($cart) != 0)
                foreach ($cart as $val) {

                    if (!empty($val['id'])) {

                        // Проверка сдека
                        $export_cdek_id = (new PHPShopProduct($val['id']))->getParam('export_cdek_id');

                        $code = null;
                        if (is_array($cdek['raw']['orderProducts'])) {
                            foreach ($cdek['raw']['orderProducts'] as $product) {

                                if ($export_cdek_id == $product['productOffer']['id']) {

                                    // Кол-во совпало со СДЕК
                                    if ($product['count'] == $val['num']) {
                                        $val['cdek'] = $product['count'];
                                        $val['main'] = 0;
                                    }
                                    // Не хватает в СДЕК
                                    elseif ($product['count'] < $val['num']) {

                                        $val['cdek'] = $product['count'];
                                        $val['main'] = $val['num'] - $product['count'];
                                    }
                                }
                            }
                        }

                        if (empty($val['cdek'])) {
                            $val['main'] = $val['num'];
                            $val['cdek'] = 0;
                        }

                        // Пропуск
                        if (empty($val['cdek'])) {
                            continue;
                        }

                        // Проверка подтипа товара
                        if (!empty($val['parent']))
                            $val['id'] = $val['parent'];
                        if (!empty($val['parent_uid']))
                            $val['uid'] = $val['parent_uid'];

                        // Артикул
                        if (!empty($val['uid']))
                            $code .= __('Артикул') . ': ' . $val['uid'];
                        else
                            $code .= __('Код') . ': ' . $val['id'];


                        if (!empty($val['pic_small']))
                            $icon = '<img src="' . $val['pic_small'] . '" onerror="this.onerror = null;this.src = \'./images/no_photo.gif\'" class="media-object">';
                        else
                            $icon = '<img class="media-object" src="./images/no_photo.gif">';

                        $name = '
<div class="media">
  <div class="media-left">
    <a href="?path=product&id=' . $val['id'] . '" >
      ' . $icon . '
    </a>
  </div>
   <div class="media-body">
    <div class="media-heading"><a href="?path=product&id=' . $val['id'] . '&return=order.' . $order['id'] . '" >' . $val['name'] . '</a></div>
    ' . $code . '
  </div>
</div>
';
                        // Цена
                        $price = $PHPShopOrder->ReturnSumma($val['price']) . $currency;
                        if (!empty((int) $val['price_n'])) {
                            $price .= '<br><s class="text-muted">' . $PHPShopOrder->ReturnSumma($val['price_n']) . '</s>' . $currency;
                        }

                        $PHPShopInterface->setRow(array('name' => $name, 'align' => 'left'), $price, array('name' => $val['cdek'], 'align' => 'center'), array('name' => $PHPShopOrder->ReturnSumma($val['price'] * $val['num']) . $currency, 'align' => 'right'));
                    }
                }

            $cart2 = $PHPShopInterface->getContent();

            $info .= '<table class="table table-hover cart-list-cdekfulfillment">' . $cart1 . $cart2 . '</table><script src="../modules/cdekfulfillment/admpanel/gui/cdekfulfillment.gui.js"></script>';

            if (is_array($cdek['orderProducts']))
                foreach ($cdek['orderProducts'] as $product) {
                    
                }
        }


        return $info;
    }

    /**
     * Создание заказа
     */
    public function sendOrder($order) {

        if (is_array($order)) {

            $PHPShopUser = new PHPShopUser($order['user']);
            if (empty($order['fio']))
                $order['fio'] = $PHPShopUser->getParam('name');

            // Корзина
            $cart = unserialize($order['orders']);
            foreach ($cart['Cart']['cart'] as $product) {

                $export_cdek_id = (new PHPShopProduct($product['id']))->getParam('export_cdek_id');

                // Сдек
                if (!empty($export_cdek_id)) {
                    $product_info = $this->getProduct($export_cdek_id);
                    if (is_array($product_info['items'])) {
                        foreach ($product_info['items'] as $items) {

                            // Кол-во товара на складе СДЕК
                            if ($items['state'] == 'normal' and $items['warehouse'] == $this->option['warehouse_id'] and $items['count'] > 0) {

                                // Проверка кол-во в заказе со сдеком
                                if ($product['num'] > $items['count']) {

                                    // Товары на главном складе
                                    $mainProduct[] = [
                                        "productOffer" => $product['id'],
                                        "count" => $product['num'] - $items['count'],
                                    ];

                                    $product['num'] = $items['count'];
                                }

                                // Товары в сдеке
                                $orderProduct[] = [
                                    "productOffer" => $export_cdek_id,
                                    "count" => $product['num'],
                                    "price" => 0, //$product['price']
                                ];
                            }
                        }
                    }
                    // Главный
                    else {

                        // Товары на главном складе
                        $mainProduct[] = [
                            "productOffer" => $product['id'],
                            "count" => $product['num'],
                        ];
                    }
                }
                // Главный
                else {

                    // Товары на главном складе
                    $mainProduct[] = [
                        "productOffer" => $product['id'],
                        "count" => $product['num'],
                    ];
                }
            }

            if ($this->option['paid'] == 1)
                $paid = 'paid';
            else
                $paid = 'not_paid';

            $phone = trim(str_replace(array('(', ')', '-', '+', '&#43;'), '', $order['tel']));

            // Проверка на первую 7 или 8
            $first_d = substr($phone, 0, 1);
            if ($first_d != 8 and $first_d != 7)
                $phone = '7' . $phone;
            
            // Проверка нулевой цены доставки
            if(empty($order['cdekfulfillment_delivery_price'])){
                $order['cdekfulfillment_delivery_price'] = $this->getDeliveryPrice($cart['Cart']['weight']);
                (new PHPShopOrm($GLOBALS['SysValue']['base']['orders']))->update(
                ['cdekfulfillment_delivery_price_new' => $order['cdekfulfillment_delivery_price']], ['id' => '=' . (int) $order['id']]);
            }

            if (is_array($orderProduct)) {
                $params = [
                    "profile" => [
                        "name" => PHPShopString::win_utf8($order['fio']),
                        "email" => PHPShopString::win_utf8($PHPShopUser->getParam('mail')),
                    ],
                    "phone" => trim(str_replace(array('(', ')', '-', '+', '&#43;'), '', $phone)),
                    "shop" => $this->option['shop_id'],
                    "paymentState" => $paid,
                    "orderProducts" => $orderProduct,
                    "address" => [
                        "postcode" => trim(PHPShopString::win_utf8($order['index'])),
                        "street" => trim(PHPShopString::win_utf8($order['street'])),
                        "house" => trim(PHPShopString::win_utf8($order['house'])),
                        "apartment" => trim(PHPShopString::win_utf8($order['flat'])),
                        "locality" => [
                            "name" => trim(PHPShopString::win_utf8($order['city'])),
                            "type" => trim(PHPShopString::win_utf8("город")),
                            "country" => 28
                        ]
                    ],
                    "comment"=>trim(PHPShopString::win_utf8($order['city'])),
                    "eav" => [
                        "order-reserve-warehouse" => $this->option['warehouse_id']
                    ],
                    "extId" => $order['uid'],
                    "deliveryRequest" => [
                        "deliveryService" => 1,
                        "rate" => $this->option['rate'],
                        "sender" => $this->option['sender'],
                        "retailPrice" => (int) $order['cdekfulfillment_delivery_price']
                    ]
                ];

                $result = $this->request(self::ORDER, $params);

                // Журнал
                $log['params'] = $params;
                $log['result'] = $result;

                if (!isset($result['id'])) {
                    $this->log($log, $order['uid'], self::ORDER, __('Ошибка передачи заказа'));
                } else {
                    (new PHPShopOrm('phpshop_orders'))->update(array('cdekfulfillment_order_data_new' => serialize($result)), array('id' => "='" . $order['id'] . "'"));
                    $this->log($log, $order['uid'], self::ORDER, __('Успешная передача заказа'));
                    return ['cdek' => $orderProduct, 'main' => $mainProduct, 'id' => $result['id']];
                }
            } else
                return ['main' => $mainProduct];
        }
    }

    /**
     *  Стоимость доставки
     */
    public function getDeliveryPrice($weight) {
        $price = (int) $this->option['price'];
        $taxa = (int) $this->option['fee'];
        $fee = 100; // гр
        // Такса за вес
        if (!empty($taxa)) {
            $delivery = $price + ceil($weight / $fee) * $taxa;
        } else {
            $delivery = $price;
        }

        return $delivery;
    }

    /**
     * Обновление товара
     */
    public function updateProduct($prod) {

        if (is_array($prod)) {

            $params = [
                "name" => PHPShopString::win_utf8($prod['name']),
                "sku" => PHPShopString::win_utf8($prod['uid']),
                "purchasingPrice" => 1,
                "weight" => (int) $prod['weight'],
                "barcodes" => [$prod['barcode_cdek']],
                "dimensions" => [
                    "x" => (int) $prod['width'] * 10,
                    'y' => (int) $prod['height'] * 10,
                    "z" => (int) $prod['length'] * 10,
                ],
            ];


            $result = $this->request(self::PRODUCT . '/' . $this->option['shop_id'] . '/' . $prod['export_cdek_id'], $params, 'PATCH');

            // Журнал
            $log['params'] = $params;
            $log['result'] = $result;

            if (!isset($result['id']))
                $this->log($log, $prod['id'], self::PRODUCT . '/' . $this->option['shop_id'] . '/' . $prod['export_cdek_id'], __('Ошибка обновления товара'));
            else
                $this->log($log, $prod['id'], self::PRODUCT . '/' . $this->option['shop_id'] . '/' . $prod['export_cdek_id'], __('Успешное обновление товара'));

            return $result;
        }
    }

    /**
     * Создание товара
     */
    public function sendProduct($prod) {

        if (is_array($prod)) {

            $params = [
                "state" => "normal",
                "type" => "simple",
                "shop" => $this->option['shop_id'],
                "name" => PHPShopString::win_utf8($prod['name']),
                "article" => "",
                "sku" => PHPShopString::win_utf8($prod['uid']),
                "extId" => PHPShopString::win_utf8($prod['uid']),
                "barcodes" => [$prod['barcode_cdek']],
                "image" => 'https://' . $_SERVER['SERVER_NAME'] . $prod['pic_big'],
                "price" => 0,
                "purchasingPrice" => 1,
                "weight" => (int) $prod['weight'],
                "dimensions" => [
                    "x" => (int) $prod['width'] * 10,
                    'y' => (int) $prod['height'] * 10,
                    "z" => (int) $prod['length'] * 10,
                ],
            ];


            $result = $this->request(self::PRODUCT, $params);

            // Журнал
            $log['params'] = $params;
            $log['result'] = $result;

            if (!isset($result['id']))
                $this->log($log, $prod['id'], self::PRODUCT, __('Ошибка создания товара'));
            else
                $this->log($log, $prod['id'], self::PRODUCT, __('Успешное создание товара'));

            return $result;
        }
    }

    /**
     *  Цена товара
     */
    protected function price($price, $baseinputvaluta) {

        // Если валюта отличается от базовой
        if ($baseinputvaluta !== $this->defvaluta) {
            $vkurs = $this->PHPShopValuta[$baseinputvaluta]['kurs'];

            // Если курс нулевой или валюта удалена
            if (empty($vkurs))
                $vkurs = 1;

            // Приводим цену в базовую валюту
            $price = $price / $vkurs;
        }

        $price = ($price + (($price * $this->percent) / 100));
        $price = round($price, intval($this->format));

        if (empty($price))
            $price = 0;

        return $price;
    }

    public function getProduct($id) {

        $result = $this->request(self::PRODUCT . '/' . $this->option['shop_id'] . '/' . $id);

        // Журнал
        $log['params'] = null;
        $log['result'] = $result;

        $this->log($log, $id, self::PRODUCT . '/' . $this->option['shop_id'] . '/' . $id);

        return $result;
    }

    public function getProductList($path = false) {

        if (empty($path)) {
            $path = self::PRODUCT;
            $api_url = false;
        } else
            $api_url = true;

        $result = $this->request($path, false, false, $api_url);

        // Журнал
        $log['params'] = null;
        $log['result'] = $result;

        $this->log($log, null, $path);

        return $result;
    }

    private function request($operation, $parameters = false, $header = false, $api_url = false) {

        if (empty($api_url))
            $operation = self::API_URL . $operation;

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $operation);

        if (is_array($parameters)) {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($parameters));
        }

        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLINFO_HEADER_OUT, true);

        if (!empty($header))
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $header);

        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Authorization: Basic ' . base64_encode($this->option['account'] . ":" . $this->option['password']),
            'Content-Type: application/json',
        ));

        $result = curl_exec($ch);

        $curlError = curl_error($ch);
        if ($curlError) {
            throw new \Exception($curlError);
        }

        curl_close($ch);

        $json = json_decode($result, true);
        return $json;
    }

    /**
     * Запись в журнал
     */
    public function log($message, $id, $type, $status = false) {

        if (!empty($this->option['log'])) {

            $PHPShopOrm = new PHPShopOrm('phpshop_modules_cdekfulfillment_log');

            $log = array(
                'message_new' => serialize($message),
                'order_id_new' => $id,
                'type_new' => $type,
                'date_new' => time(),
                'status_new' => $status
            );

            $PHPShopOrm->insert($log);
        }
    }

}
