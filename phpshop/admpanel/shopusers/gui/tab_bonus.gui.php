<?php

function tab_bonus($id) {

    $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['bonus']);
    $data = $PHPShopOrm->select(array('*'), array('user_id' => '= "' . $id . '"'), array('order' => 'id desc'), array('limit' => 10));
    $dis = '<table id="table-bonus-comment" class="table"><thead><tr><th>'.__('Дата').'</th><th>'.__('Комментарий').'</th><th class="text-right">'.__('Бонусы').'</th></tr></thead>';

    if (is_array($data)) {
        foreach ($data as $row) {
            if(empty($row['order_id']))
            $dis .= '<tr><td>' . PHPShopDate::get($row['date'],true) . '</td><td>' . $row['comment'] . '</td><td class="text-right">' . $row['bonus_operation'] . '</td></tr>';
            else $dis .= '<tr><td>' . PHPShopDate::get($row['date'],true) . '</td><td><a href="?path=order&id='.$row['order_id'].'">' . $row['comment'] . '</a></td><td class="text-right">' . $row['bonus_operation'] . '</td></tr>';
        }

    }
    $dis .= '<tr><td></td><td><input style="width:100%" placeholder="' . __('Добавить') . '" name="comment_new" class="form-control input-sm" value=""></td><td class="text-right"><input style="width:100%" placeholder="' . __('0') . '" name="bonus_operation_new" class="form-control input-sm text-right" value=""></td></tr></table>';
    
    return $dis;
}

?>