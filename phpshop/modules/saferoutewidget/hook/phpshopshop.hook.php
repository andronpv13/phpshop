<?php

include_once dirname(__DIR__) . '/class/Saferoute.php';

function UID_saferoutewidget_hook($obj, $dataArray, $rout) {
    if ($rout == 'MIDDLE') {

        $Saferoute = new Saferoute();

        if($Saferoute->options['prod_enabled'] == '1'&&!empty($Saferoute->options['key'])) {
            $html = ParseTemplateReturn($GLOBALS['SysValue']['templates']['saferoutewidget']['saferoutewidget_prod_template'], true);

            $obj->set('saferouteCart', $html);
        }
    }
}

$addHandler = array
(
    'UID' => 'UID_saferoutewidget_hook',
);


?>