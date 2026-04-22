<?php

$TitlePage = __('Диалог') . ' #' . $_GET['id'];
$PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['dialog']);
PHPShopObj::loadClass('bot');

function actionStart() {
    global $PHPShopGUI, $PHPShopOrm, $chat_name, $PHPShopSystem;

    // Новый диалог
    if (isset($_GET['new'])) {
        $user = intval($_GET['user']);
        $bot = new PHPShopBot();
        $insert = array(
            'user_id' => $user,
            'chat' => array
                (
                'id' => $user,
                'first_name' => "Администрация",
                'last_name' => "",
            ),
            'date' => time(),
            'text' => PHPShopString::win_utf8('Здравствуйте'),
            'staffid' => 1,
            'attachments' => null,
            'bot' => 'message',
            'isview' => 1,
            'isview_user' => 0,
            'ai' => 0
        );

        if (!empty($user))
            $bot->dialog($insert);
    }

    $PHPShopGUI->addCSSFiles('./css/support.css');
    $PHPShopGUI->addJSFiles('./js/jquery.waypoints.min.js', './dialog/gui/dialog.gui.js');
    $PHPShopOrm->debug = false;
    $data = $PHPShopOrm->select(array('*'), array('chat_id' => "=" . intval($_GET['id']), 'bot' => '="' . PHPShopSecurity::TotalClean($_GET['sender']) . '"', 'user_id' => '=' . intval($_GET['user'])), array('order' => 'id'), array('limit' => 500));

    // Нет данных
    if (!is_array($data)) {
        header('Location: ?path=' . $_GET['path']);
        exit();
    }

    $PHPShopGUI->action_button['Наверх'] = array(
        'name' => 'Наверх',
        'class' => 'btn btn-default btn-sm navbar-btn up hide',
        'type' => 'button',
        'icon' => 'glyphicon glyphicon-arrow-up',
    );

    $PHPShopGUI->action_select['Заказы пользователя'] = array(
        'name' => 'Заказы пользователя',
        'url' => '?path=order&where[a.user]=' . intval($_GET['user']),
    );

    $PHPShopGUI->action_select['Пользователь'] = array(
        'name' => 'Пользователь',
        'url' => '?path=shopusers&id=' . intval($_GET['user']) . '&return=dialog',
    );


    if (is_array($data)) {

        // Сообщения
        $message = viewMessage($data);
    }

    // Ответы
    $answer_list = null;
    $PHPShopOrmAnswer = new PHPShopOrm($GLOBALS['SysValue']['base']['dialog_answer']);
    $data_answer = $PHPShopOrmAnswer->select(array('*'), array('enabled' => "='1'"), array('order' => 'num'), array('limit' => 15));
    if (is_array($data_answer))
        foreach ($data_answer as $row)
            $answer_list .= '<li><a href="#" class="dialog-answer" data-content="' . str_replace('"', "", $row['message']) . '">' . $row['name'] . '</a></li>';

    if (!empty($answer_list))
        $answer_list .= '<li role="separator" class="divider"></li>';
    $answer = '<ul class="dropdown-menu">' . $answer_list . ' <li><a href="?path=dialog.answer&return=dialog&action=new"><span class="glyphicon glyphicon-plus"></span> ' . __('Добавить ответ') . '</a></li></ul>';

    $PHPShopGUI->setActionPanel(__("Диалог") . " " . $chat_name, array('Пользователь', 'Заказы пользователя', '|', 'Удалить'), array('Заказы пользователя', 'Наверх'));

    // Проверка на бан
    $PHPShopOrmUser = new PHPShopOrm($GLOBALS['SysValue']['base']['shopusers']);
    $data_user = $PHPShopOrmUser->getOne(array('*'), array('id' => "=" . intval($_GET['user'])));

    if (!empty($data_user['dialog_ban']))
        $PHPShopGUI->_CODE = $PHPShopGUI->setAlert('Диалог заблокирован', $type = 'danger');
    else
        $PHPShopGUI->_CODE = '

          <div id="message-list">' . $message . '</div>
          <div id="message-preloader"><img src="images/ajax-loader.gif" title="Загрузка"></div>
          <div class="text-muted pull-right">' . __('Удерживайте [Shift] для перевода строки') . '</div>
          <form method="post" enctype="multipart/form-data" id="product_edit" class="form-horizontal" role="form" data-toggle="validator">
          <a id="m"></a>
          <div>
          <p>
          <textarea class="form-control" name="message" id="message" placeholder="' . __('Введите текст') . '"></textarea>
          </p>
          
          <div class="row">
            <div class="col-md-6 col-xs-6">
            <a id="attachment-disp" class="text-muted" href="#f"><span class="glyphicon glyphicon-paperclip"></span> ' . __('Прикрепить файл') . '</a>
             <a id="f"></a>
             <div id="attachment" class="hide" style="max-width:70%">
             ' . $PHPShopGUI->setIcon(null, "attachment", false, array('load' => true, 'server' => true, 'url' => false, 'multi' => false, 'view' => false)) . '
             </div>
            </div>
            <div class="col-md-6 col-xs-6 text-right">

             <div class="btn-group dropup">
             <button class="btn btn-default send-message disabled" type="button"><span class="glyphicon glyphicon-send"></span> ' . __('Отправить') . '</button>
              <button type="button" class="btn btn-default dropdown-toggle " data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> <span class="caret"></span> <span class="sr-only">Toggle Dropdown</span> </button>
              ' . $answer . '
             
           </div>

           </div>
          </div>

          <br>
          </div>
          <input type="hidden" name="selectID" value="true">
          <input type="hidden" name="actionList[delID]" value="actionDelete.shopusers.edit">
          <input type="hidden" name="actionList[selectID]" value="actionReplies.shopusers.edit">
          <input type="hidden" name="chat_id" value="' . $_GET['id'] . '">
          <input type="hidden" name="user_id" value="' . intval($_GET['user']) . '">
          <input type="hidden" name="sender" value="' . $_GET['sender'] . '">
         </form>
      ';

    if (empty($_GET['search'])) {
        $class = 'none';
        $_GET['search'] = null;
    } else
        $class = null;

    // Поиск диалогов
    $search = '<div class="' . $class . '" id="dialog-search" style="padding-bottom:5px;"><div class="input-group input-sm">
                <input type="input" class="form-control input-sm" type="search" id="input-dialog-search" placeholder="' . __('Искать в диалогах...') . '" value="' . PHPShopSecurity::TotalClean($_GET['search']) . '">
                 <span class="input-group-btn">
                  <a class="btn btn-default btn-sm" id="btn-search" type="submit"><span class="glyphicon glyphicon-search"></span></a>
                 </span>
            </div></div>';

    $sidebarleft[] = array('title' => 'Пользователи', 'content' => $search . $PHPShopGUI->loadLib('tab_dialog', false, './dialog/'), 'title-icon' => '<div class="hidden-xs"><span class="glyphicon glyphicon-search" id="show-dialog-search" data-toggle="tooltip" data-placement="top" title="' . __('Поиск') . '"></span></div>');
    // Информация
    $sidebarright[] = array('id' => 'user-data-1', 'title' => 'Пользователь', 'name' => array('caption' => $data_user['name'], 'link' => '?path=shopusers&return=order.' . $data_user['id'] . '&id=' . $data_user['id'] . '&return=dialog'), 'content' => array(array('caption' => $data_user['login'], 'link' => 'mailto:' . $data_user['login']), $data_user['tel']));

    // Заказы
    $tab_order = $PHPShopGUI->loadLib('tab_order', false, './dialog/');
    if (!empty($tab_order))
        $sidebarright[] = array('title' => 'Заказы', 'content' => $tab_order);

    // Корзина
    $tab_cart = $PHPShopGUI->loadLib('tab_cart', false, './dialog/');
    if (!empty($tab_cart))
        $sidebarright[] = array('title' => 'Корзина', 'content' => $tab_cart);

    // Яндекс.Карты
    $yandex_apikey = $PHPShopSystem->getSerilizeParam("admoption.yandex_apikey");
    if (empty($yandex_apikey))
        $yandex_apikey = 'cb432a8b-21b9-4444-a0c4-3475b674a958';

    // Карта
    $mass = unserialize($data_user['data_adres']);
    if (strlen($mass['list'][$mass['main']]['street_new']) > 5) {
        $PHPShopGUI->addJSFiles('./shopusers/gui/shopusers.gui.js', '//api-maps.yandex.ru/2.0/?load=package.standard&lang=ru-RU&apikey=' . $yandex_apikey);
        $map = '<div id="map" data-geocode="' . $mass['list'][$mass['main']]['city_new'] . ', ' . $mass['list'][$mass['main']]['street_new'] . ' ' . $mass['list'][$mass['main']]['house_new'] . '"></div>';

        $sidebarright[] = array('title' => 'Адрес доставки на карте', 'content' => array($map));
    }

    $PHPShopGUI->setSidebarLeft($sidebarleft, 3);
    $PHPShopGUI->sidebarLeftCell = 3;
    $PHPShopGUI->setSidebarRight($sidebarright, 2, 'hidden-xs');
    $PHPShopGUI->Compile(false);
}

// Функция ответа 
function actionReplies() {
    global $PHPShopSystem;

    switch ($_GET['sender']) {
        case "telegram":
            $bot = new PHPShopTelegramBot();
            break;
        case "vk":
            $bot = new PHPShopVKBot();
            break;
        default: $bot = new PHPShopBot();
    }

    if (!empty($_POST['attachment'])) {

        $fileAdd = fileAdd();
        if (!empty($fileAdd))
            $_POST['attachment'] = $fileAdd;

        $file = $bot->protocol . $_SERVER['SERVER_NAME'] . $_POST['attachment'];

        // Картинка
        if (in_array(PHPShopSecurity::getExt($_POST['attachment']), array('gif', 'png', 'jpg', 'jpeg'))) {

            if (empty($_POST['message']))
                $_POST['message'] = PHPShopString::win_utf8("Картинка");

            $bot->send_image($_POST['chat_id'], $_POST['message'], $_POST['attachment']);
        }
        // Документ
        else {

            if (empty($_POST['message']))
                $_POST['message'] = PHPShopString::win_utf8("Файл");

            $bot->send_file($_POST['chat_id'], $_POST['message'], $_POST['attachment']);
        }
    }
    // Текст
    else
        $bot->send($_POST['chat_id'], $_POST['message']);


    $insert = array(
        'user_id' => $_POST['user_id'],
        'chat' => array
            (
            'id' => $_POST['chat_id'],
            'first_name' => $_SESSION['namePHPSHOP'],
            'last_name' => "",
        ),
        'date' => time(),
        'text' => $_POST['message'],
        'staffid' => 0,
        'attachments' => $file,
        'bot' => $_GET['sender'],
        'isview' => 0,
        'isview_user' => 0
    );

    $bot->dialog($insert);

    if (!empty($_POST['message']) and $_GET['sender'] == 'message') {
        PHPShopObj::loadClass("user");
        $PHPShopUser = new PHPShopUser($_POST['user_id']);
        $title = __('Новый ответ в диалоге') . ' - ' . $PHPShopSystem->getName();
        $PHPShopMail = new PHPShopMail($PHPShopUser->getLogin(), $PHPShopSystem->getEmail(), $title, '', true, true);
        $text = PHPShopString::utf8_win1251('<b>' . __('Администрация') . '</b>: ' . $_POST['message']) . ' ' . preg_replace("~(http|https|ftp|ftps)://(.*?)(\s|\n|[,.?!](\s|\n)|$)~", '<a href="$1://$2" target="_blank">$1://$2</a>$3', $file) . '<div><br>' . __('Вы можете ответить нам в') . ' <a href="' . $bot->protocol . $_SERVER['SERVER_NAME'] . $GLOBALS['SysValue']['dir']['dir'] . '/users/message.html" target="_blank">' . __('Личном кабинете') . '</a></div> ';
        PHPShopParser::set('content', $text);
        $content = PHPShopParser::file('tpl/sendmail.mail.tpl', true, false);
        $PHPShopMail->sendMailNow($content);
    }

    header("Content-Type: application/json");
    exit(json_encode(array('success' => 1)));
}

// Добавление файла
function fileAdd() {
    global $PHPShopSystem;

    // Папка сохранения
    $path = $GLOBALS['SysValue']['dir']['dir'] . '/UserFiles/Image/' . $PHPShopSystem->getSerilizeParam('admoption.image_dialog_path');

    // Копируем от пользователя
    if (!empty($_FILES['file']['name'])) {
        $_FILES['file']['ext'] = PHPShopSecurity::getExt($_FILES['file']['name']);
        $_FILES['file']['name'] = PHPShopString::toLatin(str_replace('.' . $_FILES['file']['ext'], '', PHPShopString::utf8_win1251($_FILES['file']['name']))) . '.' . $_FILES['file']['ext'];
        if (!empty($_FILES['file']['ext'])) {
            if (move_uploaded_file($_FILES['file']['tmp_name'], $_SERVER['DOCUMENT_ROOT'] . $GLOBALS['dir']['dir'] . $path . $_FILES['file']['name'])) {
                $file = $GLOBALS['dir']['dir'] . $path . $_FILES['file']['name'];
            }
        } else
            $file = 'Error_PHP_ext';
    }

    if (empty($file))
        $file = '';

    return $file;
}

/**
 * Список сообщений
 */
function viewMessage($data, $ajax = false) {
    global $chat_ids, $chat_name;


    $message = null;
    if (is_array($data)) {
        foreach ($data as $row) {

            if (empty($row['message']) and empty($row['attachments']))
                continue;

            if (empty($row['isview']) and empty($ajax))
                continue;

            if (strlen($row['name']) < 5)
                $row['name'] = 'User' . $row['user_id'];

            $chat_ids[] = $row['id'];

            // Ссылки
            $row['message'] = preg_replace("~(http|https|ftp|ftps)://(.*?)(\s|\n|[,.?!](\s|\n)|$)~", '<a href="$1://$2" target="_blank">$1://$2</a>$3', $row['message']);

            // Файлы
            if (!empty($row['attachments'])) {

                $url = parse_url($row['attachments']);

                if (in_array(PHPShopSecurity::getExt($url['path']), array('gif', 'png', 'jpg', 'jpeg'))) {
                    $flist = '<div class="col-xs-6 col-md-6">
                             <a href="' . $row['attachments'] . '" class="thumbnail" target="_blank" title="' . $row['attachments'] . '"><img src="' . $row['attachments'] . '" alt="" ></a></div>';
                } else {
                    $path = pathinfo($row['attachments']);
                    $path = parse_url($path['basename']);

                    $flist = '<div class="col-xs-12 col-md-12"><a title="" target="_blank" href="' . $row['attachments'] . '"><span class="glyphicon glyphicon-paperclip"></span> ' . $path['path'] . '</a></div>';
                }
            } else
                $flist = null;

            if (empty($row['staffid'])) {

                $message .= '
             <div class="incoming_msg">
              <div class="received_msg">
                <div class="received_withd_msg">
                   <span class="time_date">' . $row['name'] . ': ' . PHPShopDate::get($row['time'], true) . '</span>
                    <p>' . nl2br($row['message']) . '</p>
                    <span class="time_date"><div class="row">' . $flist . '</div></span>
                 </div>
              </div>
            </div>';
            } else {
                $chat_name = '<a href="?path=shopusers&id=' . $row['user_id'] . '&return=dialog"><img src="../lib/templates/messenger/' . $row['bot'] . '.svg" title="' . ucfirst($row['bot']) . '" class="bot-icon">' . $row['name'] . '</a>';
                $message .= '
            <div class="outgoing_msg">
              <div class="sent_msg">
                <span class="time_date text-right">' . $row['name'] . ': ' . PHPShopDate::get($row['time'], true) . '</span>
                <p>' . nl2br($row['message']) . '</p>
                <span class="time_date"><div class="row">' . $flist . '</div></span>
               </div>
            </div>';
            }
        }
    }
    return $message;
}

/**
 * Счетчик новых диалогов
 */
function actionGetNew() {
    global $PHPShopOrm;

    $PHPShopOrm->debug = false;
    $data = $PHPShopOrm->select(array('*'), array('chat_id' => "=" . intval($_GET['id']), 'isview' => "='0'", 'bot' => '="' . PHPShopSecurity::TotalClean($_GET['sender']) . '"'), array('order' => 'id'), array('limit' => 500));

    // Сообщения
    if (is_array($data)) {
        $message = viewMessage($data, true);
    }

    if (!empty($message) and is_array($GLOBALS['chat_ids'])) {
        $PHPShopOrm->update(array('isview_new' => 1), array('id' => ' IN (' . implode(',', $GLOBALS['chat_ids']) . ')'));
    }

    if (!empty($message)) {
        $count = count($data);
    } else {
        $count = 0;
        $message = null;
    }

    header("Content-Type: application/json");
    exit(json_encode(array('success' => 1, 'num' => intval($count), 'message' => PHPShopString::win_utf8($message))));
}

// Функция удаления
function actionDelete() {
    global $PHPShopOrm, $PHPShopModules;

    // Перехват модуля
    $PHPShopModules->setAdmHandler(__FILE__, __FUNCTION__, $_POST);

    $action = $PHPShopOrm->delete(array('chat_id' => '=' . $_POST['chat_id']));
    return array("success" => $action);
}

// Обработка событий
$PHPShopGUI->getAction();

// Вывод формы при старте
$PHPShopGUI->setAction($_GET['id'], 'actionStart', 'none');
?>