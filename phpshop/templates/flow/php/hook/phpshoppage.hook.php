<?php

/**
 * Вывод категорий для поиска
 */
function template_odnotip($obj, $data, $rout) {

    if($rout == 'START'){
        $obj->odnotip_setka_num = 4;
    }
}

$addHandler = array
    (
    'odnotip' => 'template_odnotip'
);
?>
