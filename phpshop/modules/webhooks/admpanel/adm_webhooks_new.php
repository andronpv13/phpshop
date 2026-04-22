<?php
PHPShopObj::loadClass('order');
$PHPShopOrm = new PHPShopOrm($PHPShopModules->getParam("base.webhooks.webhooks_forms"));

// Начальная функция загрузки
function actionStart() {
    global $PHPShopGUI, $_classPath;
    
    $data['enabled']=1;
    $data['name']='Новый WebHook';
    $data['send']=0;
    $data['type']=0;

    // Доступые статусы заказов
    $PHPShopOrderStatusArray = new PHPShopOrderStatusArray();
    $OrderStatusArray = $PHPShopOrderStatusArray->getArray();
    $order_status_value[] = array(__('Новый заказ'), 0, $data['create_order_status']);
    if (is_array($OrderStatusArray))
        foreach ($OrderStatusArray as $order_status)
            $order_status_value[] = array($order_status['name'], $order_status['id'], $data['create_order_status']);
    
   
    // Метод
    $send_value[]=array('POST',0,$data['send']);
    $send_value[]=array('GET',1,$data['send']);
    
    // Действие
    include_once($_classPath . 'modules/webhooks/class/webhooks.class.php');
    $PHPShopWebhooks = new PHPShopWebhooks();
    $type_array=$PHPShopWebhooks->getType();
    foreach($type_array as $k=>$type)
         $type_value[]=array($type,$k,$data['type']);

    $Tab1 =$PHPShopGUI->setField('Название', $PHPShopGUI->setInputText('', 'name_new', $data['name'], 400));
    $Tab1 .= $PHPShopGUI->setField('Статус', $PHPShopGUI->setRadio("enabled_new", 1, "Показывать", $data['enabled']) . $PHPShopGUI->setRadio("enabled_new", 0, "Скрыть", $data['enabled']));
    $Tab1 .= $PHPShopGUI->setField("URL WebHook", $PHPShopGUI->setInputText(false, 'url_new', $data['url'], 400));
    //$Tab1 .= $PHPShopGUI->setField('Передача при статусе:', $PHPShopGUI->setSelect('create_order_status_new', $order_status_value, 400));
    $Tab1 .= $PHPShopGUI->setField('Действие', $PHPShopGUI->setSelect('type_new', $type_value, 400));
    $Tab1 .= $PHPShopGUI->setField('Метод передачи', $PHPShopGUI->setSelect('send_new', $send_value, 100));
    
    // Вывод формы закладки
    $PHPShopGUI->setTab(array("Основное", $Tab1,true));

    // Вывод кнопок сохранить и выход в футер
    $ContentFooter =  $PHPShopGUI->setInput("submit", "saveID", "Сохранить", "right", false, false, false, "actionInsert.modules.create");

    $PHPShopGUI->setFooter($ContentFooter);
    return true;
}

// Функция записи
function actionInsert() {
    global $PHPShopOrm;
    
    $action = $PHPShopOrm->insert($_POST);
    header('Location: ?path=' . $_GET['path']);
    return $action;
}

// Обработка событий
$PHPShopGUI->getAction();

// Вывод формы при старте
$PHPShopGUI->setLoader($_POST['saveID'], 'actionStart');
?>