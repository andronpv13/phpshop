<?php

function tab_comment() {
    global $PHPShopSystem;

    $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['comment']);
    $PHPShopOrm->debug = false;


    // Выборка
    $PHPShopOrm->sql = 'SELECT a.*, b.name as product FROM ' . $GLOBALS['SysValue']['base']['comment'] . ' AS a 
        JOIN ' . $GLOBALS['SysValue']['base']['products'] . ' AS b ON a.parent_id = b.id    
            WHERE a.user_id=' . intval($_GET['user_id']) . ' limit 5';
    $data = $PHPShopOrm->select();

    if (is_array($data)) {
        $tab = '<ul class="nav nav-pills nav-stacked">';
        foreach ($data as $row) {
            $tab .= '<li><a href="?path=shopusers.comment&id=' . $row['id'] . '&return=' . $_GET['path'] . '.' . $_GET['id'] . '" data-toggle="tooltip" data-placement="top" title="' . substr(strip_tags($row['content']),0,100) . '...">' . substr($row['product'],0,25) . '<span class="pull-right text-muted" >' . PHPShopDate::get($row['datas'], false, false, '.') . '</span></a></li>';
        }
        $tab .= '</ul>';
    } else
        $tab = null;

    return $tab;
}

?>