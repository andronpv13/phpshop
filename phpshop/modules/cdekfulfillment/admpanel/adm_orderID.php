<?php

function cdekfulfillmentSend($data) {
    global $_classPath;

    include_once($_classPath . 'modules/cdekfulfillment/class/CDEKFulfillment.php');
    $CDEKFulfillment = new CDEKFulfillment();

    if ($data['statusi'] != $_POST['statusi_new']) {

        if ($_POST['statusi_new'] == $CDEKFulfillment->option['status']) {
            $orderProduct = $CDEKFulfillment->sendOrder($data);

            // Заказ отправлен в СДЕК
            if (!empty($orderProduct['id'])) {

                // Списывание со склада СДЕК
                if (is_array($orderProduct['cdek']))
                    foreach ($orderProduct['cdek'] as $val) {
                        $product = new PHPShopProduct((int) $val['productOffer'], 'export_cdek_id');
                        if (is_array($product->objRow)) {
                            $product->removeFromWarehouse($val['count'], 0, $CDEKFulfillment->option['warehouse_cdek']);
                        }
                    }

                // Списывание с главного склада
                if (is_array($orderProduct['main']))
                    foreach ($orderProduct['main'] as $val) {
                        $product = new PHPShopProduct((int) $val['productOffer'], 'id');
                        if (is_array($product->objRow)) {
                            $product->removeFromWarehouse($val['count'], 0, $CDEKFulfillment->option['warehouse_main']);
                        }
                    }
            }

            // Заказ локальный
            elseif (is_array($orderProduct['main'])) {

                // Списывание с главного склада
                if (is_array($orderProduct['main']))
                    foreach ($orderProduct['main'] as $val) {
                        $product = new PHPShopProduct((int) $val['productOffer'], 'id');
                        if (is_array($product->objRow)) {
                            $product->removeFromWarehouse($val['count'], 0, $CDEKFulfillment->option['warehouse_main']);
                        }
                    }
            }

            // Ошибка
            else {

                // Меняем статус заказа обратно
                $_POST['statusi_new'] = $data['statusi'];
            }
        }
    }
}

function addCdekfulfillmentTab($data) {
    global $PHPShopGUI, $_classPath;

    include_once($_classPath . 'modules/cdekfulfillment/class/CDEKFulfillment.php');
    $CDEKFulfillment = new CDEKFulfillment();

    if ($data['statusi'] == $CDEKFulfillment->option['status']) {

        $Tab1 = $CDEKFulfillment->buildInfoTable($data);
        if (!empty($Tab1))
            $PHPShopGUI->addTab(array("СДЭК Фулфилмент", $Tab1, false, 101));
    }
}

$addHandler = array(
    'actionStart' => 'addCdekfulfillmentTab',
    'actionDelete' => false,
    'actionUpdate' => 'cdekfulfillmentSend'
);
