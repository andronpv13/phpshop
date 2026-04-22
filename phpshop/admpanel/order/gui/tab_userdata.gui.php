<?php

function tab_userdata($data, $order) {
    global $PHPShopGUI;
    
    
    $PHPShopGUI->field_col=3;
    
    $help='<p class="text-muted hidden-xs data-row">'.__('Дополнительные поля в заказе и требование к их обязательному заполнению можно настроить в разделе').'  <a href="?path=delivery&id='.$order['Person']['dostavka_metod'].'&tab=1"><span class="glyphicon glyphicon-share-alt"></span>'.__('Управление доставкой').'</a></p><hr>';
    
    if(empty($data['fio'])){
        $data['fio']=$order['Person']['name_person'];
    }
    
    if(empty($data['user']))
        $user='<div class="form-group form-group-sm ">
        <label class="col-sm-3 control-label">'.__('ФИО').':</label><div class="col-sm-9">
        <input data-set="3" name="fio_new" maxlength="50" class="search_user form-control input-sm" type="search" data-trigger="manual" data-container="body" data-toggle="popover" data-placement="bottom" data-html="true"  data-content="" placeholder="'.__('Найти...').'" value="'.$data['fio'].'">
        <input name="user_new" type="hidden">
     </div></div> ';
    else $user=$PHPShopGUI->setField("ФИО", $PHPShopGUI->setInputText('', 'fio_new', $data['fio'] ));
    

    $order['Person']=$PHPShopGUI->valid($order['Person'],'adr_name','org_name','mail');
    
    $PHPShopDelivery = new PHPShopDelivery($order['Person']['dostavka_metod']);
    $data_fields = unserialize($PHPShopDelivery->getParam('data_fields'));

    // Данные покупателя
    $disp1 = $user .
            $PHPShopGUI->setField($data_fields['enabled']['tel']['name'] ? $data_fields['enabled']['tel']['name'] : 'Телефон', $PHPShopGUI->setInputText('', 'tel_new', PHPShopSecurity::TotalClean($data['tel'],6))) .
            $PHPShopGUI->setField("E-mail", $PHPShopGUI->setInputText('', 'person[mail]', PHPShopSecurity::TotalClean($order['Person']['mail']))).
            $PHPShopGUI->setField($data_fields['enabled']['country']['name'] ? $data_fields['enabled']['country']['name'] : "Страна", $PHPShopGUI->setInputText('', 'country_new', PHPShopSecurity::TotalClean($data['country']))) .
            $PHPShopGUI->setField($data_fields['enabled']['state']['name'] ? $data_fields['enabled']['state']['name'] : "Регион", $PHPShopGUI->setInputText('', 'state_new', PHPShopSecurity::TotalClean($data['state']))) .
            $PHPShopGUI->setField($data_fields['enabled']['city']['name'] ? $data_fields['enabled']['city']['name'] :"Город", $PHPShopGUI->setInputText('', 'city_new', PHPShopSecurity::TotalClean($data['city']))) .
            $PHPShopGUI->setField($data_fields['enabled']['index']['name'] ? $data_fields['enabled']['index']['name'] :"Индекс", $PHPShopGUI->setInputText('', 'index_new', PHPShopSecurity::TotalClean($data['index']))) .
            $PHPShopGUI->setField($data_fields['enabled']['street']['name'] ? $data_fields['enabled']['street']['name'] :"Улица", $PHPShopGUI->setInputText('', 'street_new', PHPShopSecurity::TotalClean($data['street'] . $order['Person']['adr_name']))) .
            $PHPShopGUI->setField($data_fields['enabled']['house']['name'] ? $data_fields['enabled']['house']['name'] :"Дом", $PHPShopGUI->setInputText('', 'house_new', PHPShopSecurity::TotalClean($data['house']))) .
            $PHPShopGUI->setField($data_fields['enabled']['porch']['name'] ? $data_fields['enabled']['porch']['name'] :"Подъезд", $PHPShopGUI->setInputText('', 'porch_new', PHPShopSecurity::TotalClean($data['porch']))) .
            $PHPShopGUI->setField($data_fields['enabled']['door_phone']['name'] ? $data_fields['enabled']['door_phone']['name'] :"Домофон", $PHPShopGUI->setInputText('', 'door_phone_new', PHPShopSecurity::TotalClean($data['door_phone']))) .
            $PHPShopGUI->setField($data_fields['enabled']['flat']['name'] ? $data_fields['enabled']['flat']['name'] :"Квартира", $PHPShopGUI->setInputText('', 'flat_new', PHPShopSecurity::TotalClean($data['flat'])));

    // Юр. данные покупателя
    $disp2 = $PHPShopGUI->setField("Компания", $PHPShopGUI->setInputText('', 'org_name_new', PHPShopSecurity::TotalClean($data['org_name'] . $order['Person']['org_name'],6))) .
            $PHPShopGUI->setField("ИНН", $PHPShopGUI->setInputText('', 'org_inn_new', PHPShopSecurity::TotalClean($data['org_inn']))) .
            $PHPShopGUI->setField("КПП", $PHPShopGUI->setInputText('', 'org_kpp_new', PHPShopSecurity::TotalClean($data['org_kpp']))) .
            $PHPShopGUI->setField("Юр. адрес", $PHPShopGUI->setInputText('', 'org_yur_adres_new', PHPShopSecurity::TotalClean($data['org_yur_adres'],6))) .
            $PHPShopGUI->setField("Адрес", $PHPShopGUI->setInputText('', 'org_fakt_adres_new', PHPShopSecurity::TotalClean($data['org_fakt_adres']))) .
            $PHPShopGUI->setField("Р/С", $PHPShopGUI->setInputText('', 'org_ras_new', PHPShopSecurity::TotalClean($data['org_ras']))) .
            $PHPShopGUI->setField("Банк", $PHPShopGUI->setInputText('', 'org_bank_new', PHPShopSecurity::TotalClean($data['org_bank']))) .
            $PHPShopGUI->setField("К/С", $PHPShopGUI->setInputText('', 'org_kor_new', PHPShopSecurity::TotalClean($data['org_kor']))) .
            $PHPShopGUI->setField("БИК", $PHPShopGUI->setInputText('', 'org_bik_new', PHPShopSecurity::TotalClean($data['org_bik']))) .
            $PHPShopGUI->setField("Город", $PHPShopGUI->setInputText('', 'org_city_new', PHPShopSecurity::TotalClean($data['org_city']))).
            $PHPShopGUI->setField("Время", $PHPShopGUI->setInputText('', 'delivtime_new', PHPShopSecurity::TotalClean($data['delivtime']))).
            $PHPShopGUI->setField("Tracking", $PHPShopGUI->setInputText('', 'tracking_new', PHPShopSecurity::TotalClean($data['tracking'])));

    return $help.$PHPShopGUI->setGrid(array($disp1, 6), array($disp2, 6));
}

?>