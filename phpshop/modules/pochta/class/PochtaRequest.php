<?php

class PochtaRequest
{
    const API_URL = 'https://otpravka-api.pochta.ru';
    const CREATE_ORDER_URL = '/1.0/user/backlog';
    const NORMALIZE_ADDRESS = '/1.0/clean/address';

    /** @var Settings */
    private $settings;

    public function __construct($settings)
    {
        $this->settings = $settings;
    }

    public function createOrder($parameters)
    {
        $result = $this->request(self::CREATE_ORDER_URL, array($parameters), false, $parameters['order-num'], 'PUT');

        if(isset($result['errors'][0]['error-codes'])) {
            $errors = array();
            foreach ($result['errors'][0]['error-codes'] as $error) {
                $errors[] = array('code' => $error['code'], 'description' => $error['description']);
            }
            return array(
                'success' => false,
                'errors' => $errors
            );
        }

        return array('success' => true);
    }

    /**
     * Нормализация адреса из строки.
     */
    public function normalizeAddress($address)
    {
        $parameters = [
            'id'               => 1,
            'original-address' => PHPShopString::win_utf8($address)
        ];

        $result = $this->request(self::NORMALIZE_ADDRESS, [$parameters]);
        $result = $this->utf8ToWindows1251($result, self::NORMALIZE_ADDRESS);

        return $result[0];
    }

    private function request($url, $parameters = [], $skipLog = true, $orderNumber = null, $method = '')
    {
        $request = json_encode($parameters);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, self::API_URL . $url);
        if(!empty($method)) {
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
        }
        curl_setopt($ch, CURLOPT_POSTFIELDS, $request);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json;charset=UTF-8',
            'Authorization: AccessToken ' . $this->settings->get('token'),
            'X-User-Authorization: Basic ' . base64_encode($this->settings->get('login') . ':' . $this->settings->get('password'))
        ));

        $result = json_decode(curl_exec($ch), true);

        if((isset($result['code']) && isset($result['desc'])) || $skipLog === false) {
            $this->log($parameters, $result, $url, $orderNumber);
        }

        return $result;
    }

    /**
     * @param array $data
     * @param string $method
     * @return array
     */
    private function utf8ToWindows1251($data, $method)
    {
        switch ($method) {
            case self::CREATE_ORDER_URL: {
                if(isset($data['errors'][0]['error-codes']) && is_array($data['errors'][0]['error-codes'])) {
                    foreach ($data['errors'][0]['error-codes'] as $key => $error) {
                        $data['errors'][0]['error-codes'][$key]['description'] = PHPShopString::utf8_win1251($data['errors'][0]['error-codes'][$key]['description']);
                    }
                }
                break;
            }
            case self::NORMALIZE_ADDRESS: {
                if(isset($data[0]['original-address'])) {
                    $data[0]['original-address'] = PHPShopString::utf8_win1251($data[0]['original-address']);
                }
                if(isset($data[0]['place'])) {
                    $data[0]['place'] = PHPShopString::utf8_win1251($data[0]['place']);
                }
                if(isset($data[0]['region'])) {
                    $data[0]['region'] = PHPShopString::utf8_win1251($data[0]['region']);
                }
                if(isset($data[0]['street'])) {
                    $data[0]['street'] = PHPShopString::utf8_win1251($data[0]['street']);
                }
                break;
            }
        }

        return $data;
    }

    /**
     * @param array $request
     * @param array $response
     * @param string $method
     * @param int|null $orderUid
     */
    private function log($request, $response, $method, $orderUid = null)
    {
        $response = $this->utf8ToWindows1251($response, $method);

        $PHPShopOrm = new PHPShopOrm('phpshop_modules_pochta_log');

        $message = array(
            'request' => $request,
            'response' => $response
        );

       $PHPShopOrm->insert(
            array(
                'message_new'   => serialize($message),
                'order_uid_new' => $orderUid,
                'status_new'    => isset($response['errors'][0]) ? 'Ошибка' : 'Успешно',
                'method_new'    => $method,
                'date_new'      => time()
            )
        );
    }
}