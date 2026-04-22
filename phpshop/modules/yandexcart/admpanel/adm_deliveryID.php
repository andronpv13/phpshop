<?php

include_once dirname(__DIR__) . '/class/YandexMarket.php';

function addYandexcartDelivery($data) {
    global $PHPShopGUI;

    if (empty($data['yandex_enabled']))
        $data['yandex_enabled'] = 1;
    if (empty($data['yandex_day']))
        $data['yandex_day'] = 2;
    if ((int) $data['yandex_type'] === 2) {
        $class = 'yandex-outlets';
    } else {
        $class = 'hide yandex-outlets';
    }

    $market = new YandexMarket();

    $PHPShopGUI->addCSSFiles(
            '../modules/yandexcart/admpanel/gui/jquery-ui.min.css', '../modules/yandexcart/admpanel/gui/style.css'
    );
    $PHPShopGUI->addJSFiles(
            '../modules/yandexcart/admpanel/gui/regions.gui.js', '../modules/yandexcart/admpanel/gui/outlets.gui.js', '../modules/yandexcart/admpanel/gui/jquery-ui.min.js'
    );

    $Tab3 = $PHPShopGUI->setField("Срок доставки дней", $PHPShopGUI->setInputText('от', 'yandex_day_min_new', $data['yandex_day_min'], 100, false, 'left') . $PHPShopGUI->set_(3) . $PHPShopGUI->setInputText(null, 'yandex_day_new', $data['yandex_day'], 100, __('до')));
    $Tab3 .= $PHPShopGUI->setField("Время увеличения доставки", $PHPShopGUI->setInputText('с', 'yandex_order_before_new', $data['yandex_order_before'], 150, __('часов')), false, 'Заказы после этого времени будут доставлены сроком +1 день. Число от 1 - 24.');
    $Tab3 .= $PHPShopGUI->setField('Яндекс.Маркет', $PHPShopGUI->setRadio('yandex_enabled_new', 1, 'Выключить', $data['yandex_enabled'], false, 'text-warning') .
            $PHPShopGUI->setRadio('yandex_enabled_new', 2, 'Включить', $data['yandex_enabled']));

    $Tab3 .= $PHPShopGUI->setField('Только для локального региона', $PHPShopGUI->setRadio('yandex_check_new', 1, 'Выключить', $data['yandex_check'], false, 'text-warning') . $PHPShopGUI->setRadio('yandex_check_new', 2, 'Включить', $data['yandex_check']));

    // Тип доставки
    $delivery_value[] = array(__('Курьерская доставка'), 1, $data['yandex_type']);
    $delivery_value[] = array(__('Самовывоз'), 2, $data['yandex_type']);
    $delivery_value[] = array(__('Почта'), 3, $data['yandex_type']);
    $Tab3 .= $PHPShopGUI->setField('Способы доставки', $PHPShopGUI->setSelect('yandex_type_new', $delivery_value));

    $Tab3 .= $PHPShopGUI->setField("Регион доставки", $PHPShopGUI->setInputText(null, 'yandex-region', '', false, false, false, 'yandex-region'));
    $Tab3 .= $PHPShopGUI->setInput('hidden', 'yandex_region_id_new', $data['yandex_region_id']);

    if ($market->options['model_2'] == 'DBS')
        $Tab3 .= $PHPShopGUI->setField("Регион доставки &#8470;2", $PHPShopGUI->setInputText(null, 'yandex-region-2', '', false, false, false, 'yandex-region-2'));
    $Tab3 .= $PHPShopGUI->setInput('hidden', 'yandex_region_id_2_new', $data['yandex_region_id_2']);

    if ($market->options['model_3'] == 'DBS')
        $Tab3 .= $PHPShopGUI->setField("Регион доставки &#8470;3", $PHPShopGUI->setInputText(null, 'yandex-region-3', '', false, false, false, 'yandex-region-3'));
    $Tab3 .= $PHPShopGUI->setInput('hidden', 'yandex_region_id_3_new', $data['yandex_region_id_3']);

    if (empty($data['is_folder']) and $market->options['model'] == 'DBS')
        $Tab3 .= $PHPShopGUI->setField('Точки продаж', $PHPShopGUI->setSelect('yandex_delivery_points[]', $market->getOutletsSelectOptions($data['yandex_region_id'], $data['yandex_delivery_points']), '', false, false, false, false, 1, true), 1, null, $class);

    if (empty($data['is_folder']) and $market->options['model_2'] == 'DBS')
        $Tab3 .= $PHPShopGUI->setField('Точки продаж &#8470;2', $PHPShopGUI->setSelect('yandex_delivery_points_2[]', $market->getOutletsSelectOptions($data['yandex_region_id_2'], $data['yandex_delivery_points_2'], 2), '', false, false, false, false, 1, true), 1, null, $class);

    if (empty($data['is_folder']) and $market->options['model_3'] == 'DBS')
        $Tab3 .= $PHPShopGUI->setField('Точки продаж &#8470;3', $PHPShopGUI->setSelect('yandex_delivery_points_3[]', $market->getOutletsSelectOptions($data['yandex_region_id_3'], $data['yandex_delivery_points_3'], 3), '', false, false, false, false, 1, true), 1, null, $class);


    if (empty($data['is_folder']) and ( $market->options['model'] == 'DBS' or $market->options['model_2'] == 'DBS' or $market->options['model_3'] == 'DBS'))
        $PHPShopGUI->addTab(array("Яндекс.Маркет для DBS", $Tab3, true));
}

function yandexMarketUpdate($data) {
    if (is_array($_POST['yandex_delivery_points']))
        $_POST['yandex_delivery_points_new'] = serialize($_POST['yandex_delivery_points']);
    if (is_array($_POST['yandex_delivery_points_2']))
        $_POST['yandex_delivery_points_2_new'] = serialize($_POST['yandex_delivery_points_2']);
    if (is_array($_POST['yandex_delivery_points_3']))
        $_POST['yandex_delivery_points_3_new'] = serialize($_POST['yandex_delivery_points_3']);
}

$addHandler = array(
    'actionStart' => 'addYandexcartDelivery',
    'actionDelete' => false,
    'actionUpdate' => 'yandexMarketUpdate'
);
?>