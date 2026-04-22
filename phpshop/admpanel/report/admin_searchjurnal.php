<?php

$TitlePage = __("Журнал поиска");

function actionStart() {
    global $PHPShopInterface,$TitlePage;

    $PHPShopInterface->action_select['Добавить в базу'] = array(
        'name' => 'Добавить в переадресацию',
        'action' => 'add-search-base',
        'class' => 'disabled'
    );

    $PHPShopInterface->action_button['Переадресация'] = array(
        'name' => __('Переадресация'),
        'action' => 'report.searchreplace',
        'class' => 'btn btn-default btn-sm navbar-btn btn-action-panel',
        'type' => 'button',
        'icon' => 'glyphicon glyphicon-screenshot'
    );
    
    $PHPShopInterface->action_title['add-search-base'] = 'Переадресация';

    $PHPShopInterface->addJSFiles('./report/gui/report.gui.js');

    $PHPShopInterface->setActionPanel($TitlePage, array('Удалить выбранные', '|', 'Добавить в базу'), array('Переадресация'),false);
    $PHPShopInterface->setCaption(array(null, "2%"), array("Запрос", "60%"), array("Дата", "10%"), array("IP-адрес", "10%"), array("", "10%"), array("Найдено", "10%"));

    // Таблица с данными
    $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['search_jurnal']);
    $PHPShopOrm->debug = false;
    $data = $PHPShopOrm->select(array('*'), false, array('order' => 'id DESC'), array('limit' => 1000));
    if (is_array($data))
        foreach ($data as $row) {

            $PHPShopInterface->setRow(
                $row['id'],
                ['name' => substr($row['name'],0,30), 'align' => 'left', 'link' => $GLOBALS['SysValue']['dir']['dir'] . '/search/?words=' . $row['name'] . '&cat=' . $row['cat'] . '&set=' . $row['set'], 'target' => '_blank'],
                PHPShopDate::get($row['datas'], true),
                ['name' => $row['ip'], 'align' => 'center'],
                ['action' => ['delete','|', 'add-search-base', 'id' => $row['id']], 'align' => 'center'],
                ['name' => $row['num'], 'align' => 'center']
            );
        }
    $PHPShopInterface->Compile();
}

?>