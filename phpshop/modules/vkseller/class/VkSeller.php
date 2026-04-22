<?php

PHPShopObj::loadClass("valuta");

/**
 * Библиотека работы с VK Товары API
 * @author PHPShop Software
 * @version 1.4
 * @package PHPShopModules
 * @todo https://dev.vk.ru/method/market
 * @todo https://vk.com/ymlapp?mid=-223238042&ref=group_menu
 */
class VkSeller {

    const API_URL = 'https://api.vk.ru/method/';
    const IMPORT_PRODUCT = 'market.add';
    const GET_TREE = 'market.getCategories';
    const GET_UPLOAD_SERVER = 'photos.getMarketUploadServer';
    const SAVE_PHOTO = 'photos.saveMarketPhoto';
    const UPDATE_PRODUCT = 'market.edit';
    const GET_ORDER_LIST = 'market.getOrders';
    const GET_ORDER = 'market.getOrderById';
    const GET_USER = 'users.get';
    const GET_PRODUCT_LIST = 'market.get';
    const GET_PRODUCT = 'market.getById';
    const VERSION = "5.199";

    public $api_key;

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
        $PHPShopOrm = new PHPShopOrm('phpshop_modules_vkseller_system');
        $this->options = $PHPShopOrm->select();
        $this->api_key = $this->options['token'];
        $this->model = $this->options['model'];
        $this->status = $this->options['status'];
        $this->fee_type = $this->options['fee_type'];
        $this->fee = $this->options['fee'];
        $this->price = $this->options['price'];
        $this->type = $this->options['type'];
        $this->client_id = $this->options['client_id'];
        $this->owner_id = $this->options['owner_id'];
        $this->client_secret = $this->options['client_secret'];

        $this->access_token = $this->options['token'];
        $this->device_id = $this->options['device_id'];
        $this->refresh_token = $this->options['refresh_token'];
        $this->token_time = $this->options['token_time'];

        $this->status_import = $this->options['status_import'];
        $this->delivery = $this->options['delivery'];

        if (!empty($_SERVER['HTTPS']) && 'off' !== strtolower($_SERVER['HTTPS']))
            $this->ssl = 'https://';
        else
            $this->ssl = 'http://';
    }

    public function getUploadServer() {
        $method = self::GET_UPLOAD_SERVER;
        $params = ['group_id' => $this->owner_id];
        $result = $this->request($method, $params);

        // Журнал
        $log['params'] = $params;
        $log['result'] = $result;

        //$this->log($log, null, $method);

        return $result;
    }

    public function getCode() {

        $verifier_bytes = random_bytes(64);
        $code_verifier = rtrim(strtr(base64_encode($verifier_bytes), "+/", "-_"), "=");
        $challenge_bytes = hash("sha256", $code_verifier, true);
        $code_challenge = rtrim(strtr(base64_encode($challenge_bytes), "+/", "-_"), "=");
        $scope = "market photos";
        $state = substr($code_verifier, 0, 32);

        $_SESSION['code_verifier'] = $code_verifier;

        header('Location: https://id.vk.ru/authorize?response_type=code&client_id=' . $this->client_id . '&scope=' . $scope . '&redirect_uri=' . $this->ssl . $_SERVER['SERVER_NAME'] . $_SERVER['PHP_SELF'] . '&state=' . $state . '&code_challenge=' . $code_challenge . '&code_challenge_method=S256');
    }

    public function getToken() {

        $api = 'https://id.vk.ru/oauth2/auth';

        $ch = curl_init();
        $header = [
            'Content-Type: application/x-www-form-urlencoded',
        ];

        $params = [
            'grant_type' => 'authorization_code',
            'client_id' => $this->client_id,
            'code_verifier' => $_SESSION['code_verifier'],
            'device_id' => $_GET['device_id'],
            'code' => $_GET['code'],
            'redirect_uri' => $this->ssl . $_SERVER['SERVER_NAME'] . $_SERVER['PHP_SELF'],
            'state' => $_GET['state']
        ];

        curl_setopt($ch, CURLOPT_URL, $api);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);

        if (!empty($params)) {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params, '', '&'));
        }

        $result = curl_exec($ch);
        curl_close($ch);
        $json = json_decode($result, true);
        return $json;
    }

    /**
     *  Обновление токена
     */
    private function updateToken() {

        // 1 час
        if (time() - $this->token_time > 600) {

            $verifier_bytes = random_bytes(64);
            $code_verifier = rtrim(strtr(base64_encode($verifier_bytes), "+/", "-_"), "=");
            $state = substr($code_verifier, 0, 32);

            $api = 'https://id.vk.ru/oauth2/auth';

            $ch = curl_init();
            $header = [
                'Content-Type: application/x-www-form-urlencoded',
            ];

            $params = [
                'grant_type' => 'refresh_token',
                'client_id' => $this->client_id,
                'device_id' => $this->device_id,
                'refresh_token' => $this->refresh_token,
                'state' => $state
            ];

            curl_setopt($ch, CURLOPT_URL, $api);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $header);

            if (!empty($params)) {
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params, '', '&'));
            }

            $result = curl_exec($ch);
            curl_close($ch);

            $json = json_decode($result, true);

            // Обновление refresh_token
            if (!empty($json['access_token'])) {
                $PHPShopOrm = new PHPShopOrm('phpshop_modules_vkseller_system');
                $PHPShopOrm->update(['token_new' => $json['access_token'],'refresh_token_new' => $json['refresh_token'],'token_time_new'=>time()]);
                $this->access_token = $json['access_token'];
            }
        }

        return $this->access_token;
    }

    /**
     *  Заказ уже загружен?
     */
    public function checkOrderBase($id) {

        $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['orders']);
        $data = $PHPShopOrm->getOne(['id'], ['vkseller_order_data' => '="' . $id . '"']);
        if (!empty($data['id']))
            return $data['id'];
    }

    /**
     * Данные товара
     */
    public function getProduct($product_id) {

        $params = [
            'item_ids' => '-' . $this->owner_id . '_' . $product_id,
            'extended' => 1
        ];

        $result = $this->request(self::GET_PRODUCT, $params, "5.131");

        // Журнал
        $log['params'] = $params;
        $log['result'] = $result;

        $this->log($log, null, self::GET_PRODUCT);

        return $result;
    }

    /**
     *  Список товаров
     */
    public function getProductList($limit = null) {

        if (empty($limit))
            $limit = 50;

        $params = [
            'owner_id' => "-" . $this->owner_id,
            'count' => $limit,
        ];


        $result = $this->request(self::GET_PRODUCT_LIST, $params);

        // Журнал
        $log['params'] = $params;
        $log['result'] = $result;


        $this->log($log, 0, self::GET_PRODUCT_LIST);

        return $result;
    }

    /**
     * Данные покупателя
     */
    public function getUser($user) {

        $params = [
            'user_ids' => $user,
            'fields' => 'screen_name'
        ];

        $result = $this->request(self::GET_USER, $params);

        // Журнал
        $log['params'] = $params;
        $log['result'] = $result;

        $this->log($log, $user, self::GET_USER);

        return $result;
    }

    /**
     * Данные заказа
     */
    public function getOrder($num) {

        $params = [
            'order_id' => $num,
        ];

        $result = $this->request(self::GET_ORDER, $params);

        // Журнал
        $log['params'] = $params;
        $log['result'] = $result;

        $this->log($log, $num, self::GET_ORDER);

        return $result;
    }

    /**
     *  Список заказов
     */
    public function getOrderList($date1, $date2) {

        $params['date_from'] = $date1;
        $params['date_to'] = $date2;
        $params['extended'] = 0;

        $result = $this->request(self::GET_ORDER_LIST, $params);

        // Журнал
        $log['params'] = ['dateFrom' => PHPShopDate::GetUnixTime($date1, '-', true), 'dateTo' => PHPShopDate::GetUnixTime($date2, '-', true)];
        $log['result'] = $result;

        $this->log($log, 0, self::GET_ORDER_LIST);

        return $result;
    }

    /**
     * Запись в журнал операций
     */
    public function log($message, $id, $type) {
        $PHPShopOrm = new PHPShopOrm('phpshop_modules_vkseller_log');

        $log = array(
            'message_new' => serialize($message),
            'order_id_new' => $id,
            'type_new' => $type,
            'date_new' => time()
        );

        $PHPShopOrm->insert($log);
    }

    /**
     * Запись в журнал экспорта
     */
    public function export_log($message, $id, $name) {
        $PHPShopOrm = new PHPShopOrm('phpshop_modules_vkseller_export');

        $log = array(
            'message_new' => $message,
            'product_id_new' => $id,
            'product_name_new' => $name,
            'date_new' => time()
        );

        $PHPShopOrm->insert($log);
    }

    /**
     * Очистка журнала экспорта
     */
    public function clean_log($id) {
        $PHPShopOrm = new PHPShopOrm('phpshop_modules_vkseller_export');
        $PHPShopOrm->delete(['product_id' => '=' . $id]);
    }

    public function getImages($id, $pic_main) {

        $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['foto']);
        $data = $PHPShopOrm->select(['*'], ['parent' => '=' . (int) $id, 'name' => '!="' . $pic_main . '"'], ['order' => 'num'], ['limit' => 4]);

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

                $images[] = $name_b;
            }
        }

        return $images;
    }

    /**
     * Экспорт изображений
     */
    public function sendImages($id, $image) {


        // создание временной картинки для WEBP
        if (stristr($image, '.webp')) {
            require_once $_SERVER['DOCUMENT_ROOT'] . $GLOBALS['SysValue']['dir']['dir'] . '/phpshop/lib/thumb/phpthumb.php';
            $thumb = new PHPThumb($_SERVER['DOCUMENT_ROOT'] . $image);
            $thumb->setFormat('JPG');
            $image = str_replace([".WEBP", '.webp'], '.jpg', $image);
            $thumb->save($image);
            $webp = true;
        } else
            $webp = false;

        $data = array(
            'file' => new CURLfile($_SERVER['DOCUMENT_ROOT'] . $GLOBALS['SysValue']['dir']['dir'] . $image)
        );

        // Url
        if (!$this->upload_url)
            $this->upload_url = $this->getUploadServer()['response']['upload_url'];

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $this->upload_url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);

        $params = json_decode(curl_exec($curl), true);
        $params['group_id'] = $this->owner_id;

        curl_close($curl);

        $result = $this->request(self::SAVE_PHOTO, $params);

        // Журнал
        $log['params'] = $params;
        $log['result'] = $result;


        // Удаление временной картинки
        if ($webp)
            @unlink($_SERVER['DOCUMENT_ROOT'] . $GLOBALS['SysValue']['dir']['dir'] . $image);

        //$this->log($log, $id, self::SAVE_PHOTO);

        return $result;
    }

    protected function getCategory($id) {
        return (new PHPShopCategory($id))->getParam('category_vkseller');
    }

    /**
     *  Обновление товара
     */
    public function updateProduct($prod, $params = []) {
        return $this->sendProduct($prod, $params, self::UPDATE_PRODUCT);
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

        return $price;
    }

    /**
     *  Экспорт товара
     */
    public function sendProduct($prod, $params = [], $method = false) {

        // price columns
        $price = $prod['price'];

        if (!empty($prod['price_vk'])) {
            $price = $prod['price_vk'];
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


        if (empty($prod['description']))
            $prod['description'] = $prod['content'];

        if (empty($prod['description']))
            $prod['description'] = $prod['name'];

        // Ключ обновления
        if ($this->vk_options['type'] == 2) {
            $prod['id'] = $prod['uid'];
        }

        $params = [
            "owner_id" => "-" . $this->owner_id,
            "name" => (string) PHPShopString::win_utf8($prod['name'], true),
            "description" => (string) PHPShopString::win_utf8(strip_tags($prod['description']), true),
            "category_id" => (int) $this->getCategory($prod['category']),
            "price" => $this->price($price, $prod['baseinputvaluta']),
            "deleted" => $prod['deleted'],
            "url" => $this->ssl . $_SERVER['SERVER_NAME'] . '/shop/UID_' . $prod['id'] . '.html',
            "dimension_width" => (int) $prod['width'],
            "dimension_height" => (int) $prod['height'],
            "dimension_length" => (int) $prod['length'],
            "weight" => (int) $prod['weight'],
            "sku" => (string) PHPShopString::win_utf8($prod['id'], true),
            "main_photo_id" => $prod['main_photo_id'],
            "photo_ids" => $prod['photo_ids'],
            "stock_amount" => (int) $prod['items']
        ];

        // Создание
        if (empty($method)) {
            $method = self::IMPORT_PRODUCT;
        }
        // Редактирование
        else {
            $params["item_id"] = $prod['export_vk_id'];
        }

        $result = $this->request($method, $params);

        // Журнал
        $log['params'] = $params;
        $log['result'] = $result;

        $this->log($log, $prod['id'], $method);
        return $result;
    }

    /**
     * Получение категорий
     */
    public function getTree() {

        $method = self::GET_TREE;
        $params = ['count' => 1000];
        $result = $this->request($method, $params, "5.131");

        // Журнал
        $log['params'] = $params;
        $log['result'] = $result;

        $this->log($log, null, $method);

        return $result;
    }

    /**
     * Запрос к API
     * @param string $method адрес метода
     * @param array $params параметры
     * @return array
     */
    public function request($method, $params = [], $version = false) {
        
        $this->updateToken();

        $api = self::API_URL;
        $ch = curl_init();
        if (empty($version))
            $version = self::VERSION;

        $params['v'] = $version;
        $params['access_token'] = $this->access_token;

        curl_setopt($ch, CURLOPT_URL, $api . $method . '?' . http_build_query($params));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

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