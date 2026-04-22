<?php

function order_mod_dolyame_hook($obj, $row, $rout) {
    if ($rout == 'MIDDLE') {

        require_once "./phpshop/modules/dolyame/class/Dolyame.php";
        $Dolyame = new Dolyame();

        if ((int) $obj->PHPShopCart->getSum() <= (int) $Dolyame->max_sum) {
            $sum = ceil($obj->PHPShopCart->getSum() / 4);
            $payments = $obj->get('orderOplata');
            $payments = str_replace('Долями', "Рассрочка Долями от $sum руб", $payments);
            $obj->set('orderOplata', $payments);
        }
    }
}

$addHandler = array
    (
    'order' => 'order_mod_dolyame_hook',
);