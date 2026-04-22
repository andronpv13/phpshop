<?php

include_once dirname(__DIR__) . '/class/include.php';

if (!defined("OBJENABLED"))
    exit(header('Location: /?error=OBJENABLED'));

class GeolocationElement extends \PHPShopElements {

    public $debug = false;
    /** @var Geolocation */
    private $Geolocaion;

    /**
     * Конструктор
     */
    function __construct() {
        parent::__construct();

        $this->Geolocaion = (new Branches())->Geolocation;
    }

    public function renderRegionSelector()
    {
        $this->set('geolocation_city', $this->Geolocaion->getCityName());
        $this->set('geolocationPopup', $this->Geolocaion->render());
    }
}

$GeolocationElement = new GeolocationElement();
$GeolocationElement->renderRegionSelector();
?>