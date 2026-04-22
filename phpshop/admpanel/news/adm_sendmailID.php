<?php

$TitlePage = __('Редактирование Рассылки') . ' #' . $_GET['id'];
$PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['newsletter']);
PHPShopObj::loadClass("bot");

function actionStart() {
    global $PHPShopGUI, $PHPShopSystem, $PHPShopModules, $result_message;

    // Выбор даты
    $PHPShopGUI->addJSFiles('./js/bootstrap-datetimepicker.min.js', './news/gui/news.gui.js');
    $PHPShopGUI->addCSSFiles('./css/bootstrap-datetimepicker.min.css');

    // Выборка
    $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['newsletter']);
    $data = $PHPShopOrm->select(array('*'), array('id' => '=' . intval($_GET['id'])));
    $PHPShopGUI->field_col = 4;


    // Нет данных
    if (!is_array($data)) {
        header('Location: ?path=' . $_GET['path']);
    }

    $PHPShopGUI->action_button['Сохранить и отправить'] = array(
        'name' => __('Сохранить и отправить'),
        'action' => 'saveID',
        'class' => 'btn  btn-default btn-sm navbar-btn hidden-xs',
        'type' => 'submit',
        'icon' => 'glyphicon glyphicon-ok'
    );


    // Имя товара
    if (strlen($data['name']) > 50)
        $title_name = substr($data['name'], 0, 70) . '...';
    else
        $title_name = $data['name'];

    $PHPShopGUI->setActionPanel(__("Рассылка") . " " . $title_name, array('Удалить'), array('Сохранить', 'Сохранить и отправить'));

    // Отчет
    if (!empty($result_message))
        $Tab1 = $PHPShopGUI->setField('Отчет', $result_message);

    // Редактор 1
    $PHPShopGUI->setEditor($PHPShopSystem->getSerilizeParam("admoption.editor"));
    $oFCKeditor = new Editor('content_new');
    $oFCKeditor->Height = '800';
    $oFCKeditor->Value = $data['content'];

    // Содержание закладки 1
    $Tab1 .= $PHPShopGUI->setField("Тема", $PHPShopGUI->setInput("text.requared", "name_new", $data['name']));

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
    $Tab1 .= $PHPShopGUI->setField('Лимит рассылок', $PHPShopGUI->setInputText(null, 'send_limit', '0,300', 150), 1, 'Пользователям c 1 по 300');
    $Tab1 .= $PHPShopGUI->setField("Витрины", $PHPShopGUI->loadLib('tab_multibase', $data, 'catalog/', '100%', false));

    if (empty($_POST['time_limit']))
        $_POST['time_limit'] = 15;

    if (empty($_POST['message_limit']))
        $_POST['message_limit'] = 50;

    $Tab2 = $PHPShopGUI->setField('Сообщений в рассылке', $PHPShopGUI->setInputText(null, 'message_limit', $_POST['message_limit'], 150), 1, 'Задается хостингом');
    $Tab2 .= $PHPShopGUI->setField('Временной интервал', $PHPShopGUI->setInputText(null, 'time_limit', $_POST['time_limit'], 150, __('минут')), 1, 'Задается хостингом');
    $Tab2 .= $PHPShopGUI->setField("Помощник", $PHPShopGUI->setCheckbox('smart', 1, __('Умная рассылка для обхода ограничений'), 0, false, false));

    $Tab3 = $PHPShopGUI->setField("Тестовое сообщение", $PHPShopGUI->setCheckbox('test', 1, __('Отправить тестовое сообщение ') . ' ' . $PHPShopSystem->getEmail(), 1, false, false));

    $Tab3 .= $PHPShopGUI->setTextarea('recipients_new', $data['recipients'], true, false, '200px', 'Укажите e-mail получателей рассылки через запятую или оставьте это поле пустым для рассылки всем пользователям');

    $Tab1 = $PHPShopGUI->setCollapse('Информация', $Tab1);

    $Tab1 .= $PHPShopGUI->setCollapse('Автоматизация', $Tab2);

    $Tab1 .= $PHPShopGUI->setCollapse('Точечная рассылка', $Tab3);


    $message_var = 'Переменные: <code>@url@</code> - адрес сайта, <code>@user@</code> - имя подписчика, <code>@email@</code> - email подписчика, <code>@name@</code> - название магазина, <code>@tel@</code> - телефон компании';

    // Текст уведомления в мессенджеры
    $Tab1 .= $PHPShopGUI->setCollapse("Текст уведомления в мессенджеры", $PHPShopGUI->setTextarea('bot_message_new', $data['bot_message'], true, false, 150) . $PHPShopGUI->setHelp($message_var));


    $Tab1 .= $PHPShopGUI->setCollapse("Текст письма", $oFCKeditor->AddGUI() . $PHPShopGUI->setAIHelpButton('content_new', 300, 'news_sendmail') . $PHPShopGUI->setHelp($message_var));


    // Запрос модуля на закладку
    $PHPShopModules->setAdmHandler(__FILE__, __FUNCTION__, $data);

    // Вывод формы закладки
    $PHPShopGUI->setTab(array("Основное", $Tab1, true, false, true));

    // Вывод кнопок сохранить и выход в футер
    $ContentFooter = $PHPShopGUI->setInput("hidden", "rowID", $data['id'], "right", 70, "", "but") .
            $PHPShopGUI->setInput("button", "delID", "Удалить", "right", 70, "", "but", "actionDelete.news.edit") .
            $PHPShopGUI->setInput("submit", "editID", "Сохранить", "right", 70, "", "but", "actionUpdate.news.edit") .
            $PHPShopGUI->setInput("submit", "saveID", "Применить", "right", 80, "", "but", "actionSave.news.edit");

    // Футер
    $PHPShopGUI->setFooter($ContentFooter);

    return true;
}

/**
 * Экшен сохранения
 */
function actionSave() {

    // Сохранение данных
    actionUpdate();
}

// Бот
function actionBot() {
    global $PHPShopBase;

    // Всего пользователей
    $total = $PHPShopBase->getNumRows('shopusers', "where sendmail='1'");

    // Выборка
    $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['newsletter']);
    $data = $PHPShopOrm->select(array('*'), array('id' => '=' . intval($_GET['id'])));

    if ($total >= $_POST['end']) {

        $option = array(
            'start' => $_POST['start'],
            'end' => $_POST['end'],
            'content' => $data['content'],
            'name' => $data['name'],
        );
        $action = actionUpdate($option);
        $action['bar'] = round($_POST['start'] * 100 / $total);

        return $action;
    } else
        return array("success" => 'done', "result" => PHPShopString::win_utf8('Успешно разослано по <strong>' . $total . '</strong> адресам с ограничением ' . ($_POST['end'] - $_POST['start']) . ' e-mail через каждые ' . $_POST['time'] . ' мин. за  ' . round($_POST['performance'] / 60000, 1) . ' мин.'));
}

// Функция обновления
function actionUpdate($option = false) {
    global $PHPShopModules, $PHPShopSystem, $PHPShopGUI, $result_message, $PHPShopBase;

    $_POST['date_new'] = time();

    PHPShopObj::loadClass("parser");

    PHPShopParser::set('url', $_SERVER['SERVER_NAME']);
    PHPShopParser::set('name', $PHPShopSystem->getValue('name'));
    PHPShopParser::set('tel', $PHPShopSystem->getValue('tel'));
    PHPShopParser::set('title', $_POST['name_new']);
    PHPShopParser::set('logo', $PHPShopSystem->getLogo());
    $from = $PHPShopSystem->getEmail();

    // Мультибаза
    if (!empty($_POST['servers_new']) and $_POST['servers_new'] != 1000) {
        $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['servers']);
        $PHPShopOrm->debug = false;
        $showcaseData = $PHPShopOrm->select(array('*'), array('enabled' => "='1'", 'id' => "=" . (int) $_POST['servers_new']), false, array('limit' => 1));
        if (is_array($showcaseData)) {

            if (!empty($showcaseData['tel']))
                $PHPShopSystem->setParam("tel", $showcaseData['tel']);

            if (!empty($showcaseData['adminmail']))
                $from = $showcaseData['adminmail'];

            if (!empty($showcaseData['name']))
                $PHPShopSystem->setParam('name', $showcaseData['name']);

            if (!empty($showcaseData['title']))
                $PHPShopSystem->setParam('title', $showcaseData['title']);

            if (!empty($showcaseData['logo']))
                $PHPShopSystem->setParam('logo', $showcaseData['logo']);

            if (!empty($showcaseData['icon']))
                $PHPShopSystem->setParam('url', $showcaseData['host']);
        }
    }

    // Рассылка новости
    if (!empty($_POST['template'])) {

        $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['news']);
        $data = $PHPShopOrm->select(array('*'), array('id' => "=" . intval($_POST['template'])), false, array('limit' => 1));
        if (is_array($data)) {
            $_POST['name_new'] = $data['zag'];
            $_POST['content_new'] = $data['podrob'];
        }
    }

    $n = $error = 0;

    if (!empty($_SERVER['HTTPS']) && 'off' !== strtolower($_SERVER['HTTPS']))
        $ssl = 'https://';
    else
        $ssl = 'http://';

    // Добавление http
    if (!strstr($_POST['content_new'], "http:") and ! strstr($_POST['content_new'], "https:")) {
        $_POST['content_new'] = str_replace('../../UserFiles/', "/UserFiles/", $_POST['content_new']);
        $_POST['content_new'] = str_replace("/UserFiles/", $ssl . $_SERVER['SERVER_NAME'] . "/UserFiles/", $_POST['content_new']);
    }

    // Тест
    if (!empty($_POST['test'])) {

        if (!empty($_POST['saveID'])) {
            PHPShopParser::set('user', $_SESSION['logPHPSHOP']);
            PHPShopParser::set('email', $from);
            PHPShopParser::set('content', preg_replace_callback("/@([a-zA-Z0-9_]+)@/", 'PHPShopParser::SysValueReturn', $_POST['content_new']));

            $PHPShopMail = new PHPShopMail($from, $from, $_POST['name_new'], '', true, true);
            $content = PHPShopParser::file('tpl/sendmail.mail.tpl', true);

            if (!empty($content)) {
                if ($PHPShopMail->sendMailNow($content))
                    $n++;
                else
                    $error++;
            }
        }
    } else {

        // Автоматизация
        if (is_array($option)) {
            $limit = $option['start'] . ',' . $option['end'];
            $title = $option['name'];
            $content = $option['content'];
        } elseif (!empty($_POST['smart'])) {
            $limit = '0,' . $_POST['message_limit'];
            $content = $_POST['content_new'];
            $title = $_POST['name_new'];
        } else {
            $limit = $_POST['send_limit'];
            $content = $_POST['content_new'];
            $title = $_POST['name_new'];
        }

        // Рассылка пользователям
        $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['shopusers']);
        $PHPShopOrm->debug = false;

        // Точечная рассылка
        if (!empty($_POST['recipients_new'])) {
            $recipients = explode(",", $_POST['recipients_new']);
            if (is_array($recipients))
                $where['mail'] = " IN ('" . implode("','", $recipients) . "')";
        } else
            $where['sendmail'] = "='1'";

        // Мультибаза
        if ($_POST['servers_new'] == 1000)
            $where['servers'] = '=' . (int) $_POST['servers_new'] . ' or servers=0';
        else
            $where['servers'] = '=' . (int) $_POST['servers_new'];

        $data = $PHPShopOrm->select(array('*'), $where, array('order' => 'id desc'), array('limit' => $limit));

        if (is_array($data))
            foreach ($data as $row) {

                PHPShopParser::set('user', $row['name']);
                PHPShopParser::set('email', $row['mail']);
                PHPShopParser::set('content', preg_replace_callback("/@([a-zA-Z0-9_]+)@/", 'PHPShopParser::SysValueReturn', $content));
                $unsubscribe = '<p>Что бы отказаться от новостной рассылки <a href="http://' . $_SERVER['SERVER_NAME'] . '/unsubscribe/?id=' . $row['id'] . '&hash=' . md5($row['mail'] . $row['password']) . '" target="_blank">перейдите по ссылке.</a></p>';
                PHPShopParser::set('unsubscribe', $unsubscribe);

                $PHPShopMail = new PHPShopMail($row['mail'], $from, $title, '', true, true);
                $content_message = PHPShopParser::file('tpl/sendmail.mail.tpl', true);
                $tel = $row['tel'];
                $bot_message = $_POST['bot_message_new'];

                if (!empty($content)) {
                    if ($PHPShopMail->sendMailNow($content_message))
                        $n++;
                    else
                        $error++;
                }
                
                if (!empty($tel) and ! empty($bot_message)) {
                    $PHPShopWappi = new PHPShopWappi();

                    PHPShopParser::set('user', $row['name']);
                    PHPShopParser::set('email', $row['mail']);
                    $message = preg_replace_callback("/@([a-zA-Z0-9_]+)@/", 'PHPShopParser::SysValueReturn', $bot_message);
                    $result = $PHPShopWappi->cascade(PHPShopString::win_utf8($message), $tel);

                    if($result['response']['status'] == 'done')
                        $n++;
                    else $error++;
                }
            }
    }

    // Перехват модуля
    $PHPShopModules->setAdmHandler(__FILE__, __FUNCTION__, $_POST);

    // Автоматизация
    if (!empty($_POST['smart']) and empty($_POST['test'])) {

        // Точечная рассылка
        if (!empty($_POST['recipients_new'])) {
            $recipients = explode(",", $_POST['recipients_new']);
            if (is_array($recipients))
                $where = "where mail IN ('" . implode("','", $recipients) . "')";
        } else
            $where = "where sendmail='1'";

        // Всего пользователей
        $total = $PHPShopBase->getNumRows('shopusers', $where);

        $bar = round($_POST['message_limit'] * 100 / $total);
        $action = true;
        $result_message = $PHPShopGUI->setAlert('<div id="bot_result">Успешно разослано по <strong>' . $n . '</strong> адресам с ограничением ' . $limit . ' записей. Ошибок <strong>' . $error . '</strong>.</div>
<div class="progress">
  <div class="progress-bar progress-bar-striped  progress-bar-success active" role="progressbar" aria-valuenow="" aria-valuemin="0" aria-valuemax="100" style="width: ' . $bar . '%"> ' . $bar . '% 
  </div>
</div>');
    } else {
        $result_ajax = 'Успешно разослано по <strong>' . $n . '</strong> адресам с ограничением ' . $limit . ' записей. Ошибок <strong>' . $error . '</strong>.';
        $result_message = $PHPShopGUI->setAlert($result_ajax);
        $action = true;
    }

    if (empty($option)) {
        $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['newsletter']);

        // Чистка дубликатов
        if (!empty($_POST['recipients_new'])) {
            $recipients = explode(",", $_POST['recipients_new']);
            if (is_array($recipients)) {

                foreach ($recipients as $mail)
                    $recipients_clean[$mail] = $mail;

                $_POST['recipients_new'] = implode(",", $recipients_clean);
            }
        }

        $action = $PHPShopOrm->update($_POST, array('id' => '=' . $_POST['rowID']));
    }

    return array("success" => $action, "result" => PHPShopString::win_utf8($result_ajax), 'limit' => $limit);
}

// Функция удаления
function actionDelete() {
    global $PHPShopOrm, $PHPShopModules;

    // Перехват модуля
    $PHPShopModules->setAdmHandler(__FILE__, __FUNCTION__, $_POST);

    $action = $PHPShopOrm->delete(array('id' => '=' . $_POST['rowID']));
    return array("success" => $action);
}

// Обработка событий
$PHPShopGUI->getAction();

// Вывод формы при старте
$PHPShopGUI->setAction($_GET['id'], 'actionStart', 'none');
?>