<?php
function addWbsellerProductTab($data) {
    global $PHPShopGUI;
    
    // Очистка при копировании товара
    $data['price_wb']=$data['export_wb_id']=$data['export_wb_id']=$data['barcode_wb']=0;
    
    // Размер названия поля
    $PHPShopGUI->field_col = 4;

    $tab = $PHPShopGUI->setField(null, $PHPShopGUI->setCheckbox('export_wb_new', 1, 'Включить экспорт в WB', $data['export_wb']));
    if (!empty($data['export_wb_task_status']))
        $tab .= $PHPShopGUI->setField('Статус товара', $PHPShopGUI->setText('<span class="text-success">Загружен ' . PHPShopDate::get($data['export_wb_task_status'], true) . '</span>'));

    // Валюты
    $PHPShopValutaArray = new PHPShopValutaArray();
    $valuta_array = $PHPShopValutaArray->getArray();
    if (is_array($valuta_array))
        foreach ($valuta_array as $val) {
            if ($data['baseinputvaluta'] == $val['id']) {
                $valuta_def_name = $val['code'];
            }
        }

    $tab .= $PHPShopGUI->setField('Цена WB', $PHPShopGUI->setInputText(null, 'price_wb_new', $data['price_wb'], 150, $valuta_def_name), 2);
    $tab .= $PHPShopGUI->setField('Артикул WB', $PHPShopGUI->setInputText(null, 'export_wb_id_new', $data['export_wb_id'], 150, $PHPShopGUI->setLink('https://www.wildberries.ru/catalog/' . $data['export_wb_id'] . '/detail.aspx', '<span class=\'glyphicon glyphicon-eye-open\'></span>', '_blank', false, __('Перейте на сайт WB'))));
    $tab .= $PHPShopGUI->setField("Баркод WB", $PHPShopGUI->setInputText(null, 'barcode_wb_new', $data['barcode_wb'], 150));

    $PHPShopGUI->addTab(array("WB", $tab, true));
}

$addHandler = array(
    'actionStart' => 'addWbsellerProductTab'
);
?>