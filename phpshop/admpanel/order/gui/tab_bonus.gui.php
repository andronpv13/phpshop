<?php

/**
 * Панель бонусов к заказу
 * @param array $data массив данных
 * @return string 
 */
function tab_bonus($data) {
    global $PHPShopGUI;

    $disp = $PHPShopGUI->setDiv('left', "<p>" . __('Списано бонусов') . ": <span class='label label-default'>" . $data['bonus_minus'] . "</span></p>");
    $disp .= $PHPShopGUI->setDiv('left', "<p>" . __('Начислено бонусов') . ": <span class='label label-default'>" . $data['bonus_plus'] . "</span></p>");

    return $disp;
}

?>