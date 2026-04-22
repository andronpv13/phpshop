<?php

function tab_addres($row) {
    global $PHPShopGUI;

    $PHPShopGUI->field_col = 3;
    $mass = unserialize($row);
    $dis=null;
    $count=0;
    
    if(!is_array($mass))
        $mass=array();
    

    if(empty($mass['list']) or count($mass['list']) < 1)
        $mass['list'][]=array('fio_new'=>__('Покупатель'));
    
    if (is_array($mass['list'])){
        
        foreach ($mass['list'] as $adrId => $adresData) {
            
            if($count>10)
                continue;
            else $count++;


            if ($mass['main'] == $adrId)
                $defaultChecked = 1;
            else
                $defaultChecked = 0;
            
            
            // Данные покупателя
            $Tab1= $PHPShopGUI->setField("ФИО", $PHPShopGUI->setInputText('', 'mass['.$adrId.'][fio_new]', @$adresData['fio_new'])) .
                    $PHPShopGUI->setField("Телефон", $PHPShopGUI->setInputText('', 'mass['.$adrId.'][tel_new]', @$adresData['tel_new'])) .
                    $PHPShopGUI->setField("Страна", $PHPShopGUI->setInputText('', 'mass['.$adrId.'][country_new]', @$adresData['country_new'])) .
                    $PHPShopGUI->setField("Регион/штат", $PHPShopGUI->setInputText('', 'mass['.$adrId.'][state_new]', @$adresData['state_new'])) .
                    $PHPShopGUI->setField("Город", $PHPShopGUI->setInputText('', 'mass['.$adrId.'][city_new]', @$adresData['city_new'])) .
                    $PHPShopGUI->setField("Индекс", $PHPShopGUI->setInputText('', 'mass['.$adrId.'][index_new]', @$adresData['index_new'])) .
                    $PHPShopGUI->setField("Улица", $PHPShopGUI->setInputText('', 'mass['.$adrId.'][street_new]', @$adresData['street_new'])) .
                    $PHPShopGUI->setField("Дом", $PHPShopGUI->setInputText('', 'mass['.$adrId.'][house_new]', @$adresData['house_new'])) .
                    $PHPShopGUI->setField("Подъезд", $PHPShopGUI->setInputText('', 'mass['.$adrId.'][porch_new]', @$adresData['porch_new'])) .
                    $PHPShopGUI->setField("Код домофона", $PHPShopGUI->setInputText('', 'mass['.$adrId.'][door_phone_new]', @$adresData['door_phone_new'])) .
                    $PHPShopGUI->setField("Квартира", $PHPShopGUI->setInputText('', 'mass['.$adrId.'][flat_new]', @$adresData['flat_new'])) .
                    $PHPShopGUI->setField("Время доставки", $PHPShopGUI->setInputText('', 'mass['.$adrId.'][delivtime_new]', @$adresData['delivtime_new'])) .
                    $PHPShopGUI->setField("Управление", $PHPShopGUI->setCheckbox('mass['.$adrId.'][default]', 1, 'Данные по умолчанию', $defaultChecked).'<br>'.$PHPShopGUI->setCheckbox('mass['.$adrId.'][delete]', 1, 'Удалить адрес', 0));

            // Юр. данные покупателя
            $Tab2= $PHPShopGUI->setField("Организация", $PHPShopGUI->setInputText('', 'mass['.$adrId.'][org_name_new]', @$adresData['org_name_new'])) .
                    $PHPShopGUI->setField("ИНН", $PHPShopGUI->setInputText('', 'mass['.$adrId.'][org_inn_new]', @$adresData['org_inn_new'])) .
                    $PHPShopGUI->setField("КПП", $PHPShopGUI->setInputText('', 'mass['.$adrId.'][org_kpp_new]', @$adresData['org_kpp_new'])) .
                    $PHPShopGUI->setField("Юр. адрес", $PHPShopGUI->setInputText('', 'mass['.$adrId.'][org_yur_adres_new]', @$adresData['org_yur_adres_new'])) .
                    $PHPShopGUI->setField("Факт. адрес", $PHPShopGUI->setInputText('', 'mass['.$adrId.'][org_fakt_adres_new]', @$adresData['org_fakt_adres_new'])) .
                    $PHPShopGUI->setField("РC", $PHPShopGUI->setInputText('', 'mass['.$adrId.'][org_ras_new]',@$adresData['org_ras_new'])) .
                    $PHPShopGUI->setField("Банк", $PHPShopGUI->setInputText('', 'mass['.$adrId.'][org_bank_new]',@$adresData['org_bank_new'])) .
                    $PHPShopGUI->setField("КC", $PHPShopGUI->setInputText('', 'mass['.$adrId.'][org_kor_new]', @$adresData['org_kor_new'])) .
                    $PHPShopGUI->setField("БИК", $PHPShopGUI->setInputText('', 'mass['.$adrId.'][org_bik_new]', @$adresData['org_bik_new'])) .
                    $PHPShopGUI->setField("Город", $PHPShopGUI->setInputText('', 'mass['.$adrId.'][org_city_new]', @$adresData['org_city_new']));

           $dis.=$PHPShopGUI->setCollapse('Адреса доставки №'.($adrId+1),'<div class="row"><div class="col-md-6">'.$Tab1.'</div><div class="col-md-6">'.$Tab2.'</div></div>');
        }
    }
         return  $dis;
}

?>