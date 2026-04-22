<?php

/**
 * Внедрение js функции
 *
 * param object $obj
 * param array $data
 */
function novaposhta_delivery_hook($obj, $data) {

    $_RESULT = $data[0];
    $xid = $data[1];

    // API
    include_once '../modules/novaposhta/class/NovaPoshta.php';
    $NovaPoshta = new NovaPoshta();

    if ($xid === $NovaPoshta->option['delivery_id']) {

        $hook['dellist'] = $_RESULT['dellist'];
        $hook['hook'] = 'novaposhtaInit();';
        $hook['delivery'] = $_RESULT['delivery'];
        $hook['total'] = $_RESULT['total'];
        $hook['adresList'] = $_RESULT['adresList'];
        $hook['free_delivery'] = $_RESULT['free_delivery'];
        $hook['success'] = 1;

        return $hook;
    }
}

$addHandler = array('delivery' => 'novaposhta_delivery_hook');
?>
