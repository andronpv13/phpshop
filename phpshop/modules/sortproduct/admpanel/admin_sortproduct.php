<?php

function actionStart() {
    global $PHPShopInterface, $PHPShopModules;

    $PHPShopInterface->setCaption(array(null, "5%"), array("Название", "70%"), array("Ссылок", "10%"), array("", "10%"), array("Статус &nbsp;&nbsp;&nbsp;", "10%", array('align' => 'right')));

    // SQL
    $PHPShopOrm = new PHPShopOrm();
    $result = $PHPShopOrm->query('SELECT a.*, b.name FROM ' . $PHPShopModules->getParam("base.sortproduct.sortproduct_forms") . ' AS a JOIN ' . $GLOBALS['SysValue']['base']['sort_categories'] . ' AS b ON a.sort = b.id order by a.id DESC limit 300');

    while ($row = mysqli_fetch_array($result)) {

        $PHPShopInterface->setRow($row['id'], array('name' => $row['name'], 'link' => '?path=modules.dir.sortproduct&id=' . $row['id'], 'align' => 'left'), $row['items'], array('action' => array('edit', 'delete', 'id' => $row['id']), 'align' => 'center'), array('status' => array('enable' => $row['enabled'], 'align' => 'right', 'caption' => array('Выкл', 'Вкл'))));
    }

    $PHPShopInterface->Compile();
}

?>