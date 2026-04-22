<?php

/**
 * Изменение сетки товаров в "Сейчас покупают"
 * @param array $obj объект
 */
function nowBuy_hook($obj) {
    $obj->limitpos = 3; // Количество выводимых позиций
    $obj->limitorders = 5; // Количество запрашиваемых заказов
    $obj->enabled=2;
}

$addHandler = array
    (
    'nowBuy' => 'nowBuy_hook'
);
?>