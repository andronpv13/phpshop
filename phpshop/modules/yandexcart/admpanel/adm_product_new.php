<?php

function addYandexcart($data) {
    global $PHPShopGUI;

    // Очистка при копировании товара
    $data['yml'] = $data['yml_2'] = $data['yml_3'] = 0;

    $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['yandexcart']['yandexcart_system']);
    $options = $PHPShopOrm->select();

    $Tab3 = null;

    // Валюты
    $PHPShopValutaArray = new PHPShopValutaArray();
    $valuta_array = $PHPShopValutaArray->getArray();
    if (is_array($valuta_array))
        foreach ($valuta_array as $val) {
            if ($data['baseinputvaluta'] == $val['id']) {
                $valuta_def_name = $val['code'];
            }
        }

    if (!empty($options['model'])) {
        $Tab3 .= $PHPShopGUI->setField($options['model'], $PHPShopGUI->setCheckbox('yml_new', 1, 'Включить экспорт в Яндекс.Маркет', $data['yml']), 1, 'Компания &#8470;1');
        $Tab3 .= $PHPShopGUI->setField('Цена ' . $options['model'], $PHPShopGUI->setInputText(null, 'price_yandex_new', $data['price_yandex'], 150, $valuta_def_name), 2);
    }

    if (!empty($options['model_2'])) {
        $Tab3 .= $PHPShopGUI->setField($options['model_2'], $PHPShopGUI->setCheckbox('yml_2_new', 1, 'Включить экспорт в Яндекс.Маркет', $data['yml_2']), 1, 'Компания &#8470;2');
        $Tab3 .= $PHPShopGUI->setField('Цена ' . $options['model_2'], $PHPShopGUI->setInputText(null, 'price_yandex_2_new', $data['price_yandex_2'], 150, $valuta_def_name), 2);
    }

    if (!empty($options['model_3'])) {
        $Tab3 .= $PHPShopGUI->setField($options['model_3'], $PHPShopGUI->setCheckbox('yml_3_new', 1, 'Включить экспорт в Яндекс.Маркет', $data['yml_3']), 1, 'Компания &#8470;3');
        $Tab3 .= $PHPShopGUI->setField('Цена ' . $options['model_3'], $PHPShopGUI->setInputText(null, 'price_yandex_3_new', $data['price_yandex_3'], 150, $valuta_def_name), 2);
    }


    $Tab3 .= $PHPShopGUI->setField('Гарантия', $PHPShopGUI->setRadio('manufacturer_warranty_new', 1, 'Включить', $data['manufacturer_warranty']) . $PHPShopGUI->setRadio('manufacturer_warranty_new', 2, 'Выключить', $data['manufacturer_warranty'], false, 'text-muted'), 1, 'Тег manufacturer_warranty');

    $Tab3 .= $PHPShopGUI->setField("Имя производителя", $PHPShopGUI->setInputText(null, 'vendor_name_new', $data['vendor_name'], 300), 1, 'Тег vendor');

    $Tab3 .= $PHPShopGUI->setField("Код производителя", $PHPShopGUI->setInputText(null, 'vendor_code_new', $data['vendor_code'], 300), 1, 'Тег vendorCode');

    $Tab3 .= $PHPShopGUI->setField("Компания производитель, адрес и рег. номер", $PHPShopGUI->setInputText(null, 'manufacturer_new', $data['manufacturer'], 300), 1, 'Тег manufacturer');

    $Tab3 .= $PHPShopGUI->setField("Штрихкод", $PHPShopGUI->setInputText(null, 'barcode_new', $data['barcode'], 300), 1, 'Тег barcode');

    $Tab3 .= $PHPShopGUI->setField("Страна производства", $PHPShopGUI->setInputText(null, 'country_of_origin_new', $data['country_of_origin'], 300), 1, 'Тег country_of_origin');

    $Tab3 .= $PHPShopGUI->setField("Идентификатор товара на Яндексе", $PHPShopGUI->setInputText(null, 'market_sku_new', $data['market_sku'], 300), 1, 'Тег market-sku для модели FBS, можно получить в личном кабинете Яндекс.Маркета');

    $Tab3 .= $PHPShopGUI->setField('Товар для взрослых', $PHPShopGUI->setRadio('adult_new', 1, 'Включить', $data['adult']) . $PHPShopGUI->setRadio('adult_new', 2, 'Выключить', $data['adult'], false, 'text-muted'), 1, 'Тег adult');

    $condition[] = array(__('Новый товар'), 1, $data['yandex_condition']);
    $condition[] = array(__('Бывший в употреблении'), 2, $data['yandex_condition']);
    $condition[] = array(__('Витринный образец'), 3, $data['yandex_condition']);
    $condition[] = array(__('Уцененный товар'), 4, $data['yandex_condition']);

    $quality[] = array(__('Новый товар'), 1, $data['yandex_quality']);
    $quality[] = array(__('Как новый, товар в идеальном состоянии'), 2, $data['yandex_quality']);
    $quality[] = array(__('Отличный, следы использования или дефекты едва заметные'), 3, $data['yandex_quality']);
    $quality[] = array(__('Хороший, есть заметные следы использования или дефекты'), 4, $data['yandex_quality']);

    $Tab3 .= $PHPShopGUI->setField('Состояние товара', $PHPShopGUI->setSelect('yandex_condition_new', $condition, 300), 1, 'Тег condition');
    $Tab3 .= $PHPShopGUI->setField('Внешний вид товара', $PHPShopGUI->setSelect('yandex_quality_new', $quality, 300), 1, 'Тег quality');
    $Tab3 .= $PHPShopGUI->setField('Причина уценки', $PHPShopGUI->setTextarea('yandex_condition_reason_new', $data['yandex_condition_reason'], true, 300), 1, 'Тег reason');

    $service_life_days[] = array(__('Ничего не выбрано'), '', $data['yandex_service_life_days']);
    $service_life_days[] = array(__('6 месяцев'), 'P6M', $data['yandex_service_life_days']);
    $service_life_days[] = array(__('1 год'), 'P1Y', $data['yandex_service_life_days']);
    $service_life_days[] = array(__('2 года'), 'P2Y', $data['yandex_service_life_days']);
    $service_life_days[] = array(__('3 года'), 'P3Y', $data['yandex_service_life_days']);

    $Tab3 .= $PHPShopGUI->setField('Срок годности', $PHPShopGUI->setSelect('yandex_service_life_days_new', $service_life_days, 300), 1, 'Тег period-of-validity-days');



    $Tab3 .= $PHPShopGUI->setField('Курьерская доставка', $PHPShopGUI->setRadio('delivery_new', 1, 'Включить', $data['delivery']) . $PHPShopGUI->setRadio('delivery_new', 2, 'Выключить', $data['delivery'], false, 'text-muted'), 1, 'Тег delivery');

    $Tab3 .= $PHPShopGUI->setField('Самовывоз', $PHPShopGUI->setRadio('pickup_new', 1, 'Включить', $data['pickup']) . $PHPShopGUI->setRadio('pickup_new', 2, 'Выключить', $data['pickup'], false, 'text-muted'), 1, 'Тег pickup');

    $Tab3 .= $PHPShopGUI->setField("Минимальное количество", $PHPShopGUI->setInputText(null, 'yandex_min_quantity_new', $data['yandex_min_quantity'], 100), 1, ' Минимальное количество товара в одном заказе');

    $Tab3 .= $PHPShopGUI->setField("Минимальный шаг", $PHPShopGUI->setInputText(null, 'yandex_step_quantity_new', $data['yandex_step_quantity'], 100), 1, ' Количество товара, добавляемое к минимальному');

    $Tab3 .= $PHPShopGUI->setField("Ссылка на товар в Яндекс.Маркете", $PHPShopGUI->setInputText(null, 'yandex_link_new', $data['yandex_link'], '100%'));


    $PHPShopGUI->addTabSeparate(array("Яндекс.Маркет", $PHPShopGUI->setPanel(null, $Tab3, 'panel'), true));
}

$addHandler = array(
    'actionStart' => 'addYandexcart'
);
