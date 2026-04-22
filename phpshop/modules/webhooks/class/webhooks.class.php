<?php

/**
 * Библиотека WebHooks
 * @author PHPShop Software
 * @version 1.0
 * @package PHPShopClass
 * @subpackage RestApi
 */
class PHPShopWebhooks {

    private $type_value = array(
        1 => 'Новый заказ',
        2 => 'Изменение заказа',
        3 => 'Списание товара со склада',
        7 => 'Новый товар',
        4 => 'Изменение товара',
        5 => 'Новый пользователь',
        6 => 'Изменение пользователя'
    );

    public function __construct($data = array()) {

        $this->PHPShopOrm = new PHPShopOrm();
        $PHPShopSystem = new PHPShopSystem();
        $this->nds = $PHPShopSystem->getParam('nds');

        /*
         * Опции модуля
         */
        $this->PHPShopOrm->objBase = $GLOBALS['SysValue']['base']['webhooks']['webhooks_system'];
        $this->option = $this->PHPShopOrm->select();

        /*
         * Код валюты
         */
        $this->iso = $PHPShopSystem->getDefaultValutaIso();

        /*
         * Исходное изображение
         */
        $this->image_source = $PHPShopSystem->ifSerilizeParam('admoption.image_save_source');

        $this->data = $data;
    }

    /*
     * Список типов подключения
     */

    public function getType() {
        return $this->type_value;
    }

    /*
     * Получит список вебхуков из базы
     */

    public function getHooks($type) {
        $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['webhooks']['webhooks_forms']);
        $this->hooks = $PHPShopOrm->select(array('*'), array('type' => '="' . $type . '"', 'enabled' => "='1'"), false, array('limit' => 100));
    }

    /**
     * Роутер для типов подключения
     */
    public function init() {
        if (is_array($this->hooks)) {
            foreach ($this->hooks as $hook) {

                switch ($hook['type']) {

                    // Поступление заказа
                    case 1:
                        if (isset($this->data['orders'])) {
                            $this->order=$this->data;
                            $this->order['orders'] = unserialize($this->data['orders']);
                            $this->getProducts();
                            $this->getDelivery();
                            $this->order($hook);
                        }
                        break;

                    // Смена статуса заказа
                    case 2:
                        if (isset($this->data['orders'])) {
                            $this->order=$this->data;
                            $this->order['orders'] = unserialize($this->data['orders']);
                            $this->getProducts();
                            $this->getDelivery();
                            $this->order($hook);
                        }
                        break;

                    // Списание товара в заказе
                    case 3:
                        $this->values($hook);
                        break;

                    // Изменение товара
                    case 4:
                        $this->values($hook);
                        break;

                    // Новый пользователь
                    case 5:
                        $this->values($hook);
                        break;

                    // Изменение пользователя
                    case 6:
                        $this->values($hook);
                        break;

                    // Новый товар
                    case 7:
                        $this->values($hook);
                        break;
                }
            }
        }
    }

    /*
     * Добавление услуги доставки.
     */

    private function getDelivery() {
        $this->PHPShopOrm->objBase = $GLOBALS['SysValue']['base']['delivery'];
        $this->PHPShopOrm->_SQL = '';
        $delivery = $this->PHPShopOrm->select(array('*'), array('id=' => '"' . $this->order['orders']['Person']['dostavka_metod'] . '"'));
        $this->delivery_name = $delivery['city'];
        $this->nds_delivery = $delivery['ofd_nds'];
    }
    
    /*
     * Получить имя способа оплаты
     */
    private function getPayment(){
         $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['payment_systems']);
         $payment = $PHPShopOrm->select(array('name'), array('id=' => '"' . $this->order['orders']['Person']['order_metod'] . '"'));
         return $payment['name'];
    }

    /*
     * Получить список товаров из заказа
     */
    private function getProducts() {
        $product_id = array();
        foreach ($this->order['orders']['Cart']['cart'] as $cart)
            $product_id[] = $cart['id'];

        $this->PHPShopOrm->_SQL = '';
        $query = $this->PHPShopOrm->query("SELECT * FROM " . $GLOBALS['SysValue']['base']['products'] . " WHERE `id` IN ('" . implode("', '", $product_id) . "')");

        while ($row = $query->fetch_assoc()) {
            $this->products[$row['id']] = $row;
        }
    }

    /*
     * Действие с обычными данными
     */

    private function values($hook) {

        $fields = $this->data;
        array_walk_recursive($fields, 'array2iconvUTF');

        // Запись
        $result = $this->send($hook, $fields);

        if (!empty($result)) {

            $this->log($hook['name'], array(
                'parameters' => $fields,
                'url' => $hook['url'],
                'response' => $result,
                    ), $hook['id'], $result, $this->type_value[$hook['type']], '');
        }
    }

    /*
     * Действие с заказом.
     */

    private function order($hook) {

        if (!empty($this->order['city']))
            $city = $this->order['city'] . ', ';

        if (!empty($this->order['street']))
            $adress = $city . $this->order['street'];

        if (!empty($this->order['house']))
            $adress .= ' дом ' . $this->order['house'];

        if (!empty($this->order['flat']))
            $adress .= ' кв. ' . $this->order['flat'];

        $PHPShopOrderStatusArray = new PHPShopOrderStatusArray();
        $StatusArray = $PHPShopOrderStatusArray->getArray();
        $StatusArray[0]['name'] = __('Новый заказ');

        $fields = array(
            'name' => $this->order['uid'],
            'sum' => floatval($this->order['sum']),
            'date' => PHPShopDate::get($this->order['datas']),
            'comment' => PHPShopString::win_utf8($this->order['dop_info']),
            'adress' => PHPShopString::win_utf8($adress),
            'fio' => PHPShopString::win_utf8($this->order['fio']),
            'tel' => PHPShopString::win_utf8($this->order['tel']),
            'tracking' => PHPShopString::win_utf8($this->order['tracking']),
            'paid' => PHPShopString::win_utf8($this->order['paid']),
            'status' => PHPShopString::win_utf8($StatusArray[$this->order['statusi']]['name']),
            'email' => $this->order['orders']['Person']['mail'],
            'payment' => PHPShopString::win_utf8($this->getPayment())
        );

        if (!empty($this->order['org_name'])) {
            $fields['org_name'] = PHPShopString::win_utf8($this->order['org_name']);
            $fields['org_inn'] = PHPShopString::win_utf8($this->order['org_inn']);
            $fields['org_adress'] = PHPShopString::win_utf8($this->order['org_fakt_adres']);
        }

        // Товары
        $rows = array();
        foreach ($this->order['orders']['Cart']['cart'] as $product) {
            $rows[] = array(
                "name" => PHPShopString::win_utf8($product['name']),
                "quantity" => $product['num'],
                "uid" => $product['uid'],
                "price" => floatval($product['price']),
                "discount" => 0,
                "vat" => intval($this->nds),
            );
        }

        // Доставка
        $rows[] = array(
            "name" => PHPShopString::win_utf8('Доставка ' . $this->delivery_name),
            "price" => floatval($this->order['orders']['Cart']['dostavka']),
            "quantity" => 1,
            "discount" => 0,
            "vat" => intval($this->nds_delivery),
        );


        $fields['positions'] = $rows;

        // Запись заказа
        $result = $this->send($hook, $fields);

        if (!empty($result)) {

            $this->log($hook['name'], array(
                'parameters' => $fields,
                'url' => $hook['url'],
                'response' => $result,
                    ), $hook['id'], $result, $this->type_value[$hook['type']], '');
        }
    }

    /**
     * @param $method
     * @param array $properties
     * @return array
     */
    private function post($method, $properties = array()) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $method);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($properties));
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
        ));

        return $this->request($ch);
    }

    /**
     * @param $method
     * @return array
     */
    private function get($method, $properties = array()) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $method . '?' . http_build_query($properties));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

        return $this->request($ch);
    }

    private function send($hook, $properties) {
        if (empty($hook['send']))
            $result = $this->post($hook['url'], $properties);
        else
            $result = $this->get($hook['url'], $properties);
        
        if(empty($result))
            $result="Send";
        
        return $result;
    }

    /**
     * Обработка результата
     * @return string
     */
    private function request($ch) {
       $curl=curl_exec($ch);
       $result = json_decode($curl, true);
       
       if(is_array($result) and !empty($result['status']))
           return $result['status'];
       else  return  PHPShopString::utf8_win1251($curl);
    }

    /**
     * Запись лога
     */
    private function log($name, $message, $id, $status, $type, $status_code) {
        $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['webhooks']['webhooks_log']);
        $log = array(
            'name_new' => $name,
            'message_new' => serialize($message),
            'form_id_new' => $id,
            'status_new' => $status,
            'type_new' => $type,
            'date_new' => time(),
            'status_code_new' => $status_code
        );
        $PHPShopOrm->insert($log);
    }

}

/*
 * Смена кодировки на UTF-8 в массиве
 */
function array2iconvUTF(&$value) {
    $value = iconv("CP1251","UTF-8", $value);
}