<?php

/**
 * Панель печатных форм
 * @param array $data массив данных
 * @return string 
 */
function tab_print($data) {
    global $PHPShopGUI;

    $disp = null;

    // Бланк заказа
    $disp.=$PHPShopGUI->setButton('Бланк заказа', 'print', 'btn-print-order','./order/forms/order.php?orderID=' . $data['id']);

    // Товарный чек
    $disp.=$PHPShopGUI->setButton('Товарный чек', 'bookmark', 'btn-print-order','./order/forms/receipt.php?orderID=' . $data['id']);

    // Счет
    $disp.=$PHPShopGUI->setButton('Счет в банк', 'credit-card', 'btn-print-order','../../../phpshop/forms/account/forma.html?orderId='.$data['id'].'&tip=2&datas='.$data['datas']);

    // Счет в сбербанк
    //$disp.=$PHPShopGUI->setButton('Сбербанк', 'list-alt', 'btn-print-order','../../../phpshop/forms/receipt/forma.html?orderId='.$data['id'].'&tip=2&datas='.$data['datas']);
    
    // Счет-Фактура
    $disp.=$PHPShopGUI->setButton('Счет-Фактура', 'barcode', 'btn-print-order','./order/forms/invoice.php?orderID=' . $data['id'] );
    
    // Торг-12
    if($_SESSION['lang'] == 'russian')
    $disp.=$PHPShopGUI->setButton('Торг-12', 'qrcode', 'btn-print-order','./order/forms/torg-12.php?orderID=' . $data['id']);
    
     // Гарантия
    $disp.=$PHPShopGUI->setButton('Гарантия', 'briefcase', 'btn-print-order','./order/forms/warranty.php?orderID=' . $data['id']);
    
    // Акт
    $disp.=$PHPShopGUI->setButton('Акт', 'file', 'btn-print-order','./order/forms/act.php?orderID=' . $data['id']);
    
    // UPD
    if($_SESSION['lang'] == 'russian')
    $disp.=$PHPShopGUI->setButton('УПД', 'book', 'btn-print-order','./order/forms/upd.php?orderID=' . $data['id']);

    return $disp;
}

?>
