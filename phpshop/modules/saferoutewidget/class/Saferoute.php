<?php

include_once dirname(__DIR__) . '/api/SafeRouteWidgetApi.php';

class Saferoute
{
    const API_URL = 'https://api.saferoute.ru/v2/';

    public $options;
    private $api;

    public function __construct()
    {
        $PHPShopOrm = new PHPShopOrm('phpshop_modules_saferoutewidget_system');

        $this->options = $PHPShopOrm->select();

        $this->api = new SafeRouteWidgetApi();
        $this->api->setToken($this->options['key']);
        $this->api->setShopId($this->options['shop_id']);
    }

    public function sendOrder($params)
    {
        if(!$this->options['key']){
            throw new Exception('No API - key defined');
        }

        $this->api->setData($params);

        return $this->api->submit(self::API_URL . 'widgets/update-order');
    }
}