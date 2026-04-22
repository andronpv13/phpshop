<?php

function tab_order() {
    global $PHPShopSystem;

    $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['orders']);
    $PHPShopOrm->debug = false;
    $currency = ' ' . $PHPShopSystem->getDefaultValutaCode();

    $data = $PHPShopOrm->select(array('*'), array('user'=>'='.intval($_GET['user'])), array('order' => 'id desc'), array('limit'=>'1000'));

    if (is_array($data)) {
        $tab = '<ul class="nav nav-pills nav-stacked">';
        foreach ($data as $row) {
            $tab .= '<li><a href="?path=order&id=' . $row['id'] . '&return='.$_GET['path'].'" data-toggle="tooltip" data-placement="top" title="' . $row['sum'] . $currency. '">' . $row['uid'] . '<span class="pull-right text-muted" >' . PHPShopDate::get($row['datas'], false, false, '.') . '</span></a></li>';
        }
        $tab .= '</ul>';
    } else
        $tab = null;

    return $tab;
}

?>