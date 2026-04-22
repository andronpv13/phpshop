<?php

$TitlePage = __("Поисковые боты");

function actionStart() {
    global $PHPShopInterface, $TitlePage;

    $PHPShopInterface->setActionPanel($TitlePage, array('Удалить выбранные'), array('Добавить'));

    $PHPShopInterface->setCaption(array(null, "3%"), array("Название", "50%"), array("Дата добавления", "15%"), array("Дата изменения", "15%"), array("", "10%"), array("Доступ на сайт", "15%", array('align' => 'right')));

    $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['bot']);
    $data = $PHPShopOrm->select(array('*'), false, array('order' => 'id DESC'), array("limit" => "1000"));
    if (is_array($data))
        foreach ($data as $row) {

            $date = PHPShopDate::get($row['date'], true);

            if (!empty($row['enabled'])) {
                $date_block = null;
                $date_block = PHPShopDate::get($row['date_block'], true);
            } else {

                if (!empty($row['date_block']))
                    $date_block = "<span class='text-danger'>" . PHPShopDate::get($row['date_block'], true) . "</span>";
                else $date_block = null;

                $date = "<span class='text-danger'>" . $date . "</span>";
            }


            $PHPShopInterface->setRow($row['id'], array('name' => $row['name'], 'link' => '?path=system.bot&id=' . $row['id'], 'align' => 'left'), $date, $date_block, array('action' => array('edit', '|', 'delete', 'id' => $row['id']), 'align' => 'center'), array('status' => array('enable' => $row['enabled'], 'align' => 'right', 'caption' => array('Выкл', 'Вкл'))));
        }

    $PHPShopInterface->title = $TitlePage;
    $PHPShopInterface->Compile();
}

?>