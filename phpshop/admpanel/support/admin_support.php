<?php

$TitlePage = __("Техподдержка");

function actionStart() {
    global $PHPShopInterface, $TitlePage;

    $licFile = PHPShopFile::searchFile('../../license/', 'getLicense', true);
    $License = parse_ini_file_true("../../license/" . $licFile, 1);

    if ($License['License']['SupportExpires'] < time() or $License['License']['DomenLocked'] == 'No')
        $action = 'noSupport';
    else
        $action = 'addNew';

    $PHPShopInterface->action_button['Новая заявка'] = array(
        'name' => __('Новая заявка в техподдержку'),
        'action' => $action,
        'class' => 'btn btn-primary btn-sm navbar-btn',
        'type' => 'button',
        'icon' => 'glyphicon glyphicon-plus',
        'tooltip' => 'data-toggle="tooltip" data-placement="left" title="' . __('Новая заявка в техподдержку') . '"'
    );

    $PHPShopInterface->setActionPanel($TitlePage, false, array('Новая заявка'));
    $PHPShopInterface->checkbox_action = false;
    $PHPShopInterface->addJSFiles('./support/gui/support.gui.js');
    $PHPShopInterface->setCaption(array("Заголовок", "60%"), array("№", "10%", array('align' => 'left')), array("Дата", "15%", array('align' => 'center')), array("Статус", "15%", array('align' => 'right')));


    PHPShopObj::loadClass('xml');
    $path = 'https://help.phpshop.ru/base-xml-manager/search/xml.php?s=' . $License['License']['Serial'] . '&u=' . $License['License']['DomenLocked'];

    if (function_exists('curl_init')) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $path);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        $data = curl_exec($ch);
        curl_close($ch);
        $dataArray = readDatabase($data, 'row', false);
    } else {
        $dataArray = readDatabase($path, "row");
    }


    $status_array = array(
        0 => '<span>'.__('Новая заявка').'</span>',
        1 => '<span class="text-warning">'.__('Ожидание ответа').'</span>',
        2 => '<span class="text-success">'.__('Есть ответ').'</span>',
        3 => '<span class="text-muted">'.__('Выполнено').'</span>',
    );

    if (is_array($dataArray))
        foreach ($dataArray as $row) {

            $PHPShopInterface->setRow(array('name' => __($row['subject']), 'link' => '?path=' . $_GET['path'] . '&id=' . $row['id'] . '#m', 'align' => 'left'), array('name' => $row['id'], 'align' => 'left'), array('name' => $row['lastchange'], 'align' => 'center'), $status_array[$row['status']]);
        }

    $PHPShopInterface->Compile();
}

?>