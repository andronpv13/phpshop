<?php
function updateSliderfilter($data) {
    $_POST['name_value']= str_replace(',', '.', $_POST['name_value']);

}

$addHandler = array(
    'actionStart' => null,
    'actionDelete' => null,
    'actionInsert'=>'updateSliderfilter',
    'actionUpdate' => 'updateSliderfilter'
);
?>