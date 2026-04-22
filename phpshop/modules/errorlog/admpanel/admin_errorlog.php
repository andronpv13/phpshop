<?php

$TitlePage = __("Журнал событий");

function actionStart() {
    global $PHPShopInterface, $PHPShopModules,$TitlePage, $select_name;

    $PHPShopInterface->checkbox_action = false;
    $PHPShopInterface->setActionPanel($TitlePage, $select_name, false);
    $PHPShopInterface->setCaption(array("Дата", "11%"), array("IP", "11%"), array("Событие", "80%"));
    $PHPShopOrm = new PHPShopOrm($PHPShopModules->getParam("base.errorlog.errorlog_log"));
    $PHPShopOrm->debug = false;
    if (!empty($_GET['sortdate_start']))
        $where = array('date' => ' < ' . (PHPShopDate::GetUnixTime($_GET['sortdate_end']) + 86400) . ' AND date > ' . (PHPShopDate::GetUnixTime($_GET['sortdate_start']) - 86400));
    else
        $where = false;

    $data = $PHPShopOrm->select(array('*'), $where, array('order' => 'id DESC'), array('limit' => 500));

    if (is_array($data))
        foreach ($data as $row) {
            $PHPShopInterface->setRow(PHPShopDate::dataV($row['date']), $row['ip'], htmlspecialchars($row['error']));
        }

    $PHPShopInterface->Compile();
}

?>