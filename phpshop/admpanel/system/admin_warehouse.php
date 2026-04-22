<?php

$TitlePage = __("Склады");
$PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['warehouses']);

// Стартовый вид
function actionStart() {
    global $PHPShopInterface, $PHPShopOrm, $TitlePage;

    $PHPShopInterface->setActionPanel($TitlePage, array('Удалить выбранные'), array('Добавить'));
    $data = $PHPShopOrm->select(array('*'), false, array('order' => 'num'), array('limit' => 1000));
    if (is_array($data)) {

        $PHPShopInterface->setCaption(array(null, "3%"), array("Название", "30%"), array("Код", "30%"), array("", "10%"), array("Статус", "10%", array('align' => 'right')));

        foreach ($data as $row) {
            $PHPShopInterface->setRow($row['id'], array('name' => $row['name'], 'link' => '?path=system.warehouse&id=' . $row['id'], 'align' => 'left'), array('name' => $row['uid']), array('action' => array('edit', '|', 'delete', 'id' => $row['id']), 'align' => 'center'), array('status' => array('enable' => $row['enabled'], 'align' => 'right', 'caption' => array('Выкл', 'Вкл'))));
        }
    } else {
        $PHPShopInterface->sort_action = false;
        $PHPShopInterface->_CODE.= $PHPShopInterface->setAlert('Дополнительные склады не заданы, используется общий основной склад для учета остатков товаров. Дополнительные склады заводятся по кнопке <span class="glyphicon glyphicon-plus"></span>. Возможен учет товаров на разных складах при синхронизации с 1С/МойСклад или на дополнительных витринах.', 'info',true);
    }

    $sidebarleft[] = array('title' => 'Категории', 'content' => $PHPShopInterface->loadLib('tab_menu', false, './system/'));
    $PHPShopInterface->setSidebarLeft($sidebarleft, 2);
    $PHPShopInterface->Compile(2);
}

?>