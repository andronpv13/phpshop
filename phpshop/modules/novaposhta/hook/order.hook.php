<?php

/**
 * Добавление js
 *
 * param obj $obj
 * param array $row
 * param string $rout
 */
function order_novaposhta_hook($obj, $row, $rout) {

    if ($rout === 'MIDDLE') {

        include_once 'phpshop/modules/novaposhta/class/NovaPoshta.php';
        $NovaPoshta = new NovaPoshta();

        $PHPShopCart = new PHPShopCart();
        $weight = $PHPShopCart->getWeight() / 1000;

        if(empty($weight))
            $weight = $NovaPoshta->option['weight'] > 0 ? $NovaPoshta->option['weight'] / 1000 : 0.1;

        try {
            $city = $NovaPoshta->getCity($NovaPoshta->option['default_city']);
        } catch (\Exception $exception) {
            return;
        }

        $obj->set('novaposhtaWeight', $weight);
        $obj->set('novaposhtaGoogleKey', $NovaPoshta->option['google_api']);
        $obj->set('novaposhtaLatitude', $city['latitude']);
        $obj->set('novaposhtaLongitude', $city['longitude']);
        $obj->set('novaposhtaDefaultCity', $city['area_description']);
        $obj->set('novaposhtaDefaultCityRef', $city['ref']);

        $popup = ParseTemplateReturn($GLOBALS['SysValue']['templates']['novaposhta']['novaposhta_template'], true);

        $obj->set('order_action_add', $popup, true);
    }
}

$addHandler = array('order' => 'order_novaposhta_hook');
?>