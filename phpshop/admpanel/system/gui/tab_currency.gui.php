<?php

function tab_currency($option) {
    global $PHPShopSystem;
    

    $def_currency = $PHPShopSystem->getDefaultValutaIso();

    $url = "http://www.cbr.ru/scripts/XML_daily.asp";
    $curs = $iso = array();
    $disp = '<li class="list-group-item">' . $def_currency . '/' . $def_currency . ' <span class="pull-right text-muted">1</span></li>';

    $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['currency']);
    $data = $PHPShopOrm->select(array('id,iso'), array('enabled' => "='1'"), false, array('limit' => 10));
    if (is_array($data))
        foreach ($data as $row)
            $iso[] = $row['iso'];

    $xml = @simplexml_load_file($url);


    if(@$xml->Valute)
    foreach ($xml->Valute as $m) {
        if (in_array($m->CharCode, $iso)) {
            $val_kurs = (float) str_replace(",", ".", (string) $m->Value);
            $curs[(string) $m->CharCode] = 1 / $val_kurs;
        }
    }

    foreach ($curs as $key => $value) {
        if($key == $option['iso']) 
        $disp.='<li class="list-group-item">' . $key . '/' . $def_currency . ' <span class="pull-right text-primary">' . round($value, 4) . '</span></li>';
        else $disp.='<li class="list-group-item">' . $key . '/' . $def_currency . ' <span class="pull-right text-muted">' . round($value, 4) . '</span></li>';
    }


    $tab = '
    <ul  class="list-group">' . $disp . '</ul>';
    return $tab;
}

?>