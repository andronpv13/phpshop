<?php

include_once dirname(__DIR__) . '/class/include.php';

class Geolocation
{
    /** @var PHPShopOrm */
    private $ormCity;

    /** @var PHPShopOrm */
    private $ormRegion;

    /** @var array */
    private $options;

    /** @var int */
    private $firstRegion;

    public function __construct($options)
    {
        $this->options = $options;

        $this->ormCity = new \PHPShopOrm('phpshop_citylist_city');
        $this->ormRegion = new \PHPShopOrm('phpshop_citylist_region');

        if(empty($_SESSION['geolocation_city_name'])) {
            $this->changeCity($this->geolocate());
        }
    }

    public function getCurrentCityId()
    {
        // Город определен, его id сохранен. Сразу возвращаем
        if(isset($_SESSION['geolocation_city_id']) && (int) $_SESSION['geolocation_city_id'] > 0) {
            return (int) $_SESSION['geolocation_city_id'];
        }

        return null;
    }

    public function getCityName(): string
    {
       return $_SESSION['geolocation_city_name'];
    }

    public function changeCity(string $cityName): void
    {
        $city = $this->ormCity->getOne(['city_id'], ['name' => "='" . trim($cityName) . "'"]);

        if(isset($city['city_id'])) {
            $_SESSION['geolocation_city_id'] = $city['city_id'];
        }

        $_SESSION['geolocation_city_name'] = $cityName;
    }

    /**
     * @param string $term
     * @return array
     */
    public function findCity($term)
    {
        $term = trim($term);

        $result = array();
        if(!empty($term)) {
            $cities = $this->ormCity->getList(array('name'), array('name' => " LIKE '" . $term . "%'", 'country_id' => '="3159"'));

            foreach ($cities as $v) {
                $result[] = iconv('windows-1251', 'UTF-8', $v['name']);
            }
        }

        return $result;
    }

    public function isCityExist($city)
    {
        $city = trim($city);

        if(empty($city)) {
            return false;
        }

        $entity = $this->ormCity->getOne(['city_id'], ['name' => "='" . $city . "'"]);

        if(!$entity) {
            return false;
        }

        return true;
    }

    /**
     * @return string
     */
    public function render()
    {
        \PHPShopParser::set('favoriteCities', $this->getFavoriteCities());

        return ParseTemplateReturn($GLOBALS['SysValue']['templates']['branches']['geolocation_template'], true);
    }

    private function getFavoriteCities(): string
    {
        if(!is_array(unserialize($this->options['favorite_cities']))) {
            return '';
        }

        $cities = $this->ormCity->getList(array('*'), array('country_id' => '="3159" and `city_id` IN ("' . implode('", "', unserialize($this->options['favorite_cities'])) . '")'));

        $html = '';
        foreach ($cities as $city) {
            $html .= '<span class="city-bubble">
                        <a data-region-id="' . $city['region_id'] . '" href="#" rel="nofollow noopener">' . $city['name'] . '</a>
                    </span>';
        }

        return $html;
    }

    private function geolocate()
    {
        $crawlerDetect = new Jaybizzle\CrawlerDetect\CrawlerDetect();

        // Ботам всегда Москва
        if($crawlerDetect->isCrawler()) {
            file_put_contents(dirname(__DIR__) . '/class/log.txt', 'IP адрес: ' . $_SERVER['REMOTE_ADDR'] . ' Определен бот' . PHP_EOL, FILE_APPEND);
            return 'Москва';
        }

        $headers = array(
            'Accept: application/json',
            'Authorization: Token fc45c399b3d67456a9d2eec7d994664811f87f82'
        );

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://suggestions.dadata.ru/suggestions/api/4_1/rs/iplocate/address?' . http_build_query(['ip' => $_SERVER['REMOTE_ADDR']]));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        $result = curl_exec($ch);
        curl_close($ch);

        $geo = json_decode($result, true);

        // Если не удалось определить
        if(is_array($geo['location']) === false) {
            file_put_contents(dirname(__DIR__) . '/class/log.txt', 'IP адрес: ' . $_SERVER['REMOTE_ADDR'] . ' Не удалось определить.' . PHP_EOL, FILE_APPEND);
            return 'Москва';
        }

        // Определен успешно
        if(isset($geo['location']['data']['city']) && !empty($geo['location']['data']['city'])) {
            file_put_contents(dirname(__DIR__) . '/class/log.txt', 'IP адрес: ' . $_SERVER['REMOTE_ADDR'] . ' Определенный город: ' . PHPShopString::utf8_win1251($geo['location']['data']['city']) . PHP_EOL, FILE_APPEND);
            return PHPShopString::utf8_win1251($geo['location']['data']['city']);
        }

        if(isset($geo['location']['data']['region_with_type']) && !empty($geo['location']['data']['region_with_type'])) {
            file_put_contents(dirname(__DIR__) . '/class/log.txt', 'IP адрес: ' . $_SERVER['REMOTE_ADDR'] . ' Определенный регион: ' . PHPShopString::utf8_win1251($geo['location']['data']['region_with_type']) . PHP_EOL, FILE_APPEND);
            return PHPShopString::utf8_win1251($geo['location']['data']['region_with_type']);
        }

        // Не определен ни город ни регион.
        return 'Москва';
    }

    public function loadRegions(): array
    {
        return ['regions' => $this->getRegions(), 'cities' => $this->getCities()];
    }

    /**
     * @return string
     */
    private function getRegions(): string
    {
        $regions = $this->ormRegion->getList(array('*'), array('country_id' => '="3159"'));

        $i = 1;
        $html = '';
        foreach ($regions as $region) {
            if($i == 1) {
                $active = 'geolocation-active';
                $this->firstRegion = (int) $region['region_id'];
            } else {
                $active = '';
            }

            $html .= '<li class="modal-row ' . $active  . '" style="display: block;">
                            <a data-region-id="' . $region['region_id'] . '" href="#" rel="nofollow noopener" class="geolocation-region">
                                ' . PHPShopString::win_utf8($region['name']) . '
                            </a>
                     </li>';
            $i++;
        }

        return $html;
    }

    /**
     * @return string
     */
    public function getCities($regionId = null): string
    {
        if(empty($regionId)) {
            $regionId = $this->firstRegion;
        }

        $cities = $this->ormCity->getList(['*'], ['country_id' => '="3159"', 'region_id' => '="' . $regionId . '"'], ['order' => 'name asc']);

        $html = '';
        foreach ($cities as $city) {
            $html .= '<li class="modal-row" data-region-id="' . $city['region_id'] . '">
                           <a href="#" rel="nofollow noopener">
                                 <span class="geolocation-city">' . PHPShopString::win_utf8($city['name']) . '</span>
                           </a>
                      </li>';
        }

        return $html;
    }
}