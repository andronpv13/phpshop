<?php

PHPShopObj::loadClass('sort');
$TitlePage = __("Характеристики");

$PHPShopSortCategoryArray = new PHPShopSortCategoryArray(array('category' => '=0'));
$SortCategoryArray = $PHPShopSortCategoryArray->getArray();

/**
 * Вывод товаров
 */
function actionStart() {
    global $PHPShopInterface, $TitlePage, $SortCategoryArray, $help;
    
    if(empty($_GET['cat']))
        $_GET['cat']=null;

    $PHPShopInterface->action_button['Добавить характеристику'] = array(
        'name' => '',
        'action' => 'addNew',
        'class' => 'btn btn-default btn-sm navbar-btn',
        'type' => 'button',
        'icon' => 'glyphicon glyphicon-plus',
        'tooltip' => 'data-toggle="tooltip" data-placement="left" title="'.__('Добавить характеристику').'" data-cat="' . $_GET['cat'] . '"'
    );

    $PHPShopInterface->action_select['Добавить группу'] = array(
        'name' => 'Добавить группу',
        'action' => 'enabled',
        'url' => '?path=' . $_GET['path'] . '&action=new&type=sub'
    );

    $PHPShopInterface->action_select['Очистить кеш'] = array(
        'name' => 'Очистить кеш фильтра',
        'action' => 'ResetCache'
    );
    
    $PHPShopInterface->action_select['Удалить неиспользуемые'] = array(
        'name' => 'Удалить неиспользуемые',
        'action' => 'CleanSort'
    );

    if (!empty($_GET['cat']))
        $PHPShopInterface->action_select['Редактировать группу'] = array(
            'name' => 'Редактировать группу',
            'action' => 'enabled',
            'url' => '?path=' . $_GET['path'] . '&type=sub&id=' . intval($_GET['cat'])
        );

    if (!empty($_GET['cat']))
        $TitlePage.=': ' . $SortCategoryArray[$_GET['cat']]['name'];

    $PHPShopInterface->setActionPanel($TitlePage, array('Редактировать группу', 'Добавить группу', '|','Очистить кеш', 'Удалить неиспользуемые', '|', 'Удалить выбранные'), array('Добавить характеристику'));
    $PHPShopInterface->setCaption(array(null, "1%"), array("Название", "40%"), array("", "8%"), array("Каталог" . "", "10%", array('align' => 'center')), array("Бренд" . "", "10%", array('align' => 'center')), array("Опция" . "", "10%", array('align' => 'center')), array("Фильтр" . "", "10%", array('align' => 'center')));

    $where = array('category' => '!=0');
    if (!empty($_GET['cat'])) {
        $where = array('category' => '=' . intval($_GET['cat']));
    }

    $PHPShopInterface->addJSFiles('./js/jquery.treegrid.js', './sort/gui/sort.gui.js');

    // Таблица с данными
    $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['sort_categories']);
    //$PHPShopOrm->Option['where'] = ' or ';
    $PHPShopOrm->debug = false;
    $data = $PHPShopOrm->select(array('*'), $where, array('order' => 'num, id desc'), array('limit' => 3000));
    if (is_array($data))
        foreach ($data as $row) {

            // Фильтр
            $filtr=array('checkbox' => array('val' => $row['filtr'],'name'=>'filtr'), 'align' => 'center');

            // Опция
            $goodoption=array('checkbox' => array('val' => $row['goodoption'],'name'=>'goodoption'), 'align' => 'center');

            // Бренд
            $brand=array('checkbox' => array('val' => $row['brand'],'name'=>'brand'), 'align' => 'center');

            // Виртуальный каталог
            $virtual=array('checkbox' => array('val' => $row['virtual'],'name'=>'virtual'), 'align' => 'center');

            // Описание
            if (!empty($row['description']))
                $help='<div class="text-muted">'.$row['description'].'</div>';
            else $help=null;

            $PHPShopInterface->path = 'sort';
            $PHPShopInterface->setRow($row['id'], array('name' => $row['name'], 'link' => '?path=sort&id=' . $row['id'], 'align' => 'left','addon' => $help), array('action' => array('edit', 'copy', '|', 'delete', 'id' => $row['id']), 'align' => 'center'), $virtual, $brand, $goodoption, $filtr);
        }

    $sidebarleft[] = array('title' => 'Группы', 'content' => $PHPShopInterface->loadLib('tab_menu_sort', false, './sort/'), 'title-icon' => '<span class="glyphicon glyphicon-plus newsub" data-toggle="tooltip" data-placement="top" title="' . __('Добавить группу') . '"></span>');
    $sidebarleft[] = array('title' => 'Подсказка', 'content' => $help, 'class' => 'hidden-xs');
    $PHPShopInterface->setSidebarLeft($sidebarleft, 3);

    $PHPShopInterface->Compile(3);
}

?>