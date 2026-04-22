<?php
/**
 * Добавление 4-го товара в спецпредложения
 */
function specMain_hook($obj) {
    $obj->cell=4;
}
 
 
/**
 * Изменение сетки товаров в "Сейчас покупают"
 * @param array $obj объект
 */
function nowBuy_hook($obj) {
    $obj->cell=4;
    $obj->limitpos = 4; // Количество выводимых позиций
    $obj->limitorders = 4; // Количество запрашиваемых заказов
    $obj->enabled=2;
}

$addHandler = array
    (
    'nowBuy' => 'nowBuy_hook',
    'specMain'=>'specMain_hook'
);
?>