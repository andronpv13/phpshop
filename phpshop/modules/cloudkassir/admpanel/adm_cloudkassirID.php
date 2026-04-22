<?php

// SQL
$PHPShopOrm = new PHPShopOrm($PHPShopModules->getParam("base.cloudkassir.cloudkassir_log"));

// Начальная функция загрузки
function actionStart() {
    global $PHPShopGUI, $PHPShopOrm;

    // Выборка
    $data = $PHPShopOrm->select(array('*'), array('id' => '=' . intval($_GET['id'])));

    if ($data['status'] == 1) {
        $status = '<span class=\'glyphicon glyphicon-ok\'></span>';
    } else {
        $status = '<span class=\'glyphicon glyphicon-remove\'></span>';
    }

    if ($data['operation'] == 'sell') {
        $operation = ' Продажа';
    } else {
        $operation = ' Возврат';
    }

    if (empty($data['fiscal']))
        $data['fiscal'] = $data['id'].' / Ошибка';

    // Панель заголовка
    $PHPShopGUI->setActionPanel('Заказ №' . $data['order_id'] . '/ ' . PHPShopDate::get($data['date'], true) . ' / Чек №' . $data['fiscal'], null, array('Закрыть'));

    // Переводим в читаемый вид
    ob_start();
    print_r(unserialize($data['message']));
    $log = ob_get_clean();

    $Tab1 = $PHPShopGUI->setTextarea(null, PHPShopString::utf8_win1251($log), "none", false, '500');

    // Вывод формы закладки
    $PHPShopGUI->setTab(array($status . $operation . ' №' . $data['fiscal'], $Tab1));


    return true;
}

// Обработка событий
$PHPShopGUI->getAction();

// Вывод формы при старте
$PHPShopGUI->setAction($_GET['id'], 'actionStart', 'none');
?>


