<?php

/**
 * Библиотека работы с Wildberries Seller API
 * @author PHPShop Software
 * @version 2.2
 * @package PHPShopModules
 * @todo https://openapi.wildberries.ru/
 */
class WbSeller {

    const GET_PRODUCT_LIST = 'https://content-api.wildberries.ru/content/v2/get/cards/list';
    const GET_PRODUCT_PRICE = 'https://discounts-prices-api.wildberries.ru/api/v2/list/goods/filter';
    const GET_PARENT_TREE = 'https://content-api.wildberries.ru/content/v2/object/parent/all';
    const GET_TREE = 'https://content-api.wildberries.ru/content/v2/object/all';
    const GET_TREE_ATTRIBUTE = 'https://content-api.wildberries.ru/content/v2/object/charcs/';
    const IMPORT_PRODUCT = 'https://content-api.wildberries.ru/content/v2/cards/upload';
    const IMPORT_MEDIA = 'https://content-api.wildberries.ru/content/v3/media/save';
    const GET_WAREHOUSE_LIST = 'https://marketplace-api.wildberries.ru/api/v3/warehouses';
    const UPDATE_PRODUCT_STOCKS = 'https://marketplace-api.wildberries.ru/api/v3/stocks/';
    const UPDATE_PRODUCT_PRICES = 'https://discounts-prices-api.wildberries.ru/api/v2/upload/task';
    const UPDATE_PRODUCT_PRICES_SIZE = 'https://discounts-prices-api.wildberries.ru/api/v2/upload/task/size';
    const GET_ORDER_LIST = 'https://marketplace-api.wildberries.ru/api/v3/orders';
    const GET_ORDER_NEW = 'https://marketplace-api.wildberries.ru/api/v3/orders/new';

    public $api_key;

    public function __construct() {
        global $PHPShopSystem;

        // Системные настройки
        PHPShopObj::loadClass("valuta");
        $this->PHPShopValuta = (new PHPShopValutaArray())->getArray();
        $this->percent = $PHPShopSystem->getValue('percent');
        $this->defvaluta = $PHPShopSystem->getValue('dengi');
        $this->format = $PHPShopSystem->getSerilizeParam('admoption.price_znak');
        $this->vat = $PHPShopSystem->getParam('nds') / 100;
        $this->image_save_source = $PHPShopSystem->ifSerilizeParam('admoption.image_save_source');

        // Настройки модуля
        $PHPShopOrm = new PHPShopOrm('phpshop_modules_wbseller_system');
        $this->options = $PHPShopOrm->select();
        $this->api_key = $this->options['token'];
        $this->status = $this->options['status'];
        $this->fee_type = $this->options['fee_type'];
        $this->fee = $this->options['fee'];
        $this->price = $this->options['price'];
        $this->type = $this->options['type'];
        $this->warehouse = $this->options['warehouse_id'];
        $this->status_import = $this->options['status_import'];
        $this->delivery = $this->options['delivery'];
        $this->create_products = $this->options['create_products'];
        $this->log = $this->options['log'];
        $this->discount = $this->options['discount'];

        $this->status_list = [
            'new' => 'Новые заказы',
            'all' => 'Все заказы',
        ];

        if (!empty($_SERVER['HTTPS']) && 'off' !== strtolower($_SERVER['HTTPS']))
            $this->ssl = 'https://';
        else
            $this->ssl = 'http://';
    }

    /**
     *  Создание товара
     */
    public function addProduct($id) {
        global $PHPShopSystem;

        $product_info = $this->getProductList($id, 1)['cards'][0];

        if (is_array($product_info['characteristics']))
            foreach ($product_info['characteristics'] as $characteristics) {

                // Вес
                if ($characteristics['name'] == PHPShopString::win_utf8('Вес товара с упаковкой (г)'))
                    $product_info['weight'] = $characteristics['value'];
            }

        $insert['name_new'] = PHPShopString::utf8_win1251($product_info['title']);
        $insert['uid_new'] = PHPShopString::utf8_win1251($product_info['vendorCode']);
        $insert['export_wb_id_new'] = PHPShopString::utf8_win1251($product_info['nmID']);
        $insert['export_wb_new'] = 1;
        $insert['datas_new'] = time();
        $insert['user_new'] = $_SESSION['idPHPSHOP'];
        $insert['export_wb_task_status_new'] = time();
        $insert['barcode_wb_new'] = PHPShopString::utf8_win1251($product_info['sizes'][0]['skus'][0]);

        // Категория
        $insert['category_new'] = (new PHPShopOrm($GLOBALS['SysValue']['base']['categories']))->getOne(['id,name'], ['category_wbseller' => '="' . PHPShopString::utf8_win1251($product_info['subjectName']) . '"'])['id'];

        $insert['items_new'] = 1;
        $insert['enabled_new'] = 1;
        $insert['price_new'] = $product_info['sizes'][0]['price'];
        $insert['baseinputvaluta_new'] = $PHPShopSystem->getDefaultOrderValutaId();
        $insert['weight_new'] = $product_info['weight'];
        $insert['height_new'] = $product_info['height'];
        $insert['width_new'] = $product_info['width'];
        $insert['length_new'] = $product_info['length'];
        $insert['content_new'] = PHPShopString::utf8_win1251($product_info['description']);

        $prodict_id = (new PHPShopOrm($GLOBALS['SysValue']['base']['products']))->insert($insert);

        // Создание изображений
        $this->addProductImage($product_info['photos'], $prodict_id);

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
            foreach ($mediaFiles as $k => $images) {

                $img = $images['big'];

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
     *  Цена товара
     */
    public function price($price, $baseinputvaluta) {

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

        return $price;
    }

    /**
     * Изменение остатка на складе
     */
    public function setProductStock($products = []) {

        if (is_array($products)) {
            foreach ($products as $product) {

                if (empty($product['enabled']))
                    $product['items'] = 0;

                if (empty($product['barcode_wb']))
                    $product['barcode_wb'] = $product['uid'];

                $params['stocks'][] = [
                    'sku' => (string) $product['barcode_wb'],
                    'amount' => (int) $product['items']
                ];
            }
        }

        $result = $this->request(self::UPDATE_PRODUCT_STOCKS . $this->warehouse, $params, true);

        // Журнал
        $log['params'] = $params;
        $log['result'] = $result;

        $this->log($log, $product['id'], self::UPDATE_PRODUCT_STOCKS . $this->warehouse);

        return $result;
    }

    /**
     * Получение списка складов
     */
    public function getWarehouse() {

        $result = $this->request(self::GET_WAREHOUSE_LIST);

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
     *  Заказ уже загружен?
     */
    public function checkOrderBase($id) {

        $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['orders']);
        $data = $PHPShopOrm->getOne(['id'], ['wbseller_order_data' => '="' . $id . '"']);
        if (!empty($data['id']))
            return $data['id'];
    }

    /**
     *  Список цен товаров из WB
     */
    public function getProductPrice($nmID, $limit = 1) {

        $params = [
            'offset' => 0,
            'limit' => (int) $limit,
            'filterNmID' => (string) $nmID
        ];


        $result = $this->request(self::GET_PRODUCT_PRICE . '?' . http_build_query($params));

        // Журнал
        $log['params'] = $params;
        $log['result'] = $result;


        $this->log($log, 0, self::GET_PRODUCT_PRICE);

        return $result;
    }

    /**
     * Данные товара из Wb
     */
    public function getProduct($vendorCode) {

        return $this->getProductList($vendorCode, 1);
    }

    /**
     *  Список товаров из WB
     */
    public function getProductList($search = "", $limit = null,$updatedAt=false,$nmID=false) {

        if (empty($limit))
            $limit = 50;

        $params = [
            'settings' => [
                'cursor' => [
                    'limit' => (int) $limit,
                ],
                'filter' => [
                    "textSearch" => (string) PHPShopString::win_utf8($search),
                    "withPhoto" => -1
                ],
                'sort' => [
                    "sortColumn" => "",
                    "ascending" > false
                ]
            ],
        ];
        
        if(!empty($updatedAt))
            $params['settings']['cursor']['updatedAt']=$updatedAt;
        
        if(!empty($nmID))
            $params['settings']['cursor']['nmID']=$nmID;


        $result = $this->request(self::GET_PRODUCT_LIST, $params);

        // Журнал
        $log['params'] = $params;
        $log['result'] = $result;


        $this->log($log, 0, self::GET_PRODUCT_LIST);

        return $result;
    }

    /**
     *  Список заказов
     */
    public function getOrderList($date1, $date2, $status = 'all') {

        if ($status == 'new') {
            $method = $method_name = self::GET_ORDER_NEW;
        } else {
            $method = self::GET_ORDER_LIST . '?dateFrom=' . PHPShopDate::GetUnixTime($date1, '-', true) . '&dateTo=' . PHPShopDate::GetUnixTime($date2, '-', true) . '&limit=1000&next=0';
            $method_name = self::GET_ORDER_LIST;
        }

        $result = $this->request($method);

        // Журнал
        $log['params'] = ['dateFrom' => PHPShopDate::GetUnixTime($date1, '-', true), 'dateTo' => PHPShopDate::GetUnixTime($date2, '-', true)];
        $log['result'] = $result;

        $this->log($log, 0, $method_name);

        return $result;
    }

    /**
     * Запись в журнал
     */
    public function log($message, $id, $type) {

        if (!empty($this->log)) {

            $PHPShopOrm = new PHPShopOrm('phpshop_modules_wbseller_log');
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
        $PHPShopOrm = new PHPShopOrm('phpshop_modules_wbseller_log');

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

        $sort = $category->unserializeParam('sort');
        $sortCat = $sortValue = null;
        $arrayVendorValue = [];

        if (is_array($sort))
            foreach ($sort as $v) {
                $sortCat .= (int) $v . ',';
            }

        if (!empty($sortCat)) {


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
                while (@$row = mysqli_fetch_array($result)) {
                    $arrayVendorValue[$row['category']]['name'][$row['id']] = $row['name'];
                    $arrayVendorValue[$row['category']]['id'][] = $row['id'];
                }

                if (is_array($arrayVendor))
                    foreach ($arrayVendor as $idCategory => $value) {

                        if (!empty($arrayVendorValue[$idCategory]['name'])) {
                            if (!empty($value['name'])) {

                                $arr = [];
                                foreach ($arrayVendorValue[$idCategory]['id'] as $valueId) {
                                    $arr[] = $arrayVendorValue[$idCategory]['name'][(int) $valueId];
                                }

                                if (is_array($arr)) {
                                    foreach ($arr as $v) {
                                        $values = PHPShopString::win_utf8($v);
                                    }
                                }
                            }
                        }

                        $attribute_wbseller = (int) $value['attribute_wbseller'];

                        if (!empty($attribute_wbseller) and ! empty($values))
                            $list[] = [
                                'id' => $attribute_wbseller,
                                'value' => [$values]
                            ];
                    }
            }
        }

        return $list;
    }

    public function getImages($id, $pic_main) {

        $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['foto']);
        $data = $PHPShopOrm->select(['*'], ['parent' => '=' . (int) $id, 'name' => '!="' . $pic_main . '"'], ['order' => 'num'], ['limit' => 15]);

        // Главное изображение
        $pic_main_b = str_replace(".", "_big.", $pic_main);
        if (!$this->image_save_source or ! file_exists($_SERVER['DOCUMENT_ROOT'] . $pic_main_b))
            $pic_main_b = $pic_main;

        if (!strstr($pic_main_b, 'http'))
            $pic_main_b = $this->ssl . $_SERVER['SERVER_NAME'] . $pic_main_b;

        $images[] = $pic_main_b;

        if (is_array($data)) {
            foreach ($data as $row) {

                $name = $row['name'];
                $name_b = str_replace(".", "_big.", $name);

                // Подбор исходного изображения
                if (!$this->image_save_source or ! file_exists($_SERVER['DOCUMENT_ROOT'] . $name_b))
                    $name_b = $name;

                if (!strstr($name_b, 'http'))
                    $name_b = $this->ssl . $_SERVER['SERVER_NAME'] . $name_b;

                $images[] = $name_b;
            }
        }

        return $images;
    }

    /**
     * Экспорт цен
     */
    public function sendPrices($params) {


        $result = $this->request(self::UPDATE_PRODUCT_PRICES, $params);

        // Журнал
        $log['params'] = $params;
        $log['result'] = $result;

        $this->log($log, null, self::UPDATE_PRODUCT_PRICES);

        // Проверка на размеры
        /*
        if ($result['errorText'] == 'No goods for process') {

            $getProductPrice = $this->getProductPrice($params['data'][0]['nmID']);
            $sizeID = $getProductPrice['data']['listGoods'][0]['sizes'][0]['sizeID'];

            if (!empty($sizeID)) {

                $prices[] = [
                    'nmID' => (int) $params['data'][0]['nmID'],
                    'sizeID' => (int) $sizeID,
                    'price' => (int) $params['data'][0]['price'],
                ];

                $result = $this->request(self::UPDATE_PRODUCT_PRICES_SIZE, ['data' => $prices]);

                // Журнал
                $log['params'] = ['data' => $prices];
                $log['result'] = $result;

                $this->log($log, null, self::UPDATE_PRODUCT_PRICES_SIZE);
            }
        }*/

        return $result;
    }

    /**
     * Экспорт изображений
     */
    public function sendImages($prod) {

        $params = [
            "nmId" => (int) $prod['export_wb_id'],
            "data" => $this->getImages($prod['id'], $prod['pic_big'])
        ];

        $result = $this->request(self::IMPORT_MEDIA, $params);

        // Журнал
        $log['params'] = $params;
        $log['result'] = $result;

        $this->log($log, $prod['id'], self::IMPORT_MEDIA);

        return $result;
    }

    /**
     *  Экспорт товаров
     */
    public function sendProducts($prod = [], $params = []) {

        if (is_array($prod)) {

            // Предмет
            $category = new PHPShopCategory((int) $prod['category']);
            $category_wbseller = $category->getParam('category_wbseller_id');

            // price columns
            $price = $prod['price'];

            if (!empty($prod['price_wb'])) {
                $price = $prod['price_wb'];
            } elseif (!empty($prod['price' . (int) $this->price])) {
                $price = $prod['price' . (int) $this->price];
            }

            if ($this->fee > 0) {
                if ($this->fee_type == 1) {
                    $price = $price - ($price * $this->fee / 100);
                } else {
                    $price = $price + ($price * $this->fee / 100);
                }
            }

            //if (empty($prod['barcode_wb']))
            //$prod['barcode_wb'] = $prod['uid'];

            $variants = [[
            "vendorCode" => (string) PHPShopString::win_utf8($prod['uid']),
            "title" => (string) PHPShopString::win_utf8($prod['name']),
            "description" => (string) PHPShopString::win_utf8(strip_tags($prod['content'])),
            "dimensions" => [
                "length" => (int) $prod['length'],
                "width" => (int) $prod['width'],
                "height" => (int) $prod['height'],
            ],
            "characteristics" => $this->getAttributes($prod),
            "sizes" => [[
            'price' => (int) $this->price($price, $prod['baseinputvaluta']),
            'skus' => [$prod['barcode_wb']]
                ]],
            ]];

            $params = [[
            'subjectID' => (int) $category_wbseller,
            'variants' => $variants
            ]];

            $result = $this->request(self::IMPORT_PRODUCT, $params);


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
    public function getTree($name = false) {

        if (!empty($name))
            $method = self::GET_TREE . '?name=' . urlencode($name) . '&top=20';
        else
            $method = self::GET_PARENT_TREE;

        $result = $this->request($method, false);

        // Журнал
        $log['params'] = $name;
        $log['result'] = $result;

        //$this->log($log, null, $method);

        return $result;
    }

    /*
     *  Получение характеристик категории
     */

    public function getTreeAttribute($subjectId) {

        $result = $this->request(self::GET_TREE_ATTRIBUTE . $subjectId);


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
    public function request($method, $params = [], $put = false, $debug = false) {
        
        if(empty($this->api_key))
            return false;

        $ch = curl_init();
        $header = [
            'Authorization: ' . $this->api_key,
            'Content-Type: application/json'
        ];

        curl_setopt($ch, CURLOPT_URL, $method);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);

        if (!empty($params)) {

            if (empty($put))
                curl_setopt($ch, CURLOPT_POST, true);
            else
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");

            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($params));
        }

        $result = curl_exec($ch);

        if ($debug)
            echo $result;

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