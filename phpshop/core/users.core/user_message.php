<?php

function sendMessage($obj) {
    PHPShopObj::loadClass('bot');
    $bot = new PHPShopBot();

    $insert = array(
        'user_id' => $obj->UsersId,
        'chat' => array
            (
            'id' => $obj->UsersId,
            'first_name' => $obj->UserName,
            'last_name' => "",
        ),
        'date' => time(),
        'text' => $_POST['message'],
        'staffid' => 1,
        'isview' => $_SESSION['UsersBan'],
        'isview_user' => 0,
        'ai' => 0
    );

    $bot->dialog($insert);
    $bot->notice($insert, 'message');
    $bot->ai($insert);
}

/**
 * Сообщения менеджеру из личного кабинета пользователя
 * @return string
 */
function user_message($obj) {


    // Сообщение менеджеру
    if (!empty($_POST['message'])) {
        sendMessage($obj);
    }


    $disp = '<link href="phpshop/lib/templates/users/dialog.css" rel="stylesheet">'
            . '<script src="phpshop/lib/templates/users/dialog.js"></script>'
            . '<audio id="play-chat" src="phpshop/lib/templates/users/dialog.mp3"></audio>';


    $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['dialog']);
    $PHPShopOrm->debug = false;
    $data = $PHPShopOrm->select(array('*'), array('bot' => '="message"', 'chat_id' => '=' . $obj->UsersId), array('order' => 'id'), array('limit' => 500));

    if (is_array($data)) {

        if (count($data) > 5)
            $obj->set('editor', "
        <script>$('html').animate({scrollTop: Number($('#m').offset().top)-100})</script>", true);

        // Сообщения
        $message = viewMessage($data);
    }

    if ($obj->PHPShopSystem->ifSerilizeParam('admoption.telegram_enabled', 1)) {
        $bot = $obj->PHPShopSystem->getSerilizeParam('admoption.telegram_bot');
        $telegram_enabled =null;
        $telegram_path = 'https://telegram.me/' . $bot . '?start=' . $_SESSION['UsersBot'];
    } else{
        $telegram_enabled='hidden d-none';
        $telegram_path = null;
    }

    if ($obj->PHPShopSystem->ifSerilizeParam('admoption.vk_enabled', 1)) {
        $bot = $obj->PHPShopSystem->getSerilizeParam('admoption.vk_bot');
        $vk_path= 'https://vk.me/' . $bot . '?ref=' . $_SESSION['UsersBot'];
        $vk_enabled = null;
    } else{
        $vk_enabled= 'hidden d-none';
        $vk_path=null;
    }

    $disp .= '
          <div id="message-list">' . $message . '</div>
          <form method="post" enctype="multipart/form-data" id="message-edit" class="form-horizontal" role="form" data-toggle="validator">
          <a id="m"><br></a>
          <div>
          <p>
          <textarea class="form-control" name="message" id="message" placeholder="' . __('Введите текст') . '" required></textarea>
          </p>
          <p class="clearfix">
             <button class="btn btn-primary pull-right send-message" type="button"><span class="glyphicon glyphicon-send"></span> ' . __('Отправить сообщение') . '</button>
             <a class="btn btn-default pull-right '.$telegram_enabled.' btn-telegram" href="'.$telegram_path.'" target="_blank" title="'.__('Открыть чат в').' Telegram"><img src="phpshop/lib/templates/messenger/telegram.svg" width="20" height="20" alt=" "></a>
             <a class="btn btn-default pull-right '.$vk_enabled.' btn-vk"  href="'.$vk_path.'" target="_blank" title="'.__('Открыть чат в').' VK"><img src="phpshop/lib/templates/messenger/vk.svg" width="20" height="20" alt=" "></a>
          </p>
          </div>
         </form>
         
      ';

    $obj->set('formaTitle', __('Связь с менеджерами'));
    $obj->set('formaContent', $disp);
    $obj->ParseTemplate($obj->getValue('templates.users_page_list'));
}

/**
 * Список сообщений
 */
function viewMessage($data) {
    global $chat_ids;

    $message=null;
    if (is_array($data)) {
        foreach ($data as $row) {

            if (empty($row['message']) and empty($row['attachments']))
                continue;

            $chat_ids[] = $row['id'];

            // Ссылки
            $row['message'] = preg_replace("~(http|https|ftp|ftps)://(.*?)(\s|\n|[,.?!](\s|\n)|$)~", '<a href="$1://$2" target="_blank">$1://$2</a>$3', $row['message']);

            // Файлы
            if (!empty($row['attachments'])) {

                if (in_array(PHPShopSecurity::getExt($row['attachments']), array('gif', 'png', 'jpg', 'jpeg'))) {
                    $flist = '<div class="col-xs-6 col-md-6">
                             <a href="' . $row['attachments'] . '" class="thumbnail" target="_blank" title="' . $row['attachments'] . '"><img src="' . $row['attachments'] . '" alt="" class="img-responsive img-fluid"></a></div>';
                } else {
                    $path = pathinfo($row['attachments']);
                    $flist = '<div class="col-xs-12 col-md-12"><a title="" target="_blank" href="' . $row['attachments'] . '"><span class="glyphicon glyphicon-paperclip"></span> ' . $path['basename'] . '</a></div>';
                }
            } else
                $flist = null;

            if (!empty($row['staffid'])) {
                $message .= '
             <div class="incoming_msg">
              <div class="received_msg">
                <div class="received_withd_msg">
                   <span class="time_date">' . PHPShopDate::get($row['time'], true) . '</span>
                    <p>' . nl2br($row['message']) . '</p>
                    <span class="time_date"><div class="row">' . $flist . '</div></span>
                 </div>
              </div>
            </div>';
            } else {
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

?>