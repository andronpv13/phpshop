<?php

include_once dirname(__DIR__) . '/class/include.php';

function pochta_delivery_hook($obj, $data) {

    $result = $data[0];
    $Pochta = new Pochta();

    if ($Pochta->isCourier((int) $data[1]) || $Pochta->isPostOffice((int) $data[1])) {
        $hook['dellist'] = $result['dellist'];
        $hook['hook'] = $Pochta->isCourier((int) $data[1]) ? "pochtaInit('courier');" : "pochtaInit('pvz');";
        $hook['delivery'] = $result['delivery'];
        $hook['total'] = $result['total'];
        $hook['adresList'] = $result['adresList'];
        $hook['free_delivery'] = $result['free_delivery'];
        $hook['success'] = 1;

        return $hook;
    }
}

$addHandler = array(
    'delivery' => 'pochta_delivery_hook'
);
?>
