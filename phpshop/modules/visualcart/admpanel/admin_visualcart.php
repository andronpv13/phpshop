<?php

function getCartInfo($cart) {
    global $PHPShopSystem;
    $dis = null;
    $cart = unserialize($cart);
    $currency = ' ' . $PHPShopSystem->getDefaultValutaCode();
    if (is_array($cart))
        foreach ($cart as $val) {
            $dis .= '<a href="?path=product&id=' . $val['id'] . '&return=modules.dir.visualcart" data-toggle="tooltip" data-placement="top" title="' . $val['name'] . ' - ' . $val['price'] . $currency . '"><img src="' . $val['pic_small'] . '" class="media-object pull-left" alt="" style="padding:3px"></a> ';
        }
    return substr($dis, 0, strlen($dis) - 2);
}

function getUserName($id) {
    $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['shopusers']);
    $data = $PHPShopOrm->select(array('name,tel'), array('id' => '=' . $id), false, array('limit' => 1));
    if (is_array($data))
        return array('name' => $data['name'] . ' ' . $data['tel'], 'link' => '?path=shopusers&id=' . $id);
}

function getReferal($str) {
    $referal = explode(',', $str);
    $dis = null;
    if (is_array($referal)) {
        foreach ($referal as $val)
            $un_array[$val] = $val;

        foreach ($un_array as $val)
            $dis .= PHPShopText::a('http://' . $val, $val, false, false, false, '_blank') . '<br>';
    }
    
    if(empty($str))
        $dis=__('прямой заход');
    
    return $dis;
}

function actionStart() {
    global $PHPShopInterface, $PHPShopModules, $TitlePage, $select_name,$PHPShopSystem;

    unset($select_name[0]);

    $PHPShopInterface->checkbox_action = false;
    $PHPShopInterface->addJSFiles('../modules/visualcart/admpanel/gui/visualcart.gui.js');
    $PHPShopInterface->action_title['order'] = 'Создать заказ';
    $PHPShopInterface->setActionPanel(__('Брошенные корзины'), $select_name,false);
    $PHPShopInterface->setCaption(array("Пользователь", "23%"), array("Дата", "10%"), array("Товары", "30%"), array("Источник", "15%"), array("", "10%"), array("Итого", "7%", array('align' => 'right')));

    // Знак рубля
    if ($PHPShopSystem->getDefaultValutaIso() == 'RUB' or $PHPShopSystem->getDefaultValutaIso() == 'RUR')
        $currency = ' <span class="rubznak hidden-xs">p</span>';
    else
        $currency = $PHPShopSystem->getDefaultValutaCode();

    // SQL
    $PHPShopOrm = new PHPShopOrm($PHPShopModules->getParam("base.visualcart.visualcart_memory"));
    $data = $PHPShopOrm->select(array('*'), false, array('order' => 'id DESC'), array("limit" => 1000));
    if (is_array($data))
        foreach ($data as $row) {

            $name = getUserName($row['user']);

            if (empty($name) and ( !empty($row['mail']) or ! empty($row['name']) or ! empty($row['tel']))) {
                if (empty($row['name']))
                    $row['name'] = $row['mail'];
                $name = '<a href="mailto:' . $row['mail'] . '">' . $row['name'] . '</a> ' . $row['tel'];
            } elseif(empty($name))
                $name = $row['ip'];

            $PHPShopInterface->setRow($name, array('name' => PHPShopDate::get($row['date'], true), 'order' => $row['date']), getCartInfo($row['cart']), getReferal($row['referal']),array('action' => array('order', '|','delete', 'id' => $row['id']), 'align' => 'center'), number_format($row['sum'], 0, '', ' ').$currency);
        }
    $PHPShopInterface->Compile();
}

?>