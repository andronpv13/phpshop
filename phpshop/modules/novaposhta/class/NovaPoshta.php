<?php

include_once dirname(__DIR__) . '/class/Loader.php';
include_once dirname(__DIR__) . '/class/Request.php';
include_once dirname(__DIR__) . '/class/Order.php';

class NovaPoshta {

    /** @var Request */
    private $request;
    /** @var Loader */
    public $loader;
    /** @var Order */
    public $order;

    private $whRef = '841339c7-591a-42e2-8233-7a0a00f0ed6f';
    private $cargoWhRef = '9a68df70-0267-42a8-bb5c-37f427e36ee4';
    private $parcelShopRef = '6f8c7162-4b72-4b0a-88e5-906948c6a92f';

    public function __construct()
    {
        $PHPShopOrm = new PHPShopOrm('phpshop_modules_novaposhta_system');

        /**
         * Опции модуля
         */
        $this->option = $PHPShopOrm->select();

        $this->request = new Request($this->option);
        $this->loader = new Loader($this->request);
        $this->order = new Order($this->option, $this->request);
    }

    public function findCity($city)
    {
        $city = trim($city);

        if(!empty($city)) {
            $PHPShopOrm = new PHPShopOrm('phpshop_modules_novaposhta_cities');

            $cities = $PHPShopOrm->getList(array('city', 'area_description', 'ref'), array(
                'area_description' => " LIKE '%".trim($city)."%' OR area_description_ru LIKE '%".trim($city)."%'")
            );

            $result = array();
            foreach ($cities as $k => $v) {
                $result[] = array(
                    'label' => iconv('windows-1251', 'UTF-8', $v['area_description']),
                    'value' => $v['ref']
                );
            }

            return $result;
        }
    }

    /**
     * @param $city
     * @throws Exception
     */
    public function getCity($city)
    {
        $PHPShopOrm = new PHPShopOrm('phpshop_modules_novaposhta_cities');
        $result = $PHPShopOrm->getOne(array('*'), array('ref' => "='".trim($city)."'"));

        if(!$result) {
            throw new \Exception('Город не найден.');
        }

        return $result;
    }

    /**
     * @param $city
     * @throws Exception
     */
    public function getPvz($city)
    {
        $PHPShopOrmWhTypes = new PHPShopOrm('phpshop_modules_novaposhta_wh_types');
        $whTypes = array_column($PHPShopOrmWhTypes->getList(), 'title', 'ref');

        $PHPShopOrm = new PHPShopOrm('phpshop_modules_novaposhta_warehouses');
        $result = $PHPShopOrm->getList(array('*'), array('city' => "='".$city."' and type in ('$this->whRef', '$this->cargoWhRef', '$this->parcelShopRef')"));

        if(!count($result)) {
            throw new \Exception('ПВЗ не найдены.');
        }

        foreach ($result as $key => $value) {
            $result[$key]['type_title'] = $whTypes[$value['type']];
        }

        $result = $this->Windows1251ToUtf8($result, 'getPvz');

        return $result;
    }

    /**
     * Статусы заказов в системе.
     *
     * @param $current
     * @return array
     */
    public static function getOrderStatuses($current)
    {
        $PHPShopOrderStatusArray = new \PHPShopOrderStatusArray();
        $OrderStatusArray = $PHPShopOrderStatusArray->getArray();

        $status = array(
            array(__('Новый заказ'), 0, $current)
        );
        if (is_array($OrderStatusArray)) {
            foreach ($OrderStatusArray as $order_status) {
                $status[] = array($order_status['name'], $order_status['id'], $current);
            }
        }

        return $status;
    }

    /**
     * Способы доставки в системе.
     *
     * @param $current
     * @return array
     */
    public static function getDeliveries($current)
    {
        $PHPShopDeliveryArray = new \PHPShopDeliveryArray(array('is_folder' => "!='1'", 'enabled' => "='1'"));

        $DeliveryArray = $PHPShopDeliveryArray->getArray();
        if (is_array($DeliveryArray)) {
            foreach ($DeliveryArray as $delivery) {
                if (strpos($delivery['city'], '.')) {
                    $name = explode(".", $delivery['city']);
                    $delivery['city'] = $name[0];
                }
                $delivery_value[] = array($delivery['city'], $delivery['id'], $current);
            }
        }

        return $delivery_value;
    }

    /**
     * Текущий статус справочника населенных пунктов с ПВЗ НП. Рекомендует обновлять раз в 3 дня.
     *
     * @param $lastUpdate
     * @return string
     */
    public static function getCitiesStatus($lastUpdate)
    {
        return self::renderStatus($lastUpdate, 86400 * 3);
    }

    /**
     * Текущий статус справочника типов отделений. Рекомендует обновлять раз в 30 дней.
     *
     * @param $lastUpdate
     * @return string
     */
    public static function getWhTypesStatus($lastUpdate)
    {
        return self::renderStatus($lastUpdate, 86400 * 30);
    }

    /**
     * Текущий статус справочника отделений. Рекомендует обновлять раз в 3 дня.
     *
     * @param $lastUpdate
     * @return string
     */
    public static function getWarehousesStatus($lastUpdate)
    {
        return self::renderStatus($lastUpdate, 86400 * 3);
    }

    /**
     * @param $cityRef
     * @param $weight
     * @throws Exception
     */
    public function getCost($cityRef, $weight)
    {
        $PHPShopCart = new PHPShopCart();

        if($weight < 0.1) {
            $weight = 0.1;
        }

        $result = $this->request->post(Request::INTERNET_DOCUMENT_MODEL, Request::CALCULATE_METHOD, array(
            'CitySender' => $this->option['city_sender'],
            'CityRecipient' => $cityRef,
            'Weight' => $weight,
            'ServiceType' => Order::SERVICE,
            'CargoType' => Order::CARGO_TYPE,
            'SeatsAmount' => Order::SEATS_AMOUNT,
            'Cost' => $PHPShopCart->getSum()
        ));

        return $result['data'][0]['Cost'];
    }

    public function getSenders()
    {
        return $this->request->post(Request::COUNTERPARTY_MODEL, Request::COUNTERPARTIES_METHOD, array('CounterpartyProperty' => 'Sender'));
    }

    public function getSenderAddresses()
    {
        return $this->request->post(Request::COUNTERPARTY_MODEL, Request::COUNTERPARTY_GET_ADDRESSES, array(
            'Ref' => $this->option['sender'],
            'CounterpartyProperty' => 'Sender'
        ));
    }

    public function getSenderContacts()
    {
        return $this->request->post(Request::COUNTERPARTY_MODEL, Request::COUNTERPARTY_GET_CONTACT_PERSONS, array(
            'Ref' => $this->option['sender']
        ));
    }

    /**
     * @return bool
     */
    public function whBaseIsNotEmpty()
    {
        $PHPShopOrm = new PHPShopOrm('phpshop_modules_novaposhta_warehouses');
        $result = $PHPShopOrm->select(array('COUNT("id")'));

        return $result['COUNT("id")'] > 0;
    }

    /**
     * @param string $currentCity
     * @return array
     */
    public function getCitiesArr($currentCity)
    {
        $orm = new PHPShopOrm('phpshop_modules_novaposhta_cities');

        $result = array_map(function ($city) use ($currentCity) {
            return [$city['area_description'], $city['ref'], $currentCity];
        }, $orm->getList());

        return $result;
    }

    private static function renderStatus($lastUpdate, $period)
    {
        if ((date('U') - $lastUpdate) < $period)
            return '<p class="text-success">' . PHPShopDate::dataV($lastUpdate) . '</p>';

        if($lastUpdate > 0)
            return '<p class="text-warning">' . PHPShopDate::dataV($lastUpdate) . '. Рекомендуется обновить.</p>';

        return '<p class="text-danger">Справочник не загружен. Нажмите "Обновить".</p>';
    }

    private function Windows1251ToUtf8($data, $method)
    {
        switch ($method) {
            case 'getPvz': {
                foreach ($data as $key => $pvz) {
                    $data[$key]['title'] = iconv('Windows-1251', 'UTF-8', $pvz['title']);
                    $data[$key]['address'] = iconv('Windows-1251', 'UTF-8', $pvz['address']);
                    $data[$key]['type_title'] = iconv('Windows-1251', 'UTF-8', $pvz['type_title']);
                }
                break;
            }
        }

        return $data;
    }
}