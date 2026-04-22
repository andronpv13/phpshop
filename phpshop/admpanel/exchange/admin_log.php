<?php

$TitlePage = __("История импортов");

function actionStart() {
    global $PHPShopInterface,$TitlePage;

    $PHPShopInterface->setActionPanel($TitlePage, false, false,false);
    $PHPShopInterface->checkbox_action = false;
    $PHPShopInterface->setCaption(array("Дата", "15%"), array("Файл", "30%"), array("Статус", "10%"), array("Отчет", "25%", array('align' => 'right')));
    

    $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['exchanges_log']);
    $PHPShopOrm->debug = false;
    $data = $PHPShopOrm->select(array('*'), false, array('order' => 'date desc'), array('limit' => 1000));
    if (is_array($data))
        foreach ($data as $row) {

            $date = PHPShopDate::get($row['date'], true);

            if (empty($row['status'])){
                $status = "<span class='text-warning'>" . __('Ошибка') . "</span>";
                $text = null;
            }
            else {
                $status = __("Выполнен");
                $info = unserialize($row['info']);
                $text = __('Обработано ') . $info[0] . __(' строк') . '. ' . $info[1] . ' ' . $info[2] . __(' записей');
            }


            $PHPShopInterface->setRow(array('name' => $date, 'link' => '?path=exchange.import&return=exchange.log&id=' . $row['id']), array('name' => pathinfo($row['file'])['basename']), $status, array('name' => $text,'link'=>'?path=catalog&cat=0&import=' . $row['import_id'], 'align' => 'right'));
        }


    $PHPShopInterface->Compile(2);
}
?>