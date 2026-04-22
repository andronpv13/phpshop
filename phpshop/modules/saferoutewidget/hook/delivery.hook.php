<?php

include_once dirname(__DIR__) . '/class/Saferoute.php';

/**
 * Õóê
 */
function saferoutewidget_delivery_hook($obj, $data) {

    $_RESULT = $data[0];
    $xid = $data[1];

    $SafeRoute = new Saferoute();

    if (in_array($xid, @explode(",", $SafeRoute->options['delivery_id']))) {

        $hook['dellist'] = $_RESULT['dellist'];
        $hook['hook'] = 'saferoutewidgetStart();';
        $hook['delivery'] = $_RESULT['delivery'];
        $hook['total'] = $_RESULT['total'];
        $hook['adresList'] = $_RESULT['adresList'];
        $hook['free_delivery'] = $_RESULT['free_delivery'];
        $hook['success'] = 1;

        return $hook;
    }
}

$addHandler = array ('delivery' => 'saferoutewidget_delivery_hook');
?>
