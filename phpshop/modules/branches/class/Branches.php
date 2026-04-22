<?php

include_once dirname(__DIR__) . '/class/include.php';

class Branches
{
    private static $branchesCity;

    public $options = [];

    /** @var Geolocation */
    public $Geolocation;

    public function __construct()
    {
        $orm = new PHPShopOrm('phpshop_modules_branches_system');

        $this->options = $orm->select();
        
        if(empty($this->options['yandex_api_key']))
            $this->options['yandex_api_key']='cb432a8b-21b9-4444-a0c4-3475b674a958';

        $this->Geolocation = new Geolocation($this->options);
    }

    public function getBranchesCoords()
    {
        $result = [];
        foreach ($this->getBranches() as $branch) {
            $result[$branch['city_id']][] = [
                'lon' => $branch['lon'],
                'lat' => $branch['lat'],
                'name' => $branch['name']
            ];
        }

        return $result;
    }

    public function getFavoriteCitiesForSelect($currentCities): array
    {
        if(!is_array($currentCities)) {
            $currentCities = array();
        }

        $ormCities = new PHPShopOrm($GLOBALS['SysValue']['base']['citylist_city']);

        $cities = $ormCities->getList(array('city_id', 'name'), array('country_id' => '="3159"'));

        $result = array();
        foreach ($cities as $city) {
            $result[] = array($city['name'], $city['city_id'], in_array($city['city_id'], $currentCities) ? $city['city_id'] : null);
        }

        return $result;
    }

    /**
     * @param string $currentCity
     * @return array
     */
    public function getCities($currentCity = null): array
    {
        $ormCities = new PHPShopOrm($GLOBALS['SysValue']['base']['citylist_city']);

        $orm = new PHPShopOrm($GLOBALS['SysValue']['base']['citylist_region']);
        $regions = array_column($orm->getList(['*'], ['country_id' => '="3159"']), 'name', 'region_id');

        $result = [];
        foreach ($ormCities->getList(['*'], ['country_id' => '="3159"']) as $city) {
            $result[] = [$city['name'] . ', ' . $regions[$city['region_id']], $city['city_id'], $currentCity];
        }

        return $result;
    }

    public function getDefaultCity($current = null): array
    {
        $orm = new PHPShopOrm($GLOBALS['SysValue']['base']['citylist_region']);
        $regions = array_column($orm->getList(['*'], ['country_id' => '="3159"']), 'name', 'region_id');

        $result = [];
        foreach ($this->getBranchesCity() as $city) {
            $result[] = [$city['name'] . ', ' . $regions[$city['region_id']], $city['city_id'], $current];
        }

        return $result;
    }

    public function getCitiesInHTML(): string
    {
        $html = '';
        foreach ($this->getBranchesCity() as $city) {
            $html .= sprintf('<li><a href="javascript:void(0)" data-city-id="%s" class="branches-city %s">%s</a></li>', $city['city_id'], $this->getCurrentCityId() === (int) $city['city_id'] ? 'active' : '', $city['name']);
        }

        return $html;
    }

    public function getBranchesInHTML(): string
    {
        $html = '';

        foreach ($this->getBranches() as $branch) {
            $this->getCurrentCityId() !== (int) $branch['city_id'] ? $class = 'hidden' : $class = '';

            $html .= '<div class="col-md-6 col-sm-12 branch-address ' .  $class . '" data-city-id="' . $branch['city_id'] . '">
                        <div><i class="fa fa-map-marker" aria-hidden="true"></i> ' . $branch['address'] . '</div>
                      </div>';
        }

        return $html;
    }

    /**
     * Выбранный город на странице ПВЗ. Если ПВЗ есть в городе юзера, иначе город с ПВЗ по умолчанию из настроек.
     * @return string
     */
    public function getCurrentCityName(): string
    {
        $cities = array_column($this->getBranchesCity(), 'name', 'city_id');

        if(isset($cities[$this->Geolocation->getCurrentCityId()])) {
            return $cities[$this->Geolocation->getCurrentCityId()];
        }

        return (string)$cities[$this->options['default_city_id']];
    }

    /**
     * ID выбранного города на странице ПВЗ. Если ПВЗ есть в городе юзера, иначе город с ПВЗ по умолчанию из настроек.
     * @return int
     */
    public function getCurrentCityId(): int
    {
        $cities = array_column($this->getBranchesCity(), 'name', 'city_id');

        if(isset($cities[$this->Geolocation->getCurrentCityId()])) {
            return (int) $this->Geolocation->getCurrentCityId();
        }

        return (int) $this->options['default_city_id'];
    }

    private function getBranchesCity(): array
    {
        if(is_array(self::$branchesCity)) {
            return self::$branchesCity;
        }

        $ormCities = new PHPShopOrm($GLOBALS['SysValue']['base']['citylist_city']);

        $citiesIds = [];
        foreach ($this->getBranches() as $branch) {
            $citiesIds[] = $branch['city_id'];
        }

        if(count(array_unique($citiesIds)) === 0) {
            return [];
        }

        self::$branchesCity = $ormCities->getList(['*'], ['city_id' => ' IN (' . implode(',', array_unique($citiesIds)) . ')']);

        return self::$branchesCity;
    }

    private function getBranches(): array
    {
        $orm = new PHPShopOrm('phpshop_modules_branches_branches');

        return $orm->getList(['*']);
    }
}