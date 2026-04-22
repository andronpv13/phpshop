<?php

function actionReceipt($data) {
    global $PHPShopModules;


    // Проверка способа оплаты
    $orders = unserialize($data['orders']);

    if ($orders['Person']['order_metod'] == 10004) {

        include_once dirname(__FILE__) . '/../class/YandexKassa.php';
        $YandexKassa = new YandexKassa();
        $YandexKassa->payment_mode = 'full_payment';

        if ($YandexKassa->options['payment_mode'] == 1 and $_POST['statusi_new'] == $YandexKassa->options['receipt_status']) {
            

            // SQL
            $PHPShopOrm = new PHPShopOrm($PHPShopModules->getParam("base.yandexkassa.yandexkassa_log"));
            $paymentData = $PHPShopOrm->getOne(['*'], ['order_id=' => $data['id']], ['order' => 'id desc']);
            $log = unserialize($paymentData['message']);
            
            // Тест
            //$log['id']='2f3c20cf-000f-5000-8000-14f2e14e27d5';

            // Заказ оплачен по логу
            if ($log['status'] == 'succeeded') {

                $receipt = $YandexKassa->createReceipt(
                        $YandexKassa->prepareProducts($orders['Cart']['cart'], $orders['Person']['discount']), $log['id'], $data['id'], $orders['Person']['mail'], $YandexKassa->prepareDelivery($orders['Cart']['dostavka'], $nds = 0)
                );
            }
        }
    }
}

// Обработка событий
$addHandler = array(
    'actionStart' => 'actionReceipt',
    'actionDelete' => false,
    'actionUpdate' => 'actionReceipt'
);
