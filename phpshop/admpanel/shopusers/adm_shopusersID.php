<?php

$TitlePage = __('Редактирование пользователя') . ' #' . $_GET['id'];
$PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['shopusers']);
PHPShopObj::loadClass('user');

// Стартовый вид
function actionStart() {
    global $PHPShopGUI, $PHPShopOrm, $PHPShopModules, $PHPShopSystem, $hideCatalog;
    
    // Выборка
    $data = $PHPShopOrm->select(array('*'), array('id' => '=' . intval($_REQUEST['id'])));

    // Нет данных
    if (!is_array($data)) {
        header('Location: ?path=' . $_GET['path']);
    }

    $PHPShopGUI->action_select['Создать заказ'] = array(
        'name' => 'Создать заказ',
        'url' => '?path=order&action=new&user=' . $data['id'],
        'class'=> $hideCatalog
    );

    $PHPShopGUI->action_select['Заказы пользователя'] = array(
        'name' => 'Заказы пользователя',
        'url' => '?path=order&where[a.user]=' . $data['id'],
        'class'=> $hideCatalog
    );

    $PHPShopGUI->action_select['Диалоги пользователя'] = array(
        'name' => 'Диалоги пользователя',
        'url' => '?path=dialog&uid=' . $data['id']
    );

    $PHPShopGUI->action_select['Отправить письмо'] = array(
        'name' => 'Отправить письмо',
        'url' => 'mailto:' . $data['login']
    );

    $PHPShopGUI->action_select['Создать диалог'] = array(
        'name' => 'Создать диалог',
        'url' => '?path=dialog&new&user=' . $data['id'] . '&bot=message&id=' . $data['id'] . '&return=dialog'
    );

    // Яндекс.Карты
    $yandex_apikey = $PHPShopSystem->getSerilizeParam("admoption.yandex_apikey");
    if (empty($yandex_apikey))
        $yandex_apikey = 'cb432a8b-21b9-4444-a0c4-3475b674a958';

    // Размер названия поля
    $PHPShopGUI->field_col = 3;
    $PHPShopGUI->setActionPanel(__("Пользователи") . '<span class="hidden-xs">: ' . $data['name'] . '</span>', array('Создать заказ', 'Заказы пользователя', 'Диалоги пользователя', 'Создать диалог', '|', 'Удалить'), array('Сохранить', 'Сохранить и закрыть'));
    $PHPShopGUI->addJSFiles('./js/validator.js');

    if ($GLOBALS['PHPShopBase']->codBase == 'utf-8')
        $PHPShopGUI->addJSFiles('./js/jquery.suggestions_utf.min.js', './order/gui/dadata.gui.js');
    else
        $PHPShopGUI->addJSFiles('./js/jquery.suggestions.min.js', './order/gui/dadata.gui.js');

    $PHPShopGUI->addCSSFiles('./css/suggestions.min.css');

    // Статусы пользователей
    $PHPShopUserStatus = new PHPShopUserStatusArray();
    $PHPShopUserStatusArray = $PHPShopUserStatus->getArray();
    $user_status_value[] = array(__('Пользователь'), 0, $data['status']);
    if (is_array($PHPShopUserStatusArray))
        foreach ($PHPShopUserStatusArray as $user_status)
            $user_status_value[] = array($user_status['name'], $user_status['id'], $data['status']);

    if (empty($data['servers']))
        $data['servers'] = 1000;

    // Содержание закладки 1
    $Tab1 = $PHPShopGUI->setCollapse('Информация', $PHPShopGUI->setField("Имя", $PHPShopGUI->setInput('text.required', "name_new", $data['name'])) .
            $PHPShopGUI->setField("E-mail", $PHPShopGUI->setInput('email.required.6', "login_new", $data['login'])) .
            $PHPShopGUI->setField("Телефон", $PHPShopGUI->setInput('tel', "tel_new", $data['tel'])) .
            $PHPShopGUI->setField("Пароль", $PHPShopGUI->setInput("password.required.4", "password_new", base64_decode($data['password']), null, false, false, false, false, false, '<a href="#" class="password-view"  data-toggle="tooltip" data-placement="top" title="' . __('Показать пароль') . '"><span class="glyphicon glyphicon-eye-open"></span></a>')) .
            $PHPShopGUI->setField("Подтверждение пароля", $PHPShopGUI->setInput("password.required.4", "password2_new", base64_decode($data['password']))) .
            $PHPShopGUI->setField("Статус", $PHPShopGUI->setCheckbox("enabled_new", 1, null, $data['enabled']) . '<br>' . $PHPShopGUI->setCheckbox('sendActivationEmail', 1, 'Оповестить пользователя', 0)) .
            $PHPShopGUI->setField("Блокировка диалогов", $PHPShopGUI->setCheckbox("dialog_ban_new", 1, null, $data['dialog_ban'])) .
            $PHPShopGUI->setField("Статус", $PHPShopGUI->setSelect('status_new', $user_status_value, 300)) .
            $PHPShopGUI->setField("Витрины", $PHPShopGUI->loadLib('tab_multibase', $data, 'catalog/', 300, false)) .
            $PHPShopGUI->setField("Накопительная скидка", $PHPShopGUI->setInput('text', "cumulative_discount_new", $data['cumulative_discount'], null, 100, false, false, false, '%'))
    );
    

    // Адреса доставок
    if (empty($hideCatalog)) {
        $Tab2 = $PHPShopGUI->loadLib('tab_addres', $data['data_adres']);

        // Бонусы
        $Tab3 = $PHPShopGUI->loadLib('tab_bonus', $data['id']);


        // Заказы
        $_GET['user'] = $data['id'];
        $tab_order = $PHPShopGUI->loadLib('tab_order', false, './dialog/');
        if (!empty($tab_order))
            $sidebarright[] = array('title' => 'Заказы', 'content' => $tab_order);

        // Корзина
        $tab_cart = $PHPShopGUI->loadLib('tab_cart', false, './dialog/');
        if (!empty($tab_cart))
            $sidebarright[] = array('title' => 'Корзина', 'content' => $tab_cart);
    }

    // Диалоги
    $_GET['user_id'] = $data['id'];
    $tab_dialog = $PHPShopGUI->loadLib('tab_dialog', false, './dialog/');

    if (!empty($tab_dialog))
        $sidebarright[] = array('title' => 'Диалоги', 'content' => $tab_dialog);
    
    // Отзывы
    $tab_comment = $PHPShopGUI->loadLib('tab_comment',false);

     if (!empty($tab_comment))
        $sidebarright[] = array('title' => 'Отзывы', 'content' => $tab_comment);
    
    // Карта
    $mass = unserialize($data['data_adres']);
    if ($PHPShopSystem->ifSerilizeParam('admoption.yandexmap_enabled')) {
        if (!empty($mass['main']) and ! empty($mass['list'][$mass['main']]['street_new'])) {
            $PHPShopGUI->addJSFiles('./shopusers/gui/shopusers.gui.js', '//api-maps.yandex.ru/2.0/?load=package.standard&lang=ru-RU&apikey=' . $yandex_apikey);
            $map = '<div id="map" data-geocode="' . $mass['list'][$mass['main']]['city_new'] . ', ' . $mass['list'][$mass['main']]['street_new'] . ' ' . $mass['list'][$mass['main']]['house_new'] . '" style="width: 280px;height:280px;"></div>';

            $sidebarright[] = array('title' => 'Адрес доставки на карте', 'content' => array($map));
        }
    }

    // Правый сайдбар
    if (!empty($sidebarright) and empty($hideCatalog)) {
        $PHPShopGUI->setSidebarRight($sidebarright, 3, 'hidden-xs');
        $PHPShopGUI->sidebarLeftRight = 3;
    }

    // Запрос модуля на закладку
    $PHPShopModules->setAdmHandler(__FILE__, __FUNCTION__, $data);

    // Бонусы
    if ($PHPShopSystem->getSerilizeParam('admoption.bonus') > 0)
        $PHPShopGUI->addTabSeparate(array("Бонусы <span class=badge>" . $data['bonus'] . "</span>", $Tab3, true));

    // Вывод формы закладки
    $PHPShopGUI->setTab(array("Основное", $Tab1, true), array("Доставка и реквизиты", $Tab2, true));

    // Вывод кнопок сохранить и выход в футер
    $ContentFooter = $PHPShopGUI->setInput("hidden", "rowID", $data['id'], "right", 70, "", "but") .
            $PHPShopGUI->setInput("hidden", "bonus_new", $data['bonus']) .
            $PHPShopGUI->setInput("button", "delID", "Удалить", "right", 70, "", "but", "actionDelete.shopusers.edit") .
            $PHPShopGUI->setInput("submit", "editID", "Сохранить", "right", 70, "", "but", "actionUpdate.shopusers.edit") .
            $PHPShopGUI->setInput("submit", "saveID", "Применить", "right", 80, "", "but", "actionSave.shopusers.edit");

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
    return array("success" => $action);
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
    global $PHPShopOrm, $PHPShopModules, $PHPShopSystem;

    if (is_array($_POST['mass']))
        foreach ($_POST['mass'] as $k => $v) {

            // Кодировка windows 1251
            $mass_decode[$k] = @array_map("urldecode", $v);

            // Управление адресами
            if (!empty($_POST['mass'][$k]['default']))
                $_POST['data_adres_new']['main'] = $k;

            if (!empty($_POST['mass'][$k]['delete']))
                unset($mass_decode[$k]);
        }

    if ($_POST['servers_new'] == 1000)
        $_POST['servers_new'] = 0;

    $_POST['mail_new'] = $_POST['login_new'];

    // Оповещение пользователя
    if (!empty($_POST['enabled_new']) and ! empty($_POST['sendActivationEmail'])) {

        PHPShopObj::loadClass("parser");
        PHPShopObj::loadClass("mail");

        PHPShopParser::set('user_name', $_POST['name_new']);
        PHPShopParser::set('login', $_POST['login_new']);
        PHPShopParser::set('password', $_POST['password_new']);

        $zag_adm = __("Ваш аккаунт был успешно активирован Администратором");
        $PHPShopMail = new PHPShopMail($_POST['login_new'], $PHPShopSystem->getParam('adminmail2'), $zag_adm, '', true, true);
        $content_adm = PHPShopParser::file('../lib/templates/users/mail_user_activation_by_admin_success.tpl', true);

        if (!empty($content_adm)) {
            $PHPShopMail->sendMailNow($content_adm);
        }
    }

    if (!empty($mass_decode))
        $_POST['data_adres_new']['list'] = $mass_decode;

    if (is_array($_POST['data_adres_new']))
        $_POST['data_adres_new'] = serialize($_POST['data_adres_new']);

    if (!empty($_POST['password_new']))
        $_POST['password_new'] = base64_encode($_POST['password_new']);

    // Бонусы
    if (!empty($_POST['comment_new'])) {

        $PHPShopOrm->query("
	INSERT INTO `" . $GLOBALS['SysValue']['base']['bonus'] . "` 
	(`date`, `comment`, `user_id`, `bonus_operation`) VALUES 
	('" . time() . "','" . $_POST['comment_new'] . "','" . $_POST['rowID'] . "','" . intval($_POST['bonus_operation_new']) . "')");

        if (intval($_POST['bonus_operation_new']) != 0) {
            $_POST['bonus_new'] = $_POST['bonus_operation_new'] + $_POST['bonus_new'];
        }
    }

    // Корректировка пустых значений
    if (empty($_POST['ajax']))
        $PHPShopOrm->updateZeroVars('enabled_new', 'dialog_ban_new');

    // Перехват модуля
    $PHPShopModules->setAdmHandler(__FILE__, __FUNCTION__, $_POST);
    $action = $PHPShopOrm->update($_POST, array('id' => '=' . $_POST['rowID']));
    return array("success" => $action);
}

// Обработка событий
$PHPShopGUI->getAction();

// Вывод формы при старте
$PHPShopGUI->setAction($_GET['id'], 'actionStart', 'none');
?>