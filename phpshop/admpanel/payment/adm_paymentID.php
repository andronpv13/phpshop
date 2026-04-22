<?php

$TitlePage = __('Редактирование способа оплаты') . ' #' . $_GET['id'];
$PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['payment_systems']);
PHPShopObj::loadClass('user');

// Стартовый вид
function actionStart() {
    global $PHPShopGUI, $PHPShopSystem, $PHPShopOrm, $PHPShopModules;

    // Выборка
    $data = $PHPShopOrm->select(array('*'), array('id' => '=' . intval($_REQUEST['id'])));

    // Нет данных
    if (!is_array($data)) {
        header('Location: ?path=' . $_GET['path']);
    }

    // bootstrap-colorpicker
    $PHPShopGUI->addCSSFiles('./css/bootstrap-colorpicker.min.css');
    $PHPShopGUI->addJSFiles('./js/bootstrap-colorpicker.min.js');

    // Размер названия поля
    $PHPShopGUI->field_col = 4;
    $PHPShopGUI->setActionPanel($data['name'], array('Создать', '|', 'Удалить'), array('Сохранить', 'Сохранить и закрыть'), false);

    // Редактор 1
    $PHPShopGUI->setEditor($PHPShopSystem->getSerilizeParam("admoption.editor"));
    $oFCKeditor = new Editor('message_new');
    $oFCKeditor->Height = '300';
    $oFCKeditor->ToolbarSet = 'Normal';
    $oFCKeditor->Value = $data['message'];

    // Юридические лица
    $PHPShopCompany = new PHPShopCompanyArray();
    $PHPShopCompanyArray = $PHPShopCompany->getArray();
    $company_value[] = array($PHPShopSystem->getSerilizeParam("bank.org_name"), 0, $data['company']);
    if (is_array($PHPShopCompanyArray))
        foreach ($PHPShopCompanyArray as $company)
            $company_value[] = array($company['name'], $company['id'], $data['company']);

    // Статусы пользователей
    $PHPShopUserStatus = new PHPShopUserStatusArray();
    $PHPShopUserStatusArray = $PHPShopUserStatus->getArray();
    $user_status_value[] = array(__('Не выбрано'), '', '');
    if (is_array($PHPShopUserStatusArray))
        foreach ($PHPShopUserStatusArray as $user_status)
            $user_status_value[] = array($user_status['name'], $user_status['id'], $data['status']);

    // Содержание 
    $Tab1 = $PHPShopGUI->setCollapse('Информация', $PHPShopGUI->setField("Наименование", $PHPShopGUI->setInput("text", "name_new", $data['name'])) .
            $PHPShopGUI->setField("Статус", $PHPShopGUI->setCheckbox("enabled_new", 1, null, $data['enabled'])) .
            $PHPShopGUI->setField("Приоритет", $PHPShopGUI->setInputText(null, "num_new", $data['num'], '100')) .
            $PHPShopGUI->setField("Юридические данные", $PHPShopGUI->setCheckbox("yur_data_flag_new", 1, "Обязательно заполнять", $data['yur_data_flag'])) .
            $PHPShopGUI->setField("Тип подключения", $PHPShopGUI->setSelect("path_new", $PHPShopGUI->loadLib('GetTipPayment', $data['path']), '100%')) .
            $PHPShopGUI->setField("Юридическое лицо", $PHPShopGUI->setSelect('company_new', $company_value, '100%')) .
            $PHPShopGUI->setField("Смена статуса", $PHPShopGUI->setSelect('status_new', $user_status_value, '100%')) .
            $PHPShopGUI->setField("Витрины", $PHPShopGUI->loadLib('tab_multibase', $data, 'catalog/', '100%'))
    );

    $Tab1 .= $PHPShopGUI->setCollapse('Внешний вид', $PHPShopGUI->setField("Иконка", $PHPShopGUI->setIcon($data['icon'], "icon_new", false)) .
            $PHPShopGUI->setField('Цвет', $PHPShopGUI->setInputColor('color_new', $data['color'])));

    $Tab2 = $PHPShopGUI->setField("Стоимость более", $PHPShopGUI->setInputText(null, "sum_max_new", $data['sum_max'], 150, $PHPShopSystem->getDefaultValutaCode()));
    $Tab2 .= $PHPShopGUI->setField("Стоимость менее", $PHPShopGUI->setInputText(null, "sum_min_new", $data['sum_min'], 150, $PHPShopSystem->getDefaultValutaCode()));
    $Tab2 .= $PHPShopGUI->setField("Скидка более", $PHPShopGUI->setInputText(null, "discount_max_new", $data['discount_max'], 80, '%'));
    $Tab2 .= $PHPShopGUI->setField("Скидка менее", $PHPShopGUI->setInputText(null, "discount_min_new", $data['discount_min'], 80, '%'));

    $Tab1 .= $PHPShopGUI->setCollapse('Блокировка', $Tab2);

    $Tab1 .= $PHPShopGUI->setCollapse('Сообщение после заказа', $PHPShopGUI->setField("Заголовок:", $PHPShopGUI->setInput("text", "message_header_new", $data['message_header'])) . '<div>' . $oFCKeditor->AddGUI() . '</div>');

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

function iconAdd() {

    // Папка сохранения
    $path = '/UserFiles/Image/';

    // Копируем от пользователя
    if (!empty($_FILES['file']['name'])) {
        $_FILES['file']['ext'] = PHPShopSecurity::getExt($_FILES['file']['name']);
        if (in_array($_FILES['file']['ext'], array('gif', 'png', 'jpg'))) {
            if (move_uploaded_file($_FILES['file']['tmp_name'], $_SERVER['DOCUMENT_ROOT'] . $GLOBALS['dir']['dir'] . $path . $_FILES['file']['name'])) {
                $file = $GLOBALS['dir']['dir'] . $path . $_FILES['file']['name'];
            }
        }
    }

    // Читаем файл из URL
    elseif (!empty($_POST['furl'])) {
        $file = $_POST['icon_new'];
    }

    // Читаем файл из файлового менеджера
    elseif (!empty($_POST['icon_new'])) {
        $file = $_POST['icon_new'];
    }

    if (empty($file))
        $file = '';

    return $file;
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

    header('Location: ?path=' . $_GET['path']);
}

// Функция обновления
function actionUpdate() {
    global $PHPShopOrm, $PHPShopModules;

    $_POST['icon_new'] = iconAdd();

    // Корректировка пустых значений
    $PHPShopOrm->updateZeroVars('yur_data_flag_new', 'enabled_new');

    // Мультибаза
    if (is_array($_POST['servers'])) {
        $_POST['servers_new'] = "";
        foreach ($_POST['servers'] as $v)
            if ($v != 'null' and ! strstr($v, ',') and $v != null and ! strstr($_POST['servers_new'], "i" . $v . "i"))
                $_POST['servers_new'] .= "i" . $v . "i";
    }

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