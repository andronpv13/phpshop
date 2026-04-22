<?php

class Dolyame {

    const API_URL = 'https://partner.dolyame.ru/v1/orders/';

    public function __construct() {
        
        $PHPShopOrm = new PHPShopOrm('phpshop_modules_dolyame_system');
        $this->option = $PHPShopOrm->select();

        // Статус для оплаты
        $this->order_status = $this->option['status'];
        $this->order_status_payment = $this->option['status_payment'];
        $this->site_id = $this->option['site_id'];
        $this->max_sum = $this->option['max_sum'];
        
    }
    
    /*
     *  Проверка вебхуков
     */
    public function check_notification(){
        if($_SERVER["PATH_INFO"] == '/'.md5($this->option['login']))
                return true;
    }

    /*
     *  Подтверждение
     */

    public function commit($products, $order_id) {

        $amount = 0;
        if (is_array($products))
            foreach ($products as $prod) {
                $amount += $prod['price'] * $prod['quantity'];
            }

        $parameters = [
            'amount' => number_format($amount, 2, '.', ''),
            'prepaid_amount' => (float) 0,
            'items' => $products,
        ];

        // Запрос
        $result = $this->request($order_id . '/commit', $parameters);

        // Лог        
        $this->log([$parameters, $result], $order_id, $order_id . '/commit', $result['status']);

        return $result;
    }

    /*
     *  Проверка заказа
     */

    public function info($order_id) {

        // Запрос
        $result = $this->request($order_id . '/info');

        // Лог        
        $this->log($result, $order_id, $order_id . '/info', 'info/' . $result['status']);

        return $result;
    }
    
     /*
     * Новая заявка из карточки
     */
    public function create_click($products, $client_info, $order_id) {

        $amount = 0;
        if (is_array($products))
            foreach ($products as $prod) {
                $amount += $prod['price'] * $prod['quantity'];
            }

        $parameters = [
            'order' => [
                'id' => (string) $order_id,
                'amount' => number_format($amount, 2, '.', ''),
                'prepaid_amount' => (float) 0,
                'items' => $products,
            ],
        ];
        
        if(is_array($client_info))
            $parameters['client_info'] = $client_info;

        // Запрос
        $result = $this->request('create', $parameters);

        // Лог        
        $this->log([$parameters, $result], $order_id, 'create', $result['status']);

        return $result;
    }

    /*
     * Новая заявка из заказа
     */
    public function create($products, $client_info, $order_id, $order_uid) {

        $amount = 0;
        if (is_array($products))
            foreach ($products as $prod) {
                $amount += $prod['price'] * $prod['quantity'];
            }

        $parameters = [
            'order' => [
                'id' => (string) $order_id,
                'amount' => number_format($amount, 2, '.', ''),
                'prepaid_amount' => (float) 0,
                'items' => $products,
            ],
            'client_info' => $client_info,
            'notification_url' => 'https://' . $_SERVER['SERVER_NAME'] . '/phpshop/modules/dolyame/status/accept.php/'.md5($this->option['login']),
            'fail_url' => 'https://' . $_SERVER['SERVER_NAME'] . '/fail/?uid=' . $order_uid . '&payment=dolyame',
            'success_url' => 'https://' . $_SERVER['SERVER_NAME'] . '/success/?uid=' . $order_uid . '&payment=dolyame',
        ];

        // Запрос
        $result = $this->request('create', $parameters);

        // Лог        
        $this->log([$parameters, $result], $order_id, 'create', $result['status']);

        return $result;
    }

    /*
     *  Запрос
     */

    private function request($operation, $parameters = false) {

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, self::API_URL . $operation);

        if (is_array($parameters)) {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($parameters));
        }


        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSLCERT, $_SERVER['DOCUMENT_ROOT'] . '/phpshop/modules/dolyame/cert/certificate.pem'); // Файл сертификата
        curl_setopt($ch, CURLOPT_SSLKEY, $_SERVER['DOCUMENT_ROOT'] . '/phpshop/modules/dolyame/cert/private.key'); // Файл ключа
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($ch, CURLINFO_HEADER_OUT, true);

        //$uuid = vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex(random_bytes(16)), 4));
        $uuid = vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex(openssl_random_pseudo_bytes(16)), 4));

        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'X-Correlation-ID: ' . $uuid,
            'Authorization: Basic ' . base64_encode($this->option['login'] . ":" . $this->option['password']),
            'Content-Type: application/json',
        ));

        $result = curl_exec($ch);

        $curlError = curl_error($ch);
        if ($curlError) {
            throw new \Exception($curlError);
        }

        curl_close($ch);
        
        $json = json_decode($result, 1);
        if(is_array($json))
            $json['X-Correlation-ID']=$uuid;

        return $json;
    }

    /**
     * Запись лога
     * @param array $message содержание запроса в ту или иную сторону
     * @param string $order_id номер заказа
     * @param string $status статус отправки
     * @param string $status_code код ответа
     */
    public function log($message, $order_id, $status, $status_code = 'succes') {

        $PHPShopOrm = new PHPShopOrm('phpshop_modules_dolyame_log');

        $log = array(
            'message_new' => serialize($message),
            'order_id_new' => $order_id,
            'status_new' => $status,
            'date_new' => time(),
            'type_new' => $status_code
        );
        $PHPShopOrm->insert($log);
    }

}
