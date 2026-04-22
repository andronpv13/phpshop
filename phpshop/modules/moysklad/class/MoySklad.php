<?php

/**
 * Библиотека связи с МойСклад
 * @author PHPShop Software
 * @version 1.3
 * @package PHPShopClass
 * @subpackage RestApi
 * @todo https://dev.moysklad.ru/doc/api/remap/1.2/documents/#dokumenty-zakaz-pokupatelq-zakazy-pokupatelej
 */
class MoySklad {

    var $request;

    const API_URL = 'https://api.moysklad.ru/api/remap/1.2/entity';
    const CREATE_ORDER_METHOD = 'customerorder';
    const GET_ORGANIZATIONS = 'organization';
    const GET_AGENT = 'counterparty';
    const CREATE_PRODUCT = 'product';
    const CREATE_VARIANT = 'variant';
    const CREATE_DELIVERY = 'service';
    const GET_CURRENCYS = 'currency';
    const GET_CHARACTER = 'variant/metadata';
    const CREATE_CHARACTER = 'variant/metadata/characteristics';
    const GET_PRICETYPE = '../context/companysettings/pricetype';
    const GET_REGION = 'region';
    const WEBHOOK = 'webhook';

    public function __construct($order = array()) {

        $this->PHPShopOrm = new PHPShopOrm();
        $this->PHPShopSystem = new PHPShopSystem();
        $this->nds = $this->PHPShopSystem->getParam('nds');

        /*
         * Опции модуля
         */
        $this->PHPShopOrm->objBase = $GLOBALS['SysValue']['base']['moysklad']['moysklad_system'];
        $this->option = $this->PHPShopOrm->select();
        $this->token = $this->option['token'];
        $this->account = $this->option['account'];

        /*
         * Код валюты
         */
        $this->iso = $this->PHPShopSystem->getDefaultValutaIso();

        /*
         * Исходное изображение
         */
        $this->image_source = $this->PHPShopSystem->ifSerilizeParam('admoption.image_save_source');

        /*
         * Заказ
         */
        if (isset($order['orders']) and ! empty($order['orders'])) {
            $order['orders'] = unserialize($order['orders']);
            $order['status'] = unserialize($order['status']);
        }
        $this->order = $order;
    }

    public function init() {
        $this->getProducts();
        $this->products();
        $this->customer();
        $this->delivery();
        $this->deal();
    }

    /**
     * Авторизация
     * @param string $accountId
     * @return boolean
     */
    public function checkauth($accountId) {
        if ($this->account == $accountId)
            return true;
    }

    /**
     * Обновление склада товара
     * @param string $url
     */
    public function updateWarehouse($url) {

        if (!empty($url)) {
            $fields = $this->get($url, true);

            $positions = $this->get($fields['positions']['meta']['href'], true);
            $warehouse = array();

            if (is_array($positions['rows']))
                foreach ($positions['rows'] as $row) {


                    $products = $this->get('https://api.moysklad.ru/api/remap/1.2/report/stock/bystore?filter=product=' . $row['assortment']['meta']['href'], true);
                    $path_parts = pathinfo($row['assortment']['meta']['href']);

                    if (is_array($products['rows']))
                        foreach ($products['rows'] as $rows) {

                            foreach ($rows['stockByStore'] as $stores) {
                                $warehouse[$path_parts['filename']] += $stores['stock'];
                            }
                        }
                }


            if (is_array($warehouse) and $this->PHPShopSystem->getSerilizeParam("1c_option.update_item") == 1) {
                $this->PHPShopOrm->objBase = $GLOBALS['SysValue']['base']['products'];
                $this->PHPShopOrm->_SQL = '';
                $this->PHPShopOrm->debug = false;

                foreach ($warehouse as $id => $stock) {
                    $update['items_new'] = $stock;
                    $result = $this->PHPShopOrm->update($update, array('moysklad_product_id=' => '"' . $id . '"'));

                    // Журнал
                    if (!empty($result))
                        $this->log(array('parameters' => $warehouse, 'response' => $update), null, 'Успешное обновление склада товара', 'updateWarehouse', 'success');
                }
            }
        }
    }

    /**
     * Обновление данных товара
     * @param string $url
     */
    public function updateProducts($url) {

        if (!empty($url)) {
            $fields = $this->get($url, true);

            // Тест
            //$fields[id]='0dc7fe7c-e902-11ea-0a80-064c0016071a';

            if (is_array($fields)) {

                if ($this->PHPShopSystem->getSerilizeParam("1c_option.update_name") == 1 and ! empty($fields['name']))
                    $update['name_new'] = iconv('UTF-8', 'Windows-1251', $fields['name']);

                if ($this->PHPShopSystem->getSerilizeParam("1c_option.update_description") == 1 and ! empty($fields['description']))
                    $update['description_new'] = iconv('UTF-8', 'Windows-1251', $fields['description']);

                $update['uid_new'] = iconv('UTF-8', 'Windows-1251', $fields['article']);
                $update['weight_new'] = $fields['weight'];

                if ($this->PHPShopSystem->getSerilizeParam("1c_option.update_price") == 1 and ! empty($fields['salePrices'][0]['value'])) {
                    $update['price_new'] = $fields['salePrices'][0]['value'] / 100;
                    $update['price2_new'] = $fields['salePrices'][1]['value'] / 100;
                    $update['price3_new'] = $fields['salePrices'][2]['value'] / 100;
                    $update['price4_new'] = $fields['salePrices'][3]['value'] / 100;
                    $update['price5_new'] = $fields['salePrices'][4]['value'] / 100;
                }

                $this->PHPShopOrm->objBase = $GLOBALS['SysValue']['base']['products'];
                $this->PHPShopOrm->_SQL = '';
                $result = $this->PHPShopOrm->update($update, array('moysklad_product_id=' => '"' . $fields[id] . '"'));

                // Журнал
                if (!empty($result))
                    $this->log(array('parameters' => $fields, 'response' => $update), null, 'Успешное обновление данных товара', 'updateProducts', 'success');
            }
        }
    }

    /**
     *  Работа c веб-хуками
     */
    public function webhook($action = 'list') {

        $protocol = 'http://';
        if (!empty($_SERVER['HTTPS']) && 'off' !== strtolower($_SERVER['HTTPS'])) {
            $protocol = 'https://';
        }
        $url = $protocol . $_SERVER['SERVER_NAME'] . '/phpshop/modules/moysklad/api.php';

        switch ($action) {

            case "list":
                $result = $this->get(self::WEBHOOK);
                break;

            case "on":

                // Обновление товаров
                $fields = array(
                    "url" => $url,
                    "action" => "UPDATE",
                    "entityType" => "product"
                );
                $result = $this->post(self::WEBHOOK, $fields);

                // Приемка
                $fields = array(
                    "url" => $url,
                    "action" => "CREATE",
                    "entityType" => "supply"
                );
                $result = $this->post(self::WEBHOOK, $fields);

                // Оприходование
                $fields = array(
                    "url" => $url,
                    "action" => "CREATE",
                    "entityType" => "enter"
                );
                $result = $this->post(self::WEBHOOK, $fields);

                // Отгрузка
                $fields = array(
                    "url" => $url,
                    "action" => "CREATE",
                    "entityType" => "demand"
                );
                $result = $this->post(self::WEBHOOK, $fields);

                // Списание
                $fields = array(
                    "url" => $url,
                    "action" => "CREATE",
                    "entityType" => "loss"
                );
                $result = $this->post(self::WEBHOOK, $fields);

                // Розничная продажа
                $fields = array(
                    "url" => $url,
                    "action" => "CREATE",
                    "entityType" => "retaildemand"
                );
                $result = $this->post(self::WEBHOOK, $fields);
                break;

            case "off": // Удаление хуков
                $result = $this->get(self::WEBHOOK);

                if (is_array($result['rows']))
                    foreach ($result['rows'] as $rows) {
                        $result = $this->delete($rows['meta']['href']);
                    }

                break;
        }
        return $result;
    }

    /*
     * Добавление услуги доставки.
     */

    public function delivery() {
        $this->PHPShopOrm->objBase = $GLOBALS['SysValue']['base']['delivery'];
        $this->PHPShopOrm->_SQL = '';
        $delivery = $this->PHPShopOrm->select(array('*'), array('id=' => '"' . $this->order['orders']['Person']['dostavka_metod'] . '"'));
        $this->delivery_name = $delivery['city'];
        $this->nds_delivery = $delivery['ofd_nds'];

        if (empty($delivery['moysklad_delivery_id'])) {

            $fields = array(
                "name" => PHPShopString::win_utf8($delivery['city']),
                "vat" => intval($this->nds_delivery),
                "salePrices" => array(
                    "value" => floatval($this->order['orders']['Cart']['dostavka'] * 100),
                    "currency" => array(
                        "meta" => array(
                            "href" => self::API_URL . "/currency/" . $this->option['currency'],
                            "metadataHref" => "https://api.moysklad.ru/api/remap/1.2/entity/currency/metadata",
                            "type" => "currency",
                            "mediaType" => "application/json"
                        )
                    ),
                    "priceType" => array(
                        "meta" => array(
                            "href" => self::API_URL . "../context/companysettings/pricetype/" . $this->option['pricetype'],
                            "type" => "pricetype",
                            "mediaType" => "application/json"
                        )
                    )
                )
            );

            $result = $this->post(self::CREATE_DELIVERY, $fields);

            if (!empty($result['id'])) {
                $this->PHPShopOrm->objBase = $GLOBALS['SysValue']['base']['delivery'];
                $this->PHPShopOrm->_SQL = '';
                $this->PHPShopOrm->update(array('moysklad_delivery_id_new' => "$result[id]"), array('id=' => '"' . $this->order['orders']['Person']['dostavka_metod'] . '"'));
                $this->delivery_id = $result['id'];
            } else {
                $this->log(array('parameters' => $fields, 'response' => $result), $this->order['uid'], 'Ошибка создания товара доставки', 'createDelivery', 'error');
            }
        } else
            $this->delivery_id = $delivery['moysklad_delivery_id'];
    }

    public function getProducts() {
        $product_id = array();
        foreach ($this->order['orders']['Cart']['cart'] as $cart)
            $product_id[] = $cart['id'];

        $this->PHPShopOrm->_SQL = '';
        $query = $this->PHPShopOrm->query("SELECT * FROM " . $GLOBALS['SysValue']['base']['products'] . " WHERE `id` IN ('" . implode("', '", $product_id) . "')");

        while ($row = $query->fetch_assoc()) {
            $this->products[$row['id']] = $row;
        }
    }

    /**
     * Изображения товаров
     */
    public function getPicture($pictureLink, $sourceSetting = false) {

        if (!empty($pictureLink)) {
            if (strpos('http:', $pictureLink) === false or strpos('https:', $pictureLink) === false) {

                if (!empty($sourceSetting))
                    $pictureLink = str_replace(".", "_big.", $pictureLink);

                $link = 'http://' . $_SERVER['SERVER_NAME'] . $pictureLink;
            } else
                $link = $pictureLink;

            $pictureLinkParts = explode('/', $link);

            return array(
                array(
                    'filename' => array_pop($pictureLinkParts),
                    'content' => base64_encode(file_get_contents($link))
                )
            );
        }
    }

    /*
     * Добавление характеристик.
     */

    public function getCharacteristic($categoryId) {

        $this->PHPShopOrm->objBase = $GLOBALS['SysValue']['base']['categories'];
        $this->PHPShopOrm->_SQL = '';
        $parent_titles = $this->PHPShopOrm->select(array('parent_title'), array('id=' => '"' . $categoryId . '"'));

        $this->PHPShopOrm->objBase = $GLOBALS['SysValue']['base']['parent_name'];
        $this->PHPShopOrm->_SQL = '';
        $sort = $this->PHPShopOrm->select(array('*'), array('id=' => '"' . $parent_titles['parent_title'] . '"'));

        // Поиск созданной характеристики
        if (empty($sort['moysklad_char_id']) or empty($sort['moysklad_char2_id'])) {
            $characteristics = $this->get(self::GET_CHARACTER);
            if (is_array($characteristics))
                foreach ($characteristics['rows'] as $characteristic) {

                    // Размер
                    if ($sort['name'] == $characteristic['name']) {
                        $sort['moysklad_char_id'] = $characteristic['id'];

                        // Обновление данных в базе
                        $this->PHPShopOrm->objBase = $GLOBALS['SysValue']['base']['parent_name'];
                        $this->PHPShopOrm->_SQL = '';
                        $this->PHPShopOrm->update(array('moysklad_char_id_new' => $sort['moysklad_char_id']), array('id=' => '"' . $sort['id'] . '"'));
                    }

                    // Цвет
                    if ($sort['color'] == $characteristic['name']) {
                        $sort['moysklad_char2_id'] = $characteristic['id'];

                        // Обновление данных в базе
                        $this->PHPShopOrm->objBase = $GLOBALS['SysValue']['base']['parent_name'];
                        $this->PHPShopOrm->_SQL = '';
                        $this->PHPShopOrm->update(array('moysklad_char_id2_new' => $sort['moysklad_char_id']), array('id=' => '"' . $sort['id'] . '"'));
                    }
                }
        }

        // Создание новой характеристики Размер
        if (empty($sort['moysklad_char_id'])) {

            $fields = array(
                "name" => PHPShopString::win_utf8($sort['name']),
            );

            $result = $this->post(self::CREATE_CHARACTER, $fields);

            if (!empty($result['id'])) {
                $this->PHPShopOrm->objBase = $GLOBALS['SysValue']['base']['parent_name'];
                $this->PHPShopOrm->_SQL = '';
                $this->PHPShopOrm->update(array('moysklad_char_id_new' => "$result[id]"), array('id=' => '"' . $sort['id'] . '"'));
                $sort['moysklad_char_id'] = $result['id'];
            } else {
                $this->log(array('parameters' => $fields, 'response' => $result), $this->order['uid'], 'Ошибка создания 1 характеристики товара', 'createCharacter', 'error');
            }
        }

        // Создание новой характеристики Цвет
        if (empty($sort['moysklad_char2_id'])) {

            $fields = array(
                "name" => PHPShopString::win_utf8($sort['color']),
            );

            $result = $this->post(self::CREATE_CHARACTER, $fields);

            if (!empty($result['id'])) {
                $this->PHPShopOrm->objBase = $GLOBALS['SysValue']['base']['parent_name'];
                $this->PHPShopOrm->_SQL = '';
                $this->PHPShopOrm->update(array('moysklad_char2_id_new' => "$result[id]"), array('id=' => '"' . $sort['id'] . '"'));
                $sort['moysklad_char2_id'] = $result['id'];
            } else {
                $this->log(array('parameters' => $fields, 'response' => $result), $this->order['uid'], 'Ошибка создания 2 характеристики товара', 'createCharacter', 'error');
            }
        }

        return $sort;
    }

    /*
     * Синхронизация товаров.
     * @return void
     */

    public function products() {

        foreach ($this->products as $product) {

            // Обычный товар
            if (empty($product['parent_enabled'])) {
                $method = self::CREATE_PRODUCT;
                $fields = array(
                    "name" => PHPShopString::win_utf8($product['name']),
                    "price" => floatval($this->order['orders']['Cart']['cart'][$product['id']]['price'] * 100),
                    "article" => $product['uid'],
                    "description" => PHPShopString::win_utf8($product['description']),
                    "weight" => floatval($product['weight']),
                    "vat" => intval($this->nds),
                    "salePrices" => array(
                        array(
                            "value" => floatval($this->order['orders']['Cart']['cart'][$product['id']]['price'] * 100),
                            "currency" => array(
                                "meta" => array(
                                    "href" => self::API_URL . "/currency/" . $this->option['currency'],
                                    "metadataHref" => "https://api.moysklad.ru/api/remap/1.2/entity/currency/metadata",
                                    "type" => "currency",
                                    "mediaType" => "application/json"
                                )
                            ),
                            "priceType" => array(
                                "meta" => array(
                                    "href" => "https://api.moysklad.ru/api/remap/1.2/context/companysettings/pricetype/" . $this->option['pricetype'],
                                    "type" => "pricetype",
                                    "mediaType" => "application/json"
                                )
                            )
                        )
                    ),
                    "images" => $this->getPicture($product['pic_big'])
                );
            }
            // Подтип
            else {

                // Изображение родительского товара подтипа (если у подтипа нет изображения)
                if (empty($product['pic_big'])) {
                    $product['pic_big'] = $this->order['orders']['Cart']['cart'][$product['id']]['pic_small'];
                }

                $method = self::CREATE_VARIANT;
                $PHPShopProduct = new PHPShopProduct($this->order['orders']['Cart']['cart'][$product['id']]['parent']);
                $fields = array(
                    "name" => PHPShopString::win_utf8($product['parent'] . ' ' . $product['parent2']),
                    "article" => $product['uid'],
                    "weight" => floatval($product['weight']),
                    "vat" => intval($this->nds),
                    "product" => array(
                        "meta" => array(
                            "href" => self::API_URL . "/product/" . $PHPShopProduct->getValue('moysklad_product_id'),
                            "metadataHref" => self::API_URL . "/product/metadata",
                            "type" => "product",
                            "mediaType" => "application/json"
                        )
                    ),
                    "salePrices" => array(
                        array(
                            "value" => floatval($this->order['orders']['Cart']['cart'][$product['id']]['price'] * 100),
                            "currency" => array(
                                "meta" => array(
                                    "href" => self::API_URL . "/currency/" . $this->option['currency'],
                                    "metadataHref" => "https://api.moysklad.ru/api/remap/1.2/entity/currency/metadata",
                                    "type" => "currency",
                                    "mediaType" => "application/json"
                                )
                            ),
                            "priceType" => array(
                                "meta" => array(
                                    "href" => "https://api.moysklad.ru/api/remap/1.2/context/companysettings/pricetype/" . $this->option['pricetype'],
                                    "type" => "pricetype",
                                    "mediaType" => "application/json"
                                )
                            )
                        )
                    ),
                    "images" => $this->getPicture($product['pic_big'])
                );

                // Создание характеристики
                $sort = $this->getCharacteristic($product['category']);
                if (is_array($sort)) {

                    // Размер
                    $fields['characteristics'][] = array(
                        "id" => $sort['moysklad_char_id'],
                        "value" => PHPShopString::win_utf8($product['parent'])
                    );

                    // Цвет
                    if (!empty($product['parent2'])) {
                        $fields['characteristics'][] = array(
                            "id" => $sort['moysklad_char2_id'],
                            "value" => PHPShopString::win_utf8($product['parent2'])
                        );
                    }
                }
            }

            // Если товар еще не добавлялся в CRM - добавляем
            if (empty($product['moysklad_product_id'])) {

                $result = $this->post($method, $fields);

                if (!empty($result['id'])) {
                    $this->PHPShopOrm->objBase = $GLOBALS['SysValue']['base']['products'];
                    $this->PHPShopOrm->_SQL = '';
                    $this->PHPShopOrm->update(array('moysklad_product_id_new' => "$result[id]"), array('id=' => "$product[id]"));
                    $this->products[$product['id']]['moysklad_product_id'] = $result['id'];

                    $this->log(array('parameters' => $fields, 'response' => $result), $this->order['uid'], 'Успешное создание товара', 'createProduct', 'success');
                } else {
                    $this->log(array('parameters' => $fields, 'response' => $result), $this->order['uid'], 'Ошибка создания товара', 'createProduct', 'error');
                }
            }
        }
    }

    /*
     * Синхронизация покупателя.
     *
     * @return void
     */

    public function customer() {
        $this->PHPShopOrm->objBase = $GLOBALS['SysValue']['base']['shopusers'];
        $this->PHPShopOrm->_SQL = '';
        $moysklad_client_id = $this->PHPShopOrm->select(array('moysklad_client_id'), array('id=' => '"' . $this->order['user'] . '"'));

        if (!empty($moysklad_client_id['moysklad_client_id']))
            $this->client_id = $moysklad_client_id['moysklad_client_id'];
        else {
            if (!empty($this->order['org_name']))
                $this->addCompany();
            else {
                $this->addContact();
            }
        }
    }

    /*
     * Добавление покупателя
     *
     * @return void
     */

    public function addContact() {
        $fields = array(
            'name' => PHPShopString::win_utf8($this->order['fio']),
        );

        if (!empty($this->order['tel'])) {
            $fields['phone'] = $this->order['tel'];
        }

        if (!empty($this->order['orders']['Person']['mail'])) {
            $fields['email'] = $this->order['orders']['Person']['mail'];
        }
        $result = $this->post(self::GET_AGENT, $fields);
        if (!empty($result['id'])) {
            $this->PHPShopOrm->objBase = $GLOBALS['SysValue']['base']['shopusers'];
            $this->PHPShopOrm->_SQL = '';
            $this->PHPShopOrm->update(array('moysklad_client_id_new' => "$result[id]"), array('id=' => '"' . $this->order['user'] . '"'));
            $this->client_id = $result['id'];
        } else {
            $this->log(array('parameters' => $fields, 'response' => $result), $this->order['uid'], 'Ошибка создания покупателя', 'addContact', 'error');
        }
    }

    /*
     * Добавление компании.
     *
     * @return void
     */

    public function addCompany() {
        $fields = array(
            'name' => PHPShopString::win_utf8($this->order['org_name']),
            'inn' => PHPShopString::win_utf8($this->order['org_inn']),
            'kpp' => PHPShopString::win_utf8($this->order['org_kpp']),
            'actualAddress' => PHPShopString::win_utf8($this->order['org_fakt_adress']),
            'legalAddress' => PHPShopString::win_utf8($this->order['org_yur_adress']),
        );

        if (!empty($this->order['tel'])) {
            $fields['phone'] = $this->order['tel'];
        }

        if (!empty($this->order['orders']['Person']['mail'])) {
            $fields['email'] = $this->order['orders']['Person']['mail'];
        }
        $result = $this->post(self::GET_AGENT, $fields);
        if (!empty($result['id'])) {
            $this->PHPShopOrm->objBase = $GLOBALS['SysValue']['base']['shopusers'];
            $this->PHPShopOrm->_SQL = '';
            $this->PHPShopOrm->update(array('moysklad_client_id_new' => "$result[id]"), array('id=' => '"' . $this->order['user'] . '"'));
            $this->client_id = $result['id'];
        } else {
            $this->log(array('parameters' => $fields, 'response' => $result), $this->order['uid'], 'Ошибка создания компании', 'createCompany', 'error');
        }
    }

    /*
     * Добавление сделки.
     *
     * @return void
     */

    public function deal() {

        if (empty($this->order['moysklad_deal_id'])) {

            $this->PHPShopOrm->objBase = $GLOBALS['SysValue']['base']['payment_systems'];
            $this->PHPShopOrm->_SQL = '';
            $payment_method = $this->PHPShopOrm->select(array('name'), array('id=' => '"' . $this->order['orders']['Person']['order_metod'] . '"'));

            $fields = array(
                'name' => $this->order['uid'],
                'organization' => array(
                    'meta' => array(
                        'href' => self::API_URL . "/" . $this->option['organization'],
                        'type' => 'organization',
                        "mediaType" => "application/json"
                    )
                ),
                'agent' => array(
                    'meta' => array(
                        'href' => self::API_URL . "/" . $this->client_id,
                        'type' => 'counterparty',
                        "mediaType" => "application/json"
                    )
                ),
                'description' => PHPShopString::win_utf8($this->order['status']['maneger']),
                'shipmentAddressFull' => array(
                    'apartment' => PHPShopString::win_utf8($this->order['flat']),
                    'city' => PHPShopString::win_utf8($this->order['city']),
                    'comment' => PHPShopString::win_utf8($this->order['dop_info']),
                    'house' => PHPShopString::win_utf8($this->order['house']),
                    'postalCode' => PHPShopString::win_utf8($this->order['index']),
                    'street' => PHPShopString::win_utf8($this->order['street'])
                )
            );

            // Регион
            if (!empty($this->order['state'])) {
                $region = $this->getRegion($this->order['state']);
                if (is_array($region))
                    $fields['shipmentAddressFull']['region']['meta'] = $region;
            }

            // Товары
            $rows = array();
            foreach ($this->products as $product) {

                // Товар
                if (empty($this->order['orders']['Cart']['cart'][$product['id']]['parent'])) {
                    $rows[] = array(
                        "name" => PHPShopString::win_utf8($this->order['orders']['Cart']['cart'][$product['id']]['name']),
                        "quantity" => $this->order['orders']['Cart']['cart'][$product['id']]['num'],
                        "price" => floatval($this->order['orders']['Cart']['cart'][$product['id']]['price'] * 100),
                        "discount" => 0,
                        "vat" => intval($this->nds),
                        "assortment" => array(
                            "meta" => array(
                                "href" => self::API_URL . "/product/" . $product['moysklad_product_id'],
                                "type" => "product",
                                "mediaType" => "application/json"
                            )
                        ),
                    );
                }
                // Подтип
                else {
                    $rows[] = array(
                        "name" => PHPShopString::win_utf8($this->order['orders']['Cart']['cart'][$product['id']]['name']),
                        "quantity" => $this->order['orders']['Cart']['cart'][$product['id']]['num'],
                        "price" => floatval($this->order['orders']['Cart']['cart'][$product['id']]['price'] * 100),
                        "discount" => 0,
                        "vat" => intval($this->nds),
                        "assortment" => array(
                            "meta" => array(
                                "href" => self::API_URL . "/variant/" . $product['moysklad_product_id'],
                                "metadataHref" => self::API_URL . "/variant/metadata",
                                "type" => "variant",
                                "mediaType" => "application/json"
                            )
                        ),
                    );
                }
            }

            // Доставка
            $rows[] = array(
                "name" => PHPShopString::win_utf8($this->delivery_name),
                "price" => floatval($this->order['orders']['Cart']['dostavka'] * 100),
                "quantity" => 1,
                "discount" => 0,
                "vat" => intval($this->nds_delivery),
                "assortment" => array(
                    "meta" => array(
                        "href" => self::API_URL . "/service/" . $this->delivery_id,
                        "type" => "service",
                        "mediaType" => "application/json"
                    )
                ),
            );

            $fields['positions'] = $rows;

            // Запись заказа
            $result = $this->post(self::CREATE_ORDER_METHOD, $fields);



            if (!empty($result['id'])) {

                $this->log(array(
                    'parameters' => $fields,
                    'response' => $result,
                    'products' => $fields['positions']
                        ), $this->order['id'], 'Успешная передача заказа', 'createDeal', 'success');

                $orm = new PHPShopOrm($GLOBALS['SysValue']['base']['orders']);
                $orm->update(array('moysklad_deal_id_new' => $result['id']), array('uid' => "='" . $this->order['uid'] . "'"));
            } else {
                $this->log(array('parameters' => $fields, 'response' => $result), $this->order['id'], 'Ошибка передачи заказа', 'createDeal', 'error');
            }
        }
    }

    /**
     * Выбор организации
     */
    public function getOrganizations($currentOrganization) {
        $organizations = $this->get(self::GET_ORGANIZATIONS);
        $result = array();
        if (is_array($organizations['rows'])) {
            $this->account = $organizations['rows'][0]['accountId'];
            foreach ($organizations['rows'] as $organization) {
                $result[] = array($organization['name'], $organization['id'], $currentOrganization);
            }
        }
        return $result;
    }

    /**
     * Выбор валюты
     */
    public function getCurrencys($currentCurrency) {
        $currencys = $this->get(self::GET_CURRENCYS);
        $result = array();
        if (is_array($currencys['rows']))
            foreach ($currencys['rows'] as $currency) {
                $result[] = array($currency['name'], $currency['id'], $currentCurrency);
            }

        return $result;
    }

    /**
     * Выбор региона
     */
    public function getRegion($name) {
        $regions = $this->get(self::GET_REGION . '?filter=name=~' . urlencode(PHPShopString::win_utf8($name)));
        $result = $regions['rows'][0]['meta'];
        return $result;
    }

    /**
     * Выбор типов цен
     */
    public function getPricetype($current) {
        $data = $this->get(self::GET_PRICETYPE);
        $result = array();
        if (is_array($data))
            foreach ($data['rows'] as $row) {
                $result[] = array($row['name'], $row['id'], $current);
            }

        return $result;
    }

    /**
     * @param $method
     * @param array $properties
     * @return array
     */
    public function post($method, $properties = array()) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, self::API_URL . '/' . $method);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($properties));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_ENCODING, "");
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Authorization: Bearer ' . $this->token,
            'Content-Type: application/json',
        ));

        return $this->request($ch, $method);
    }

    /**
     * @param $method
     * @return array
     */
    public function get($method, $url = false) {
        $ch = curl_init();

        if ($url)
            curl_setopt($ch, CURLOPT_URL, $method);
        else
            curl_setopt($ch, CURLOPT_URL, self::API_URL . '/' . $method);

        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_ENCODING, "");
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Authorization: Bearer ' . $this->token,
            'Content-Type: application/json',
        ));

        return $this->request($ch, $method);
    }

    /**
     * @param $method
     * @return array
     */
    public function delete($url) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Authorization: Bearer ' . $this->token,
            'Content-Type: application/json',
        ));

        return $this->request($ch, $method);
    }

    /**
     * @param $ch
     * @return array
     */
    public function request($ch, $method) {
        $result = json_decode(curl_exec($ch), true);
        $result = $this->utf8ToWindows1251($result, $method);
        return $result;
    }

    /**
     * Запись лога
     * @param array $message содержание запроса в ту или иную сторону
     * @param string $order_id номер заказа
     * @param string $status статус отправки
     * @param string $type request
     */
    public function log($message, $order_id, $status, $type, $status_code = 'succes') {

        $PHPShopOrm = new PHPShopOrm('phpshop_modules_moysklad_log');
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

    public function utf8ToWindows1251($data, $method) {

        // errors
        if (isset($data['errors'])) {
            foreach ($data['errors'] as $errorKey => $error) {
                $data['errors'][$errorKey]['error'] = iconv('utf-8', 'windows-1251', $error['error']);
            }
        }

        switch ($method) {

            // Продавец
            case self::GET_ORGANIZATIONS: {

                    if (is_array($data['rows']))
                        foreach ($data['rows'] as $key => $organization) {
                            $data['rows'][$key]['name'] = iconv('UTF-8', 'Windows-1251', $organization['name']);
                        }
                    break;
                }

            // Валюты
            case self::GET_CURRENCYS: {

                    if (is_array($data['rows']))
                        foreach ($data['rows'] as $key => $currency) {
                            $data['rows'][$key]['name'] = iconv('UTF-8', 'Windows-1251', $currency['name']);
                        }
                    break;
                }

            // Валюты
            case self::GET_PRICETYPE: {
                    if (is_array($data))
                        foreach ($data as $key => $price) {
                            $data['rows'][$key]['name'] = iconv('UTF-8', 'Windows-1251', $price['name']);
                            $data['rows'][$key]['id'] = $price['id'];
                        }
                    break;
                }

            // Характеристики
            case self::GET_CHARACTER: {
                    if (is_array($data['characteristics']))
                        foreach ($data['characteristics'] as $key => $sort) {
                            $data['rows'][$key]['name'] = iconv('UTF-8', 'Windows-1251', $sort['name']);
                            $data['rows'][$key]['id'] = $sort['id'];
                        }
                    break;
                }
        }

        return $data;
    }

}
