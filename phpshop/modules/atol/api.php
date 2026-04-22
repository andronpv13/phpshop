<?php

#ini_set('error_reporting', E_ALL);
#ini_set('display_errors', 1);
#ini_set('display_startup_errors', 1);

/**
 * Отправка чека в ОФД Атол
 * @param array $data данные заказа
 * @param string $operation операция [sale|sale_refund]
 * @param bool $json передача упакованных внешних данных в JSON формате
 */
function OFDStart($data, $operation = 'sell', $json = false) {
    global $PHPShopSystem, $_classPath;

    $OrderId = $data['uid'] . '-' . $operation;

    // Данные из заказа
    if (empty($json)) {
        $order = @unserialize($data['orders']);
    }
    // Внешний JSON
    else {
        $order['Person']['mail'] = $data['receipt']['client']['email'];
        $data['tel'] = $data['receipt']['client']['phone'];
        $order['Cart']['cart'] = $data['receipt']['items'];
        $data['sum'] = $data['receipt']['total'];
    }

    // НДС
    if ($PHPShopSystem->getParam('nds_enabled') == '') {
        $tax = $tax_delivery = 'none';
    } else {
        $tax = 'vat' . $PHPShopSystem->getParam('nds');

        // НДС Доставки
        if (!empty($order['Cart']['dostavka'])) {
            PHPShopObj::loadClass('delivery');
            $PHPShopDelivery = new PHPShopDelivery($order['Person']['dostavka_metod']);

            $tax_delivery = $PHPShopDelivery->getParam('ofd_nds');
            if ($tax_delivery == '')
                $tax_delivery = $tax;
            else
                $tax_delivery = 'vat' . $PHPShopDelivery->getParam('ofd_nds');
        }
    }

    include_once($_classPath . 'modules/atol/class/atol.class.php');
    if (class_exists('AtolRest')) {
        $AtolRest = new AtolRest($OrderId);

        // Только ручная отправка.
        if((int) $AtolRest->option['manual_control'] === 1 && !isset($_REQUEST['manual'])) {
            exit;
        }

        $sell['timestamp'] = date('d.m.Y H:m:s');
        $sell['external_id'] = $OrderId;
        $sell['receipt'] = array(
            'client' => array(
                'email' => $order['Person']['mail'],
                'phone' => str_replace(array('(', ')', '-', ' '), '', $data['tel'])
            ),
            'company' => array(
                'email' => $order['Person']['mail'],
                'inn' => $AtolRest->option['inn'],
                'payment_address' => $AtolRest->option['payment_address']
            )
        );

        // Корзина
        if (is_array($order['Cart']['cart'])) {
            foreach ($order['Cart']['cart'] as $product) {

                // Скидка
                if ($order['Person']['discount'] > 0)
                    $price = $product['price'] - ($product['price'] * $order['Person']['discount'] / 100);
                else
                    $price = $product['price'];

                $sum+=$price * $product['num'];

                $sell['receipt']['items'][] = array(
                    'name' => $product['name'],
                    'price' => floatval(number_format($price, 2, '.', '')),
                    'quantity' => floatval(number_format($product['num'], 2, '.', '')),
                    'sum' => floatval(number_format($price * $product['num'], 2, '.', '')),
                    'vat' => array('type' => $tax),
                    'payment_method' => 'full_prepayment',
                    'payment_object' => 'commodity'
                );
            }
        }

        // Доставка
        if (!empty($order['Cart']['dostavka'])) {

            // Усреднее стоимости доставка при скидках
            if ($order['Person']['discount'] > 0)
                $order['Cart']['dostavka'] = $data['sum'] - $sum;

            $sell['receipt']['items'][] = array(
                'name' => 'Доставка',
                'price' => floatval(number_format($order['Cart']['dostavka'], 2, '.', '')),
                'quantity' => floatval(number_format(1, 2, '.', '')),
                'sum' => floatval(number_format($order['Cart']['dostavka'], 2, '.', '')),
                'vat' => array('type' => $tax_delivery),
                'payment_method' => 'full_prepayment',
                'payment_object' => 'service'
            );
        }

        $sell['receipt']['total'] = floatval(number_format($data['sum'], 2, '.', ''));
        $sell['receipt']['payments'][] = array('type' => 1, 'sum' => $sell['receipt']['total']);

        // Операция
        $sell = $AtolRest->setOparation($sell, $operation);

        // Состояние продажи
        $status = $AtolRest->setOparation(null, 'report/' . $sell['uuid'], false);
        if (is_array($status)) {
            $i = 0;

            // Опрос 15 раз 
            while ($i < 15) {
                if ($status['status'] == 'wait') {
                    $status = $AtolRest->setOparation(null, 'report/' . $status['uuid'], false);
                    $i++;
                    sleep(1);
                }
                else
                    $i = 15;
            }
        }

        // Выполнено
        if ($status['status'] == 'done') {
            $ofd_status = 1;
        }
        else
            $ofd_status = 2;

        // Запись лога
        $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['atol']['atol_log']);
        $log = array(
            'message_new' => serialize($AtolRest->log),
            'order_id_new' => $data['id'],
            'order_uid_new' => $data['uid'],
            'status_new' => $ofd_status,
            'path_new' => $_SERVER['REQUEST_URI'],
            'date_new' => time(),
            'operation_new' => $operation,
            'fiscal_new' => $status['payload']['fiscal_document_attribute']
        );

        $status['log_id'] = $PHPShopOrm->insert($log);
        $status['operation'] = $operation;

        // Статус заказа
        $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['orders']);
        $PHPShopOrm->update(array('ofd_new' => serialize($status), 'ofd_status_new' => $ofd_status), array('id' => '="' . $data['id'] . '"'));

        return array('status' => $ofd_status, 'data' => $status);
    }
}

// Запрос по URL
if (!empty($_REQUEST['ajax']) and !empty($_REQUEST['operation'])) {

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
        $status = OFDStart($data, $_REQUEST['operation']);
        echo json_encode(array('status' => $status['status'], 'operation' => $_REQUEST['operation']));
    }
}
?>