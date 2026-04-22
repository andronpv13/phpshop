<?php

/**
 * Библиотека работы с Яндекс.Маркет API
 * @author PHPShop Software
 * @version 2.1
 * @package PHPShopClass
 * @subpackage RestApi
 * @todo https://yandex.ru/dev/market/partner-marketplace-cd/doc/dg/reference/post-campaigns-id-offer-mapping-entries-updates.html
 * @todo https://yandex.ru/dev/market/partner-api/doc/ru/reference/stocks/updateStocks
 */
class YandexMarket {

    const API_URL = 'https://api.partner.market.yandex.ru/v2/';
    const IMPORT_CONDITION = [
        'yml' => '="1"',
        'enabled' => '="1"',
        'parent_enabled' => '="0"',
        'manufacturer' => ' is not null and trim(manufacturer) != ""',
        'country_of_origin' => ' is not null and trim(country_of_origin) != ""',
        'weight' => ' is not null and trim(weight) != ""',
        'length' => ' is not null and trim(length) != ""',
        'width' => ' is not null and trim(width) != ""',
        'height' => ' is not null and trim(height) != ""'
    ];

    public $options;
    private $image_source = false;

    public function __construct() {
        $this->options = (new PHPShopOrm('phpshop_modules_yandexcart_system'))->select();
        $this->system = new PHPShopSystem();
        $this->image_source = $this->system->ifSerilizeParam('admoption.image_save_source');
        $this->type = $this->options['type'];
        $this->export = $this->options['export'];
        $this->log = $this->options['log'];
        $this->create_products = $this->options['create_products'];

        $this->status_list = [
            'PROCESSING' => 'В обработке',
            'CANCELLED' => 'Отменен',
            'DELIVERED' => 'Получен покупателем',
            'DELIVERY' => 'Передан в службу доставки',
            'PICKUP' => 'Доставлен в пункт самовывоза',
            'UNPAID' => 'Оформлен, но еще не оплачен'
        ];
    }

    /**
     *  Заказ уже загружен?
     */
    public function checkOrderBase($id) {

        $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['orders']);
        $data = $PHPShopOrm->getOne(['id'], ['yandex_order_id' => '="' . $id . '"']);
        if (!empty($data['id']))
            return $data['id'];
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

    /**
     *  Создание товара
     */
    public function addProduct($id) {
        global $PHPShopSystem;

        $product_info = $this->getProductList($visibility = "ALL", $id, null, $limit = 1)['result']['offerMappings'][0]['offer'];

        $insert['name_new'] = PHPShopString::utf8_win1251($product_info['name']);
        $insert['uid_new'] = PHPShopString::utf8_win1251($product_info['offerId']);
        $insert['yml_new'] = 1;
        $insert['datas_new'] = time();
        $insert['user_new'] = $_SESSION['idPHPSHOP'];
        $insert['barcode_new'] = $product_info['barcodes'][0];
        $insert['vendor_name_new'] = $product_info['vendor'];
        $insert['vendor_code_new'] = $product_info['vendorCode'];
        $insert['country_of_origin_new'] = $product_info['manufacturerCountriese'][0];
        $insert['items_new'] = 1;
        $insert['enabled_new'] = 1;

        // Цена
        $insert['price_new'] = $product_info['basicPrice']['value'];
        $insert['price_n_new'] = $product_info['basicPrice']['discountBase'];

        $insert['baseinputvaluta_new'] = $PHPShopSystem->getDefaultOrderValutaId();
        $insert['weight_new'] = $product_info['weightDimensions']['weight'];
        $insert['height_new'] = $product_info['weightDimensions']['height'];
        $insert['width_new'] = $product_info['weightDimensions']['width'];
        $insert['length_new'] = $product_info['weightDimensions']['length'];
        $insert['content_new'] = PHPShopString::utf8_win1251($product_info['description']);

        $prodict_id = (new PHPShopOrm($GLOBALS['SysValue']['base']['products']))->insert($insert);

        // Создание изображений
        $this->addProductImage($product_info['pictures'], $prodict_id);

        return $prodict_id;
    }

    /**
     *  Создание изображений
     */
    public function addProductImage($mediaFiles, $prodict_id) {
        global $PHPShopSystem;

        require_once $_SERVER['DOCUMENT_ROOT'] . $GLOBALS['SysValue']['dir']['dir'] . '/phpshop/lib/thumb/phpthumb.php';
        $width_kratko = $PHPShopSystem->getSerilizeParam('admoption.width_kratko');
        $img_tw = $PHPShopSystem->getSerilizeParam('admoption.img_tw');
        $img_th = $PHPShopSystem->getSerilizeParam('admoption.img_th');

        // Папка картинок
        $path = $PHPShopSystem->getSerilizeParam('admoption.image_result_path');
        if (!empty($path))
            $path = $path . '/';

        $img_load = 0;

        if (is_array($mediaFiles)) {
            foreach ($mediaFiles as $k => $img) {

                if (!empty($img)) {


                    // Файл загружен
                    if ($this->downloadFile($img, $_SERVER['DOCUMENT_ROOT'] . $GLOBALS['dir']['dir'] . '/UserFiles/Image/' . $path . 'img' . $prodict_id . '_' . ($k + 1) . '.jpg'))
                        $img_load++;
                    else
                        continue;

                    // Новое имя
                    $img = $GLOBALS['dir']['dir'] . '/UserFiles/Image/' . $path . 'img' . $prodict_id . '_' . ($k + 1) . '.jpg';

                    // Запись в фотогалерее
                    $PHPShopOrmImg = new PHPShopOrm($GLOBALS['SysValue']['base']['foto']);
                    $PHPShopOrmImg->insert(array('parent_new' => $prodict_id, 'name_new' => $img, 'num_new' => ($k + 1)));

                    $file = $_SERVER['DOCUMENT_ROOT'] . $img;
                    $name = str_replace(array(".png", ".jpg", ".jpeg", ".gif", ".PNG", ".JPG", ".JPEG", ".GIF"), array("s.png", "s.jpg", "s.jpeg", "s.gif", "s.png", "s.jpg", "s.jpeg", "s.gif"), $file);

                    if (!file_exists($name) and file_exists($file)) {

                        // Генерация тубнейла 
                        if (!empty($_POST['export_imgproc'])) {
                            $thumb = new PHPThumb($file);
                            $thumb->setOptions(array('jpegQuality' => $width_kratko));
                            $thumb->resize($img_tw, $img_th);
                            $thumb->save($name);
                        } else
                            copy($file, $name);
                    }

                    // Главное изображение
                    if ($k == 0 and ! empty($file)) {

                        $update['pic_big_new'] = $img;

                        // Главное превью
                        $update['pic_small_new'] = str_replace(array(".png", ".jpg", ".jpeg", ".gif", ".PNG", ".JPG", ".JPEG", ".GIF"), array("s.png", "s.jpg", "s.jpeg", "s.gif", "s.png", "s.jpg", "s.jpeg", "s.gif"), $img);
                    }
                }
            }

            (new PHPShopOrm($GLOBALS['SysValue']['base']['products']))->update($update, ['id' => '=' . intval($prodict_id)]);
        }
    }

    /**
     *  Загрузка изображения по ссылке 
     */
    public function downloadFile($url, $path) {
        $newfname = $path;

        $arrContextOptions = array(
            "ssl" => array(
                "verify_peer" => false,
                "verify_peer_name" => false,
            ),
        );

        $file = fopen($url, 'rb', false, stream_context_create($arrContextOptions));
        if ($file) {
            $newf = fopen($newfname, 'wb');
            if ($newf) {
                while (!feof($file)) {
                    fwrite($newf, fread($file, 1024 * 8), 1024 * 8);
                }
            }
        }
        if ($file) {
            fclose($file);
        }
        if ($newf) {
            fclose($newf);
            return true;
        }
    }

    /**
     *  Статусы заказа
     */
    public function getStatus($name) {
        return $this->status_list[$name];
    }

    /*
     *  Данные по заказу
     */

    public function getOrder($orderId, $campaign_num = false) {

        if (!empty($campaign_num))
            $campaign = $this->options['campaign_id_' . $campaign_num];
        else
            $campaign = $this->options['campaign_id'];

        if (!empty($campaign)) {

            $method = 'campaigns/' . trim($campaign) . '/orders/' . $orderId;
            $result = $this->get($method, null);

            $log = [
                'result' => $result
            ];

            // Журнал
            $this->log($log, $method);

            return $result;
        }
    }

    /*
     *  Список заказов
     */

    public function getOrderList($date1, $date2, $status, $limit, $campaign_num = false) {

        if (!empty($campaign_num))
            $campaign = $this->options['campaign_id_' . $campaign_num];
        else
            $campaign = $this->options['campaign_id'];

        if (!empty($campaign)) {
            $params = [
                'fromDate' => $date1,
                'limit' => $limit,
                'toDate' => $date2,
            ];

            if (!empty($status))
                $params['status'] = $status;


            $method = 'campaigns/' . trim($campaign) . '/orders';
            $result = $this->get($method, http_build_query($params));

            $log = [
                'request' => $params,
                'result' => $result
            ];

            // Журнал
            $this->log($log, $method);

            return $result;
        }
    }

    /**
     *  Список товаров из Яндекс.Маркет
     */
    public function getProductList($visibility = "ALL", $offer_id = null, $vendorName = null, $limit = 5) {

        $method = 'businesses/' . trim($this->options['businesses_id']) . '/offer-mappings';

        if (!empty($limit)) {
            if ($visibility == 'ALL')
                $params['archived'] = 'false';
            else
                $params['archived'] = 'true';

            if (!empty($offer_id)) {
                $params['offerIds'] = [$offer_id];
                unset($params['archived']);
            }

            if (!empty($vendorName)) {
                $params['vendorNames'] = [$vendorName];
            }

            $method .= '?limit=' . $limit;
        }

        $result = $this->post($method, $params);

        $log = [
            'request' => $params,
            'result' => $result
        ];

        // Журнал
        $this->log($log, $method);

        return $result;
    }

    // Обновление цен
    public function updatePrices($products, $campaign_num = false) {

        if ($this->export != 2) {

            if (!empty($campaign_num))
                $campaign = $this->options['campaign_id_' . $campaign_num];
            else
                $campaign = $this->options['campaign_id'];

            if (is_array($products) and ! empty($campaign)) {
                foreach ($products as $product) {

                    // Ключ обновления 
                    if ($this->options['type'] == 1)
                        $product['uid'] = $product['id'];
                    else
                        $product['uid'] = PHPShopString::win_utf8($product['uid']);

                    if (!empty($product['uid']) and strlen($product['uid']) > 2)
                        $prices["offers"][] = [
                            "offerId" => (string) $product['uid'],
                            "price" => [
                                "value" => (int) $this->getPrice($product, $campaign_num),
                                "currencyId" => "RUR"
                            ]
                        ];
                }


                $method = 'campaigns/' . trim($campaign) . '/offer-prices/updates';
                $result = $this->post($method, $prices);

                if (count($prices) < 50)
                    $log = [
                        'request' => $prices,
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
    }

    // Получение остатков по складам
    public function getWarehouse($product, $campaign_num) {

        if (!empty($campaign_num))
            $warehouse = $this->options['warehouse_' . $campaign_num];
        else
            $warehouse = $this->options['warehouse'];

        if (empty($warehouse))
            $items = $product['items'];
        else
            $items = $product['items' . $warehouse];

        return $items;
    }

    // Обновление остатков
    public function updateStocks($products, $campaign_num = false) {

        if ($this->export != 1) {

            if (!empty($campaign_num))
                $campaign = $this->options['campaign_id_' . $campaign_num];
            else
                $campaign = $this->options['campaign_id'];

            if (is_array($products) and ! empty($campaign)) {
                foreach ($products as $product) {

                    // Ключ обновления 
                    if ($this->options['type'] == 1)
                        $product['uid'] = $product['id'];
                    else
                        $product['uid'] = PHPShopString::win_utf8($product['uid']);

                    // Склад
                    $items = $this->getWarehouse($product, $campaign_num);

                    if ($items < 0)
                        $items = 0;

                    if (!empty($product['uid']) and strlen($product['uid']) > 2)
                        $skus["skus"][] = [
                            "sku" => (string) $product['uid'],
                            "items" => [[
                            "count" => (int) $items,
                                ]]
                        ];
                }


                $method = 'campaigns/' . trim($campaign) . '/offers/stocks';
                $result = $this->put($method, $skus);

                if (count($skus) < 50)
                    $log = [
                        'request' => $skus,
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
    }

    // Лог
    public function log($data, $path) {

        if ($this->log == 1) {

            $PHPShopOrm = new PHPShopOrm('phpshop_modules_yandexcart_log');

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

    public function getProductsCount() {
        $data = (new PHPShopOrm($GLOBALS['SysValue']['base']['products']))->select(["count('id') as count"], self::IMPORT_CONDITION);

        return (int) $data['count'];
    }

    public function importProducts($from, $imported, $id = false) {
        $limit = 100;
        if (($imported + $limit) >= 5000) {
            $limit = 5000 - $imported;
        }

        $orm = new PHPShopOrm($GLOBALS['SysValue']['base']['products']);

        if (empty($id)) {
            $products = $orm->getList(
                    ['*'], self::IMPORT_CONDITION, ['order' => 'id ASC'], ['limit' => $from . ', ' . $limit]
            );
        } else {
            $where = self::IMPORT_CONDITION;
            $where['id'] = '=' . $id;
            $products = $orm->getList(
                    ['*'], $where, ['order' => 'id ASC'], ['limit' => '10']
            );
        }

        if (count($products) === 0) {
            return 0;
        }

        $categories = array_column(
                (new PHPShopOrm($GLOBALS['SysValue']['base']['categories']))
                        ->getList(['id', 'name'], ['id' => sprintf(' IN (%s)', implode(',', array_column($products, 'category')))]), 'name', 'id'
        );

        $modules = array_column((new PHPShopOrm('phpshop_modules'))->getList(['path']), 'path');

        $data = [];

        foreach ($products as $product) {

            $urls = [];
            $pictures = [];

            if (empty($product['market_sku'])) {
                $product['market_sku'] = $this->getMarketSku($product, $categories[$product['category']]);
            }

            // Стандартный урл
            $url = '/shop/UID_' . $product['id'] . '.html';

            // SEOURL
            if (in_array('seourl', $modules))
                $url .= '_' . PHPShopString::toLatin($product['name']) . '.html';

            // SEOURLPRO
            if (in_array('seourlpro', $modules)) {
                if (is_null($GLOBALS['PHPShopSeoPro'])) {
                    include_once dirname(dirname(dirname(__DIR__))) . '/modules/seourlpro/inc/option.inc.php';
                    $GLOBALS['PHPShopSeoPro'] = new PHPShopSeoPro();
                }

                if (empty($product['prod_seo_name']))
                    $url = '/id/' . $GLOBALS['PHPShopSeoPro']->setLatin($product['name']) . '-' . $product['id'] . '.html';
                else
                    $url = '/id/' . $product['prod_seo_name'] . '-' . $product['id'] . '.html';
            }

            $photos = (new PHPShopOrm($GLOBALS['SysValue']['base']['foto']))->getList(['*'], ['parent' => '=' . $product['id']], ['order' => 'num']);

            $urls[] = $this->getFullUrl($url);

            foreach ($photos as $photo) {

                // Исходое изображение
                if (!empty($this->image_source))
                    $photo['name'] = str_replace(".", "_big.", $photo['name']);

                $pictures[] = $this->getFullUrl($photo['name']);
            }
            if (count($pictures) === 0) {
                $pictures[] = $this->getFullUrl($product['pic_big']);
            }


            $options = unserialize($this->options['options']);

            // Блокировка изображений
            if (empty($options['block_image']))
                $pictures = [];

            // Шаблон описания
            if (strstr($this->options['description_template'], '@Description@'))
                $product['content'] = $product['description'];

            // Блокировка описаний
            if (empty($options['block_content']))
                $product['content'] = null;

            // Ключ обновления
            if ($this->type == 2)
                $shopSku = str_replace(['-', '_'], [' ', '-'], $product['uid']);
            else
                $shopSku = $product['id'];

            $offer = [
                'offer' => [
                    'shopSku' => $shopSku,
                    'name' => $product['name'],
                    'category' => $categories[$product['category']],
                    'manufacturer' => $product['manufacturer'],
                    'manufacturerCountries' => !empty($product['country_of_origin']) ? [$product['country_of_origin']] : null,
                    'urls' => $urls,
                    'pictures' => $pictures,
                    'vendor' => $product['vendor_name'],
                    'vendorCode' => $product['vendor_code'],
                    'barcodes' => !empty($product['barcode']) ? [$product['barcode']] : null,
                    'description' => trim(strip_tags($product['content'], '<p><h3><ul><li><br>')),
                    'weightDimensions' => [
                        'length' => str_replace(',', '.', $product['length']),
                        'width' => str_replace(',', '.', $product['width']),
                        'height' => str_replace(',', '.', $product['height']),
                        'weight' => (float) $product['weight'] / 1000
                    ]
                ]
            ];

            if (!empty($product['market_sku'])) {
                $offer['mapping'] = [
                    'marketSku' => $product['market_sku']
                ];
            }

            $data[] = $offer;
        }

        $method = sprintf('campaigns/%s/offer-mapping-entries/updates.json', trim($this->options['campaign_id']));

        // Отладочный токен
        //$debug='?dbg=4B00000152811A67';
        //print_r($data);

        $result = $this->post($method . $debug, ['offerMappingEntries' => $data]);

        if ($result['status'] === 'ERROR') {
            $errors = [];
            foreach ($result['errors'] as $error) {
                $errors[] = PHPShopString::utf8_win1251($error['message']);
            }

            throw new \Exception(implode('<br>', $errors));
        }

        // Устанавливаем цены, для товаров с marketSku
        $this->importProductsPrice($products);

        return count($products);
    }

    public function getFullUrl($url) {
        if (!empty($_SERVER['HTTPS']) && 'off' !== strtolower($_SERVER['HTTPS'])) {
            $protocol = 'https://';
        } else {
            $protocol = 'http://';
        }

        if (strpos($url, 'http:') === false && strpos($url, 'https:') === false) {
            $url = $protocol . $_SERVER['SERVER_NAME'] . $url;
        }

        return $url;
    }

    /**
     * Получение категорий
     */
    public function getTree() {

        $tree = $this->post('categories/tree', ['language' => 'RU']);
        return $tree;
    }

    public function getRegionById($id) {
        $region = $this->get('regions/' . $id . '.json');

        if (!isset($region['regions'][0]) or ! is_array($region['regions'][0])) {
            return false;
        }

        return $this->getRegionName($region['regions'][0]);
    }

    public function findRegion($term) {
        $regions = $this->get('regions.json', 'name=' . urlencode(PHPShopString::win_utf8($term)));
        $result = array();
        if (!is_array($regions['regions']) or count($regions['regions']) === 0) {
            return $result;
        }

        foreach ($regions['regions'] as $region) {
            $result[] = array(
                'label' => implode(', ', $this->getRegionName($region)),
                'value' => $region['id']
            );
        }

        return $result;
    }

    public function changeStatus($orderId, $status, $campaign_num = false) {

        if (!empty($campaign_num))
            $campaign = $this->options['campaign_id_' . $campaign_num];
        else
            $campaign = $this->options['campaign_id'];

        $this->put('campaigns/' . trim($campaign) . '/orders/' . $orderId . '/status.json', $status);
    }

    public function getOutlets($regionId = 0, $campaign_num = false) {

        if (!empty($campaign_num))
            $campaign = $this->options['campaign_id_' . $campaign_num];
        else
            $campaign = $this->options['campaign_id'];


        $parameters = [];
        if ((int) $regionId > 0) {
            $parameters['region_id'] = (int) $regionId;
        }

        $outlets = $this->get('campaigns/' . trim($campaign) . '/outlets.json', $parameters);

        if (isset($outlets['outlets']) && is_array($outlets['outlets'])) {
            return $outlets['outlets'];
        }

        return [];
    }

    public function getOutletsSelectOptions($regionId = 0, $current = null, $campaign_num = false) {
        $current = unserialize($current);
        if (!is_array($current)) {
            $current = [];
        }

        $outlets = $this->getOutlets($regionId, $campaign_num = false);

        $result = [];
        foreach ($outlets as $outlet) {
            $result[] = [
                PHPShopString::utf8_win1251($outlet['name']), $outlet['shopOutletCode'], in_array($outlet['shopOutletCode'], $current) ? $outlet['shopOutletCode'] : null
            ];
        }

        return $result;
    }

    private function getRegionName($region, $names = array()) {
        $names[] = $region['name'];
        if (isset($region['parent']) && is_array($region['parent'])) {
            $names = $this->getRegionName($region['parent'], $names);
        }

        return $names;
    }

    private function getMarketSku($product, $categoryTitle) {
        $method = sprintf('campaigns/%s/offer-mapping-entries/suggestions.json', trim($this->options['campaign_id']));

        // Ключ обновления
        if ($this->type == 2)
            $shopSku = $product['uid'];
        else
            $shopSku = $product['id'];

        $parameters = [
            'offers' => [
                [
                    'offer' => [
                        'shopSku' => $shopSku,
                        'name' => $product['name'],
                        'category' => $categoryTitle,
                        'vendor' => $product['vendor_name'],
                        'vendorCode' => $product['vendor_code'],
                        'barcodes' => !empty($product['barcode']) ? [$product['barcode']] : null,
                        'price' => $this->getPrice($product)
                    ]
                ]
            ]
        ];

        $result = $this->post($method, $parameters);

        if (isset($result['result']['offers'][0]['shopSku'])) {
            return $result['result']['offers'][0]['shopSku'];
        }

        return null;
    }

    public function getOldPrice($product, $campaign_num) {

        $options = unserialize($this->options['options']);
        $format = $this->system->getSerilizeParam('admoption.price_znak');
        $price = $product['oldprice'];

        if (empty($campaign_num)) {

            // Наценка %
            if (isset($options['price_fee']) && (float) $options['price_fee'] > 0)
                $fee = (float) $options['price_fee'];
            else
                $fee = $this->system->getValue('percent');

            $markup = $options['price_markup'];
        } else {

            // Наценка %
            if (isset($options['price_fee_' . $campaign_num]) && (float) $options['price_fee_' . $campaign_num] > 0)
                $fee = (float) $options['price_fee_' . $campaign_num];
            else
                $fee = $this->system->getValue('percent');

            $markup = $options['price_markup_' . $campaign_num];
        }

        // Наценка руб.
        $price = $price + (int) $markup;

        // Наценка %
        $price = ($price + (($price * $fee) / 100));

        $price = round($price, (int) $format);

        return $price;
    }

    public function getPrice($product, $campaign_num) {
        global $PHPShopValutaArray;

        $options = unserialize($this->options['options']);

        // Колонка цен
        if (isset($options['price']) && (int) $options['price'] > 1)
            $column = 'price' . $options['price'];
        else
            $column = $this->system->getPriceColumn();

        $defaultCurrency = $this->system->getValue('dengi');
        $format = $this->system->getSerilizeParam('admoption.price_znak');

        // Валюты
        $PHPShopValutaArray = new PHPShopValutaArray();
        $PHPShopValutaArr = $PHPShopValutaArray->getArray();

        $price = $product[$column];

        // Промоакции
        $promotions = new PHPShopPromotions();
        $promotions = $promotions->getPrice($product);
        if (is_array($promotions)) {
            $price = $promotions['price'];
        }

        // Отдельное поле цены
        if (empty($campaign_num)) {
            $price_yandex = $product['price_yandex'];

            // Наценка %
            if (isset($options['price_fee']) && (float) $options['price_fee'] > 0)
                $fee = (float) $options['price_fee'];
            else
                $fee = $this->system->getValue('percent');

            $markup = $options['price_markup'];

            // Отдельная колонка цен
            if ($options['price_' . $campaign_num] > 1)
                $price_yandex = $product['price' . $options['price_' . $campaign_num]];
        } else {

            $price_yandex = $product['price_yandex_' . $campaign_num];

            // Наценка %
            if (isset($options['price_fee_' . $campaign_num]) && (float) $options['price_fee_' . $campaign_num] > 0)
                $fee = (float) $options['price_fee_' . $campaign_num];
            else
                $fee = $this->system->getValue('percent');

            $markup = $options['price_markup_' . $campaign_num];

            // Отдельная колонка цен
            if ($options['price_' . $campaign_num] > 1)
                $price_yandex = $product['price' . $options['price_' . $campaign_num]];
        }


        if (!empty($price_yandex)) {

            $price = $price_yandex;
        }

        $currency = $product['baseinputvaluta'];

        // Если валюта отличается от базовой
        if ($currency !== $defaultCurrency) {
            $vkurs = $PHPShopValutaArr[$currency]['kurs'];

            // Если курс нулевой или валюта удалена
            if (empty($vkurs))
                $vkurs = 1;

            // Приводим цену в базовую валюту
            $price = $price / $vkurs;
        }


        // Наценка руб.
        $price = $price + (int) $markup;

        // Наценка %
        $price = ($price + (($price * $fee) / 100));

        $price = round($price, (int) $format);

        return $price;
    }

    private function importProductsPrice($products) {
        $products = array_column($products, null, 'id');

        $imported = $this->getProductsInMarketByShopSku(array_keys($products));

        $offers = [];
        foreach ($imported as $prod) {
            if (isset($prod['mapping']['marketSku'])) {
                $offers[] = [
                    'marketSku' => $prod['mapping']['marketSku'],
                    'price' => [
                        'currencyId' => 'RUR',
                        'value' => $this->getPrice($products[$prod['offer']['shopSku']])
                    ]
                ];
            }
        }

        if (count($offers) === 0) {
            return;
        }

        $method = sprintf('campaigns/%s/offer-prices/updates.json', trim($this->options['campaign_id']));

        $this->post($method, ['offers' => $offers]);
    }

    private function getProductsInMarketByShopSku($shopSkus) {
        $method = sprintf('campaigns/%s/offer-mapping-entries.json', trim($this->options['campaign_id']));

        $skus = '';
        foreach ($shopSkus as $key => $shopSku) {
            $skus .= 'shop_sku=' . urlencode($shopSku) . '&';
        }

        $result = $this->get($method, $skus);

        if ($result['status'] === 'ERROR') {
            $errors = [];
            foreach ($result['errors'] as $error) {
                $errors[] = PHPShopString::utf8_win1251($error['message']);
            }

            throw new \Exception(implode('<br>', $errors));
        }

        return $result['result']['offerMappingEntries'];
    }

    private function get($method, $parameters = null) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, self::API_URL . $method . '?' . $parameters);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

        // OAuth 2.0
        if (empty($this->options['auth_token_2']))
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                sprintf('Authorization: OAuth oauth_token="%s", oauth_client_id="%s"', $this->options['client_token'], $this->options['client_id']),
            ]);
        // API-Key
        else
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                sprintf('Api-Key: %s', $this->options['auth_token_2']),
            ]);

        $result = json_decode(curl_exec($ch), 1);
        curl_close($ch);

        return $result;
    }

    private function put($method, $parameters = array()) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, self::API_URL . $method);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

        // OAuth 2.0
        if (empty($this->options['auth_token_2']))
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                sprintf('Authorization: OAuth oauth_token="%s", oauth_client_id="%s"', $this->options['client_token'], $this->options['client_id']),
                'Content-Type: application/json',
            ]);
        // API-Key
        else
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                sprintf('Api-Key: %s', $this->options['auth_token_2']),
                'Content-Type: application/json',
            ]);

        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($parameters));

        $result = curl_exec($ch);
        $status = curl_getinfo($ch);

        curl_close($ch);

        return json_decode($result, true);
    }

    private function post($method, $parameters = []) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, self::API_URL . $method);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

        // OAuth 2.0
        if (empty($this->options['auth_token_2']))
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                sprintf('Authorization: OAuth oauth_token="%s", oauth_client_id="%s"', $this->options['client_token'], $this->options['client_id']),
                'Content-Type: application/json',
            ]);
        // API-Key
        else
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                sprintf('Api-Key: %s', $this->options['auth_token_2']),
                'Content-Type: application/json',
            ]);

        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, PHPShopString::json_safe_encode($parameters));

        $result = curl_exec($ch);
        $status = curl_getinfo($ch);

        if ($status['http_code'] === 401) {
            //throw new \Exception('Доступ запрещен. Пожалуйста, проверьте введенный ID приложения Яндекс.OAuth и OAuth-токен.');
        }

        if ($status['http_code'] === 413) {
            //throw new \Exception('Request Entity Too Large.');
        }

        curl_close($ch);

        return json_decode($result, true);
    }

}
