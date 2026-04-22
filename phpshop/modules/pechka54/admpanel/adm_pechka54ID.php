<?php

// SQL
$PHPShopOrm = new PHPShopOrm($PHPShopModules->getParam("base.pechka54.pechka54_log"));

// Начальная функция загрузки
function actionStart() {
    global $PHPShopGUI, $PHPShopOrm, $TitlePage, $select_name;

    // Выборка
    $data = $PHPShopOrm->select(array('*'), array('id' => '=' . intval($_GET['id'])));

    if ($data['status'] == 1) {
        $status = '<span class=\'glyphicon glyphicon-ok\'></span> Продажа';
    } else {
        $status = '<span class=\'glyphicon glyphicon-remove\'></span> Возврат';
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
    $PHPShopGUI->setTab(array($status . ' №' . $data['fiscal'], $Tab1));


    return true;
}

// Обработка событий
$PHPShopGUI->getAction();

// Вывод формы при старте
$PHPShopGUI->setAction($_GET['id'], 'actionStart', 'none');
?>


