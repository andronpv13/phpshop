<?php
PHPShopObj::loadClass("array");
PHPShopObj::loadClass("order");
// SQL
$PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['notes']);
$TitlePage = __('Создание события');

// Начальная функция загрузки
function actionStart() {
    global $PHPShopGUI, $TitlePage;

    $PHPShopGUI->field_col = 2;

    $PHPShopGUI->addJSFiles('./js/bootstrap-datetimepicker.min.js', './news/gui/news.gui.js');
    $PHPShopGUI->addCSSFiles('./css/bootstrap-datetimepicker.min.css');

    $data['message']=__('Событие');

    // Статусы заказов
    $PHPShopOrderStatusArray = new PHPShopOrderStatusArray();
    $OrderStatusArray = $PHPShopOrderStatusArray->getArray();
    $order_status_value[] = array(__('Новый'), 0, $data['status'], 'data-content="<span class=\'glyphicon glyphicon-text-background\' style=\'color:#35A6E8\'></span> ' . __('Новый') . '"');
    if (is_array($OrderStatusArray))
        foreach ($OrderStatusArray as $order_status) {
            $order_status_value[] = array($order_status['name'], $order_status['id'], $data['status'], 'data-content="<span class=\'glyphicon glyphicon-text-background\' style=\'color:' . $order_status['color'] . '\'></span> ' . $order_status['name'] . '"');
        }

    $PHPShopGUI->setActionPanel($TitlePage, false, array('Сохранить и закрыть'));

    $Tab1 = $PHPShopGUI->setField("Дата", $PHPShopGUI->setInputDate("date_new", PHPShopDate::dataV($data['date'], false)));
    $Tab1 .= $PHPShopGUI->setField('Сообщение', $PHPShopGUI->setTextarea('message_new', $data['message'], false, 600));
    $Tab1 .= $PHPShopGUI->setField('Статус', $PHPShopGUI->setSelect('status_new', $order_status_value, 300) . $help);


    // Вывод формы закладки
    $PHPShopGUI->setTab(array("Основное", $Tab1, true));

     // Вывод кнопок сохранить и выход в футер
    $ContentFooter = $PHPShopGUI->setInput("submit", "saveID", "ОК", "right", 70, "", "but", "actionInsert.order.create");

    $PHPShopGUI->setFooter($ContentFooter);
    return true;
}

// Функция записи
function actionInsert() {
    global $PHPShopOrm, $PHPShopModules;
    
    $_POST['date_new']=time();

    // Перехват модуля
    $PHPShopModules->setAdmHandler(__FILE__, __FUNCTION__, $_POST);
    $action = $PHPShopOrm->insert($_POST);

    if(empty($_POST['ajax']))
      header('Location: ?path=' . $_GET['return']);
    else return array('success' =>true,'id'=>$action,'date'=>PHPShopDate::dataV($_POST['date_new'],true));
}

// Обработка событий
$PHPShopGUI->getAction();

// Вывод формы при старте
$PHPShopGUI->setLoader($_POST['editID'], 'actionStart');
?>