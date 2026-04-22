<?php

function addModStatusHistoryNew($id) {
    // Создание заказа (копирование, либо новый заказ пользователя)
    require_once(dirname(__FILE__) . '/../class/statushistory.class.php');
    $PHPShopStatusHistory = new PHPShopStatusHistory();
    $PHPShopStatusHistory->add($id, 0, true);
}

$addHandler=array(
        'actionStart'=>'addModStatusHistoryNew',
        'actionDelete'=>false,
        'actionUpdate'=>false,
);

?>