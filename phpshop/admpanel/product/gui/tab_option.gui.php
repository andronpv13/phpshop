<?php

/**
 * Панель подтипов товара
 * @param array $row массив данных
 * @return string 
 */
function tab_option($data) {
    global $PHPShopInterface, $PHPShopSystem, $CategoryArray;

    $PHPShopInterface = new PHPShopInterface();
    $PHPShopInterface->action_title['value-edit'] = 'Редактировать';
    $PHPShopInterface->action_title['value-delete'] = 'Удалить';
    $PHPShopInterface->action_title['value-copy'] = 'Сделать копию';

    PHPShopObj::loadClass("sort");
    $PHPShopParentNameArray = new PHPShopParentNameArray(array('id' => '=' . @$CategoryArray[$data['category']]['parent_title']));
    $parent_title = $PHPShopParentNameArray->getParam(@$CategoryArray[$data['category']]['parent_title'] . ".name");
    $parent_color = $PHPShopParentNameArray->getParam(@$CategoryArray[$data['category']]['parent_title'] . ".color");

    if (empty($parent_title))
        $parent_title = __("Размер");

    if (empty($parent_color))
        $parent_color = __("Цвет");

    $PHPShopInterface->dropdown_action_form = false;
    $PHPShopInterface->checkbox_action = false;
    $PHPShopInterface->setCaption(array("Иконка", "5%", array('sort' => 'none')), array($parent_title . ' <a class="glyphicon glyphicon glyphicon-cog" href="?path=catalog&id=' . $data['category'] . '&tab=3" target="_blank" title="'.__('Изменить').'" style="cursor:pointer;"></a>', "35%",array('locale'=>false)), array($parent_color . ' <a class="glyphicon glyphicon glyphicon-cog" href="?path=catalog&id=' . $data['category'] . '&tab=3" title="'.__('Изменить').'" style="cursor:pointer;"></a>', "20%",array('locale'=>false)), array("Кол-во", "10%"), array("Цена", "15%"), array(null, "10%"), array("Вывод", "5%", array('align' => 'right')));

    // Таблица с данными
    $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['products']);
    $PHPShopOrm->debug = false;
    $PHPShopOrm->mysql_error = false;

    $parent_array = @explode(",", $data['parent']);
    if (is_array($parent_array))
        foreach ($parent_array as $v)
            if (!empty($v))
                $parent_array_true[] = $v;

    if (!empty($data['parent']) and is_array($parent_array_true)) {

        // Подтипы из 1С
        if ($PHPShopSystem->ifSerilizeParam('1c_option.update_option'))
            $data_option = $PHPShopOrm->select(array('*'), array('uid' => ' IN ("' . implode('","', $parent_array_true) . '")', 'parent_enabled' => "='1'"), array('order' => 'num,name DESC'), array('limit' => 300));
        else
            $data_option = $PHPShopOrm->select(array('*'), array('id' => ' IN ("' . implode('","', $parent_array_true) . '")', 'parent_enabled' => "='1'"), array('order' => 'num,name DESC'), array('limit' => 300));
    }

    if (!empty($data_option) and is_array($data_option))
        foreach ($data_option as $row) {

            // Иконка
            if (!empty($row['pic_small']))
                $image = '<img src="' . $row['pic_small'] . '" data-big="'.$row['pic_big'].'" onerror="this.onerror = null;this.src = \'./images/no_photo.gif\'" class="media-object">';
            elseif (!empty($data['pic_small'])) {
                $image = '<img src="' . $data['pic_small'] . '" data-big="'.$data['pic_big'].'" onerror="this.onerror = null;this.src = \'./images/no_photo.gif\'" class="media-object">';
                $row['pic_small'] = $data['pic_small'];
            } else
                $image = '<img class="media-object" data-big="" src="./images/no_photo.gif">';


            // Название
            if (empty($row['parent']) and empty($row['parent2']) and ! empty($row['name']))
                $row['parent'] = htmlentities($row['name'], ENT_COMPAT, $GLOBALS['PHPShopBase']->codBase);

            // Вывод
            if (empty($row['enabled']) or ! empty($row['sklad']))
                $icon = '<span class="pull-right text-muted glyphicon glyphicon-eye-close" data-toggle="tooltip" data-placement="top" title="' . __('Скрыто') . '"></span>';
            else
                $icon = null;

            if ($row['color'] == '#ffffff')
                $row['color'] = '';

            $PHPShopInterface->setRow(array('name' => $image, 'link' => $row['pic_small'], 'target' => '_blank', 'align' => 'left'), array('name' => $row['parent'], 'editable' => 'parent_new', 'id' => $row['id']), array('name' => $row['parent2'], 'editable' => 'parent2_new', 'id' => $row['id'], 'color' => $row['color']), array('name' => $row['items'], 'align' => 'center', 'editable' => 'items_new', 'id' => $row['id']), array('name' => $row['price'], 'editable' => 'price_new', 'id' => $row['id']), array('action' =>
                array('value-edit', 'value-copy', '|', 'value-delete', 'id' => $row['id']), 'align' => 'center'), array('name' => $icon));
        }

    $PHPShopInterface->setRow(array('name' => null), array('name' => '<input style="width:100%" data-id="" placeholder="' . __('Добавить') . '" name="name_option_new" class="form-control input-sm" value="">'), array('name' => '<input style="width:100%" data-id="" placeholder="' . __('Добавить') . '" name="name2_option_new" class="form-control input-sm " value="">'), array('name' => '<input style="width:100%" class="form-control input-sm" name="items_option_new" value="1">'), array('name' => '<input style="width:100%" class="form-control input-sm" name="price_option_new" value="' . $data['price'] . '">'), array('name' => '<button data-toggle="tooltip" data-placement="top" type="button" name="addOption" class="btn btn-default btn-sm" value="" data-original-title="' . __('Добавить подтип') . '"><span class="glyphicon glyphicon-plus"></span> ' . __('Добавить') . '</button>', 'align' => 'left'), '');
    $disp = '<table class="table table-hover value-list">' . $PHPShopInterface->getContent() . '</table>';

    return $disp;
}

?>