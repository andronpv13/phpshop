<?php

function map_seourlpro_hook() {
    $GLOBALS['PHPShopSeoPro']->catArrayToMemory();
    $GLOBALS['PHPShopSeoPro']->catPageArrayToMemory();
}

$addHandler = array
    (
    'seourl' => 'map_seourlpro_hook'
);
?>