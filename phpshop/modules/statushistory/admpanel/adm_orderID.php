<?php

// Подключаем класс
require_once(dirname(__FILE__) . '/../class/statushistory.class.php');

function addModStatusHistory($data) {
    global $PHPShopGUI;
    
    // Вывод вкладки истории
    $PHPShopStatusHistory = new PHPShopStatusHistory();
    $Tab = $PHPShopStatusHistory->table($data['id']);
    if(!empty($Tab))
    $PHPShopGUI->addTab(array("История статусов", $Tab));
}

function updateModStatusHistory($post) {
    global $PHPShopOrm;
    
    $PHPShopOrm->debug = false;
    $dataorder = $PHPShopOrm->select(array('*'), array('id' => '=' . intval($_POST['rowID'])));
    
    //Если изменен статус
    if ($dataorder['statusi'] != $_POST['statusi_new']) {
        $PHPShopStatusHistory = new PHPShopStatusHistory();
        $PHPShopStatusHistory->add($_POST['rowID'], $_POST['statusi_new'], true);
    }
}

function deleteModStatusHistory($post) {
    // Удаление истории изменения статусов заказа
    $PHPShopStatusHistory = new PHPShopStatusHistory();
    $PHPShopStatusHistory->delete($post['rowID']);    
}

$addHandler=array(
    'actionStart' => 'addModStatusHistory',
    'actionDelete' => 'deleteModStatusHistory',
    'actionUpdate' => 'updateModStatusHistory'
);

?>