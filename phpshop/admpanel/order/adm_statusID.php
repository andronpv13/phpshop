<?php

$TitlePage = __('Редактирование Статуса') . ' #' . $_GET['id'];
$PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['order_status']);

// Стартовый вид
function actionStart() {
    global $PHPShopGUI, $PHPShopOrm, $PHPShopModules, $PHPShopSystem;

    // Выборка
    $data = $PHPShopOrm->select(array('*'), array('id' => '=' . intval($_GET['id'])));


    // Нет данных
    if (!is_array($data)) {
        header('Location: ?path=' . $_GET['path']);
    }

    // bootstrap-colorpicker
    $PHPShopGUI->addCSSFiles('./css/bootstrap-colorpicker.min.css');
    $PHPShopGUI->addJSFiles('./js/bootstrap-colorpicker.min.js');
    $PHPShopGUI->field_col = 3;
    $PHPShopGUI->setActionPanel(__("Редактирование Статуса") . ": " . $data['name'], array('Удалить'), array('Сохранить', 'Сохранить и закрыть'));

    // Содержание закладки 1
    $Tab1 = $PHPShopGUI->setField("Название", $PHPShopGUI->setInput("text", "name_new", $data['name']));
    $Tab1 .= $PHPShopGUI->setField('Цвет', $PHPShopGUI->setInputColor('color_new', $data['color']));
    $Tab1 .= $PHPShopGUI->setField("Приоритет", $PHPShopGUI->setInputText(null, "num_new", $data['num'], '100'));

    $Tab1 .= $PHPShopGUI->setField("Дополнительно", $PHPShopGUI->setCheckbox('mail_action_new', 1, 'Email уведомление', $data['mail_action']) . '<br>' .
            $PHPShopGUI->setCheckbox('sms_action_new', 1, 'SMS уведомление', $data['sms_action']) . '<br>' .
            $PHPShopGUI->setCheckbox('bot_action_new', 1, 'Уведомление в мессенджеры', $data['bot_action']) . '<br>' .
            $PHPShopGUI->setCheckbox("sklad_action_new", 1, "Списание со склада товаров в заказе", $data['sklad_action']) . '<br>' .
            $PHPShopGUI->setCheckbox("cumulative_action_new", 1, "Учет скидки покупателя", $data['cumulative_action']) . $PHPShopGUI->setHelp(__('Сумма заказа пользователя будет засчитана в накопительную сумму, указанную в') . ' <a href="?path=shopusers.status"><span class="glyphicon glyphicon-share-alt"></span>' . __('Статусах и скидках покупателей') . '</a>', false, false)
    );

    $Tab1 = $PHPShopGUI->setCollapse('Информация', $Tab1);
    
    // Переменные
    $message_var='Переменные: <code>@ouid@</code> - номер заказа, <code>@date@</code> - дата заказа, <code>@status@</code> - новый статус заказа, <code>@fio@</code> - имя покупателя, <code>@sum@</code> - стоимость заказа, <code>@manager@</code> - примечание, <code>@tracking@</code> - номер для отслеживания, <code>@account@</code> - ссылка на счет, <code>@bonus@</code> - начисленные бонусы за заказ, <code>@pay@</code> - ссылка на оплату, <code>@order@</code> - ссылка на бланк заказа, <code>@receipt@</code> - ссылка на товарный чек, <code>@invoice@</code> - ссылка на счет-фактуру, <code>@torg@</code> - ссылка на тор-12, <code>@warranty@</code> - ссылка на гарантию, <code>@act@</code> - ссылка на акт';

    // Текст уведомления в мессенджеры
    $Tab1 .= $PHPShopGUI->setCollapse("Текст уведомления в мессенджеры и sms", $PHPShopGUI->setTextarea('bot_message_new', $data['bot_message'], true, false, 150) . $PHPShopGUI->setHelp($message_var));

    // Сообщение
    $PHPShopGUI->setEditor($PHPShopSystem->getSerilizeParam("admoption.editor"));
    $oFCKeditor = new Editor('mail_message_new');
    $oFCKeditor->Height = '380';
    $oFCKeditor->Value = $data['mail_message'];

    $Tab1 .= $PHPShopGUI->setCollapse("Текст письма", $oFCKeditor->AddGUI() . $PHPShopGUI->setHelp($message_var));

    // Внешний код
    $Tab1 .= $PHPShopGUI->setCollapse('Интеграция', $PHPShopGUI->setField('Внешний код', $PHPShopGUI->setInputText(null, 'external_code_new', $data['external_code'], '100%')));

    // Запрос модуля на закладку
    $PHPShopModules->setAdmHandler(__FILE__, __FUNCTION__, $data);

    // Вывод формы закладки
    $PHPShopGUI->setTab(array("Основное", $Tab1, true, false, true));


    // Вывод кнопок сохранить и выход в футер
    $ContentFooter = $PHPShopGUI->setInput("hidden", "rowID", $data['id'], "right", 70, "", "but") .
            $PHPShopGUI->setInput("button", "delID", "Удалить", "right", 70, "", "but", "actionDelete.order.edit") .
            $PHPShopGUI->setInput("submit", "editID", "Сохранить", "right", 70, "", "but", "actionUpdate.order.edit") .
            $PHPShopGUI->setInput("submit", "saveID", "Применить", "right", 80, "", "but", "actionSave.order.edit");

    // Футер
    $PHPShopGUI->setFooter($ContentFooter);
    return true;
}

// Функция удаления
function actionDelete() {
    global $PHPShopOrm, $PHPShopModules;

    // Перехват модуля
    $PHPShopModules->setAdmHandler(__FILE__, __FUNCTION__, $_POST);

    $action = $PHPShopOrm->delete(array('id' => '=' . $_POST['rowID']));
    return array('success' => $action);
}

/**
 * Экшен сохранения
 */
function actionSave() {

    // Сохранение данных
    actionUpdate();

    if (!empty($_GET['return']))
        header('Location: ?path=' . $_GET['return']);
    else
        header('Location: ?path=' . $_GET['path']);
}

// Функция обновления
function actionUpdate() {
    global $PHPShopOrm, $PHPShopModules;

    // Корректировка пустых значений
    $PHPShopOrm->updateZeroVars('sklad_action_new', 'cumulative_action_new', 'mail_action_new', 'sms_action_new', 'bot_action_new');

    // Перехват модуля
    $PHPShopModules->setAdmHandler(__FILE__, __FUNCTION__, $_POST);

    $action = $PHPShopOrm->update($_POST, array('id' => '=' . $_POST['rowID']));
    return array('success' => $action);
}

// Обработка событий
$PHPShopGUI->getAction();

// Вывод формы при старте
$PHPShopGUI->setAction($_GET['id'], 'actionStart', 'none');
?>
