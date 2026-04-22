<?php

PHPShopObj::loadClass("valuta");

/**
 * Библиотека работы с Ozon Seller API
 * @author PHPShop Software
 * @version 3.2
 * @package PHPShopModules
 * @todo https://docs.ozon.ru/api/seller/#tag/Environment
 */
class OzonSeller {

    const GET_PARENT_TREE = '/v1/description-category/tree';
    const GET_TREE_ATTRIBUTE = '/v1/description-category/attribute';
    const GET_ATTRIBUTE_VALUES = '/v1/description-category/attribute/values';
    const API_URL = 'https://api-seller.ozon.ru';
    const IMPORT_PRODUCT = '/v3/product/import';
    const IMPORT_PRODUCT_INFO = '/v1/product/import/info';
    const GET_FBS_ORDER_LIST = '/v3/posting/fbs/list';
    const GET_FBS_ORDER = '/v3/posting/fbs/get';
    const GET_FBO_ORDER_LIST = '/v2/posting/fbo/list';
    const GET_FBO_ORDER = '/v2/posting/fbo/get';
    const GET_WAREHOUSE_LIST = '/v1/warehouse/list';
    const UPDATE_PRODUCT_STOCKS = '/v2/products/stocks';
    const GET_PRODUCT_LIST = '/v3/product/list'; // 22.08.2025
    const GET_PRODUCT = '/v3/product/info/list'; // 25.12.2024
    const GET_PRODUCT_DESCRIPTION = "/v1/product/info/description";
    const GET_PRODUCT_ATTRIBUTES = '/v4/product/info/attributes'; // 25.12.2024
    const GET_PRODUCT_PRICES = '/v5/product/info/prices'; // 8.01.2025
    const UPDATE_PRODUCT_PRICES = '/v1/product/import/prices';
    const ADD_PRODUCT_BARCODE = '/v1/barcode/add';
    const GET_ACTIONS = '/v1/actions';
    const GET_ACTIONS_PRODUCT = '/v1/actions/products';
    const GET_PRODUCT_INFO_LIST = '/v3/product/info/list'; // 25.12.2024
    const DEACTIVATE_ACTIONS_PRODUCT = '/v1/actions/products/deactivate';

    public $api_key;
    public $client_id;

    public function __construct() {
        global $PHPShopSystem;

        // Системные настройки
        $this->PHPShopValuta = (new PHPShopValutaArray())->getArray();
        $this->percent = $PHPShopSystem->getValue('percent');
        $this->defvaluta = $PHPShopSystem->getValue('dengi');
        $this->format = $PHPShopSystem->getSerilizeParam('admoption.price_znak');
        $this->vat = $PHPShopSystem->getParam('nds') / 100;
        $this->image_save_source = $PHPShopSystem->ifSerilizeParam('admoption.image_save_source');

        // Настройки модуля
        $PHPShopOrm = new PHPShopOrm('phpshop_modules_ozonseller_system');
        $this->options = $PHPShopOrm->select();
        $this->client_id = $this->options['client_id'];
        $this->api_key = $this->options['token'];
        $this->status = $this->options['status'];
        $this->fee_type = $this->options['fee_type'];
        $this->fee = $this->options['fee'];
        $this->price = $this->options['price'];
        $this->type = $this->options['type'];
        $this->warehouse = unserialize($this->options['warehouse']);
        $this->status_import = $this->options['status_import'];
        $this->delivery = $this->options['delivery'];
        $this->create_products = $this->options['create_products'];
        $this->log = $this->options['log'];
        $this->export = $this->options['export'];

        $this->status_list = [
            'acceptance_in_progress' => 'идёт приёмка',
            'awaiting_approve' => 'ожидает подтверждения',
            'awaiting_packaging' => 'ожидает упаковки',
            'awaiting_deliver' => 'ожидает отгрузки',
            'arbitration' => 'арбитраж',
            'client_arbitration' => 'клиентский арбитраж доставки',
            'delivering' => 'доставляется',
            'driver_pickup' => 'у водителя',
            'delivered' => 'доставлено',
            'cancelled' => 'отменено'
        ];

        if (!empty($_SERVER['HTTPS']) && 'off' !== strtolower($_SERVER['HTTPS']))
            $this->ssl = 'https://';
        else
            $this->ssl = 'http://';
    }

    /**
     * Выключение товаров из акции
     */
    public function deactivationActionsProduct($action_id, $product_id) {


        $params = [
            "action_id" => (int) $action_id,
            "product_ids" => $product_id
        ];

        $result = $this->request(self::DEACTIVATE_ACTIONS_PRODUCT, $params);

        // Журнал
        $log['params'] = $params;
        $log['result'] = $result;

        $this->log($log, null, self::DEACTIVATE_ACTIONS_PRODUCT);

        return $result;
    }

    /**
     * Получение списка товаров в акции
     */
    public function getActionsProduct($action_id) {

        $params = [
            "action_id" => (int) $action_id
        ];

        $result = $this->request(self::GET_ACTIONS_PRODUCT, $params);

        // Журнал
        $log['params'] = $params;
        $log['result'] = $result;

        $this->log($log, null, self::GET_ACTIONS_PRODUCT);

        return $result;
    }

    /**
     * Получение списка акций
     */
    public function getActions() {

        $result = $this->request(self::GET_ACTIONS);

        // Журнал
        $log['params'] = [];
        $log['result'] = $result;

        $this->log($log, null, self::GET_ACTIONS);

        return $result;
    }

    /**
     *  Передача штрихкода
     */
    public function addBarcode($product) {

        $params["barcodes"][] = [
            "barcode" => (string) $product['barcode_ozon'],
            "sku" => (int) $product['sku_ozon']
        ];

        $result = $this->request(self::ADD_PRODUCT_BARCODE, $params);


        // Журнал
        $log['params'] = $params;
        $log['result'] = $result;

        $this->log($log, $product['sku_ozon'], self::ADD_PRODUCT_BARCODE);

        return $result;
    }

    /**
     *  Создание товара
     */
    public function addProduct($id) {
        global $PHPShopSystem;

        $product_info = $this->getProductAttribures($id, 'offer_id')['result'][0];

        $insert['name_new'] = PHPShopString::utf8_win1251($product_info['name']);
        $insert['uid_new'] = PHPShopString::utf8_win1251($product_info['offer_id']);
        $insert['export_ozon_id_new'] = $product_info['id'];
        $insert['export_ozon_new'] = 1;
        $insert['datas_new'] = time();
        $insert['user_new'] = $_SESSION['idPHPSHOP'];
        $insert['export_ozon_task_status_new'] = time();
        $insert['barcode_ozon_new'] = $product_info['barcode'];
        $insert['sku_ozon_new'] = $product_info['sku'];

        // Категория
        $insert['category_new'] = (new PHPShopOrm($GLOBALS['SysValue']['base']['categories']))->getOne(['id,name'], ['category_ozonseller' => '="' . $product_info['category_id'] . '"'])['id'];

        $insert['items_new'] = 1;
        $insert['enabled_new'] = 1;

        // Цена
        $product_price = $this->getProductPrices($product_info['id'])['items'][0]['price'];
        $insert['price_new'] = $product_price['price'];
        $insert['price_n_new'] = $product_price['old_price'];

        $insert['baseinputvaluta_new'] = $PHPShopSystem->getDefaultOrderValutaId();
        $insert['weight_new'] = $product_info['weight'];
        $insert['height_new'] = $product_info['height'];
        $insert['width_new'] = $product_info['width'];
        $insert['length_new'] = $product_info['depth'];
        $insert['content_new'] = PHPShopString::utf8_win1251($this->getProductDescription($product_info['id'])['result']['description']);

        $prodict_id = (new PHPShopOrm($GLOBALS['SysValue']['base']['products']))->insert($insert);

        // Создание изображений
        $this->addProductImage($product_info['images'], $prodict_id);

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
            foreach ($mediaFiles as $k => $image) {

                $img = $image;

                if (!empty($img) and ! stristr($img, '.mp4')) {

                    $path_parts = pathinfo($img);

                    $path_parts['basename'] = $prodict_id . '_' . $path_parts['basename'];

                    // Файл загружен
                    if ($this->downloadFile($img, $_SERVER['DOCUMENT_ROOT'] . $GLOBALS['dir']['dir'] . '/UserFiles/Image/' . $path . $path_parts['basename']))
                        $img_load++;
                    else
                        continue;

                    // Новое имя
                    $img = $GLOBALS['dir']['dir'] . '/UserFiles/Image/' . $path . $path_parts['basename'];

                    // Запись в фотогалерее
                    $PHPShopOrmImg = new PHPShopOrm($GLOBALS['SysValue']['base']['foto']);
                    $PHPShopOrmImg->insert(array('parent_new' => intval($prodict_id), 'name_new' => $img, 'num_new' => $k));

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
     * Запись в журнал экспорта
     */
    public function export_log($message, $id, $name, $image) {
        $PHPShopOrm = new PHPShopOrm('phpshop_modules_ozonseller_export');

        $log = array(
            'message_new' => $message,
            'product_id_new' => $id,
            'product_name_new' => $name,
            'product_image_new' => $image,
            'date_new' => time()
        );

        $PHPShopOrm->insert($log);
    }

    /**
     * Очистка журнала экспорта
     */
    public function clean_log($id) {
        $PHPShopOrm = new PHPShopOrm('phpshop_modules_ozonseller_export');
        $PHPShopOrm->delete(['product_id' => '=' . $id]);
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

    /**
     * Изменение цены
     */
    public function setProductPrice($products) {

        if ($this->export != 2) {

            if (is_array($products)) {
                $n = 0;
                foreach ($products as $product) {

                    // Ключ обновления артикул
                    if ($this->type == 2) {
                        $info['offer_id'] = PHPShopString::win_utf8($product['uid']);
                    } else {
                        $info['offer_id'] = $product['id'];
                    }

                    // price columns
                    $price = $product['price'];
                    $oldprice = $product['price_n'];

                    if (!empty($product['price_ozon'])) {
                        $price = $product['price_ozon'];
                    } elseif (!empty($product['price' . (int) $this->price])) {
                        $price = $product['price' . (int) $this->price];
                    }

                    if ($this->fee > 0) {
                        if ($this->fee_type == 1) {
                            $price = $price - ($price * $this->fee / 100);
                            $oldprice = $oldprice - ($oldprice * $this->fee / 100);
                        } else {
                            $price = $price + ($price * $this->fee / 100);
                            $oldprice = $oldprice + ($oldprice * $this->fee / 100);
                        }
                    }

                    $params['prices'][$n] = [
                        'offer_id' => PHPShopString::win_utf8($info['offer_id']),
                        'price' => (string) $this->price($price, $product['baseinputvaluta']),
                        'min_price' => (string) $this->price($price, $product['baseinputvaluta']),
                        'old_price' => (string) $this->price($oldprice, $product['baseinputvaluta']),
                        'price_strategy_enabled' => (string) "DISABLED",
                        'auto_action_enabled' => (string) "DISABLED",
                    ];

                    // OZON ID
                    if (!empty($product['export_ozon_id'])) {
                        $params['prices'][$n]['product_id'] = $product['export_ozon_id'];
                    }

                    $n++;
                }

                $result = $this->request(self::UPDATE_PRODUCT_PRICES, $params);
            }

            // Журнал
            $log['params'] = $params;
            $log['result'] = $result;

            $this->log($log, $product['id'], self::UPDATE_PRODUCT_PRICES);

            return $result;
        }
    }

    /**
     * Изменение остатка на складе
     */
    public function setProductStock($products, $warehouse) {

        if ($this->export != 1) {

            if (is_array($products)) {
                $n = 0;
                foreach ($products as $product) {

                    // Ключ обновления артикул
                    if ($this->type == 2) {
                        $info['offer_id'] = PHPShopString::win_utf8($product['uid']);
                    } else {
                        $info['offer_id'] = $product['id'];
                    }

                    if (empty($product['enabled']) or $product['items'] < 0)
                        $product['items'] = 0;

                    // Лимит по складу
                    if (!empty($this->ozon_options['export_items_limit']) and $product['items'] >= $this->ozon_options['export_items_limit'])
                        $product['items'] = $this->ozon_options['export_items_limit'];

                    $params['stocks'][$n] = [
                        'offer_id' => $info['offer_id'],
                        'stock' => (int) $product['items'],
                        'warehouse_id' => $warehouse,
                        'quant_size' => 1
                    ];

                    // OZON ID
                    if (!empty($product['export_ozon_id'])) {
                        $params['stocks'][$n]['product_id'] = $product['export_ozon_id'];
                    }

                    $n++;
                }

                $result = $this->request(self::UPDATE_PRODUCT_STOCKS, $params);
            }

            // Журнал
            $log['params'] = $params;
            $log['result'] = $result;

            $this->log($log, $product['id'], self::UPDATE_PRODUCT_STOCKS);

            return $result;
        }
    }

    /**
     * Получение списка складов
     */
    public function getWarehouse() {

        $result = $this->request(self::GET_WAREHOUSE_LIST, ['name' => $this->warehouse_name]);

        // Журнал
        $log['params'] = [];
        $log['result'] = $result;

        $this->log($log, null, self::GET_WAREHOUSE_LIST);

        return $result;
    }

    /**
     * Преобразование даты
     */
    public function getTime($date, $full = true) {
        $d = explode('T', $date);
        $t = explode('Z', $d[1]);

        if ($full)
            return $d[0] . ' ' . $t[0];
        else
            return $d[0];
    }

    /**
     * Данные FBS заказа
     */
    public function getOrderFbo($num) {

        $params = [
            'posting_number' => $num,
        ];

        $result = $this->request(self::GET_FBO_ORDER, $params);

        // Журнал
        $log['params'] = $params;
        $log['result'] = $result;

        $this->log($log, $num, self::GET_FBO_ORDER);

        return $result;
    }

    /**
     * Данные FBS заказа
     */
    public function getOrderFbs($num) {

        $params = [
            'posting_number' => $num,
        ];

        $result = $this->request(self::GET_FBS_ORDER, $params);

        // Журнал
        $log['params'] = $params;
        $log['result'] = $result;

        $this->log($log, $num, self::GET_FBS_ORDER);

        return $result;
    }

    /**
     *  Заказ уже загружен?
     */
    public function checkOrderBase($id) {

        $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['orders']);
        $data = $PHPShopOrm->getOne(['id'], ['ozonseller_order_data' => '="' . $id . '"']);
        if (!empty($data['id']))
            return $data['id'];
    }

    /**
     *  Статус заказа
     */
    public function getStatus($name) {

        return $this->status_list[$name];
    }

    /**
     * Цены товара из Ozon
     */
    public function getProductPrices($product_id) {

        $params = [
            'filter' => [
                'product_id' => [$product_id],
                'visibility' => 'ALL',
            ],
            'limit' => 1,
        ];

        $result = $this->request(self::GET_PRODUCT_PRICES, $params);

        // Журнал
        $log['params'] = $params;
        $log['result'] = $result;

        $this->log($log, $product_id, self::GET_PRODUCT_PRICES);

        return $result;
    }

    /**
     * Атрибуты товара из Ozon
     */
    public function getProductAttribures($product_id, $flag = 'product_id', $limit = 1) {

        if (!is_array($product_id))
            $product = [$product_id];
        else
            $product = $product_id;

        $params = [
            'filter' => [
                $flag => $product,
                'visibility' => 'ALL',
            ],
            'limit' => $limit,
        ];

        $result = $this->request(self::GET_PRODUCT_ATTRIBUTES, $params);

        // Журнал
        $log['params'] = $params;
        $log['result'] = $result;

        $this->log($log, $product_id, self::GET_PRODUCT_ATTRIBUTES);

        return $result;
    }

    /**
     * Описание товара из Ozon
     */
    public function getProductDescription($product_id) {

        $params = [
            'product_id' => $product_id,
        ];

        $result = $this->request(self::GET_PRODUCT_DESCRIPTION, $params);

        // Журнал
        $log['params'] = $params;
        $log['result'] = $result;

        $this->log($log, $product_id, self::GET_PRODUCT_DESCRIPTION);

        return $result;
    }

    /**
     * Данные товаров из Ozon
     */
    public function getProduct($product_id) {

        $params = [
            'product_id' => [$product_id],
        ];

        $result = $this->request(self::GET_PRODUCT, $params);

        // Журнал
        $log['params'] = $params;
        $log['result'] = $result;

        $this->log($log, $product_id, self::GET_PRODUCT);

        return $result;
    }

    /**
     *  Список данных по товарам из Ozon
     */
    public function getProductInfoList($offer_id = null, $product_id = null, $sku = null) {

        if (!empty($offer_id)) {

            if (is_array($offer_id))
                $params['offer_id'] = $offer_id;
            else
                $params['offer_id'] = [$offer_id];
        }

        if (!empty($product_id)) {

            if (is_array($product_id))
                $params['product_id'] = $product_id;
            else
                $params['product_id'] = [$product_id];
        }

        if (!empty($sku)) {

            if (is_array($sku))
                $params['sku'] = $sku;
            else
                $params['sku'] = [$sku];
        }


        $result = $this->request(self::GET_PRODUCT_INFO_LIST, $params);

        // Журнал
        $log['params'] = $params;
        $log['result'] = $result;

        $this->log($log, 0, self::GET_PRODUCT_INFO_LIST);

        return $result;
    }

    /**
     *  Список товаров из Ozon
     */
    public function getProductList($visibility = "ALL", $offer_id = null, $product_id = null, $limit = null, $last_id = null) {

        if (empty($limit))
            $limit = 50;

        $params = [
            'filter' => [
                'visibility' => $visibility,
            ],
            'limit' => $limit,
        ];

        if (!empty($offer_id)) {

            if (is_array($offer_id))
                $params['filter']['offer_id'] = $offer_id;
            else
                $params['filter']['offer_id'] = [$offer_id];
        }

        if (!empty($product_id)) {

            if (is_array($product_id))
                $params['filter']['product_id'] = $product_id;
            else
                $params['filter']['product_id'] = [$product_id];
        }

        if (!empty($last_id)) {
            $params['last_id'] = $last_id;
        }

        $result = $this->request(self::GET_PRODUCT_LIST, $params);

        // Журнал
        $log['params'] = $params;
        $log['result'] = $result;

        $this->log($log, 0, self::GET_PRODUCT_LIST);

        return $result;
    }

    /**
     *  Список заказов FBS
     */
    public function getOrderListFbs($date1, $date2, $status) {

        $params = [
            'dir' => 'desc',
            'filter' => [
                'since' => $date1 . 'T' . date('H:m:s') . 'Z',
                'status' => $status,
                'to' => $date2 . 'T' . date('H:m:s') . 'Z',
            ],
            'limit' => 100,
            'offset' => 0,
        ];

        $result = $this->request(self::GET_FBS_ORDER_LIST, $params);

        // Журнал
        $log['params'] = $params;
        $log['result'] = $result;

        $this->log($log, 0, self::GET_FBS_ORDER_LIST);

        return $result;
    }

    /**
     *  Список заказов FBO
     */
    public function getOrderListFbo($date1, $date2, $status) {

        $params = [
            'dir' => 'desc',
            'filter' => [
                'since' => $date1 . 'T' . date('h:m:s') . 'Z',
                'status' => $status,
                'to' => $date2 . 'T' . date('h:m:s') . 'Z',
            ],
            'limit' => 100,
            'offset' => 0,
        ];

        $result = $this->request(self::GET_FBO_ORDER_LIST, $params);

        // Журнал
        $log['params'] = $params;
        $log['result'] = $result;

        $this->log($log, 0, self::GET_FBO_ORDER_LIST);

        return $result;
    }

    /**
     * Запись в журнал
     */
    public function log($message, $id, $type) {

        if (!empty($this->log)) {

            $PHPShopOrm = new PHPShopOrm('phpshop_modules_ozonseller_log');

            $log = array(
                'message_new' => serialize($message),
                'order_id_new' => $id,
                'type_new' => $type,
                'date_new' => time()
            );

            $PHPShopOrm->insert($log);
        }
    }

    /**
     * Запись в журнал JSON
     */
    public function log_json($message, $id, $type) {
        $PHPShopOrm = new PHPShopOrm('phpshop_modules_ozonseller_log');

        $log = array(
            'message_new' => $message,
            'order_id_new' => $id,
            'type_new' => $type,
            'date_new' => time()
        );

        $PHPShopOrm->insert($log);
    }

    private function getAttributes($product) {

        $category = new PHPShopCategory((int) $product['category']);
        $category_ozonseller = (new PHPShopOrm('phpshop_modules_ozonseller_type'))->getOne(['parent_to'], ['id' => '=' . $category->getParam('category_ozonseller')])['parent_to'];

        $sort = $category->unserializeParam('sort');
        $sortCat = $sortValue = null;
        $arrayVendorValue = [];

        if (is_array($sort))
            foreach ($sort as $v) {
                $sortCat .= (int) $v . ',';
            }

        if (!empty($sortCat)) {

            $PHPShopOrmType = new PHPShopOrm('phpshop_modules_ozonseller_type');

            // Массив имен характеристик
            $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['sort_categories']);
            $arrayVendor = array_column($PHPShopOrm->getList(['*'], ['id' => sprintf(' IN (%s 0)', $sortCat)], ['order' => 'num']), null, 'id');

            $product['vendor_array'] = unserialize($product['vendor_array']);

            if (is_array($product['vendor_array']))
                foreach ($product['vendor_array'] as $v) {
                    foreach ($v as $value)
                        if (is_numeric($value))
                            $sortValue .= (int) $value . ',';
                }

            if (!empty($sortValue)) {

                // Массив значений характеристик
                $PHPShopOrm = new PHPShopOrm();
                $result = $PHPShopOrm->query("select * from " . $GLOBALS['SysValue']['base']['sort'] . " where id IN ( $sortValue 0) order by num");
                while ($row = mysqli_fetch_array($result)) {
                    $arrayVendorValue[$row['category']]['name'][$row['id']] = $row['name'];
                    $arrayVendorValue[$row['category']]['id'][] = $row['id'];
                }

                // Название товара
                $list[] = [
                    'complex_id' => 0,
                    'id' => (int) 4180,
                    'values' => [0 => ['value' => (string) PHPShopString::win_utf8($product['name'])]],
                ];

                // Описание
                $list[] = [
                    'complex_id' => 0,
                    'id' => (int) 4191,
                    'values' => [0 => ['value' => (string) PHPShopString::win_utf8(strip_tags($product['content'], '<br><ul><li>'))]],
                ];

                // Тип товара
                $type = $PHPShopOrmType->getOne(['name'], ['id' => '=' . $category->getParam('category_ozonseller')])['name'];
                $dictionary_value_id = $this->getAttributesValues(8229, $category_ozonseller, $type, false, $category->getParam('category_ozonseller'));
                $list[] = [
                    'complex_id' => 0,
                    'id' => (int) 8229,
                    'values' => [0 => ['value' => (string) PHPShopString::win_utf8($type), 'dictionary_value_id' => (int) $dictionary_value_id]],
                ];

                if (is_array($arrayVendor))
                    foreach ($arrayVendor as $idCategory => $value) {

                        /*
                          if (strstr($value['name'], 'Название')) {
                          $values[] = [
                          'value' => PHPShopString::win_utf8($product['name']),
                          ];
                          } */


                        if (!empty($arrayVendorValue[$idCategory]['name'])) {
                            if (!empty($value['name'])) {

                                $values = [];

                                $arr = [];
                                foreach ($arrayVendorValue[$idCategory]['id'] as $valueId) {
                                    $arr[] = $arrayVendorValue[$idCategory]['name'][(int) $valueId];
                                }

                                if (is_array($arr)) {
                                    foreach ($arr as $k => $v) {

                                        if (empty($v))
                                            continue;

                                        $values[$k] = [
                                            "value" => PHPShopString::win_utf8($v)
                                        ];
                                        $dictionary_value_id = $this->getAttributesValues($value['attribute_ozonseller'], $category_ozonseller, $v, false, $category->getParam('category_ozonseller'));
                                        if (!empty($dictionary_value_id))
                                            $values[$k]["dictionary_value_id"] = (int) $dictionary_value_id;
                                    }
                                }
                            }
                        }

                        if (!empty($value['attribute_ozonseller']) and ! empty($values) and $value['attribute_ozonseller'] != 8229)
                            $list[] = ["id" => (int) $value['attribute_ozonseller'], 'complex_id' => 0, "values" => $values];
                    }

                return ['attributes' => $list, 'category' => $category_ozonseller];
            }
        }
    }

    public function getAttributesValues($attribute_id, $description_category_id, $sort_name, $return_array, $type_id) {

        $sort_name = PHPShopString::win_utf8($sort_name);
        $str = [];

        $params = [
            'attribute_id' => $attribute_id,
            'description_category_id' => $description_category_id,
            'type_id' => $type_id,
            'last_value_id' => 0,
            'limit' => 1000,
            'language' => 'DEFAULT'
        ];

        $result = $this->request(self::GET_ATTRIBUTE_VALUES, $params);

        // Журнал
        $log['params'] = $params;
        $log['result'] = $result;

        $this->log($log, $attribute_id, self::GET_ATTRIBUTE_VALUES);

        if (is_array($result['result'])) {
            foreach ($result['result'] as $val) {

                // Поиск по имени
                if (empty($return_array)) {

                    if ($val['value'] == $sort_name)
                        return $val['id'];
                } else
                    $str[] = PHPShopString::utf8_win1251($val['value']);
            }
        }

        if (!empty($return_array))
            return $str;
    }

    public function sendProductsInfo($product) {
        $params = ['task_id' => $product['export_ozon_task_id']];
        $result = $this->request(self::IMPORT_PRODUCT_INFO, $params);

        // Журнал
        $log['params'] = $params;
        $log['result'] = $result;

        $this->log($log, $product['id'], self::IMPORT_PRODUCT_INFO);

        return $result;
    }

    public function getImages($id, $pic_main) {

        $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['foto']);
        $data = $PHPShopOrm->select(['*'], ['parent' => '=' . (int) $id, 'name' => '!="' . $pic_main . '"'], ['order' => 'num'], ['limit' => 15]);

        // Главное изображение
        $pic_main_b = str_replace(".", "_big.", $pic_main);
        if (!$this->image_save_source or ! file_exists($_SERVER['DOCUMENT_ROOT'] . $pic_main_b))
            $pic_main_b = $pic_main;

        if (!empty($pic_main_b)) {
            if (!strstr($pic_main_b, 'https'))
                $pic_main_b = 'https://' . $_SERVER['SERVER_NAME'] . $pic_main_b;

            $images[] = $pic_main_b;
        }

        if (is_array($data)) {
            foreach ($data as $row) {

                $name = $row['name'];
                $name_b = str_replace(".", "_big.", $name);

                // Подбор исходного изображения
                if (!$this->image_save_source or ! file_exists($_SERVER['DOCUMENT_ROOT'] . $name_b))
                    $name_b = $name;

                if (!strstr($name_b, 'https'))
                    $name_b = 'https://' . $_SERVER['SERVER_NAME'] . $name_b;

                $images[] = $name_b;
            }
        }

        return $images;
    }

    public function sendProducts($prod, $params = []) {

        if (is_array($prod)) {

            // price columns
            $price = $prod['price'];
            $oldprice = $prod['price_n'];

            if (!empty($prod['price_ozon'])) {
                $price = $prod['price_ozon'];
            } elseif (!empty($prod['price' . (int) $this->price])) {
                $price = $prod['price' . (int) $this->price];
            }

            if ($this->fee > 0) {
                if ($this->fee_type == 1) {
                    $price = $price - ($price * $this->fee / 100);
                    $oldprice = $oldprice - ($oldprice * $this->fee / 100);
                } else {
                    $price = $price + ($price * $this->fee / 100);
                    $oldprice = $oldprice - ($oldprice * $this->fee / 100);
                }
            }

            // Ключ обновления артикул
            if ($this->type == 2) {
                $offer_id = PHPShopString::win_utf8($prod['uid']);
            } else
                $offer_id = $prod['id'];

            // Атрибуты
            $attributes = $this->getAttributes($prod)['attributes'];

            // Тип товара
            if (is_array($attributes)) {
                foreach ($attributes as $attribute) {
                    if ($attribute['id'] == 8229)
                        $type_id = $attribute['values'][0]['dictionary_value_id'];
                }
            }

            $params['items'][] = [
                "attributes" => $attributes,
                //"barcode" => (string) $prod['barcode_ozon'],
                "description_category_id" => (int) $this->getAttributes($prod)['category'],
                "color_image" => "",
                "complex_attributes" => [],
                "depth" => (int) $prod['length'],
                "dimension_unit" => "cm",
                "height" => (int) $prod['height'],
                "images" => $this->getImages($prod['id'], $prod['pic_big']),
                "images360" => [],
                "name" => PHPShopString::win_utf8($prod['name']),
                "offer_id" => $offer_id,
                "old_price" => (string) $this->price($oldprice, $prod['baseinputvaluta']),
                "pdf_list" => [],
                "premium_price" => "",
                "price" => (string) $this->price($price, $prod['baseinputvaluta']),
                "primary_image" => "",
                "vat" => (string) $this->vat,
                "weight" => (int) $prod['weight'],
                "weight_unit" => "g",
                "width" => (int) $prod['width'],
                "type_id" => (int) $type_id
            ];


            $result = $this->request(self::IMPORT_PRODUCT, $params);

            // Лог JSON
            //$this->log_json(json_encode($params), 0, 'sendProducts JSON');
            // Журнал
            $log['params'] = $params;
            $log['result'] = $result;

            $this->log($log, $prod['id'], self::IMPORT_PRODUCT);

            return $result;
        }
    }

    /**
     * Получение категорий
     */
    public function getTree($params = []) {
        $method = self::GET_PARENT_TREE;
        return $this->request($method, $params);
    }

    /*
     *  Получение характеристик категории
     */

    public function getTreeAttribute($params = []) {
        $result = $this->request(self::GET_TREE_ATTRIBUTE, $params);

        // Журнал
        $log['params'] = $params;
        $log['result'] = $result;

        $this->log($log, null, self::GET_TREE_ATTRIBUTE);

        return $result;
    }

    /**
     * Запрос к API
     * @param string $method адрес метода
     * @param array $params параметры
     * @return array
     */
    public function request($method, $params = []) {

        $api = self::API_URL;
        $ch = curl_init();
        $header = [
            'Client-Id: ' . $this->client_id,
            'Api-Key: ' . $this->api_key,
            'Content-Type: application/json'
        ];
        curl_setopt($ch, CURLOPT_URL, $api . $method);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);

        if (!empty($params)) {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($params));
        }

        $result = curl_exec($ch);
        curl_close($ch);

        return json_decode($result, true);
    }

    // номер заказа
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

}

?>