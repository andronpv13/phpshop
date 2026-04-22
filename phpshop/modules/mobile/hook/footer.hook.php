<?php

function mobile_footer_hook() {
    $message = $GLOBALS['AddToTemplateMDetect']->message(); 
    if(!empty( $message))
    echo  $message;
}


$addHandler=array
        (
        'footer'=>'mobile_footer_hook'
);
?>