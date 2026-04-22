<?php

/**
 * Панель подробного описания товара
 * @param array $row массив данных
 * @return string 
 */
function tab_pbrf_new($data, $option = false) {
    global $PHPShopGUI, $PHPShopSystem;
    $PHPShopGUI->addJSFiles('/phpshop/modules/pbrf/js/pbrf.js');

    // Библиотека заказа
    $PHPShopOrder = new PHPShopOrderFunction($data['id']);

    $order = unserialize($data['orders']);

    // Библиотека доставки
    $PHPShopDelivery = new PHPShopDelivery($order['Person']['dostavka_metod']);

    $blank.=$PHPShopGUI->setButton(__('Ярлык Ф.7'), false, $onclick = "\" onclick=\"DoPrintBig('/phpshop/modules/pbrf/admpanel/forms/forma.php?orderID=" . $data['id'] ."&datas=".$data['datas']. "'); return false;");
    $blank.=$PHPShopGUI->setLine(false, 10);
    $blank.=$PHPShopGUI->setButton(__('Ф.113/Ф.117 посылка с наложенным платежом'), false, $onclick = "\" onclick=\"DoPrintBig('/phpshop/modules/pbrf/admpanel/forms/forma2.php?orderID=" . $data['id'] ."&datas=".$data['datas']. "'); return false;");
    $blank.=$PHPShopGUI->setLine(false, 10);
    $blank.=$PHPShopGUI->setButton(__('Почтовый перевод Ф.112эп'), false, $onclick = "\" onclick=\"DoPrintBig('/phpshop/modules/pbrf/admpanel/forms/forma3.php?orderID=" . $data['id'] ."&datas=".$data['datas']. "'); return false;");
    $blank.=$PHPShopGUI->setLine(false, 10);
    $blank.=$PHPShopGUI->setButton(__('Ф112ЭК для федеральных клиентов Почты России'), false, $onclick = "\" onclick=\"DoPrintBig('/phpshop/modules/pbrf/admpanel/forms/forma4.php?orderID=".$data['id']."&datas=".$data['datas']."'); return false;");
    $blank.=$PHPShopGUI->setLine(false, 10);
    $blank.=$PHPShopGUI->setButton(__('Наложенного платежа Ф.113\эн'), false, $onclick = "\" onclick=\"DoPrintBig('/phpshop/modules/pbrf/admpanel/forms/forma5.php?orderID=".$data['id']."&datas=".$data['datas']."'); return false;");
    $blank.=$PHPShopGUI->setLine(false, 10);
    $blank.=$PHPShopGUI->setButton(__('Опись Ф.107'), false, $onclick = "\" onclick=\"DoPrintBig('/phpshop/modules/pbrf/admpanel/forms/forma6.php?orderID=" . $data['id'] ."&datas=".$data['datas']. "'); return false;");
    $blank.=$PHPShopGUI->setLine(false, 10);
    $blank.=$PHPShopGUI->setButton(__('Посылка Ф.116'), false, $onclick = "\" onclick=\"DoPrintBig('/phpshop/modules/pbrf/admpanel/forms/forma7.php?orderID=" . $data['id'] ."&datas=".$data['datas']. "'); return false;");
    $blank.=$PHPShopGUI->setLine(false, 10);
    $blank.=$PHPShopGUI->setButton(__('Таможенная декларация CN23'), false, $onclick = "\" onclick=\"DoPrintBig('/phpshop/modules/pbrf/admpanel/forms/forma8.php?orderID=" . $data['id'] ."&datas=".$data['datas']. "'); return false;");


    $disp = '<div id="blank-pbrf">'.$blank.'</div>';

    return $disp;
}

?>
