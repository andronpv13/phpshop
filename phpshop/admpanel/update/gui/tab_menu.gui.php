<?php

function tab_menu() {
    global $subpath,$help;

    ${'menu_active_' . $subpath[1]} = 'active';
    
    $tree = '
       <ul class="nav nav-pills nav-stacked">
       <li class="' . @$menu_active_update . '"><a href="?path=update">'.__('Обновление').'</a></li>
       <li class="' . @$menu_active_restore . '"><a href="?path=update.restore">'.__('Восстановление').'</a></li>
       </ul>';
        
        $help = '<p class="text-muted">'.__('Полный список доступных инструментов для обновления в <a href="https://wiki.phpshop.ru/ustanovka-i-obnovlenie/obnovlenie-phpshop" target="_blank" class="btn btn-default btn-xs"><span class="glyphicon glyphicon-book"></span> Учебнике</a>').'</p>';
    
    return $tree;
}

?>
