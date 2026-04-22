<?php

/**
 * Список сообщений
 */
function viewMessage($data) {
    global $chat_ids;

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
                             <a href="' . $row['attachments'] . '" class="thumbnail" target="_blank" title="' . $row['attachments'] . '"><img src="' . $row['attachments'] . '" alt="" ></a></div>';
                } else {
                    $path = pathinfo($row['attachments']);
                    $flist = '<div class="col-xs-12 col-md-12"><a title="" target="_blank" href="' . $row['attachments'] . '"><span class="glyphicon glyphicon-paperclip"></span> ' . $path['basename'] . '</a></div>';
                }
            } else
                $flist = null;

            if (empty($row['staffid'])) {
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

function tab_bot($option) {

    $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['dialog']);
    $data = $PHPShopOrm->select(array('chat_id,id,message,name,time,bot,user_id'), array('staffid' => "='1'", 'user_id' => "=" . $option['user']), array('group' => 'chat_id desc'), array('limit' => 500));
    $tab = '<ul class="nav nav-pills nav-stacked" style="width:300px">';

    if (is_array($data))
        foreach ($data as $row) {

            $PHPShopOrm->debug = false;
            $data_chat = $PHPShopOrm->select(array('chat_id,id,message,name,time,bot,user_id'), array('staffid' => "='1'", 'isview' => "='0'", 'chat_id' => '=' . $row['chat_id']), array('order' => 'id desc'), array('limit' => 500));
            
            if(is_array($data_chat))
            $count = count($data_chat);

            if (!empty($count))
                $badge = '<span class="badge pull-right">' . $count . '</span>';
            else
                $badge = null;

            if (!empty($data_chat['message']))
                $message = '<div style="padding-top:5px"><span class="text-muted">' . substr($data_chat['message'], 0, 20) . '</span><span class="pull-right text-muted">' . PHPShopDate::get($row['time'], false, false, '.') . '</span></div>';
            else
                $message = null;


            $tab .= '<li><a href="?path=dialog&id=' . $row['chat_id'] . '&sender=' . $row['bot'] . '&user=' . $row['user_id'] . '&return=order.' . $_GET['id'] . '"><img src="../lib/templates/messenger/' . $row['bot'] . '.svg" title="' . ucfirst($row['bot']) . '" class="bot-icon">' . $row['name'] . $badge . $message . '</a></li>';
        }
    $tab .= '</ul>';

    return $tab;
}

function tab_dialog($data, $option) {
    global $PHPShopGUI;

    $PHPShopGUI->addCSSFiles('./css/support.css');
    $PHPShopGUI->addJSFiles('./js/jquery.waypoints.min.js', './dialog/gui/dialog.gui.js');

    $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['dialog']);
    $PHPShopOrm->debug = false;
    $data_bot = $PHPShopOrm->select(array('bot'), array('user_id' => "=" . intval($data['user'])), array('group' => 'bot'), array('limit' => 500));

    // Несколько чатов
    if (is_array($data_bot) and count($data_bot) > 1) {

        $message = tab_bot($data);
    } else {
        $data_chat = $PHPShopOrm->select(array('*'), array('user_id' => "=" . intval($data['user'])), array('order' => 'id'), array('limit' => 500));

        if (is_array($data_chat)) {

            // Сообщения
            $message = viewMessage($data_chat);

            $message = '
          <div id="message-list">' . $message . '</div>
          <div class="text-muted pull-right">' . __('Удерживайте [Shift] для перевода строки') . '</div>
          <form method="post" enctype="multipart/form-data" id="message-edit" class="form-horizontal" role="form" data-toggle="validator">
          <a id="m"></a>
          <p>
          <textarea class="form-control" name="message" id="message" placeholder="' . __('Введите текст') . '"></textarea>
          </p>
          <p>
             <button class="btn btn-default pull-right send-message disabled" type="button"><span class="glyphicon glyphicon-send"></span> ' . __('Отправить сообщение') . '</button>
             <a id="attachment-disp" class="text-muted" href="#f"><span class="glyphicon glyphicon-paperclip"></span> ' . __('Прикрепить файл') . '</a>
             <a id="f"></a>
             <div id="attachment" class="hide" style="max-width:70%">
             ' . $PHPShopGUI->setIcon(null, "attachment", false, array('load' => true, 'server' => true, 'url' => false, 'multi' => false, 'view' => false)) . '
             </div>
          </p><br>
          <input type="hidden" name="selectID" value="true">
          <input type="hidden" name="actionList[selectID]" value="actionReplies">
          <input type="hidden" name="chat_id" value="' . $data_chat[0]['chat_id'] . '">
          <input type="hidden" name="user_id" value="' . $data['user'] . '">
          <input type="hidden" name="sender" value="' . $data_chat[0]['bot'] . '">
         </form>';
        }
    }

    return $message;
}

?>