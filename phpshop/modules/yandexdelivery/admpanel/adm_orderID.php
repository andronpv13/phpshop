<?php

include_once dirname(__DIR__) . '/class/include.php';

function yadeliverySend($data) {

    $order = unserialize($data['orders']);
    $YandexDelivery = new YandexDelivery();

    if ($YandexDelivery->isYandexDeliveryMethod((int) $order['Person']['dostavka_metod'])) {
        if ((int) $data['statusi'] !== (int) $_POST['statusi_new']) {
            if ((int) $_POST['statusi_new'] === (int) $YandexDelivery->options['status']) {


                $tracking = $YandexDelivery->setDataFromOrderEdit($data);
                if ($tracking) {
                    $_POST['tracking_new'] = $tracking;
                }
            }
        }
    }
}

function addYadeliveryTab($data) {
    global $PHPShopGUI;

    $YandexDelivery = new YandexDelivery();
    $order = unserialize($data['orders']);
    $weight = $order['Cart']['weight'];
    if (empty($weight))
        $weight = $YandexDelivery->options['weight'];


    if ($YandexDelivery->isYandexDeliveryMethod((int) $order['Person']['dostavka_metod'])) {

        $PHPShopGUI->addJSFiles('../modules/yandexdelivery/admpanel/gui/script.gui.js');

        $Tab = $YandexDelivery->buildOrderTab($data);

        PHPShopParser::set('yandexdelivery_weight', $weight);
        PHPShopParser::set('yandexdelivery_city', $YandexDelivery->options['city']);
        PHPShopParser::set('yandexdelivery_station', $YandexDelivery->options['warehouse_id']);
        //$Tab .= ParseTemplateReturn('../modules/yandexdelivery/templates/template.tpl', true);
        $GLOBALS['modalExternalModules'] = ParseTemplateReturn(dirname(__DIR__) . '/templates/template.tpl', true);

        $PHPShopGUI->addTab(["Яндекс.Доставка", $Tab]);
    }
}

$addHandler = array(
    'actionStart' => 'addYadeliveryTab',
    'actionDelete' => false,
    'actionUpdate' => 'yadeliverySend'
);
?>