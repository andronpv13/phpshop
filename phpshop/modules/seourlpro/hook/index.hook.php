<?php

function index_seourlpro_hook($obj, $row, $rout){
    if($rout == 'START'){
        return true;
    }
}

$addHandler = array
    (
    'index' => 'index_seourlpro_hook',
);
?>