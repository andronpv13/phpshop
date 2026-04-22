<?php

$TitlePage = __("Настройка диалогов");
$PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['system']);

// Стартовый вид
function actionStart() {
    global $PHPShopGUI, $PHPShopModules, $TitlePage, $PHPShopOrm, $PHPShopBase;

    // Выборка
    $data = $PHPShopOrm->select();
    $option = unserialize($data['admoption']);

    // Размер названия поля
    $PHPShopGUI->field_col = 3;
    $PHPShopGUI->addCSSFiles('./css/bootstrap-colorpicker.min.css');
    $PHPShopGUI->addJSFiles('./js/bootstrap-colorpicker.min.js','./js/jquery.waypoints.min.js', './system/gui/system.gui.js');

    $PHPShopGUI->setActionPanel($TitlePage, false, array('Сохранить'));

    // Демо-режим
    if ($PHPShopBase->getParam('template_theme.demo') == 'true') {
        $option['telegram_token'] = '';
    }

    // Диалоги
    $PHPShopGUI->_CODE .= $PHPShopGUI->setCollapse('Диалоги', $PHPShopGUI->setField("Оповещение о диалогах", $PHPShopGUI->setCheckbox('option[telegram_dialog]', 1, 'Включить оповещение в Telegram', $option['telegram_dialog']) . '<br>' .
                    $PHPShopGUI->setCheckbox('option[vk_dialog]', 1, 'Включить оповещение в VK', $option['vk_dialog']) . '<br>' .
                    $PHPShopGUI->setCheckbox('option[mail_dialog]', 1, 'Включить оповещение на E-mail', $option['mail_dialog'])) .
            $PHPShopGUI->setField("Размещение вложений", $PHPShopGUI->setInputText($GLOBALS['SysValue']['dir']['dir'] . '/UserFiles/Image/', "option[image_dialog_path]", $option['image_dialog_path'], 300), 1, 'Путь сохранения передаваемых файлов')
            , 'in', false);
    
    // Чат
    if (empty($option['avatar_dialog']))
        $option['avatar_dialog'] = '/phpshop/lib/templates/chat/avatar.png';
    
    if(empty($option['color_dialog']))
        $option['color_dialog']='#42a5f5';
    
    if(empty($option['day_dialog']))
        $option['day_dialog']=1;
    
    $value_day[] = array('5 рабочих дней', 1, $option['day_dialog']);
    $value_day[] = array('6 рабочих дней', 2, $option['day_dialog']);
    $value_day[] = array('7 рабочих дней', 3, $option['day_dialog']);
    
    if(empty($option['margin_dialog']))
        $option['margin_dialog']=0;
    
    if(empty($option['size_dialog']))
        $option['size_dialog']=56;
    
    if(empty($option['sizem_dialog']))
        $option['sizem_dialog']=56;
    
    if(empty($option['time_from_dialog']) and empty($option['time_until_dialog'])){
        $option['time_until_dialog'] = 20;
        $option['time_from_dialog'] = 8;
    }
    
    $PHPShopGUI->_CODE .= $PHPShopGUI->setCollapse('Виджет чата',
            $PHPShopGUI->setField("Чат", $PHPShopGUI->setCheckbox('option[chat_dialog]', 1, 'Включить виджет чата', $option['chat_dialog']).'<br>'.
            $PHPShopGUI->setCheckbox('option[tel_dialog]', 1, 'Включить обязательное поле телефон для чата', $option['tel_dialog'])).
            $PHPShopGUI->setField("Заголовок чата", $PHPShopGUI->setInputText(null, "option[title_dialog]", $option['title_dialog'], 300)) .
            $PHPShopGUI->setField("Приветствие в чате", $PHPShopGUI->setTextarea('option[text_dialog]',$option['text_dialog'],false,300)).
            $PHPShopGUI->setField("График работы чата", $PHPShopGUI->setInputText('c'.'&nbsp;&nbsp;', "option[time_from_dialog]", (int) $option['time_from_dialog'], 150,__('ч.')).'<br>'.
                    $PHPShopGUI->setInputText('до', "option[time_until_dialog]", (int) $option['time_until_dialog'], 150,__('ч.')) .'<br>'.
                    $PHPShopGUI->setSelect('option[day_dialog]', $value_day,150,true).'<br>'.
                    $PHPShopGUI->setCheckbox('option[time_off_dialog]', 1, 'Скрыть виджет чата в нерабочее время', $option['time_off_dialog'])
                    ) .
            $PHPShopGUI->setField("Аватар сотрудника в чате", $PHPShopGUI->setIcon($option['avatar_dialog'], "avatar_dialog", false, array('load' => false, 'server' => true))).
            $PHPShopGUI->setField('Цвет чата', $PHPShopGUI->setInputColor('option[color_dialog]', $option['color_dialog'],150)).
            $PHPShopGUI->setField("Отступ снизу", $PHPShopGUI->setInputText(null, "option[margin_dialog]", $option['margin_dialog'], 150,'px')).
            $PHPShopGUI->setField("Размер кнопки", $PHPShopGUI->setInputText(null, "option[size_dialog]", $option['size_dialog'], 150,'px')).
            $PHPShopGUI->setField("Размер кнопки для телефонов", $PHPShopGUI->setInputText(null, "option[sizem_dialog]", $option['sizem_dialog'], 150,'px'))
            );
    
    // Telegram
    $PHPShopGUI->_CODE .= $PHPShopGUI->setCollapse('Telegram', $PHPShopGUI->setField("Чат бот", $PHPShopGUI->setCheckbox('option[telegram_enabled]', 1, 'Включить чат бот Telegram', $option['telegram_enabled']).'<br>'.
            $PHPShopGUI->setCheckbox('option[telegram_order]', 1, 'Включить оповещение о заказах администратору', $option['telegram_order']) ) .
            $PHPShopGUI->setField("Имя бота", $PHPShopGUI->setInputText('@', "option[telegram_bot]", $option['telegram_bot'], 300)) .
            $PHPShopGUI->setField("Chat IDS", $PHPShopGUI->setInputText(null, "option[telegram_admin]", $option['telegram_admin'], 300), 1, 'Уведомление о заказе и диалогах администраторам. Несколько значений указывается через запятую.') .
            $PHPShopGUI->setField("API-ключ", $PHPShopGUI->setInputText(null, "option[telegram_token]", $option['telegram_token'], 300) . $PHPShopGUI->setHelp('Информация о сервисе, регистрация, получение ключей <a href="https://wiki.phpshop.ru/nastroiky/dialog#telegram" target="_blank">Инструкция</a>')));

    // VK
    $PHPShopGUI->_CODE .= $PHPShopGUI->setCollapse('Вконтакте', $PHPShopGUI->setField("Чат бот", $PHPShopGUI->setCheckbox('option[vk_enabled]', 1, 'Включить чат бот Вконтакте', $option['vk_enabled']) .'<br>'.
            $PHPShopGUI->setCheckbox('option[vk_order]', 1, 'Включить оповещение о заказах администратору', $option['vk_order'])) .
            $PHPShopGUI->setField("Имя сообщества", $PHPShopGUI->setInputText(null, "option[vk_bot]", $option['vk_bot'], 300)) .
            $PHPShopGUI->setField("Код подтверждения", $PHPShopGUI->setInputText(null, "option[vk_confirmation]", $option['vk_confirmation'], 300)) .
            $PHPShopGUI->setField("Ключ подтверждения", $PHPShopGUI->setInputText(null, "option[vk_secret]", $option['vk_secret'], 300)) .
            $PHPShopGUI->setField("Chat IDS", $PHPShopGUI->setInputText(null, "option[vk_admin]", $option['vk_admin'], 300), 1, 'Уведомление о заказе и диалогах администраторам. Несколько значений указывается через запятую.') .
            $PHPShopGUI->setField("API-ключ", $PHPShopGUI->setInputText(null, "option[vk_token]", $option['vk_token'], 300) . $PHPShopGUI->setHelp('Информация о сервисе, регистрация, получение ключей <a href="https://wiki.phpshop.ru/nastroiky/dialog#vkontakte" target="_blank">Инструкция</a>'))
    );


    // Запрос модуля на закладку
    $PHPShopModules->setAdmHandler(__FILE__, __FUNCTION__, $data);

    // Вывод кнопок сохранить и выход в футер
    $ContentFooter = $PHPShopGUI->setInput("hidden", "rowID", $data['id'], "right", 70, "", "but") .
            $PHPShopGUI->setInput("submit", "editID", "Сохранить", "right", 70, "", "but", "actionUpdate.system.edit") .
            $PHPShopGUI->setInput("submit", "saveID", "Применить", "right", 80, "", "but", "actionSave.system.edit");

    $PHPShopGUI->setFooter($ContentFooter);

    $sidebarleft[] = array('title' => 'Категории', 'content' => $PHPShopGUI->loadLib('tab_menu', false, './system/'));
    $PHPShopGUI->setSidebarLeft($sidebarleft, 2);

    // Футер
    $PHPShopGUI->Compile(2);
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
    global $PHPShopOrm, $PHPShopModules;

    // Выборка
    $data = $PHPShopOrm->select();
    $option = unserialize($data['admoption']);

    // Корректировка пустых значений
    $PHPShopOrm->updateZeroVars('option.telegram_enabled', 'option.vk_enabled', 'option.telegram_dialog', 'option.vk_dialog', 'option.mail_dialog', 'option.push_dialog', 'option.telegram_order', 'option.vk_order','option.chat_dialog','option.mobil_dialog','option.tel_dialog','option.time_off_dialog');

    // Иконка
    $_POST['option']['avatar_dialog'] = $_POST['avatar_dialog'];

    if (is_array($_POST['option']))
        foreach ($_POST['option'] as $key => $val)
            $option[$key] = $val;

    // Создаем папку
    if (!is_dir($_SERVER['DOCUMENT_ROOT'] . $GLOBALS['SysValue']['dir']['dir'] . '/UserFiles/Image/' . $option['image_dialog_path']))
        @mkdir($_SERVER['DOCUMENT_ROOT'] . $GLOBALS['SysValue']['dir']['dir'] . '/UserFiles/Image/' . $option['image_dialog_path'], 0777, true);

    // Проверка пути сохранения изображений
    if (stristr($option['image_dialog_path'], '..') or ! is_dir($_SERVER['DOCUMENT_ROOT'] . $GLOBALS['SysValue']['dir']['dir'] . '/UserFiles/Image/' . $option['image_dialog_path']))
        $option['image_dialog_path'] = null;

    if (substr($option['image_dialog_path'], -1) != '/' and ! empty($option['image_dialog_path']))
        $option['image_dialog_path'] .= '/';

    $_POST['admoption_new'] = serialize($option);

    // Telegram регистрация вебхука
    if (!empty($option['telegram_enabled']) and ! empty($option['telegram_token'])) {

        $url = 'https://api.telegram.org/bot' . $option['telegram_token'] . '/setWebhook?url=https://' . $_SERVER['SERVER_NAME'] . '/bot/telegram.php';
        $сurl = curl_init();
        curl_setopt_array($сurl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
        ));
        $result = curl_exec($сurl);
        curl_close($сurl);
    }

    // Перехват модуля
    $PHPShopModules->setAdmHandler(__FILE__, __FUNCTION__, $_POST);

    $action = $PHPShopOrm->update($_POST, array('id' => '=' . $_POST['rowID']));


    return array("success" => $action);
}

// Обработка событий
$PHPShopGUI->getAction();
?>