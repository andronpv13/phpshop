<?php
PHPShopObj::loadClass("array");
PHPShopObj::loadClass("order");
// SQL
$PHPShopOrm = new PHPShopOrm($PHPShopModules->getParam("base.returncall.returncall_system"));

// Обновление версии модуля
function actionBaseUpdate() {
    global $PHPShopModules, $PHPShopOrm;
    $PHPShopOrm->clean();
    $option = $PHPShopOrm->select();
    $new_version = $PHPShopModules->getUpdate($option['version']);
    $PHPShopOrm->clean();
    $PHPShopOrm->update(array('version_new' => $new_version));
}

// Функция обновления
function actionUpdate() {
    global $PHPShopOrm,$PHPShopModules;

    // Настройки витрины
    $PHPShopModules->updateOption($_GET['id'],$_POST['servers']);
    
    $PHPShopOrm->debug = false;
    $action = $PHPShopOrm->update($_POST);
    header('Location: ?path=modules&id=' . $_GET['id']);
    return $action;
}


function actionStart() {
    global $PHPShopGUI, $PHPShopOrm;

    // Выборка
    $data = $PHPShopOrm->select();

    // Вывод
    $e_value[] = array('кнопка звонок', 0, $data['enabled']);
    $e_value[] = array('слева', 1, $data['enabled']);
    $e_value[] = array('справа', 2, $data['enabled']);

    // Тип вывода
    $w_value[] = array('форма', 0, $data['windows']);
    $w_value[] = array('всплывающее окно', 1, $data['windows']);

    // Captcha
    $c_value[] = array('да', 1, $data['captcha_enabled']);
    $c_value[] = array('нет', 2, $data['captcha_enabled']);


    $Tab1 = $PHPShopGUI->setField('Заголовок', $PHPShopGUI->setInputText(false, 'title_new', $data['title']));
    $Tab1.=$PHPShopGUI->setField('Сообщение', $PHPShopGUI->setTextarea('title_end_new', $data['title_end']));
    $Tab1.=$PHPShopGUI->setField('Место вывода', $PHPShopGUI->setSelect('enabled_new', $e_value, 300,true));
    $Tab1.=$PHPShopGUI->setField('Тип вывода', $PHPShopGUI->setSelect('windows_new', $w_value, 300,true));
    $Tab1.=$PHPShopGUI->setField('Защитная картинка', $PHPShopGUI->setSelect('captcha_enabled_new', $c_value, 300,true));
    
    
        // Статусы заказов
    $PHPShopOrderStatusArray = new PHPShopOrderStatusArray();
    $OrderStatusArray = $PHPShopOrderStatusArray->getArray();
    if (is_array($OrderStatusArray))
        foreach ($OrderStatusArray as $order_status) {
            $order_status_value[] = array($order_status['name'], $order_status['id'], $data['status'], 'data-content="<span class=\'glyphicon glyphicon-text-background\' style=\'color:' . $order_status['color'] . '\'></span> ' . $order_status['name'] . '"');
        }
    
     $Tab1 .= $PHPShopGUI->setField('Статус перевода в заказ', $PHPShopGUI->setSelect('status_new', $order_status_value, 300) . $PHPShopGUI->setHelp('Создается  пустой заказ с данными клиента'));

    // Форма регистрации
    $Tab3 = $PHPShopGUI->setPay($data['serial'], false, $data['version'], true);

    // Вывод формы закладки
    $PHPShopGUI->setTab(array("Основное", $Tab1, true),array("О Модуле", $Tab3), array("Обзор заявок", null, '?path=modules.dir.returncall'));

    // Вывод кнопок сохранить и выход в футер
    $ContentFooter =
            $PHPShopGUI->setInput("hidden", "rowID", $data['id']) .
            $PHPShopGUI->setInput("submit", "saveID", "Применить", "right", 80, "", "but", "actionUpdate.modules.edit");

    $PHPShopGUI->setFooter($ContentFooter);
    return true;
}

// Обработка событий
$PHPShopGUI->getAction();

// Вывод формы при старте
$PHPShopGUI->setLoader($_POST['saveID'], 'actionStart');
?>