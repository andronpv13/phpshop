<?php

$TitlePage = __("Список задач");
unset($_SESSION['jsort']);

function actionStart() {
    global $PHPShopInterface, $PHPShopSystem, $TitlePage, $PHPShopBase;

    // Статусы заказов
    PHPShopObj::loadClass('order');
    $PHPShopOrderStatusArray = new PHPShopOrderStatusArray();
    $status_array = $PHPShopOrderStatusArray->getArray();
    $status[] = __('Новый заказ');
    
    if(empty($_GET['where']['statusi']))
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


    $PHPShopInterface->action_button['Список'] = array(
        'name' => '',
        'class' => 'btn btn-default btn-sm navbar-btn active',
        'type' => 'button',
        'icon' => 'glyphicon glyphicon-align-justify',
        'tooltip' => 'data-toggle="tooltip" data-placement="bottom" title="' . __('Список') . '" '
    );
    
    $PHPShopInterface->action_button['Канбан'] = array(
        'name' => '',
        'class' => 'btn btn-default btn-sm navbar-btn btn-action-panel',
        'type' => 'button',
        'action' => 'lead.kanban',
        'icon' => 'glyphicon glyphicon-th-large',
        'tooltip' => 'data-toggle="tooltip" data-placement="left" title="' . __('Канбан') . '" '
    );
    
        $PHPShopInterface->action_button['Добавить событие'] = array(
        'name' => '',
        'action' => 'lead.kanban&action=new&return=lead',
        'class' => 'btn btn-default btn-sm navbar-btn btn-action-panel',
        'type' => 'button',
        'icon' => 'glyphicon glyphicon-plus',
        'tooltip' => 'data-toggle="tooltip" data-placement="bottom" title="'.__('Добавить событие').'" '
    );

    $PHPShopInterface->setActionPanel($TitlePage, false, array('Канбан','Список','Добавить событие'));


    $PHPShopInterface->checkbox_action = false;
    $PHPShopInterface->setCaption( 
            array("№", "10%", array('align' => 'left')),
            array("Статус", "15%"),
            array("Дата", "10%"), 
            array("Имя", "25%"), 
            array("Телефон", "15%",array('align'=>'right')));

    $PHPShopInterface->addJSFiles('./js/bootstrap-datetimepicker.min.js', './lead/gui/lead.gui.js');
    $PHPShopInterface->addCSSFiles('./css/bootstrap-datetimepicker.min.css');

    $PHPShopInterface->Compile();
}


// Обработка событий
$PHPShopInterface->getAction();
?>