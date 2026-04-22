<?php

/**
 * ѕанель оплаты
 * @param array $data массив данных
 * @return string 
 */
function tab_pay($data) {
    global $PHPShopGUI;

    $host = $GLOBALS['SysValue']['connect']['host'];
    $dbname = $GLOBALS['SysValue']['connect']['dbase'];
    $uname = $GLOBALS['SysValue']['connect']['user_db'];
    $upass = $GLOBALS['SysValue']['connect']['pass_db'];


    $disp = $PHPShopGUI->setButton('—сылка на оплату', 'credit-card', 'btn-print-order', '/pay/?orderID=' . $data['id'] . '-' . md5($host . $dbname . $uname . $upass . $data['id']));
    return $disp;
}
