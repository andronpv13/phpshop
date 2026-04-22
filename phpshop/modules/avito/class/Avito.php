<?php

/**
 * Библиотека работы с Avito API
 * @author PHPShop Software
 * @version 2.1
 * @package PHPShopModules
 * @todo https://www.avito.ru/autoload/documentation/templates/111801?fileFormat=xml
 * @todo https://developers.avito.ru/api-catalog/auth/documentation
 */
class Avito {

    public $avitoTypes;
    public $avitoSubTypes;
    public $avitoCategories;
    public static $options;
    protected $fake = false;

    const API_URL = 'https://api.avito.ru/';

    public function __construct() {
        global $PHPShopSystem;

        $PHPShopOrm = new PHPShopOrm('phpshop_modules_avito_system');
        $this->options = $PHPShopOrm->select();
        $this->log = $this->options['log'];
        $this->create_products = $this->options['create_products'];
        $this->status_import = $this->options['status_import'];
        $this->transition = $this->options['transition'];
        $this->type = $this->options['type'];
        $this->fee_type = $this->options['fee_type'];
        $this->fee = $this->options['fee'];
        $this->price = $this->options['price'];
        $this->export = $this->options['export'];
        $this->export_items_limit = $this->Avito->options['export_items_limit'];
        $this->PHPShopSystem = $PHPShopSystem;

        $this->getToken();

        $this->status_list = [
            'on_confirmation' => 'Ожидает подтверждения',
            'ready_to_ship' => 'Ждет отправки',
            'in_transit' => 'В пути',
            'canceled' => 'Отменный заказ',
            'delivered' => 'Доставлен покупателю',
            'on_return' => 'На возврате',
            'in_dispute' => 'По заказу открыт спор',
            'closed' => 'Заказ закрыт',
            'closed' => 'Заказ закрыт',
            'confirming' => 'Подтвержден',
        ];

        if (!empty($_SERVER['HTTPS']) && 'off' !== strtolower($_SERVER['HTTPS']))
            $this->ssl = 'https://';
        else
            $this->ssl = 'http://';
    }

    /**
     *  Получение полей категории Авито
     */
    public function getTreeAttribute($node_slug) {


        $method = '/autoload/v1/user-docs/node/' . $node_slug . '/fields';
        $result = $this->get($method, []);



        $log = [
            'result' => $result
        ];

        // Журнал
        //$this->log($log, $method);

        return $result;
    }

    /**
     *  Получение дерево категорий Авито
     */
    public function getTree() {


        $method = '/autoload/v1/user-docs/tree';
        $result = $this->get($method, []);

        $log = [
            'result' => $result
        ];

        // Журнал
        //$this->log($log, $method);

        return $result;
    }

    /**
     * Цена товара
     */
    private function price($price, $baseinputvaluta) {
        $PHPShopValuta = new PHPShopValutaArray();
        $currencies = $PHPShopValuta->getArray();
        $defvaluta = $this->PHPShopSystem->getValue('dengi');
        $percent = $this->PHPShopSystem->getValue('percent');
        $format = $this->PHPShopSystem->getSerilizeParam('admoption.price_znak');

        // Если валюта отличается от базовой
        if ($baseinputvaluta !== $defvaluta) {
            $vkurs = $currencies[$baseinputvaluta]['kurs'];

            // Если курс нулевой или валюта удалена
            if (empty($vkurs))
                $vkurs = 1;

            // Приводим цену в базовую валюту
            $price = $price / $vkurs;
        }

        return round($price + (($price * $percent) / 100), (int) $format);
    }

    /**
     *  Обновление цены
     */
    public function updatePrices($product) {

        $i = 0;
        if ($this->export != 2) {

            if (is_array($product)) {

                if (!empty($product['export_avito_id']) and strlen($product['export_avito_id']) > 2) {


                    // price columns
                    if (!empty($product['price_avito'])) {
                        $price = $product['price_avito'];
                    } elseif (!empty($product['price' . (int) $this->price])) {
                        $price = $product['price' . (int) $this->price];
                    } else
                        $price = $product['price'];

                    $price = $this->price($price, $product['baseinputvaluta']);

                    if ($this->fee > 0) {
                        if ($this->fee_type == 1) {
                            $price = $price - ($price * $this->fee / 100);
                        } else {
                            $price = $price + ($price * $this->fee / 100);
                        }
                    }

                    $prices = ['price' => (int) $price];

                    $method = '/core/v1/items/' . $product['export_avito_id'] . '/update_price';
                    $result = $this->post($method, $prices);

                    $log = [
                        'request' => $prices,
                        'result' => $result
                    ];

                    // Журнал
                    $this->log($log, $method);

                    $i++;
                }
            }
        }

        return $i;
    }

    /**
     * Обновление остатков
     */
    public function updateStocks($products) {

        $i = 0;
        if ($this->export != 1) {

            if (is_array($products)) {
                foreach ($products as $product) {

                    // Ключ обновления 
                    if ($this->options['type'] == 1)
                        $product['uid'] = $product['id'];
                    else
                        $product['uid'] = PHPShopString::win_utf8($product['uid']);

                    $items = $product['items'];

                    if ($items < 0)
                        $items = 0;

                    // Лимит остатков
                    if (!empty($this->export_items_limit) and $items > $this->export_items_limit)
                        $items = $this->export_items_limit;

                    if (!empty($product['export_avito_id']) and strlen($product['export_avito_id']) > 2) {
                        $stocks["stocks"][] = [
                            "external_id" => (string) $product['uid'],
                            "item_id" => (int) $product['export_avito_id'],
                            "quantity" => (int) $items,
                        ];
                        $i++;
                    }
                }


                $method = '/stock-management/1/stocks';
                $result = $this->put($method, $stocks);

                if (is_array($stocks) and count($stocks) < 50)
                    $log = [
                        'request' => $stocks,
                        'result' => $result
                    ];
                else
                    $log = [
                        'result' => $result
                    ];

                // Журнал
                $this->log($log, $method);
            }
        }

        return $i;
    }

    /**
     *  Создание товара
     */
    public function addProduct($product_info) {
        global $PHPShopSystem;

        $insert['name_new'] = PHPShopString::utf8_win1251($product_info['title']);
        $insert['uid_new'] = PHPShopString::utf8_win1251($product_info['id']);
        $insert['export_avito_id_new'] = PHPShopString::utf8_win1251($product_info['avitoId']);
        $insert['name_avito_new'] = PHPShopString::utf8_win1251($product_info['title']);
        $insert['export_avito_new'] = 1;
        $insert['datas_new'] = time();
        $insert['user_new'] = $_SESSION['idPHPSHOP'];

        $insert['items_new'] = 1;
        $insert['enabled_new'] = 1;
        $insert['price_new'] = $product_info['prices']['price'];
        $insert['baseinputvaluta_new'] = $PHPShopSystem->getDefaultOrderValutaId();
        $insert['weight_new'] = $product_info['weight'];
        $insert['height_new'] = $product_info['height'];
        $insert['width_new'] = $product_info['width'];
        $insert['length_new'] = $product_info['length'];
        $insert['content_new'] = PHPShopString::utf8_win1251($product_info['description']);

        $prodict_id = (new PHPShopOrm($GLOBALS['SysValue']['base']['products']))->insert($insert);

        return $prodict_id;
    }

    /**
     *  Тестовый заказ
     */
    private function fakeOrder() {
        $result = [
            "hasMore" => true,
            "orders" => [
                [
                    "availableActions" => [
                        [
                            "name" => "setTrackNumber",
                            "required" => true
                        ],
                        [
                            "name" => "setMarkings",
                            "required" => false
                        ]
                    ],
                    "createdAt" => "2016-11-01T20:44:39Z",
                    "delivery" => [
                        "buyerInfo" => [
                            "fullName" => "Пушкин Александр Сергеевич",
                            "phoneNumber" => 79876543210
                        ],
                        "courierInfo" => [
                            "address" => "Москва, ул. Каретный ряд, 16, ст. 1, к. 2",
                            "comment" => "Подъезд во дворе дома, домофон не работает"
                        ],
                        "dispatchNumber" => "0000042642072",
                        "serviceName" => "Boxberry",
                        "serviceType" => "pvz",
                        "terminalInfo" => [
                            "address" => "Москва, Настасьинский 8 стр2 6",
                            "code" => "MSK14"
                        ],
                        "trackingNumber" => "0000012642072"
                    ],
                    "id" => 5000000000,
                    "items" => [
                        [
                            "avitoId" => "2799377316",
                            "chatId" => "u2i-isUW4p7EZVu4R4Zk6ts2G",
                            "count" => 2,
                            "discounts" => [
                                [
                                    "id" => "myfriendpromo",
                                    "type" => "promocode",
                                    "value" => 10
                                ]
                            ],
                            "id" => "132768483",
                            "location" => "Новосибирск",
                            "prices" => [
                                "commission" => 10,
                                "discountSum" => 10,
                                "price" => 500,
                                "total" => 480
                            ],
                            "title" => "Кеды Venice"
                        ]
                    ],
                    "marketplaceId" => 70000000000000000,
                    "prices" => [
                        "commission" => 20,
                        "delivery" => 1000,
                        "discount" => 20,
                        "price" => 1000,
                        "total" => 960
                    ],
                    "returnPolicy" => [
                        "returnStatus" => "in_transit",
                        "trackingNumber" => "0000012642072"
                    ],
                    "schedules" => [
                        "confirmTill" => "2016-11-01T20:44:39Z",
                        "deliveryDate" => "2016-11-01T20:44:39Z",
                        "deliveryDateMaх" => "2016-11-01T20:44:39Z",
                        "deliveryDateMin" => "2016-11-01T20:44:39Z",
                        "setTermsTill" => "2016-11-01T20:44:39Z",
                        "setTrackingNumberTill" => "2016-11-01T20:44:39Z",
                        "shipTill" => "2016-11-01T20:44:39Z"
                    ],
                    "status" => "confirming",
                    "updatedAt" => "2016-11-01T20:44:39Z"
                ]
            ]
        ];

        return $result;
    }

    /**
     *  Статусы заказа
     */
    public function getStatus($name) {
        return $this->status_list[$name];
    }

    public function uploadFile() {

        $method = '/autoload/v1/upload';
        $result = $this->post($method, null);

        $log = [
            'request' => $params,
            'result' => $result
        ];

        // Журнал
        $this->log($log, $method);
    }

    /**
     *  Получение ID объявлений
     */
    public function getAvitoID($offer_id) {

        if (is_array($offer_id))
            $params['query'] = implode("|", $offer_id);

        $method = '/autoload/v2/items/avito_ids';
        $result = $this->get($method, http_build_query($params));

        $log = [
            'request' => $params,
            'result' => $result
        ];

        // Журнал
        $this->log($log, $method);

        return $result;
    }

    /**
     *  Список товаров из Авито
     */
    public function getProductList($visibility = "ALL", $offer_id = null, $cat = null, $limit = 5) {


        if ($visibility == 'ALL')
            $params['status'] = 'active,old';
        else
            $params['status'] = $visibility;

        if (!empty($cat))
            $params['category'] = $cat;


        $params['per_page'] = $limit;

        $method = '/core/v1/items';
        $result = $this->get($method, http_build_query($params));

        // Данные по товару
        if (!empty($offer_id) and is_array($result['resources'])) {
            foreach ($result['resources'] as $products_list) {

                if ($products_list['id'] == $offer_id) {
                    unset($result);
                    $result['resources'][] = $products_list;
                    continue;
                }
            }
        }


        $log = [
            'request' => $params,
            'result' => $result
        ];

        // Журнал
        $this->log($log, $method);

        return $result;
    }

    /**
     * Номер заказа
     */
    function setOrderNum() {

        $PHPShopOrm = new PHPShopOrm();
        $res = $PHPShopOrm->query("select uid from " . $GLOBALS['SysValue']['base']['orders'] . " order by id desc LIMIT 0, 1");
        $row = mysqli_fetch_array($res);
        $last = $row['uid'];
        $all_num = explode("-", $last);
        $ferst_num = $all_num[0];

        if ($ferst_num < 100)
            $ferst_num = 100;
        $order_num = $ferst_num + 1;

        // Номер заказа
        $ouid = $order_num . "-" . substr(abs(crc32(uniqid(session_id()))), 0, 3);
        return $ouid;
    }

    private function getToken() {

        $ch = curl_init();
        $headers = array(
            'accept: application/json',
            'Content-Type: application/x-www-form-urlencoded'
        );
        curl_setopt($ch, CURLOPT_URL, self::API_URL . '/token');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query(array(
            'grant_type' => 'client_credentials',
            'client_id' => $this->options['client_id'],
            'client_secret' => $this->options['сlient_secret']
        )));

        $result = curl_exec($ch);
        curl_close($ch);

        $token = json_decode($result, true);

        $this->token = $token['access_token'];
    }

    /**
     *  Смена статуса заказа
     */
    public function updateStatusOrder($orderId, $status = 'confirm') {

        $params = [
            'orderId' => (string) $orderId,
            'transition' => (string) $status
        ];


        $method = '/order-management/1/order/applyTransition';
        $result = $this->post($method, $params);

        $log = [
            'request' => $params,
            'result' => $result
        ];

        // Журнал
        $this->log($log, $method);

        return $result;
    }

    /*
     *  Данные по заказу
     */

    public function getOrder($id) {


        $params = [
            'ids' => [$id],
        ];


        $method = '/order-management/1/orders';
        $result = $this->get($method, http_build_query($params));

        if ($this->fake)
            $result = $this->fakeOrder();

        $log = [
            'request' => $params,
            'result' => $result
        ];

        // Журнал
        $this->log($log, $method);

        return $result;
    }

    /*
     *  Список заказов
     */

    public function getOrderList($date1, $date2, $status, $limit) {


        $params = [
            'dateFrom' => $date1,
            'limit' => $limit
        ];

        if (!empty($status))
            $params['status'] = $status;


        $method = '/order-management/1/orders';
        $result = $this->get($method, http_build_query($params));

        if ($this->fake)
            $result = $this->fakeOrder();

        $log = [
            'request' => $params,
            'result' => $result
        ];

        // Журнал
        $this->log($log, $method);

        return $result;
    }

    private function put($method, $parameters = array()) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, self::API_URL . $method);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            sprintf('Authorization: Bearer %s', $this->token),
            'Content-Type: application/json',
        ]);

        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, PHPShopString::json_safe_encode($parameters));

        $result = curl_exec($ch);
        curl_close($ch);

        return json_decode($result, true);
    }

    private function post($method, $parameters = []) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, self::API_URL . $method);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            sprintf('Authorization: Bearer %s', $this->token),
            'Content-Type: application/json',
        ]);

        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, PHPShopString::json_safe_encode($parameters));

        $result = curl_exec($ch);
        curl_close($ch);

        return json_decode($result, true);
    }

    private function get($method, $parameters = null) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, self::API_URL . $method . '?' . $parameters);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            sprintf('Authorization: Bearer %s', $this->token),
        ]);

        $result = json_decode(curl_exec($ch), 1);
        curl_close($ch);

        return $result;
    }

    /**
     *  Заказ уже загружен?
     */
    public function checkOrderBase($id) {

        $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['orders']);
        $data = $PHPShopOrm->getOne(['id'], ['avito_order_id' => '="' . $id . '"']);
        if (!empty($data['id']))
            return $data['id'];
    }

    // Лог
    public function log($data, $path) {

        if ($this->log == 1) {

            $PHPShopOrm = new PHPShopOrm('phpshop_modules_avito_log');

            $log = array(
                'message_new' => serialize($data),
                'order_id_new' => null,
                'date_new' => time(),
                'status_new' => null,
                'path_new' => $path
            );

            $PHPShopOrm->insert($log);
        }
    }

    /**
     * Название категории в Авито.
     * @param int $categoryId
     * @return string|null
     */
    public function getCategoryById($categoryId) {
        if (!is_array($this->avitoCategories)) {
            $orm = new PHPShopOrm('phpshop_modules_avitoapi_categories');
            $categories = $orm->getList();
            foreach ($categories as $category) {
                $this->avitoCategories[$category['id']] = $category['name'];
            }
        }

        if (isset($this->avitoCategories[$categoryId])) {
            return $this->avitoCategories[$categoryId];
        }

        return null;
    }

    public static function getOption($key) {
        if (!is_array(self::$options)) {
            $PHPShopOrm = new PHPShopOrm('phpshop_modules_avito_system');
            self::$options = $PHPShopOrm->select();
        }

        if (isset(self::$options[$key])) {
            return self::$options[$key];
        }

        return null;
    }

}
