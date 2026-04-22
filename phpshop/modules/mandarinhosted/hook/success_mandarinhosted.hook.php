<?php

function success_mod_mandarinhosted_hook($obj, $value){
  if(!empty($_REQUEST['payment']) && $_REQUEST['payment'] === 'mandarinhosted'){
    $obj->order_metod = 'modules" and id="10027';
    $obj->message();
    return true;
  }
}

$addHandler = array('index' => 'success_mod_mandarinhosted_hook');
