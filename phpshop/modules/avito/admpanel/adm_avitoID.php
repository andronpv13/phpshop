<?php

// SQL
$PHPShopOrm = new PHPShopOrm($PHPShopModules->getParam("base.avito.avito_log"));

// Начальная функция загрузки
function actionStart() {
    global $PHPShopGUI, $PHPShopOrm,$TitlePage,$select_name;

    // Выборка
    $data = $PHPShopOrm->select(array('*'), array('id' => '=' . $_GET['id']));
    
    // Панель заголовка
    $PHPShopGUI->setActionPanel($TitlePage. ' &#8470;'.$data['id'], $select_name, array('Закрыть'));

    // Переводим в читаемый вид
    ob_start();
    print_r(unserialize($data['message']));
    $log = ob_get_clean();
    
    

    $Tab1 = $PHPShopGUI->setTextarea(null, PHPShopString::utf8_win1251($log), "none", false, '450');

    // Вывод формы закладки
    $PHPShopGUI->setTab(array($data['path'], $Tab1, 370));


    $PHPShopGUI->setFooter($ContentFooter);
    return true;
}

// Обработка событий
$PHPShopGUI->getAction();

// Вывод формы при старте
$PHPShopGUI->setAction($_GET['id'], 'actionStart', 'none');
?>


