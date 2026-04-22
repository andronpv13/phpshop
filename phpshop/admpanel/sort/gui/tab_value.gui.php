<?php

/**
 * Значение характеристик
 */
function tab_value($product) {
    global $PHPShopInterface;

    $PHPShopInterface->action_title['remove'] = 'Удалить';
    $PHPShopInterface->action_title['value-edit'] = 'Редактировать';

    $PHPShopInterface->dropdown_action_form = false;
    $PHPShopInterface->checkbox_action = false;
    $PHPShopInterface->path = 'sort.value';
    $PHPShopInterface->setCaption(array("Приоритет", "7%"), array("Название", "75%"), array(null, "10%"), array(null, "3%"));

    // Таблица с данными
    $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['sort']);
    $PHPShopOrm->Option['where'] = ' or ';
    $PHPShopOrm->debug = false;
    $data = $PHPShopOrm->select(array('*'), array('category' => "=" . $product['id']), array('order' => 'num,name DESC'), array('limit' => 1000));
    if (is_array($data))
        foreach ($data as $row) {

            $PHPShopInterface->setRow(array('name' => $row['num'], 'editable' => 'num_value', 'id' => $row['id']), array('name' => $row['name'], 'editable' => 'name_value', 'id' => $row['id']), array('action' => array('value-edit', '|', 'remove', 'id' => $row['id']), 'align' => 'center'), '<span class="glyphicon glyphicon-remove remove hide" data-toggle="tooltip" data-id="' . $row['id'] . '" data-placement="top" title="'.__('Удалить').'"></span>');
        }

    $PHPShopInterface->setRow(array('name' => '<input style="width:100%" class="form-control input-sm" name="num_value" value="">'), array('name' => '<input style="width:100%" data-id="" placeholder="'.__('Добавить').'" name="name_value" class="form-control input-sm editable-add" value="">'), array('name' => '<button data-toggle="tooltip" data-placement="top" type="button" name="addValue" class="btn btn-default btn-sm" value="" data-original-title="' . __('Добавить значение') . '"><span class="glyphicon glyphicon-plus"></span> ' . __('Добавить') . '</button>', 'align' => 'left'), '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;');
    $disp = '<table class="table table-hover value-list">' . $PHPShopInterface->getContent() . '</table>';

    return $disp;
}

?>