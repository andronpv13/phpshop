<?php

include_once dirname(__FILE__) . '/../class/PbKredit.php';

function pbkredit_uid_hook($obj, $product, $route) {
    if($route === 'MIDDLE') {
        if((int) $product['pbkredit_disabled'] !== 1) {
            $PbKredit = new PbKredit();
            $product['price'] = number_format($obj->price($product), 2, '.', '');
            $obj->set('pbkreditUid', $PbKredit->render($product));
        } else {
            $obj->set('pbkreditUid', '');
        }
    }
}

$addHandler = array ('UID' => 'pbkredit_uid_hook');