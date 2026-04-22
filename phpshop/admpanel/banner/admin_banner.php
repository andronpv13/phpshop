<?php

$TitlePage = __("Баннеры");

function actionStart() {
    global $PHPShopInterface,$TitlePage;

    $PHPShopInterface->setActionPanel($TitlePage, array('Удалить выбранные'), array('Добавить'));

    $PHPShopInterface->setCaption(array(null, "3%"), array("Название", "30%"), array("Таргетинг", "30%", array('align' => 'center')), array("Вывод", "10%"), array("", "10%"), array("Статус", "10%", array('align' => 'right')));

    $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['banner']);
    $data = $PHPShopOrm->select(array('*'), false, array('order' => 'id DESC'), array("limit" => "1000"));
    if (is_array($data))
        foreach ($data as $row) {
        
            switch($row['type']){
                case 0:
                    $type="glyphicon-resize-vertical";
                    break;
                case 1:
                    $type="glyphicon-comment";
                    break;
                case 2:
                    $type="glyphicon-resize-horizontal";
                    break;
                case 3:
                    $type="glyphicon-th-list";
                    break;
            }

            $PHPShopInterface->setRow($row['id'], array('name' => $row['name'], 'link' => '?path=banner&id=' . $row['id'], 'align' => 'left'), $row['dir'],array('name'=>'<span class="glyphicon '.$type.'"></span>','align' => 'center'),array('action' => array('edit', '|', 'delete', 'id' => $row['id']), 'align' => 'center'), array('status' => array('enable' => $row['flag'], 'align' => 'right', 'caption' => array('Выкл', 'Вкл'))));
        }

    $PHPShopInterface->setAddItem('ajax/banner/adm_banner_new.php');
    $PHPShopInterface->title = $TitlePage;
    $PHPShopInterface->Compile();
}

?>
