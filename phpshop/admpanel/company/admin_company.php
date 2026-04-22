<?php

$TitlePage = __("Юридические лица");

function actionStart() {
    global $PHPShopInterface, $TitlePage;


    $PHPShopInterface->setActionPanel($TitlePage, array('Удалить выбранные'), array('Добавить'));
    $PHPShopInterface->setCaption(array(null, "3%"), array("Название", "60%"), array("ИНН", "20%"), array("", "10%"), array("Статус", "10%", array('align' => 'right')));

    $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['company']);
    $data = $PHPShopOrm->select(array('*'), false, array('order' => 'id DESC'), array('limit' => 1000));
    if (is_array($data))
        foreach ($data as $row) {

            $bank = unserialize($row['bank']);


            $PHPShopInterface->setRow($row['id'], array('name' => $row['name'], 'link' => '?path=company&id=' . $row['id'], 'align' => 'left'), $bank['org_inn'], array('action' => array('edit', '|', 'delete', 'id' => $row['id']), 'align' => 'center'), array('status' => array('enable' => $row['enabled'], 'align' => 'right', 'caption' => array('Выкл', 'Вкл'))));
        }

    $sidebarleft[] = array('title' => 'Категории', 'content' => $PHPShopInterface->loadLib('tab_menu', false, './system/'));
    $PHPShopInterface->setSidebarLeft($sidebarleft, 2);
    $PHPShopInterface->Compile(2);
}

?>