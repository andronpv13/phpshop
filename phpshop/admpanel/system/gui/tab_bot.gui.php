<?php

function tab_bot() {

    $PHPShopInterface = new PHPShopInterface();
    $PHPShopInterface->checkbox_action = false;
    $PHPShopInterface->setCaption(array("Название", "30%"), array("Дата добавления", "15%"), array("Статус", "25%", array('align' => 'right')));

    $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['bot']);
    $PHPShopOrm->debug = false;
    $data = $PHPShopOrm->select(array('*'), false, array('order' => 'date desc'), array('limit' => 20));
    if (is_array($data)){
        foreach ($data as $row) {
            
            $date = PHPShopDate::get($row['date'], true);

            if (empty($row['enabled'])){
                $status = "<span class='text-danger'>" . __('Заблокирован') . "</span>";
                $date = "<span class='text-danger'>" . $date . "</span>";
            }
            else {
                $status = __("Разрешен");
            }

            $PHPShopInterface->setRow(array('name' => $row['name'], 'link' => '?path=system.bot&id=' . $row['id']), array('name' => $date), array('name' => $status, 'align' => 'right'));
        }

    return '<table class="table table-hover">' . $PHPShopInterface->_CODE . '</table><a class="btn btn-default btn-sm pull-right" href="?path=system.bot">'.__('Показать все записи').'</a>';
    }
    else  return '<a class="btn btn-default btn-sm" href="?path=system.bot">'.__('Показать все записи').'</a>';
    
    
}

?>