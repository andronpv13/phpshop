<?php

$TitlePage = __("Диалоги");

function actionStart() {
    global $TitlePage, $PHPShopInterface;

    $PHPShopInterface->action_button['Добавить ответ'] = array(
        'name' => '',
        'action' => 'dialog.answer&return=dialog&action=new',
        'class' => 'btn btn-default btn-sm navbar-btn btn-action-panel',
        'type' => 'button',
        'icon' => 'glyphicon glyphicon-plus',
        'tooltip' => 'data-toggle="tooltip" data-placement="left" title="' . __('Добавить ответ') . '"'
    );

    $PHPShopInterface->path = 'dialog.answer&return=dialog';
    $PHPShopInterface->setActionPanel($TitlePage, array('Удалить выбранные'), array('Добавить ответ'));
    $PHPShopInterface->addJSFiles('./dialog/gui/dialog.gui.js');

    if (PHPShopString::is_mobile()) {
        $PHPShopInterface->sort_action = false;
        $PHPShopInterface->mobile = true;
    }

    if (empty($_GET['search'])){
        $class = 'none';
        $_GET['search']=null;
    }
    else
        $class = null;

    // Поиск диалогов
    $search = '<div class="' . $class . '" id="dialog-search" style="padding-bottom:5px;"><div class="input-group input-sm">
                <input type="input" class="form-control input-sm" type="search" id="input-dialog-search" placeholder="' . __('Искать в диалогах...') . '" value="' . PHPShopSecurity::TotalClean($_GET['search']) . '">
                 <span class="input-group-btn">
                  <a class="btn btn-default btn-sm" id="btn-search" type="submit"><span class="glyphicon glyphicon-search"></span></a>
                 </span>
            </div></div>';

    $sidebarleft[] = array('title' => 'Пользователи', 'content' => $search . $PHPShopInterface->loadLib('tab_dialog', false, './dialog/'), 'title-icon' => '<div class="hidden-xs"><span class="glyphicon glyphicon-search" id="show-dialog-search" data-toggle="tooltip" data-placement="top" title="' . __('Поиск') . '"></span></div>');
    $PHPShopInterface->setSidebarLeft($sidebarleft, 3);
    $PHPShopInterface->sidebarLeftCell = 3;


    $view = array(null, '<span class="glyphicon glyphicon-ok"></span>', '<span class="glyphicon glyphicon-remove text-muted"></span>');
    $PHPShopInterface->setCaption(array(null, "3%"), array("Варианты ответов", "70%"), array("Чат", "10%"), array("", "10%"), array("Статус", "10%", array('align' => 'right')));

    // Таблица с данными
    $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['dialog_answer']);
    $PHPShopOrm->debug = false;
    $data = $PHPShopOrm->select(array('*'), array(), array('order' => 'id DESC'), array('limit' => 1000));
    if (is_array($data))
        foreach ($data as $row) {

            $PHPShopInterface->setRow($row['id'], array('name' => $row['name'], 'link' => '?path=dialog.answer&return=dialog&id=' . $row['id'], 'align' => 'left'), array('name' => $view[intval($row['view'])]), array('action' => array('edit', '|', 'delete', 'id' => $row['id']), 'align' => 'center'), array('status' => array('enable' => $row['enabled'], 'align' => 'right', 'caption' => array('Выкл', 'Вкл'))));
        }



    $PHPShopInterface->Compile(3);
}

/**
 * Счетчик новых диалогов
 */
function actionGetNew() {
    global $PHPShopBase;
    header("Content-Type: application/json");
    exit(json_encode(array('success' => 1, 'num' => $PHPShopBase->getNumRows('dialog', "where isview='0'"))));
}

// Обработка событий
$PHPShopInterface->getAction();
?>