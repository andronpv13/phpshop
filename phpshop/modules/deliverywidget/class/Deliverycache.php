<?php

/**
 * Библиотека виджета доставок
 * @author PHPShop Software
 * @version 1.1
 * @package PHPShopModules
 */
class DeliveryWidget {

    public function __construct() {
        $this->option = (new PHPShopOrm('phpshop_modules_deliverywidget_system'))->getOne();
    }

    public function printDays($days) {
        if (empty($days[1]))
            return $days . ' ' . $this->getWordForm($days, ['день', 'дня', 'дней']);

        if ($days[0] == $days[1])
            return $days[0] . ' ' . $this->getWordForm($days[0], ['день', 'дня', 'дней']);

        return $days[0] . ' - ' . $days[1] . ' ' . $this->getWordForm($days[1], ['день', 'дня', 'дней']);
    }

    private function getWordForm($number, $forms) {
        $number = abs($number) % 100;
        $lastDigit = $number % 10;

        if ($number > 10 && $number < 20) {
            return $forms[2];
        }
        if ($lastDigit > 1 && $lastDigit < 5) {
            return $forms[1];
        }
        if ($lastDigit == 1) {
            return $forms[0];
        }

        return $forms[2];
    }

    public function suggestCity($city) {
        global $PHPShopSystem;

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'http://suggestions.dadata.ru/suggestions/api/4_1/rs/suggest/address');
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
            'query' => $city
        ]));
        $headers = [
            'Content-Type: application/json',
            'Accept: application/json',
            'Authorization: Token ' . $PHPShopSystem->getSerilizeParam('admoption.dadata_token'),
        ];
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        $result = json_decode(curl_exec($ch), true)['suggestions'];
        curl_close($ch);

        if (is_array($result))
            foreach ($result as $city) {
                if (!empty($city['data']['postal_code']))
                    return $city['data']['postal_code'];
            }
    }

    public function get($url, $protocol = false) {

        if (empty($protocol)) {

            if (!empty($_SERVER['HTTPS']) && 'off' !== strtolower($_SERVER['HTTPS'])) {
                $protocol = 'https://';
            } else
                $protocol = 'http://';
        }

        $сurl = curl_init();
        curl_setopt_array($сurl, [
            CURLOPT_URL => $protocol . $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
        ]);
        $result = curl_exec($сurl);
        curl_close($сurl);

        return json_decode($result, true);
    }

}

class DeliveryWidgetMysqlcached extends PHPShopMysqlCache {

    public function __construct() {
        parent::__construct();
    }

    public function addServer($server, $port) {
        $this->PHPShopOrm = new PHPShopOrm('phpshop_modules_deliverywidget_cache');
        $this->PHPShopOrm->debug = false;
    }

}

class DeliveryWidgetNocached {

    public function __construct() {
        
    }

    function __call($name, $arguments) {
        if ($name == __CLASS__) {
            self::__construct();
        }
    }

}
