<?php

if (!defined("OBJENABLED"))
    exit(header('Location: /?error=OBJENABLED'));

/**
 * Элемент GEOIP редиректа
 */
class AddToTemplateGeoIpElement extends PHPShopElements {

    var $debug = false;
    var $city = null;
    var $path = './phpshop/modules/geoipredirect/class/';

    /**
     * Конструктор
     */
    function __construct() {
        parent::__construct();

        if (empty($_SESSION['geoipredirect']) or $_SESSION['geoipredirect'] > 1) {

            if (fopen($this->path . 'SxGeoCity.dat', 'rb')) {
                include_once($this->path . "SxGeo.class.php");
                $SxGeo = new SxGeo($this->path . 'SxGeoCity.dat');
                $result = $SxGeo->get($_SERVER['REMOTE_ADDR']);
                $this->city = $result['city']['name_ru'];
                $_SESSION['geoipredirect'] = 1;
            }
        }
    }

    function redirect() {

        if (!empty($this->city)) {
            $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['geoipredirect']['geoipredirect_city']);
            $PHPShopOrm->debug = $this->debug;
            $data = $PHPShopOrm->select(array('host'), array('name' => '="' . $this->city . '"', 'enabled' => "='1'"), null, array('limit' => 1));

            if (!empty($data['host']) and $_SERVER['SERVER_NAME'] != $data['host']) {
                $_SESSION['geoipredirect'] = 2;
                header("Location: http://" . $data['host']);
            }
        }
    }

}

$AddToTemplateGeoIpElement = new AddToTemplateGeoIpElement();
$AddToTemplateGeoIpElement->redirect();
?>