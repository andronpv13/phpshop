<?php

$TitlePage = __("Переадресация поиска");

function actionStart() {
    global $PHPShopInterface,$TitlePage;

    $PHPShopInterface->action_button['Журнал'] = array(
        'name' => __('Журнал'),
        'action' => 'report.searchjurnal',
        'class' => 'btn btn-default btn-sm navbar-btn btn-action-panel',
        'type' => 'button',
        'icon' => 'glyphicon glyphicon-search'
    );

    $PHPShopInterface->action_button['Добавить Переадресацию'] = array(
        'name' => '',
        'action' => 'addNew',
        'class' => 'btn btn-default btn-sm navbar-btn',
        'type' => 'button',
        'icon' => 'glyphicon glyphicon-plus',
        'tooltip' => 'data-toggle="tooltip" data-placement="left" title="'.__('Добавить Переадресацию').'"'
    );

    $PHPShopInterface->setActionPanel($TitlePage, array('Удалить выбранные'), array('Добавить Переадресацию', 'Журнал'));
    $PHPShopInterface->setCaption(array(null, "2%"), array("Запрос", "30%"), array("Товары", "40%"), array("Каталог", "15%"), array("", "10%"), array("Статус", "10%"));


    // Таблица с данными
    $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['search_base']);
    $PHPShopOrm->debug = false;
    $data = $PHPShopOrm->select(array('*'), false, array('order' => 'id DESC'), array('limit' => 1000));
    if (is_array($data))
        foreach ($data as $row) {
        
            if(empty($row['category'])) $row['category']=null;
            else $row['category']=array('name' =>  $row['category'], 'align' => 'left', 'link' => '?path=catalog&cat=' . $row['category']);

            $row['name'] = str_replace('ii', ',', $row['name']);
            $PHPShopInterface->setRow($row['id'], array('name' => str_replace('i', '', $row['name']), 'align' => 'left', 'link' => '?path=' . $_GET['path'] . '&id=' . $row['id']), $row['uid'],$row['category'], array('action' => array('edit', '|','delete', 'id' => $row['id']), 'align' => 'center'), array('status' => array('enable' => $row['enabled'], 'align' => 'right', 'caption' => array('Выкл', 'Вкл'))));
        }
    $PHPShopInterface->Compile();
}

?>