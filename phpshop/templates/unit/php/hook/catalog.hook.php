<?php

function template_subcatalog_hook($obj,$data){

        if($data['i'] > 10){
            $obj->set('catalogClass','hide');
            $obj->set('catalogMoreClass','show');
        }
        else {
            $obj->set('catalogClass','');
            $obj->set('catalogMoreClass','hide');
        }
    
}

$addHandler = array
    (
    'subcatalog' => 'template_subcatalog_hook',
);
?>
