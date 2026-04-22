<?php

function showcaseelement_menu_hook($obj, $row, $rout) {

    if ($rout == 'MIDDLE') {
        if (!empty($row['selector_enabled'])){
             $obj->set('topMenuLink', '#" data-toggle="modal" data-target="#ShowcaseMenu" data-href="');
        }
           
    }
   
}

$addHandler = array
    (
    'topMenu' => 'showcaseelement_menu_hook',
);
