<?php

/**
 * Отправка чека в ОФД CloudPayments
 * @param array $data данные заказа
 * @param string $operation операция [sale|sale_refund]
 * @param bool $json передача упакованных внешних данных в JSON формате
 */
function OFDStart($data, $operation = 'sell', $json = false) {
    global $PHPShopSystem, $_classPath;

    $OrderId = $data['uid'];

    // Данные из заказа
    if (empty($json)) {
        $order = @unserialize($data['orders']);
    }
    // Внешний JSON
    else {
        $order['Person']['mail'] = $data['receipt']['attributes']['email'];
        $data['tel'] = $data['receipt']['attributes']['phone'];
        $order['Cart']['cart'] = $data['receipt']['items'];
        $data['sum'] = $data['receipt']['total'];
    }

    // НДС
    if ($PHPShopSystem->getParam('nds_enabled') == '') {
        $tax = $tax_delivery = null;
    } else {
        $tax = $PHPShopSystem->getParam('nds');

        // НДС Доставки
        if (!empty($order['Cart']['dostavka'])) {
            PHPShopObj::loadClass('delivery');
            $PHPShopDelivery = new PHPShopDelivery($order['Person']['dostavka_metod']);

            $tax_delivery = $PHPShopDelivery->getParam('ofd_nds');
            if ($tax_delivery == '')
                $tax_delivery = $tax;
            else
                $tax_delivery = $PHPShopDelivery->getParam('ofd_nds');
        }
    }

    include_once($_classPath . 'modules/cloudkassir/class/cloudkassir.class.php');

    if (class_exists('CloudPaymentsRest')) {

        $CloudPaymentsRest = new CloudPaymentsRest($OrderId);

        $sell['Inn'] = $CloudPaymentsRest->option['inn'];
        $sell['InvoiceId'] = $OrderId;
        $sell['AccountId'] = $order['Person']['mail'];

        if($operation == "sell")
            $sell['Type'] = 'Income';
        elseif ($operation == "sell_refund")
            $sell['Type'] = 'IncomeReturn';

        // Корзина
        if (is_array($order['Cart']['cart'])) {
            foreach ($order['Cart']['cart'] as $orderItem) {
                $amountItem = intval($orderItem['price']) * intval($orderItem['num']);

                $aItem[] = array(
                    "label" => PHPShopString::win_utf8($orderItem['name']),
                    "price" => floatval(number_format($orderItem['price'], 2, '.', '')),
                    "quantity" => intval($orderItem['num']),
                    "amount" => $amountItem,
                    "vat" => $tax,
                    "method"   => 1,
                    "object"   => 1
                );
            }
        }

        if (!empty($order['Cart']['dostavka'])) {
            $aItem[] = array(
                "label" => PHPShopString::win_utf8('Доставка'),
                'price' => floatval(number_format($order['Cart']['dostavka'], 2, '.', '')),
                'quantity' => floatval(number_format(1, 2, '.', '')),
                'amount' => floatval(number_format($order['Cart']['dostavka'], 2, '.', '')),
                'vat' => $tax_delivery,
                "method"   => 1,
                "object"   => 4
            );
        }

        $array = array(
            "Items" =>$aItem,
            "taxationSystem" => $CloudPaymentsRest->option['taxationSystem'],
            "email" => $order['Person']['mail'],
            "phone" => str_replace(array('(', ')', '-', ' '), '', $data['tel'])
        );

        $sell['CustomerReceipt'] = $array;

        // Операция
        $result = $CloudPaymentsRest->setOparation($sell, $operation);

        $result['operation'] = $operation;

        return array('status' => $result['Success'], 'data' => $result);
    }
}
// AJAX запрос вовзврата средств
if(intval($_REQUEST['ajax'] == 1)){
    $_classPath = "../../";
    include($_classPath . "class/obj.class.php");
    PHPShopObj::loadClass("base");
    $PHPShopBase = new PHPShopBase($_classPath . "inc/config.ini", true, true);
    $PHPShopBase->chekAdmin();
    PHPShopObj::loadClass("orm");
    PHPShopObj::loadClass("system");
    PHPShopObj::loadClass("text");
    PHPShopObj::loadClass("string");
    PHPShopObj::loadClass("product");
    PHPShopObj::loadClass("valuta");
    PHPShopObj::loadClass("mail");
    PHPShopObj::loadClass("parser");
    PHPShopObj::loadClass("modules");
    PHPShopObj::loadClass("security");


    $PHPShopModules = new PHPShopModules($_classPath . "modules/");
    $PHPShopSystem = new PHPShopSystem();
    $PHPShopValutaArray = new PHPShopValutaArray();

    if (!empty($_REQUEST['id'])) {
        $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['orders']);
        $data = $PHPShopOrm->select(array('*'), array('id' => "=" . intval($_REQUEST['id'])), false, array('limit' => 1));
        $result = OFDStart($data, $_REQUEST['operation']);

        if($result['status'] == true)
            $status = 1;
        else
            $status = 2;

        $PHPShopOrm->update(array('ofd_new' => '', 'ofd_status_new' => $status), array('id' => '="' . $data['id'] . '"'));

        echo json_encode(array('status' => $status, 'operation' => $_REQUEST['operation']));
    }
}
?>