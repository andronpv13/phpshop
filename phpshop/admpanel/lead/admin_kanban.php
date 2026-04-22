<?php

$TitlePage = __("Канбан доска");

function actionStart() {
    global $PHPShopGUI, $PHPShopSystem, $TitlePage, $PHPShopBase;

    // Статусы заказов
    PHPShopObj::loadClass('order');
    $PHPShopOrderStatusArray = new PHPShopOrderStatusArray();
    $status_array = $PHPShopOrderStatusArray->getArray();
    $status[] = __('Новый заказ');
    
    if(empty($_GET['where']['statusi']) )
        $_GET['where']['statusi']=null;
    
    $order_status_value[] = array(__('Новый заказ'), 0, $_GET['where']['statusi']);
    if (is_array($status_array))
        foreach ($status_array as $status_val) {

            $status[$status_val['id']] = substr($status_val['name'], 0, 22);
            $order_status_value[] = array($status_val['name'], $status_val['id'], $_GET['where']['statusi']);
        }

    if (!isset($_GET['where']['statusi']))
        $_GET['where']['statusi'] = 'none';
    $order_status_value[] = array(__('Все заказы'), 'none', $_GET['where']['statusi']);

    // Знак рубля
    if ($PHPShopSystem->getDefaultValutaIso() == 'RUB' or $PHPShopSystem->getDefaultValutaIso() == 'RUR')
        $currency = ' <span class="rubznak hidden-xs">p</span>';
    else
        $currency = $PHPShopSystem->getDefaultValutaCode();


    $PHPShopGUI->action_button['Добавить заказ'] = array(
        'name' => '',
        'action' => 'addNew',
        'class' => 'btn btn-default btn-sm navbar-btn',
        'type' => 'button',
        'icon' => 'glyphicon glyphicon-plus',
        'tooltip' => 'data-toggle="tooltip" data-placement="left" title="' . __('Добавить заказ') . '" '
    );
    
    $PHPShopGUI->action_button['Список'] = array(
        'name' => '',
        'class' => 'btn btn-default btn-sm navbar-btn btn-action-panel hidden-xs',
        'type' => 'button',
        'action' => 'lead',
        'icon' => 'glyphicon glyphicon-align-justify',
        'tooltip' => 'data-toggle="tooltip" data-placement="bottom" title="' . __('Список') . '" '
    );
    
    $PHPShopGUI->action_button['Канбан'] = array(
        'name' => '',
        'class' => 'btn btn-default btn-sm navbar-btn active hidden-xs',
        'type' => 'button',
        'icon' => 'glyphicon glyphicon-th-large',
        'tooltip' => 'data-toggle="tooltip" data-placement="left" title="' . __('Канбан') . '" '
    );
    
    $PHPShopGUI->action_button['Добавить статус'] = array(
        'name' => '',
        'action' => 'order.status&action=new&return=lead.kanban',
        'class' => 'btn btn-default btn-sm navbar-btn btn-action-panel hidden-xs',
        'type' => 'button',
        'icon' => 'glyphicon glyphicon-plus',
        'tooltip' => 'data-toggle="tooltip" data-placement="bottom" title="'.__('Добавить статус').'" '
    );

    $PHPShopGUI->setActionPanel($TitlePage, false, array('Канбан','Список','Добавить статус'));
    $PHPShopGUI->addJSFiles('./js/bootstrap-datetimepicker.min.js', './lead/gui/lead.gui.js','./js/jkanban.min.js');
    $PHPShopGUI->addCSSFiles('./css/bootstrap-datetimepicker.min.css','./css/jkanban.min.css');


    if (isset($_GET['date_start']))
        $date_start = $_GET['date_start'];
    else
        $date_start = PHPShopDate::get(time() - 2592000);

    if (isset($_GET['date_end']))
        $date_end = $_GET['date_end'];
    else
        $date_end = PHPShopDate::get(time() - 1);

    // Статусы пользователей
    PHPShopObj::loadClass('user');
    $PHPShopUserStatus = new PHPShopUserStatusArray();
    $PHPShopUserStatusArray = $PHPShopUserStatus->getArray();
    
     if(empty($_GET['where']['b.status']) )
        $_GET['where']['b.status']=null;
    
    $user_status_value[] = array(__('Все пользователи'), '', $_GET['where']['b.status']);
    if (is_array($PHPShopUserStatusArray))
        foreach ($PHPShopUserStatusArray as $user_status)
            $user_status_value[] = array($user_status['name'], $user_status['id'], $_GET['where']['b.status']);

    // Менеджеры
    if ($PHPShopBase->Rule->CheckedRules('order', 'rule')) {
        $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['users']);
        $data_manager = $PHPShopOrm->select(array('*'), array('enabled' => "='1'", 'id' => '!=' . $_SESSION['idPHPSHOP']), array('order' => 'id DESC'), array('limit' => 100));
        $manager_status_value[] = array(__('Все менеджеры'), '', '');
        if (is_array($data_manager))
            foreach ($data_manager as $manager_status)
                $manager_status_value[] = array($manager_status['name'], $manager_status['id'], $_GET['where']['b.status']);
    }

    // Витрины
    $PHPShopServerOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['servers']);
    $data_server = $PHPShopServerOrm->select(array('*'), array('enabled' => "='1'"), false, array('limit' => 1000));

    if (is_array($data_server)) {
        $server_value[] = array(__('Все витрины'), 'none', 'none');
        foreach ($data_server as $row) {
            $server_value[] = array(PHPShopString::check_idna($row['host'], true), $row['id'], 0);
        }
    }
    $PHPShopGUI->_CODE='<div id="data_wrapper" class="row">
          <div class="col-sm-12">
          </div>
        </div>
        <div id="kanban-wrapper">
            <div class="kanban-wrapper-container">&nbsp;
           </div>
        </div>
        <div id="kanban"></div>';

    $PHPShopGUI->Compile();
}


// Обработка событий
$PHPShopInterface->getAction();
?>