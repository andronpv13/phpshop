<?php

/**
 * Панель изображений товара
 * @param array $data массив данных
 * @return string 
 */
function tab_img($data) {
    global $PHPShopGUI, $PHPShopSystem;

    $img_width = $PHPShopSystem->getSerilizeParam('admoption.img_tw');


    // Маленькое изображение
    $Tab6_1 = $PHPShopGUI->setInput('hidden', "pic_small_new", $data['pic_small']);

    // Большое изображение
    $Tab6_1 .= $PHPShopGUI->setInput('hidden', "pic_big_new", $data['pic_big']);


    $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['foto']);
    $data_pic = $PHPShopOrm->select(array('*'), array('parent' => '=' . intval($data['id'])), array('order' => 'num,id'), array('limit' => 100));
    $i = 1;
    $count = 0;

    // Сетка для превью
    if (isset($_GET['frame']))
        $row_num = 3;
    else
        $row_num = 4;


    $img_list = null;

    // Подтипы
    $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['products']);
    $PHPShopOrm->debug = false;
    $PHPShopOrm->mysql_error = false;

    if (strstr($data['parent'], ','))
        $parent_array = explode(",", $data['parent']);

    if (is_array($parent_array))
        foreach ($parent_array as $v)
            if (!empty($v))
                $parent_array_true[] = $v;

    if (!empty($data['parent']) and is_array($parent_array_true)) {

        $parent_style = null;
        
        // Подтипы из 1С
        if ($PHPShopSystem->ifSerilizeParam('1c_option.update_option'))
            $data_option = $PHPShopOrm->select(array('*'), array('uid' => ' IN ("' . implode('","', $parent_array_true) . '")', 'parent_enabled' => "='1'"), array('order' => 'num,name DESC'), array('limit' => 300));
        else
            $data_option = $PHPShopOrm->select(array('*'), array('id' => ' IN ("' . implode('","', $parent_array_true) . '")', 'parent_enabled' => "='1'"), array('order' => 'num,name DESC'), array('limit' => 300));
    } else
        $parent_style = 'hide';

    if (is_array($data_pic))
        foreach ($data_pic as $row) {

            $path_parts = pathinfo($row['name']);

            if ($i == 1)
                $img_list .= '<div class="row intro-row">';

            if ($row['name'] == $data['pic_big'])
                $main = "btn-success";
            else
                $main = "btn-default";

            if (strlen($path_parts['basename']) > 15)
                $basename = substr($path_parts['basename'], 0, 15) . '..';
            else
                $basename = $path_parts['basename'];

            $num = $PHPShopGUI->setSelectValue($row['num'], 10);

            $select = $PHPShopGUI->setSelect("foto_num_new[" . $row['id'] . "]", $num, 45, null, false, false, false, false, false, $row['id'], 'selectpicker pull-right img-num ', false, 'btn btn-default btn-xs hidden-xs');

            unset($value_option);
            //$value_option[] = array(__('Основной товар'), 0, 0);
            if (!empty($data_option) and is_array($data_option))
                foreach ($data_option as $row_option) {

                    if (empty($row_option['parent']) and empty($row_option['parent2']) and ! empty($row_option['name']))
                        $row_option['parent'] = $row_option['name'];

                    if ($row_option['pic_big'] == $row['name'])
                        $check = true;
                    else
                        $check = false;

                    $value_option[] = array($row_option['parent'] . ' ' . $row_option['parent2'], $row_option['id'], $check);
                }

            $select_option = $PHPShopGUI->setSelect("foto_parent_new[" . $row['id'] . "]", $value_option, 170, null, false, true, false, false, true, $row['name'], 'selectpicker pull-right img-parent ', false, 'btn btn-default btn-xs hidden-xs');

            if (empty($row['info']))
                $row['info'] = str_replace(array('"', '\''), array('', ''), $data['name']);

            $img = str_replace(array('.png', '.jpg', '.gif', '.jpeg', '.webp'), array('s.png', 's.jpg', 's.gif', 's.jpeg', 's.webp'), $row['name']);
            if (!file_exists($_SERVER['DOCUMENT_ROOT'] . $img))
                $img = $row['name'];
            
            // Видео
            if (in_array($path_parts['extension'], array('mp4','mov'))) {
                $img_file = '<video style="max-width:220px;max-height:220px;" src="'. $img . '" controls=""></video>';
                $main = 'hide';
            }
            // Изображение
            else {
                $img_file = '<img data-id="' . $row['id'] . '" title="' . $row['info'] . '" alt="' . $row['info'] . '" style="max-width:220px;max-height:220px;" src="' . $img . '"/>';
            }

            $img_list .= '<div class="col-md-3 col-sm-4 col-xs-12 data-row col-panel"><div class="panel panel-default"><div class="panel-heading" title="' . $path_parts['basename'] . '"><a href="' . $row['name'] . '" target="_blank">' . $basename . '</a><span class="glyphicon glyphicon-remove pull-right btn btn-default btn-xs img-delete" data-id="' . $row['id'] . '" data-toggle="tooltip" data-main="' . $main . '" data-placement="top" title="' . __('Удалить') . '"></span><span class="pull-right">&nbsp;</span><span class="glyphicon glyphicon-heart pull-right btn ' . $main . ' btn-xs img-main" data-path="' . $row['name'] . '" data-path-s="' . $img . '"  data-toggle="tooltip" data-placement="top" title="' . __('Главное превью товара') . '"></span><span class="pull-right">&nbsp;</span>' . $select . '</div><div class="panel-body text-center"><a href="#" class="setAlt" data-id="' . $row['id'] . '" data-alt="' . $row['info'] . '">'.$img_file.'</a></div><div class="panel-footer ' . $parent_style . '">' . __('Подтип') . ': ' . $select_option . '</div></div></div>';

            if ($i == $row_num) {
                $img_list .= '</div>';
                $i = 1;
            } else
                $i++;

            $count++;
        }

    if (!empty($data_pic) and is_array($data_pic) and count($data_pic) % $row_num != 0)
        $img_list .= '</div>';

    $img_add = $PHPShopGUI->setIcon(false, "img_new", false, array('load' => true, 'server' => true, 'url' => true, 'multi' => true,'search'=>true,'ffmpeg'=>true), $img_width);

    $disp = $PHPShopGUI->setCollapse('Добавить изображение или видео', $img_add);

    if (!empty($img_list))
        $disp .= $PHPShopGUI->setCollapse('Дополнительные изображения и видео', $img_list);

    return $disp;
}

?>
