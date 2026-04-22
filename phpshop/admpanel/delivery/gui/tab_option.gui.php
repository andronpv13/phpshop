<?php

/**
 * Панель дополнительных опций
 * @param array $row массив данных
 * @return string 
 */
function tab_option($data) {
    global $PHPShopGUI;
    
    $data_fields = unserialize($data['data_fields']);
    
    $disp="<table class='table table-striped autofill'>
              <tr><th>".__('Поле')."</th><th>".__('Вкл / Выкл')."</th><th>".__('Название при выводе')."</th><th>".__('Обязательное')."</th><th>".__('Приоритет')."</th></tr>"
            . "<tr><td>".__('Страна')."</td>"
            . "<td>" . $PHPShopGUI->setCheckbox('data_fields[enabled][country][enabled]', 1, false, @$data_fields['enabled']['country']['enabled']) . "</td>"
            . "<td>" . $PHPShopGUI->setInputText(false, 'data_fields[enabled][country][name]', @$data_fields['enabled']['country']['name'], "200") . "</td>"
            . "<td>" . $PHPShopGUI->setCheckbox('data_fields[enabled][country][req]', 1, false, @$data_fields['enabled']['country']['req']) . "</td>"
            . "<td>" . $PHPShopGUI->setInputText(false, 'data_fields[num][country]', @$data_fields['num']['country'], "50") . "</td></tr>"
            . "<tr><td>".__('Регион/штат')."</td>"
            . "<td>" . $PHPShopGUI->setCheckbox('data_fields[enabled][state][enabled]', 1, false, @$data_fields['enabled']['state']['enabled']) . "</td>"
            . "<td>" . $PHPShopGUI->setInputText(false, 'data_fields[enabled][state][name]', @$data_fields['enabled']['state']['name'], "200") . "</td>"
            . "<td>" . $PHPShopGUI->setCheckbox('data_fields[enabled][state][req]', 1, false, @$data_fields['enabled']['state']['req']) . "</td>"
            . "<td>" . $PHPShopGUI->setInputText(false, 'data_fields[num][state]', @$data_fields['num']['state'], "50") . "</td></tr>"
            . "<tr><td>".__('Город')."</td>"
            . "<td>" . $PHPShopGUI->setCheckbox('data_fields[enabled][city][enabled]', 1, false, @$data_fields['enabled']['city']['enabled']) . "</td>"
            . "<td>" . $PHPShopGUI->setInputText(false, 'data_fields[enabled][city][name]', @$data_fields['enabled']['city']['name'], "200") . "</td>"
            . "<td>" . $PHPShopGUI->setCheckbox('data_fields[enabled][city][req]', 1, false, @$data_fields['enabled']['city']['req']) . "</td>"
            . "<td>" . $PHPShopGUI->setInputText(false, 'data_fields[num][city]', @$data_fields['num']['city'], "50") . "</td></tr>"
            . "<tr><td>".__('Индекс')."</td>"
            . "<td>" . $PHPShopGUI->setCheckbox('data_fields[enabled][index][enabled]', 1, false, @$data_fields['enabled']['index']['enabled']) . "</td>"
            . "<td>" . $PHPShopGUI->setInputText(false, 'data_fields[enabled][index][name]', @$data_fields['enabled']['index']['name'], "200") . "</td>"
            . "<td>" . $PHPShopGUI->setCheckbox('data_fields[enabled][index][req]', 1, false, @$data_fields['enabled']['index']['req']) . "</td>"
            . "<td>" . $PHPShopGUI->setInputText(false, 'data_fields[num][index]', @$data_fields['num']['index'], "50") . "</td></tr>"
            . "<tr><td>".__('ФИО')."</td>"
            . "<td>" . $PHPShopGUI->setCheckbox('data_fields[enabled][fio][enabled]', 1, false, @$data_fields['enabled']['fio']['enabled']) . "</td>"
            . "<td>" . $PHPShopGUI->setInputText(false, 'data_fields[enabled][fio][name]', @$data_fields['enabled']['fio']['name'], "200") . "</td>"
            . "<td>" . $PHPShopGUI->setCheckbox('data_fields[enabled][fio][req]', 1, false, @$data_fields['enabled']['fio']['req']) . "</td>"
            . "<td>" . $PHPShopGUI->setInputText(false, 'data_fields[num][fio]', @$data_fields['num']['fio'], "50") . "</td></tr>"
            . "<tr><td>".__('Телефон')."</td>"
            . "<td>" . $PHPShopGUI->setCheckbox('data_fields[enabled][tel][enabled]', 1, false, @$data_fields['enabled']['tel']['enabled']) . "</td>"
            . "<td>" . $PHPShopGUI->setInputText(false, 'data_fields[enabled][tel][name]', @$data_fields['enabled']['tel']['name'], "200") . "</td>"
            . "<td>" . $PHPShopGUI->setCheckbox('data_fields[enabled][tel][req]', 1, false, @$data_fields['enabled']['tel']['req']) . "</td>"
            . "<td>" . $PHPShopGUI->setInputText(false, 'data_fields[num][tel]', @$data_fields['num']['tel'], "50") . "</td></tr>"
            . "<tr><td>".__('Улица')."</td>"
            . "<td>" . $PHPShopGUI->setCheckbox('data_fields[enabled][street][enabled]', 1, false, @$data_fields['enabled']['street']['enabled']) . "</td>"
            . "<td>" . $PHPShopGUI->setInputText(false, 'data_fields[enabled][street][name]', @$data_fields['enabled']['street']['name'], "200") . "</td>"
            . "<td>" . $PHPShopGUI->setCheckbox('data_fields[enabled][street][req]', 1, false, @$data_fields['enabled']['street']['req']) . "</td>"
            . "<td>" . $PHPShopGUI->setInputText(false, 'data_fields[num][street]', @$data_fields['num']['street'], "50") . "</td></tr>"
            . "<tr><td>".__('Дом')."</td>"
            . "<td>" . $PHPShopGUI->setCheckbox('data_fields[enabled][house][enabled]', 1, false, @$data_fields['enabled']['house']['enabled']) . "</td>"
            . "<td>" . $PHPShopGUI->setInputText(false, 'data_fields[enabled][house][name]', @$data_fields['enabled']['house']['name'], "200") . "</td>"
            . "<td>" . $PHPShopGUI->setCheckbox('data_fields[enabled][house][req]', 1, false, @$data_fields['enabled']['house']['req']) . "</td>"
            . "<td>" . $PHPShopGUI->setInputText(false, 'data_fields[num][house]', @$data_fields['num']['house'], "50") . "</td></tr>"
            . "<tr><td>".__('Подъезд')."</td>"
            . "<td>" . $PHPShopGUI->setCheckbox('data_fields[enabled][porch][enabled]', 1, false, @$data_fields['enabled']['porch']['enabled']) . "</td>"
            . "<td>" . $PHPShopGUI->setInputText(false, 'data_fields[enabled][porch][name]', @$data_fields['enabled']['porch']['name'], "200") . "</td>"
            . "<td>" . $PHPShopGUI->setCheckbox('data_fields[enabled][porch][req]', 1, false, @$data_fields['enabled']['porch']['req']) . "</td>"
            . "<td>" . $PHPShopGUI->setInputText(false, 'data_fields[num][porch]', @$data_fields['num']['porch'], "50") . "</td></tr>"
            . "<tr><td>".__('Код домофона')."</td>"
            . "<td>" . $PHPShopGUI->setCheckbox('data_fields[enabled][door_phone][enabled]', 1, false, @$data_fields['enabled']['door_phone']['enabled']) . "</td>"
            . "<td>" . $PHPShopGUI->setInputText(false, 'data_fields[enabled][door_phone][name]', @$data_fields['enabled']['door_phone']['name'], "200") . "</td>"
            . "<td>" . $PHPShopGUI->setCheckbox('data_fields[enabled][door_phone][req]', 1, false, @$data_fields['enabled']['door_phone']['req']) . "</td>"
            . "<td>" . $PHPShopGUI->setInputText(false, 'data_fields[num][door_phone]', @$data_fields['num']['door_phone'], "50") . "</td></tr>"
            . "<tr><td>".__('Квартира')."</td>"
            . "<td>" . $PHPShopGUI->setCheckbox('data_fields[enabled][flat][enabled]', 1, false, @$data_fields['enabled']['flat']['enabled']) . "</td>"
            . "<td>" . $PHPShopGUI->setInputText(false, 'data_fields[enabled][flat][name]', @$data_fields['enabled']['flat']['name'], "200") . "</td>"
            . "<td>" . $PHPShopGUI->setCheckbox('data_fields[enabled][flat][req]', 1, false, @$data_fields['enabled']['flat']['req']) . "</td>"
            . "<td>" . $PHPShopGUI->setInputText(false, 'data_fields[num][flat]', @$data_fields['num']['flat'], "50") . "</td></tr>"
            . "<tr><td>".__('Время доставки')."</td>"
            . "<td>" . $PHPShopGUI->setCheckbox('data_fields[enabled][delivtime][enabled]', 1, false, @$data_fields['enabled']['delivtime']['enabled']) . "</td>"
            . "<td>" . $PHPShopGUI->setInputText(false, 'data_fields[enabled][delivtime][name]', @$data_fields['enabled']['delivtime']['name'], "200") . "</td>"
            . "<td>" . $PHPShopGUI->setCheckbox('data_fields[enabled][delivtime][req]', 1, false, @$data_fields['enabled']['delivtime']['req']) . "</td>"
            . "<td>" . $PHPShopGUI->setInputText(false, 'data_fields[num][delivtime]', @$data_fields['num']['delivtime'], "50") . "</td></tr>"
            . "</table>";
    
    return $disp;
}
?>
