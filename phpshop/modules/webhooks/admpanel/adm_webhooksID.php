<?php

PHPShopObj::loadClass('order');
$PHPShopOrm = new PHPShopOrm($PHPShopModules->getParam("base.webhooks.webhooks_forms"));

// Начальная функция загрузки
function actionStart() {
    global $PHPShopGUI, $PHPShopOrm,$_classPath;

    // Выборка
    $data = $PHPShopOrm->select(array('*'), array('id' => '=' . intval($_GET['id'])));

    // Доступые статусы заказов
    $PHPShopOrderStatusArray = new PHPShopOrderStatusArray();
    $OrderStatusArray = $PHPShopOrderStatusArray->getArray();
    $order_status_value[] = array(__('Новый заказ'), 0, $data['create_order_status']);
    if (is_array($OrderStatusArray))
        foreach ($OrderStatusArray as $order_status)
            $order_status_value[] = array($order_status['name'], $order_status['id'], $data['create_order_status']);


    // Метод
    $send_value[] = array('POST', 0, $data['send']);
    $send_value[] = array('GET', 1, $data['send']);

    // Действие
    include_once($_classPath . 'modules/webhooks/class/webhooks.class.php');
    $PHPShopWebhooks = new PHPShopWebhooks();
    $type_array=$PHPShopWebhooks->getType();
    foreach($type_array as $k=>$type)
         $type_value[]=array($type,$k,$data['type']);


    $Tab1 = $PHPShopGUI->setField('Название', $PHPShopGUI->setInputText('', 'name_new', $data['name'], 400));
    $Tab1 .= $PHPShopGUI->setField('Статус', $PHPShopGUI->setRadio("enabled_new", 1, "Показывать", $data['enabled']) . $PHPShopGUI->setRadio("enabled_new", 0, "Скрыть", $data['enabled']));
    $Tab1 .= $PHPShopGUI->setField("URL WebHook", $PHPShopGUI->setInputText(false, 'url_new', $data['url'], 400));
    //$Tab1 .= $PHPShopGUI->setField('Передача при статусе:', $PHPShopGUI->setSelect('create_order_status_new', $order_status_value, 400));
    $Tab1 .= $PHPShopGUI->setField('Действие', $PHPShopGUI->setSelect('type_new', $type_value, 400));
    $Tab1 .= $PHPShopGUI->setField('Метод передачи', $PHPShopGUI->setSelect('send_new', $send_value, 100));

    // Вывод формы закладки
    $PHPShopGUI->setTab(array("Основное", $Tab1, true),array("Журнал выполнения", null, '?path=modules.dir.webhooks.log&uid='.$data['id']));

    // Вывод кнопок сохранить и выход в футер
    $ContentFooter = $PHPShopGUI->setInput("hidden", "rowID", $data['id'], "right", 70, "", "but") .
            $PHPShopGUI->setInput("button", "delID", "Удалить", "right", 70, "", "but", "actionDelete.modules.edit") .
            $PHPShopGUI->setInput("submit", "editID", "Сохранить", "right", 70, "", "but", "actionUpdate.modules.edit") .
            $PHPShopGUI->setInput("submit", "saveID", "Применить", "right", 80, "", "but", "actionSave.modules.edit");

    $PHPShopGUI->setFooter($ContentFooter);
    return true;
}

/**
 * Экшен сохранения
 */
function actionSave() {

    // Сохранение данных
    actionUpdate();

    header('Location: ?path=' . $_GET['path']);
}

// Функция обновления
function actionUpdate() {
    global $PHPShopOrm;

    if (empty($_POST['enabled_new']))
        $_POST['enabled_new'] = 0;

    $action = $PHPShopOrm->update($_POST, array('id' => '=' . $_POST['rowID']));
    return array('success'=>$action);
}

// Функция удаления
function actionDelete() {
    global $PHPShopOrm;
    $action = $PHPShopOrm->delete(array('id' => '=' . $_POST['rowID']));
    return array("success" => $action);
}

// Обработка событий
$PHPShopGUI->getAction();

// Вывод формы при старте
$PHPShopGUI->setAction($_GET['id'], 'actionStart', 'none');
?>