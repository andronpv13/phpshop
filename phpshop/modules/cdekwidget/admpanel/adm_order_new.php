<?php

function cdekOrderCopy($data) {
    (new PHPShopOrm($GLOBALS['SysValue']['base']['orders']))->update(
        ['cdek_order_data_new' => ''],
        ['id' => sprintf('="%s"', $data)]
    );
}

$addHandler = ['actionStart' => 'cdekOrderCopy'];
?>