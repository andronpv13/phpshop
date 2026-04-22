<?php

/**
 * Методы работы с удаленным сервисом и логирование.
 *
 * Class Request
 */
class Request {

    private $options;

    const API_URL = 'http://api.novaposhta.ua/v2.0/json/';
    const API_PER_PAGE = 150;

    const ADDRESS_MODEL = 'Address';
    const INTERNET_DOCUMENT_MODEL = 'InternetDocument';
    const COUNTERPARTY_MODEL = 'Counterparty';

    const SETTLEMETS_METHOD = 'getSettlements';
    const WAREHOUSE_TYPES_METHOD = 'getWarehouseTypes';
    const WAREHOUSES_METHOD = 'getWarehouses';
    const CREATE_ORDER_METHOD = 'save';
    const CALCULATE_METHOD = 'getDocumentPrice';
    const COUNTERPARTIES_METHOD = 'getCounterparties';
    const COUNTERPARTY_CREATE_METHOD = 'save';
    const COUNTERPARTY_GET_ADDRESSES = 'getCounterpartyAddresses';
    const COUNTERPARTY_GET_CONTACT_PERSONS = 'getCounterpartyContactPersons';

    public function __construct($options)
    {
        $this->options = $options;
    }

    public function post($model, $method, $data = array(), $orderNumber = null)
    {
        if(!empty($this->options['api_key'])) {
            $params = array(
                'apiKey' => $this->options['api_key'],
                'modelName' => $model,
                'calledMethod' => $method,
            );
            if(!empty($data)) {
                $params['methodProperties'] = $data;
            }

            $request = json_encode($params);

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, self::API_URL . $model . '/' . $method);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $request);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json',
                'Content-Length: ' . strlen($request)
            ));
            $result = curl_exec($ch);

            $result = $this->utf8ToWindows1251(json_decode($result, true), $method);

            $this->log(json_decode($request, true), $result, $model, $method, $orderNumber);

            return $result;
        }
    }

    /**
     * @param $request
     * @param $response
     * @param $model
     * @param $method
     * @param null $orderId
     */
    private function log($request, $response, $model, $method, $orderId = null)
    {
        $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['novaposhta']['novaposhta_log']);

        // Убираем с логов сам массив результатов. Оставляем только количество и результат.
        // С массивом результатов логи растут очень быстро и не всегда сериализуется массив. Для режима разработки - можно закомментировать.
        unset($response['data']);

        $message = array(
            'request' => $request,
            'response' => $response
        );

        $PHPShopOrm->insert(
            array(
                'message_new'  => serialize($message),
                'order_id_new' => $orderId,
                'status_new'   => $response['success'] ? 'Успешно' : 'Ошибка',
                'model_new'    => $model,
                'method_new'   => $method,
                'date_new'     => time()
            )
        );
    }

    private function utf8ToWindows1251($response, $method)
    {
        switch ($method) {
            case self::SETTLEMETS_METHOD: {
                foreach ($response['data'] as $key => $city) {
                    $response['data'][$key]['Description'] = iconv('UTF-8', 'Windows-1251', $city['Description']);
                    $response['data'][$key]['DescriptionRu'] = iconv('UTF-8', 'Windows-1251', $city['DescriptionRu']);
                    $response['data'][$key]['SettlementTypeDescription'] = iconv('UTF-8', 'Windows-1251', $city['SettlementTypeDescription']);
                    $response['data'][$key]['SettlementTypeDescriptionRu'] = iconv('UTF-8', 'Windows-1251', $city['SettlementTypeDescriptionRu']);
                    $response['data'][$key]['RegionsDescription'] = iconv('UTF-8', 'Windows-1251', $city['RegionsDescription']);
                    $response['data'][$key]['RegionsDescriptionRu'] = iconv('UTF-8', 'Windows-1251', $city['RegionsDescriptionRu']);
                    $response['data'][$key]['AreaDescription'] = iconv('UTF-8', 'Windows-1251', $city['AreaDescription']);
                    $response['data'][$key]['AreaDescriptionRu'] = iconv('UTF-8', 'Windows-1251', $city['AreaDescriptionRu']);
                }
                break;
            }
            case self::WAREHOUSES_METHOD: {
                foreach ($response['data'] as $key => $warehouse) {
                    $response['data'][$key]['Description'] = iconv('UTF-8', 'Windows-1251', $warehouse['Description']);
                    $response['data'][$key]['DescriptionRu'] = iconv('UTF-8', 'Windows-1251', $warehouse['DescriptionRu']);
                    $response['data'][$key]['ShortAddress'] = iconv('UTF-8', 'Windows-1251', $warehouse['ShortAddress']);
                    $response['data'][$key]['ShortAddressRu'] = iconv('UTF-8', 'Windows-1251', $warehouse['ShortAddressRu']);
                    $response['data'][$key]['CityDescription'] = iconv('UTF-8', 'Windows-1251', $warehouse['CityDescription']);
                    $response['data'][$key]['CityDescriptionRu'] = iconv('UTF-8', 'Windows-1251', $warehouse['CityDescriptionRu']);
                    $response['data'][$key]['DistrictCode'] = iconv('UTF-8', 'Windows-1251', $warehouse['DistrictCode']);
                }
                break;
            }
            case self::WAREHOUSE_TYPES_METHOD: {
                foreach ($response['data'] as $key => $warehouseType) {
                    $response['data'][$key]['Description'] = iconv('UTF-8', 'Windows-1251', $warehouseType['Description']);
                }
                break;
            }
            case self::COUNTERPARTIES_METHOD: {
                foreach ($response['data'] as $key => $counterparty) {
                    $response['data'][$key]['Description'] = iconv('UTF-8', 'Windows-1251', $counterparty['Description']);
                    $response['data'][$key]['FirstName'] = iconv('UTF-8', 'Windows-1251', $counterparty['FirstName']);
                    $response['data'][$key]['LastName'] = iconv('UTF-8', 'Windows-1251', $counterparty['LastName']);
                    $response['data'][$key]['MiddleName'] = iconv('UTF-8', 'Windows-1251', $counterparty['MiddleName']);
                    $response['data'][$key]['OwnershipFormDescription'] = iconv('UTF-8', 'Windows-1251', $counterparty['OwnershipFormDescription']);
                }
                break;
            }
            case self::COUNTERPARTY_GET_ADDRESSES: {
                foreach ($response['data'] as $key => $address) {
                    $response['data'][$key]['Description'] = iconv('UTF-8', 'Windows-1251', $address['Description']);
                    $response['data'][$key]['CityDescription'] = iconv('UTF-8', 'Windows-1251', $address['CityDescription']);
                    $response['data'][$key]['StreetDescription'] = iconv('UTF-8', 'Windows-1251', $address['StreetDescription']);
                    $response['data'][$key]['BuildingDescription'] = iconv('UTF-8', 'Windows-1251', $address['BuildingDescription']);
                }
                break;
            }
            case self::COUNTERPARTY_GET_CONTACT_PERSONS: {
                foreach ($response['data'] as $key => $contacts) {
                    $response['data'][$key]['Description'] = iconv('UTF-8', 'Windows-1251', $contacts['Description']);
                    $response['data'][$key]['LastName'] = iconv('UTF-8', 'Windows-1251', $contacts['LastName']);
                    $response['data'][$key]['FirstName'] = iconv('UTF-8', 'Windows-1251', $contacts['FirstName']);
                    $response['data'][$key]['MiddleName'] = iconv('UTF-8', 'Windows-1251', $contacts['MiddleName']);
                    $response['data'][$key]['MarketplacePartnerDescription'] = iconv('UTF-8', 'Windows-1251', $contacts['MarketplacePartnerDescription']);
                }
                break;
            }
        }

        return $response;
    }
}