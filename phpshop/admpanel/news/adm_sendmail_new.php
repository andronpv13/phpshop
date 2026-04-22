<?php

$TitlePage = __('Создание Рассылки');

function actionStart() {
    global $PHPShopGUI, $PHPShopSystem, $PHPShopOrm, $PHPShopModules, $result_message, $TitlePage;

    // Выбор даты
    $PHPShopGUI->addJSFiles('./js/bootstrap-datetimepicker.min.js');
    $PHPShopGUI->addCSSFiles('./css/bootstrap-datetimepicker.min.css');

    // Выборка
    $data = array();
    $PHPShopGUI->field_col = 4;
    $data = $PHPShopGUI->valid($data, 'name', 'content', 'servers');

    $PHPShopGUI->action_button['Сохранить и отправить'] = array(
        'name' => 'Сохранить и отправить',
        'action' => 'saveID',
        'class' => 'btn  btn-default btn-sm navbar-btn hidden-xs',
        'type' => 'submit',
        'icon' => 'glyphicon glyphicon-ok'
    );


    $PHPShopGUI->action_select['Разослать'] = array(
        'name' => 'Разослать пользователям',
        'action' => 'send-user'
    );

    // Имя товара
    if (strlen($data['name']) > 50)
        $title_name = substr($data['name'], 0, 70) . '...';
    else
        $title_name = $data['name'];

    $PHPShopGUI->setActionPanel($TitlePage, false, array('Сохранить и закрыть'));

    // Отчет
    if (!empty($result_message))
        $Tab1 = $PHPShopGUI->setField('Отчет', $result_message);

    // Редактор 1
    $PHPShopGUI->setEditor($PHPShopSystem->getSerilizeParam("admoption.editor"));
    $oFCKeditor = new Editor('content_new');
    $oFCKeditor->Height = '500';
    $oFCKeditor->Value = $data['content'];

    // Содержание закладки 1
    $Tab1 = $PHPShopGUI->setField("Тема", $PHPShopGUI->setInput("text.requared", "name_new", $data['name']));


    // Новости
    $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['news']);
    $data_page = $PHPShopOrm->select(array('*'), false, array('order' => 'id desc'), array('limit' => 10));

    $value = array();
    $value[] = array(__('Не использовать'), 0, false);
    if (is_array($data_page))
        foreach ($data_page as $val) {
            $value[] = array($val['zag'] . ' &rarr;  ' . $val['datas'], $val['id'], false);
        }

    $Tab1 .= $PHPShopGUI->setField('Содержание из новости', $PHPShopGUI->setSelect('template', $value, '100%', false, false, false, false, false, false));
    $Tab1 .= $PHPShopGUI->setField("Витрины", $PHPShopGUI->loadLib('tab_multibase', $data, 'catalog/', '100%'));

    $Tab1 = $PHPShopGUI->setCollapse('Информация', $Tab1);

    $Tab3 .= $PHPShopGUI->setTextarea('recipients_new', $data['recipients'], true, false, '200px', 'Укажите e-mail получателей рассылки через запятую или оставьте это поле пустым для рассылки всем пользователям');

    $Tab1 .= $PHPShopGUI->setCollapse('Точечная рассылка', $Tab3);

    // Переменные
    $message_var = 'Переменные: <code>@ouid@</code> - номер заказа, <code>@date@</code> - дата заказа, <code>@status@</code> - новый статус заказа, <code>@fio@</code> - имя покупателя, <code>@sum@</code> - стоимость заказа, <code>@manager@</code> - примечание, <code>@tracking@</code> - номер для отслеживания, <code>@account@</code> - ссылка на счет, <code>@bonus@</code> - начисленные бонусы за заказ, <code>@pay@</code> - ссылка на оплату, <code>@order@</code> - ссылка на бланк заказа, <code>@receipt@</code> - ссылка на товарный чек, <code>@invoice@</code> - ссылка на счет-фактуру, <code>@torg@</code> - ссылка на тор-12, <code>@warranty@</code> - ссылка на гарантию, <code>@act@</code> - ссылка на акт';

    // Текст уведомления в мессенджеры
    $Tab1 .= $PHPShopGUI->setCollapse("Текст уведомления в мессенджеры", $PHPShopGUI->setTextarea('bot_message_new', $data['bot_message'], true, false, 150) . $PHPShopGUI->setHelp($message_var));

    $Tab1 .= $PHPShopGUI->setCollapse("Текст письма", $oFCKeditor->AddGUI() . $PHPShopGUI->setAIHelpButton('content_new', 300, 'news_sendmail') . $PHPShopGUI->setHelp($message_var));

    // Запрос модуля на закладку
    $PHPShopModules->setAdmHandler(__FILE__, __FUNCTION__, $data);

    // Вывод формы закладки
    $PHPShopGUI->setTab(array("Основное", $Tab1, true, false, true));

    // Вывод кнопок сохранить и выход в футер
    $ContentFooter = $PHPShopGUI->setInput("submit", "saveID", "ОК", "right", 70, "", "but", "actionInsert.news.create");

    // Футер
    $PHPShopGUI->setFooter($ContentFooter);

    return true;
}

// Функция обновления
function actionInsert() {
    global $PHPShopOrm, $PHPShopModules;

    // Рассылка новости
    if (!empty($_POST['template'])) {

        $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['news']);
        $data = $PHPShopOrm->select(array('*'), array('id' => "=" . intval($_POST['template'])), false, array('limit' => 1));
        if (is_array($data)) {
            $_POST['name_new'] = $data['zag'];
            $_POST['content_new'] = $data['podrob'];
        }
    }

    // Перехват модуля
    $PHPShopModules->setAdmHandler(__FILE__, __FUNCTION__, $_POST);

    $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['newsletter']);
    $action = $PHPShopOrm->insert($_POST);
    header('Location: ?path=' . $_GET['path']);
    return $action;
}

// Обработка событий
$PHPShopGUI->getAction();

// Вывод формы при старте
$PHPShopGUI->setLoader($_POST['saveID'], 'actionStart');
?>