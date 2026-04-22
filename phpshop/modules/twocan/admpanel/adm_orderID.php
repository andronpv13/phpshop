<?php

function twocan($data) {
    global $PHPShopGUI,$PHPShopModules;

    // Проверка способа оплаты
    $orders = unserialize($data['orders']);
    

    if($orders['Person']['order_metod']  == 10028){
        $Tab1 .= '';
        $PHPShopOrder = new PHPShopOrderFunction($data['id'], $data);
       

        // SQL
        $PHPShopOrm = new PHPShopOrm($PHPShopModules->getParam("base.twocan.twocan_log"));        

        // Выборка логов
        $log = $PHPShopOrm->getList(array('*'), array("order_id=" => "'$data[uid]'"), array('order' => 'date DESC'));

        // SQL Заказов
        $PHPShopOrmTwocanOrders = new PHPShopOrm($PHPShopModules->getParam("base.twocan.twocan_orders"));

        // Выборка заказа
        $order = $PHPShopOrmTwocanOrders->getOne(array('*'), array("id=" => "'$data[uid]'"));

        // Знак рубля
        $currency = $PHPShopOrder->default_valuta_code;

        // Персональная скидка 
        $orderData = unserialize($data['orders']);
        $discount = $orderData['Person']['discount']/100;


        if(in_array($order['status'], ['authorized']))
            $Tab1 .= $PHPShopGUI->setInput("submit", "charge", "Списать " . number_format($data['sum'], 2, '.', ' '). ' ' . $currency, "center", null, "", "btn-sm btn-success", "twocanActionCharge") . '&nbsp;';
       
        // Выводи кнопку запрос статуса заказа
        $Tab1 .= $PHPShopGUI->setInput("submit", "check", "Проверить статус", "center", null, "", "btn-sm", "twocanActionCheck") . '&nbsp;';

        $couldRefund =  (float)$order['charged'] - (float)$order['refunded'];

        // Выводим кнопку возврата, если еще есть, что возвращать
        if(in_array($order['status'], ['charged', 'refunded']) && ($couldRefund > 0))
            $Tab1 .= $PHPShopGUI->setInput("submit", "refund", "Полный возврат (". number_format($couldRefund, 2, '.', ' '). ' ' . $currency .")", "center", null, "", "btn-sm btn-danger", "twocanActionRefund");

        $couldRefundPart =  (float)$order['charged'] - (float)$order['refunded'] - (float)$data['sum'] ;

        // Выводим кнопку частичного возврата, если заказ изменился и сумма списанных средств больше суммы заказа
        if(in_array($order['status'], ['charged', 'refunded']) && ($couldRefundPart > 0))
            $Tab1 .=  '&nbsp;'.$PHPShopGUI->setInput("submit", "refundpart", "Частичный возврат (" . number_format($couldRefundPart, 2, '.', ' '). ' ' . $currency.")", "center", null, "", "btn-sm btn-warning", "twocanActionRefundpart");

        // Выводим кнопку для чарджа, если включена двухстадийная оплата, и средства все еще заходированы
        if(in_array($order['status'], ['authorized']))
            $Tab1 .= $PHPShopGUI->setInput("submit", "reverse", "Отмена авторизации", "center", null, "", "btn-sm btn-danger", "twocanActionReverse");
                
        $PHPShopInterfaceOrder = new PHPShopInterface();
        $PHPShopInterfaceOrder->checkbox_action = false;

        // Выводим информацию по заказа полученную от iBox
        $PHPShopInterfaceOrder->setCaption(array("Номер заказа 2can&ibox", "30%"),  array("Статус", "15%"), array("Сумма заказа", "20%"), array("Авторизовано", "15%"), array("Возвращено", "15%"));

        if (is_array($order))
           $PHPShopInterfaceOrder->setRow(array('name' => $order['twocanid']), $order['status'],number_format($order['amount'], 2, '.', ' '),number_format($order['charged'], 2, '.', ' '),number_format($order['refunded'], 2, '.', ' ') );    

        $Tab1 .= '<hr><table class="table table-hover">'.$PHPShopInterfaceOrder->getContent().'</table>';

        $PHPShopInterface = new PHPShopInterface();
        $PHPShopInterface->checkbox_action = false;

        // выводим журнал операций по заказу
        $PHPShopInterface->setCaption(array("Журнал операций", "30%"),array("Номер заказа 2can&ibox", "30%"),  array("Дата", "15%"), array("Статус", "15%"));

        if (is_array($log))
            foreach ($log as $row) {
                $PHPShopInterface->setRow(array('name' => $row['type'], 'link' => '?path=modules.dir.twocan&id=' . $row['id']), array('name' => $row['twocan_id']), PHPShopDate::get($row['date'], true), $row['status']);
            }

        $Tab1 .= '<hr><table class="table table-hover">'.$PHPShopInterface->getContent().'</table>';

        $PHPShopGUI->addTab(array("2can&ibox", $Tab1, true));
        
    }
}

/**
 * Функция запроса информации по заказу в 2can&ibox
 *
 * @return void
 */
function twocanActionCheck(){
    global $PHPShopModules, $PHPShopSystem;

    $PHPShopOrm = new PHPShopOrm($PHPShopModules->getParam("base.twocan.twocan_orders"));
    $ordersORM = new PHPShopOrm($GLOBALS['SysValue']['base']['orders']);

    $orderData = $ordersORM->select(array('*'), array('id=' => intval($_GET['id'])));

    // Настройки модуля
    include_once(dirname(__FILE__) . '/../hook/mod_option.hook.php');
    $PHPShopTwocanArray = new PHPShopTwocanArray();
    $option = $PHPShopTwocanArray->getArray();

    // Выборка заказа
    $order = $PHPShopOrm->getOne(array('*'), array("id=" => "'$orderData[uid]'"));
    if($order){
    
        // Режим разработки и боевой режим
        if ($option["dev_mode"] == 0)
            $url = $option["test_url"];
        else
            $url = $option["url"];
        
        // Получение информации по заказу
        $result = $PHPShopTwocanArray->getOrder($order['twocanid'], $option);
        
        // Обработка результата запроса
        if($result['status'] == 'error' || isset($result['result']['orders'][0]['failure_message'])) {
            // Ошибка запроса
            $PHPShopTwocanArray->log($result, $orderData['uid'], "Ошибка оплаты", 'Запрос состояния заказа', $result['result']['orders'][0]['id']);
            $PHPShopTwocanArray->updateOrderStatus($orderData['uid'], $result['result']['orders'][0]['status']);

        }else{
            // Обрабатываем заказ в зависимости от статуса
            switch ($result['status']) {
                case 'authorized': // Деньги захолдированы
                    $ordersORM->update(array('statusi_new' => $option["status_auth"]), array('id=' => $orderData['id']));
                    $PHPShopTwocanArray->log($result, $orderData['uid'], "Ожидает завершения расчета", 'Запрос состояния заказа', $result['result']['orders'][0]['id']);
                    break;
                case 'charged': // Деньги списаны
                    if($orderData['statusi'] != '101'){
                        $ordersORM->query("UPDATE `phpshop_orders` SET `statusi`='101', `paid` = 1 WHERE `id`='$orderData[id]'");

                        // Лог оплат
                        $paymentOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['payment']);
                        $paymentOrm->insert(array('uid_new' => str_replace('-', '', $orderData['uid']), 'name_new' => '2can&ibox',
                            'sum_new' => $result['result']['orders'][0]['amount'], 'datas_new' => time()));
                    }
                    $PHPShopTwocanArray->log($result, $orderData['uid'], 'Платеж проведен', 'Запрос состояния заказа', $result['result']['orders'][0]['id']);
                    
                    break;
                case 'refunded': // Произведен возврат либо частичный возврат
                    $sts = ((float)$order['charged'] - (float)$order['refunded'] > 0)?"Произведен частичный возврат":"Произведен возврат";
                    $PHPShopTwocanArray->log($result, $orderData['uid'], $sts , 'Запрос состояния заказа', $result['result']['orders'][0]['id']);                    
                    $ordersORM->update(array('statusi_new' => 1), array('id=' => $orderData['id']));                
                    break;
                case 'reversed': // Произведена отмена авторизации
                    $PHPShopTwocanArray->log($result, $orderData['uid'], "Отмена авторизации проведена", 'Запрос состояния заказа', $result['result']['orders'][0]['id']);
                    $ordersORM->update(array('statusi_new' => 1), array('id=' => $orderData['id']));
                    break;
                default: // не удалось определить статус, либо действия по указанному статусу не описаны
                    $PHPShopTwocanArray->log($result, $orderData['uid'], "Не определено", 'Запрос состояния заказа', $result['result']['orders'][0]['id']);
                    break;

                
            }
            // Обновляем информацию по заказу в модуле оплаты
           $PHPShopTwocanArray->setOrder($orderData['uid'],$result['result']['orders'][0]['id'], $result['result']['orders'][0]['amount'],$result['result']['orders'][0]['status'],$result['result']['orders'][0]['amount_charged'],$result['result']['orders'][0]['amount_refunded']);
        }
    }
}

/**
 * Функция частичного возврата по заказу
 *
 * @return void
 */
function twocanActionRefundpart(){
    global $PHPShopModules, $PHPShopSystem;

    // SQL
    $PHPShopOrm = new PHPShopOrm($PHPShopModules->getParam("base.twocan.twocan_orders"));
    $ordersORM = new PHPShopOrm($GLOBALS['SysValue']['base']['orders']);
    
    $orderData = $ordersORM->select(array('*'), array('id=' => intval($_GET['id'])));

    // Настройки модуля
    include_once(dirname(__FILE__) . '/../hook/mod_option.hook.php');
    $PHPShopTwocanArray = new PHPShopTwocanArray();
    $option = $PHPShopTwocanArray->getArray();

    // Выборка заказа
    $order = $PHPShopOrm->getOne(array('*'), array("id=" => "'$orderData[uid]'"));
    
    // Считаем сумму, которую можно вернуть
    $couldRefund =  (float)$order['charged'] - (float)$orderData['sum'] - (float)$order['refunded'];

    if($couldRefund > 0){
        // Если есть что возвращать, то отправляем запрос на шлюз
        $result = $PHPShopTwocanArray->refundOrder($order['twocanid'], $couldRefund,  $option);   

        // Обработка результата запроса
        if($result['status'] == 'error' || isset($result['result']['orders'][0]['failure_message']) || isset($result['result']['failure_message'])) {
           // Ошибка запроса
            $PHPShopTwocanArray->log($result, $orderData['uid'], "Ошибка возврата", 'Частичный возврат', $order['twocanid']);
        }elseif($result['status'] == 'refunded'){
            // Заказ возвращен, сохраняем действие в лог и обновляем информацию по заказу в модуле          
            $PHPShopTwocanArray->log($result, $orderData['uid'], "Произведен частичный возврат", 'Частичный возврат', $order['twocanid']);
            $PHPShopTwocanArray->setOrder($orderData['uid'],$order['twocanid'], $result['result']['orders'][0]['amount'],$result['result']['orders'][0]['status'],$result['result']['orders'][0]['amount_charged'],$result['result']['orders'][0]['amount_refunded']);
        }else{
            // не удалось определить статус, либо действия по указанному статусу не описаны
            $PHPShopTwocanArray->log($result, $orderData['uid'], "Не определено", 'Частичный возврат', $order['twocanid']);
        }
    }else{
        // Если возвращать уже нечего, пишем в лог ошибку
        $PHPShopTwocanArray->log(['error'=> "Сумма возврата должна быть > 0"], $orderData['uid'], "Ошибка", 'Частичный возврат', $order['twocanid']);
    }
}

/**
 * Функция полного возврата по заказу
 *
 * @return void
 */
function twocanActionRefund(){

    global $PHPShopModules, $PHPShopSystem;

    // SQL
    $PHPShopOrm = new PHPShopOrm($PHPShopModules->getParam("base.twocan.twocan_orders"));
    $ordersORM = new PHPShopOrm($GLOBALS['SysValue']['base']['orders']);
    
    $orderData = $ordersORM->select(array('*'), array('id=' => intval($_GET['id'])));

    // Настройки модуля
    include_once(dirname(__FILE__) . '/../hook/mod_option.hook.php');
    $PHPShopTwocanArray = new PHPShopTwocanArray();
    $option = $PHPShopTwocanArray->getArray();

    // Выборка заказа
    $order = $PHPShopOrm->getOne(array('*'), array("id=" => "'$orderData[uid]'"));

    // Считаем сумму, которую можно вернуть
    $couldRefund =  (float)$order['charged'] - (float)$order['refunded'];
    
    if($couldRefund > 0){
        // Если есть что возвращать, то отправляем запрос на шлюз

        $result = $PHPShopTwocanArray->refundOrder($order['twocanid'], $couldRefund,  $option);    
        

        if($result['status'] == 'error' || isset($result['result']['orders'][0]['failure_message']) || isset($result['result']['failure_message'])) {
            // Ошибка запроса
            $PHPShopTwocanArray->log($result, $orderData['uid'], "Ошибка возврата", 'Возврат', $order['twocanid']);
        }elseif($result['status'] == 'refunded'){
            // Заказ возвращен, выставляем статус заказа на Аннулирован, сохраняем действие в лог и обновляем информацию по заказу в модуле
            $PHPShopTwocanArray->log($result, $orderData['uid'], "Возврат проведен", 'Возврат', $order['twocanid']);
            $ordersORM->update(array('statusi_new' => 1), array('id=' => $orderData['id']));
            $PHPShopTwocanArray->setOrder($orderData['uid'],$order['twocanid'], $result['result']['orders'][0]['amount'],$result['result']['orders'][0]['status'],$result['result']['orders'][0]['amount_charged'],$result['result']['orders'][0]['amount_refunded']);
        }else{
            // не удалось определить статус, либо действия по указанному статусу не описаны
            $PHPShopTwocanArray->log($result, $orderData['uid'], "Не определено", 'Возврат', $order['twocanid']);
        }
    }else{
        // Если возвращать уже нечего, пишем в лог ошибку
        $PHPShopTwocanArray->log(['error'=> "Сумма возврата должна быть > 0"], $orderData['uid'], "Ошибка", 'Частичный возврат', $order['twocanid']);
    }
}


/**
 * Функция списания средств по заказу после холдирования
 *
 * @return void
 */
function twocanActionCharge(){
    global $PHPShopModules, $PHPShopSystem;

    // SQL
    $PHPShopOrm = new PHPShopOrm($PHPShopModules->getParam("base.twocan.twocan_orders"));
    $ordersORM = new PHPShopOrm($GLOBALS['SysValue']['base']['orders']);
    
    $orderData = $ordersORM->select(array('*'), array('id=' => intval($_GET['id'])));
 
    // Настройки модуля
    include_once(dirname(__FILE__) . '/../hook/mod_option.hook.php');
    $PHPShopTwocanArray = new PHPShopTwocanArray();
    $option = $PHPShopTwocanArray->getArray();

    // Выборка заказа
    $order = $PHPShopOrm->getOne(array('*'), array("id=" => "'$orderData[uid]'"));    
    
    // Делаем запрос на списание
    $result = $PHPShopTwocanArray->chargeOrder($order['twocanid'], number_format($orderData['sum'], 2, '.', ''),  $option);  

    if($result['status'] == 'error' || isset($result['result']['orders'][0]['failure_message']) || isset($result['result']['failure_message'])) {
        $PHPShopTwocanArray->log($result, $orderData['uid'], "Ошибка списания", 'Списание', $order['twocanid']);
    }elseif($result['status'] == 'charged'){
        $PHPShopTwocanArray->log($result, $orderData['uid'], "Сумма списана", 'Списание', $order['twocanid']);
        $ordersORM->query("UPDATE `phpshop_orders` SET `statusi`='101', `paid` = 1 WHERE `id`='$orderData[id]'");
        $PHPShopTwocanArray->setOrder($orderData['uid'],$order['twocanid'], $result['result']['orders'][0]['amount'],$result['result']['orders'][0]['status'],$result['result']['orders'][0]['amount_charged'],$result['result']['orders'][0]['amount_refunded']);
        // Лог оплат
        $paymentOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['payment']);
        $paymentOrm->insert(array('uid_new' => str_replace('-', '', $orderData['uid']), 'name_new' => '2can&ibox',
            'sum_new' => $result['result']['orders'][0]['amount'], 'datas_new' => time()));
    }else{
        $PHPShopTwocanArray->log($result, $orderData['uid'], "Не определено", 'Списание', $order['twocanid']);
    }
}

function twocanActionReverse(){
    global $PHPShopModules, $PHPShopSystem;

    // SQL
    $PHPShopOrm = new PHPShopOrm($PHPShopModules->getParam("base.twocan.twocan_orders"));
    $ordersORM = new PHPShopOrm($GLOBALS['SysValue']['base']['orders']);
    
    $orderData = $ordersORM->select(array('*'), array('id=' => intval($_GET['id'])));

    // Настройки модуля
    include_once(dirname(__FILE__) . '/../hook/mod_option.hook.php');
    $PHPShopTwocanArray = new PHPShopTwocanArray();
    $option = $PHPShopTwocanArray->getArray();

    // Выборка заказа
    $order = $PHPShopOrm->getOne(array('*'), array("id=" => "'$orderData[uid]'"));
   
    $result = $PHPShopTwocanArray->reverseOrder($order['twocanid'], $option);   
    

    if($result['status'] == 'error' || isset($result['result']['orders'][0]['failure_message']) || isset($result['result']['failure_message'])) {
        $PHPShopTwocanArray->log($result, $orderData['uid'], "Ошибка отмены авторизации", 'Отмена авторизации', $order['twocanid']);
    }elseif($result['status'] == 'reversed'){
        $PHPShopTwocanArray->log($result, $orderData['uid'], "Отмена авторизации проведена", 'Отмена авторизации', $order['twocanid']);
        $ordersORM->update(array('statusi_new' => 1), array('id=' => $orderData['id']));
        $PHPShopTwocanArray->setOrder($orderData['uid'],$order['twocanid'], $result['result']['orders'][0]['amount'],$result['result']['orders'][0]['status'],$result['result']['orders'][0]['amount_charged'],$result['result']['orders'][0]['amount_refunded']);
    }else{
        $PHPShopTwocanArray->log($result, $orderData['uid'], "Не определено", 'Отмена авторизации', $order['twocanid']);
    }
}


$addHandler = array(
    'actionStart' => 'twocan',
    'actionDelete' => false,
    'actionEdit' => false,
);