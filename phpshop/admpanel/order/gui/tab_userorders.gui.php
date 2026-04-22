<?php

function tab_userorders($data, $option) {
    global $currency;
    
    $status=$option['status'];
    $status[0]['name']=__('Новый заказ');
    $color=$option['color'];
    $currency=$option['currency'];
    $total=$i=0;
    
    $PHPShopInterface = new PHPShopInterface();
    $PHPShopInterface->checkbox_action = false;
    $PHPShopInterface->setCaption(array("№", "20%"),array("Статус", "40%"),  array("Дата", "15%"), array("Скидка", "15%"), array("Сумма", "15%", array('align' => 'right')));


    // Таблица с данными заказов
    $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['orders']);
    $PHPShopOrm->debug = false;
    $data = $PHPShopOrm->select(array('*'), array('user' => '=' . intval($data['user'])), array('order' => 'datas desc'), array('limit' => 100));
    if (is_array($data))
        foreach ($data as $row) {

            if (empty($row['fio']) and !empty($row['mail'])) {
                $row['fio'] = $row['mail'];
            }

            $datas = PHPShopDate::get($row['datas']);

            // Статус
            if (is_array($status[$row['statusi']]))
                $status_name = $status[$row['statusi']]['name'];
            else
                $status_name = __('Не определен');

            if ($row['id'] < 100)
                $uid = '<span class="hidden-xs">' . __('Заказ') . '</span> ' . $row['uid'];
            else
                $uid = $row['uid'];
            
            $PHPShopOrder = new PHPShopOrderFunction($row['id'], $row);
            if(empty($row['sum'])){
                $row['sum']=$PHPShopOrder->getTotal(false, '');
            }
                
            
            $total+=$row['sum'];
            $i++;
           
            $color[0]['color']=null;

            $PHPShopInterface->setRow(array('name' => $uid, 'link' => '?path=order&return=intro&id=' . $row['id']),array('name' => '<span class="hidden-xs" style="color:' . $color[$row['statusi']]['color'] . '">' . $status_name . '</span>', 'link' => '?path=order&return=intro&id=' . $row['id'], 'class' => 'label-link'),  array('name' => $datas),array('name' => $PHPShopOrder->getDiscount().' %'),  array('name' => $row['sum'] . ' ' . $currency, 'align' => 'right', 'class' => ''));
        }
        
        $PHPShopInterface->setRow(false,array('name' => __('Количество заказов').': '.$i,'class'=>'text-muted'), false, array('name' => __('Итого').':','class'=>'text-muted'), array('name' => $total . ' ' . $currency, 'align' => 'right', 'class' => 'text-success '));


    return '<table class="table table-hover">'.$PHPShopInterface->_CODE.'</table>';
}

?>