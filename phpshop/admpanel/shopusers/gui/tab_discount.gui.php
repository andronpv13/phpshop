<?php

function tab_discount($cumulative_discount) {
    global $PHPShopGUI, $PHPShopSystem;
    
    $cumulative_discount = unserialize($cumulative_discount);
    $cumulative_html = null;
    $key=0;
    if (is_array($cumulative_discount))
        foreach ($cumulative_discount as $key => $value) {

            if(!empty($value))
            $cumulative_html.= "<tr>"
                    . "<td>" . $PHPShopGUI->setInputText(false, 'cumulative_sum_ot[' . $key . ']', $value['cumulative_sum_ot'], "150") . "</td>"
                    . "<td>" . $PHPShopGUI->setInputText(false, 'cumulative_sum_do[' . $key . ']', $value['cumulative_sum_do'], "150") . "</td>"
                    . "<td>" . $PHPShopGUI->setInputText(false, 'cumulative_discount[' . $key . ']', $value['cumulative_discount'], "100") . "</td>"
                    . "<td class='text-center'>" . $PHPShopGUI->setCheckbox('cumulative_enabled[' . $key . ']', 1, false, $value['cumulative_enabled']) . "</td>"
                    . "</tr>"
                    . "<tr>";
        }

    $disp = "<table class='table table-striped'>
              <tr><th>".__("Сумма от")." " . $PHPShopSystem->getDefaultValutaCode() . "</th><th>".__("Сумма до")." " . $PHPShopSystem->getDefaultValutaCode() . "</th><th>".__("Скидка")." %</th><th class='text-center'>".__("Вкл / Выкл")."</th></tr>"
            . $cumulative_html
            . "<tr>"
            . "<td>" . $PHPShopGUI->setInputText(false, 'cumulative_sum_ot['.($key+1).']', null, "150") . "</td>"
            . "<td>" . $PHPShopGUI->setInputText(false, 'cumulative_sum_do['.($key+1).']', null, "150") . "</td>"
            . "<td>" . $PHPShopGUI->setInputText(false, 'cumulative_discount['.($key+1).']', null, "100") . "</td>"
            . "<td class='text-center'>" . $PHPShopGUI->setCheckbox('cumulative_enabled['.($key+1).']', 1, false, null) . "</td>"
            . "</tr>"
            . "<tr>"
            . "<td>" . $PHPShopGUI->setInputText(false, 'cumulative_sum_ot['.($key+2).']', null, "150") . "</td>"
            . "<td>" . $PHPShopGUI->setInputText(false, 'cumulative_sum_do['.($key+2).']', null, "150") . "</td>"
            . "<td>" . $PHPShopGUI->setInputText(false, 'cumulative_discount['.($key+2).']', null, "100") . "</td>"
            . "<td class='text-center'>" . $PHPShopGUI->setCheckbox('cumulative_enabled['.($key+2).']', 1, false, null) . "</td>"
            . "</tr>"
            . "<tr>"
            . "<td >" . $PHPShopGUI->setInputText(false, 'cumulative_sum_ot['.($key+3).']', null, "150") . "</td>"
            . "<td>" . $PHPShopGUI->setInputText(false, 'cumulative_sum_do['.($key+3).']', null, "150") . "</td>"
            . "<td>" . $PHPShopGUI->setInputText(false, 'cumulative_discount['.($key+3).']', null, "100") . "</td>"
            . "<td class='text-center'>" . $PHPShopGUI->setCheckbox('cumulative_enabled['.($key+3).']', 1, false, null) . "</td>"
            . "</tr>"
            . "</table>";

    $disp.=$PHPShopGUI->setHelp('Новые поля появятся после заполнения текущих полей и сохранения результата.');

    return $disp;
}

?>