<?php
/**
 * Функция хук, обработка результата выполнения платежа
 * @param object $obj объект функции
 * @param array $value данные о заказе
 */
function success_mod_twocan_hook($obj, $value) {

    if (isset($_REQUEST['uid']) && isset($_REQUEST['order_id'])) {

       $orderNum = $_REQUEST['uid'];
        
        // Проверяем статус оплаты и пишем лог модуля
        $status = twocan_check($obj, $orderNum, $_REQUEST['order_id']);
        
        if($status == 'charged' || $status == 'authorized'){
            $obj->order_metod = 'modules" and id="10028';

            $mrh_ouid = explode("-", $orderNum);
            $obj->inv_id = $mrh_ouid[0] . $mrh_ouid[1];

            $obj->ofd();

            $obj->message();

            return true;
        } else
            $obj->error();
    }
}

/**
 * Функция проверки статуса платежа в 2can&ibox
 * @param object $obj объект функции
 * @param string $id номер заказа
 */
function twocan_check($obj, $id, $twocanorderid){

    $statuses = [
        "processing" => "Выполняется обработка заказа",
        "prepared" => "Ожидаются действия извне",
        "reversed" => "Проведено успешное аннулирование", 
        "refunded" => "Проведен успешный возврат средств", 
        "rejected" => "Заказ был отклонен системой",
        "fraud" => "Заказ был определен как фродный и отклонен системой",
        "declined" => "Заказ был отклонен банком-эквайером",
        "chargedback" => "Заказ был отмечен как чарджбек"

    ];

    $PHPShopOrm = new PHPShopOrm();

    // Настройки модуля
    include_once(dirname(__FILE__) . '/mod_option.hook.php');
    $PHPShopTwocanArray = new PHPShopTwocanArray();
    $option = $PHPShopTwocanArray->getArray();
    
    $result = $PHPShopTwocanArray->getOrder( $twocanorderid, $option );
    
    if($result['status'] == 'error' || isset($result['result']['orders'][0]['failure_message'])) {
        $PHPShopTwocanArray->log($result, $id, "Ошибка оплаты", 'Запрос состояния заказа', $result['result']['orders'][0]['id']);
    }elseif($result['status'] == 'authorized'){
        $order_status = $option['status_auth'];
        $PHPShopTwocanArray->log($result, $id, "Ожидает завершения расчета", 'Запрос состояния заказа', $result['result']['orders'][0]['id']);
        $PHPShopOrm->query("UPDATE `phpshop_orders` SET `statusi`='$order_status', `paid` = 1 WHERE `uid`='$id'");
        $PHPShopTwocanArray->setOrder($id,$result['result']['orders'][0]['id'], $result['result']['orders'][0]['amount'],$result['result']['orders'][0]['status'],$result['result']['orders'][0]['amount_charged'],$result['result']['orders'][0]['amount_refunded']);
        
    }elseif($result['status'] == 'charged'){
        $order_status = $obj->set_order_status_101();
        $PHPShopOrm->query("UPDATE `phpshop_orders` SET `statusi`='$order_status', `paid` = 1 WHERE `uid`='$id'");

        $PHPShopTwocanArray->log($result, $id, 'Платеж проведен', 'Запрос состояния заказа', $result['result']['orders'][0]['id']);
        $PHPShopTwocanArray->setOrder($id,$result['result']['orders'][0]['id'], $result['result']['orders'][0]['amount'],$result['result']['orders'][0]['status'],$result['result']['orders'][0]['amount_charged'],$result['result']['orders'][0]['amount_refunded']);
        // Лог оплат
        $paymentOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['payment']);
        $paymentOrm->insert(array('uid_new' => str_replace('-', '', $id), 'name_new' => '2can&ibox',
            'sum_new' => $result['result']['orders'][0]['amount'], 'datas_new' => time()));      
    }elseif(in_array($result['status'], $statuses)){
        $PHPShopTwocanArray->log($result, $id, "Ошибка оплаты", 'Запрос состояния заказа', $result['result']['orders'][0]['id']);
        $PHPShopTwocanArray->updateOrderStatus($id, $result['status']);
    }else{
        $PHPShopTwocanArray->log($result, $id, 'Не определено', 'Запрос состояния заказа', $result['result']['orders'][0]['id']);
    }
    
    return $result['status'];

}
$addHandler = array(
    'index' => 'success_mod_twocan_hook'
);
?>