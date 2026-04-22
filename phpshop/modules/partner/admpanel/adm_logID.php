<?php

// SQL
$PHPShopOrm = new PHPShopOrm($PHPShopModules->getParam("base.partner.partner_log"));

// Функция удаления
function actionDelete() {
    global $PHPShopOrm;

    $action = $PHPShopOrm->delete(array('id' => '=' . $_POST['rowID']));
    return array("success" => $action);
}

// Вывод формы при старте
$PHPShopGUI->setAction($_GET['id'], 'actionStart', 'none');

// Обработка событий
$PHPShopGUI->getAction();
?>