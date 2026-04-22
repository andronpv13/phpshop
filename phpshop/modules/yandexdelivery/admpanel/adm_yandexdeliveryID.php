<?php

// Начальная функция загрузки
function actionStart() {
    global $PHPShopGUI,$PHPShopModules;

    // SQL
    $PHPShopOrm = new PHPShopOrm($PHPShopModules->getParam("base.yandexdelivery.yandexdelivery_log"));

    // Выборка
    $data = $PHPShopOrm->getOne(['*'], ['id' => '=' . intval($_GET['id'])]);
    $PHPShopGUI->setActionPanel('Журнал от ' . PHPShopDate::get($data['date']), false, ['Закрыть']);

    // Переводим в читаемый вид
    ob_start();
    print_r(unserialize($data['message']));
    $log = ob_get_clean();

    $Tab1 = $PHPShopGUI->setTextarea(null, PHPShopString::utf8_win1251($log), $float = "none", $width = '99%', $height = '550');

    // Вывод формы закладки
    $PHPShopGUI->setTab(["Детальная информация", $Tab1]);

    $PHPShopGUI->setFooter($ContentFooter);
    return true;
}

// Обработка событий
$PHPShopGUI->getAction();

// Вывод формы при старте
$PHPShopGUI->setAction($_GET['id'], 'actionStart', 'none');
?>


