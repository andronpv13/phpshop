<?php

// SQL
$PHPShopOrm = new PHPShopOrm($PHPShopModules->getParam("base.returncall.returncall_jurnal"));
$TitlePage = __('Редактирование заявки ') . ' #' . $_GET['id'];
PHPShopObj::loadClass("array");
PHPShopObj::loadClass("order");

// Настройки
$PHPShopOrmOption = new PHPShopOrm($PHPShopModules->getParam("base.returncall.returncall_system"));
$option = $PHPShopOrmOption->getOne(array('status'));

/**
 * Генерация номера заказа
 */
function setNum() {
    global $PHPShopBase;

    // Кол-во знаков в постфиксе заказа №_XX, по умолчанию 2
    $format = $PHPShopBase->getParam('my.order_prefix_format');
    if (empty($format))
        $format = 2;

    $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['orders']);
    $row = $PHPShopOrm->select(array('uid'), false, array('order' => 'id desc'), array('limit' => 1));
    $last = $row['uid'];
    $all_num = explode("-", $last);
    $ferst_num = $all_num[0];
    $order_num = $ferst_num + 1;
    $order_num = $order_num . "-" . substr(abs(crc32(uniqid(session_id()))), 0, $format);
    return $order_num;
}

// Начальная функция загрузки
function actionStart() {
    global $PHPShopGUI, $PHPShopOrm, $option, $select_name;

    $PHPShopGUI->field_col = 2;

    $PHPShopGUI->addJSFiles('./js/bootstrap-datetimepicker.min.js', './news/gui/news.gui.js');
    $PHPShopGUI->addCSSFiles('./css/bootstrap-datetimepicker.min.css');

    // Выборка
    $data = $PHPShopOrm->select(array('*'), array('id' => '=' . intval($_GET['id'])));

    // Статусы заказов
    $PHPShopOrderStatusArray = new PHPShopOrderStatusArray();
    $OrderStatusArray = $PHPShopOrderStatusArray->getArray();
    $order_status_value[] = array(__('Новый'), 0, $data['status'], 'data-content="<span class=\'glyphicon glyphicon-text-background\' style=\'color:#35A6E8\'></span> ' . __('Новый') . '"');
    if (is_array($OrderStatusArray))
        foreach ($OrderStatusArray as $order_status) {
            $order_status_value[] = array($order_status['name'], $order_status['id'], $data['status'], 'data-content="<span class=\'glyphicon glyphicon-text-background\' style=\'color:' . $order_status['color'] . '\'></span> ' . $order_status['name'] . '"');
        }

    $PHPShopGUI->setActionPanel(__("Заявка") . ' &#8470;' . $data['id'], $select_name, array('Сохранить и закрыть'), false);

    $Tab1 = $PHPShopGUI->setField("Дата", $PHPShopGUI->setInputDate("date_new", PHPShopDate::dataV($data['date'], false)));
    $Tab1 .= $PHPShopGUI->setField('Имя: ', $PHPShopGUI->setInputText(false, 'name_new', $data['name'], 600), false, 'IP: ' . $data['ip']);
    $Tab1 .= $PHPShopGUI->setField('Телефон:', $PHPShopGUI->setInputText(false, 'tel_new', $data['tel'], 300));
    $Tab1 .= $PHPShopGUI->setField('Время звонка:', $PHPShopGUI->setInputText(null, 'time_start_new', $data['time_start'] . ' ' . $data['time_end'], 300));
    $Tab1 .= $PHPShopGUI->setField('Сообщение', $PHPShopGUI->setTextarea('message_new', $data['message'], false, 600));


    if (!empty($option['status']))
        $help = $PHPShopGUI->setHelp('Статус "' . $OrderStatusArray[$option['status']]['name'] . '" создает пустой заказ с данными клиента');

    $Tab1 .= $PHPShopGUI->setField('Статус', $PHPShopGUI->setSelect('status_new', $order_status_value, 300) . $help);
    $Tab1 .= $PHPShopGUI->setInput("hidden", "status", $data['status']);


    // Вывод формы закладки
    $PHPShopGUI->setTab(array("Основное", $Tab1, true));

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
    global $PHPShopModules, $option;

    if (!empty($_POST['date_new']) and empty($_POST['ajax']))
        $_POST['date_new'] = PHPShopDate::GetUnixTime($_POST['date_new']);

    $PHPShopOrm = new PHPShopOrm($PHPShopModules->getParam("base.returncall.returncall_jurnal"));
    $action = $PHPShopOrm->update($_POST, array('id' => '=' . $_POST['rowID']));

    // Новый заказ
    if (!empty($_POST['status_new']) and $_POST['status_new'] == $option['status'] and $_POST['status'] != $option) {

        // Выборка
        $data_call = $PHPShopOrm->select(array('*'), array('id' => '=' . intval($_POST['rowID'])));

        // Запись пустого заказа для получения идентификатора заказа
        $PHPShopOrmOrder = new PHPShopOrm($GLOBALS['SysValue']['base']['orders']);
        $data['fio_new'] = $data_call['name'];
        $data['tel_new'] = $data_call['tel'];
        $data['datas_new'] = time();
        $data['uid_new'] = setNum();
        $data['dop_info_new'] = 'Заявка с сайта №' . $data_call['id'] . ' ' . $data_call['message_new'] . ' ' . $data_call['time_start_new'];
        $id = $PHPShopOrmOrder->insert($data);

        // Удаление звонка
        $PHPShopOrm->delete(array('id' => '=' . $data_call['id']));

        if (!empty($_POST['ajax']))
            return array('success' => $action);
        else
            header('Location: ?path=order&id=' . $id);
    } elseif (!empty($_POST['ajax']))
        return array('success' => $action);
    elseif (!empty($_GET['return']))
        header('Location: ?path=' . $_GET['return']);
    else
        header('Location: ?path=' . $_GET['path']);
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