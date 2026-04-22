<?php

/**
 * Вывод хитов в каталоге
 * param obj $obj
 * param array $row
 */
function add_catalog_hit_hook($obj, $row)
{
    include_once 'phpshop/modules/hit/class/Hit.php';
    $Hit = new Hit();
    $obj->set('hit', $Hit->getCatalogHits($obj->category));
}

function hit_UID_hook($obj, $dataArray, $rout)
{
    if (!empty($dataArray['hit'])) {
        $obj->set('hitIcon', PHPShopParser::file($GLOBALS['SysValue']['templates']['hit']['icon'], true, false, true));
    }
    else
        $obj->set('hitIcon', null);
}

$addHandler = array(
    'catalog_content' => 'add_catalog_hit_hook', 
    'UID' => 'hit_UID_hook'
    );
?>