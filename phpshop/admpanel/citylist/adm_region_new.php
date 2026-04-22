<?php

$TitlePage = __('Создание региона');

/**
 * Экшен загрузки форм редактирования
 */
function actionStart() {
    global $PHPShopGUI, $PHPShopModules;

    // Размер названия поля
    $PHPShopGUI->field_col = 3;

    $orm = new PHPShopOrm($GLOBALS['SysValue']['base']['citylist_country']);
    $countries = array_map(function ($country) {
        return [
            $country['name'],
            $country['country_id'],
            0
        ];
    }, $orm->getList(['country_id', 'name']));

    // Начальные данные
    $PHPShopGUI->setActionPanel(__("Создание региона"), false, ['Создать и редактировать', 'Сохранить и закрыть']);

    $PHPShopGUI->addJSFiles('./js/jquery.treegrid.js', './citylist/gui/citylist.gui.js');

    // Наименование
    $Tab = $PHPShopGUI->setField("Наименование региона", $PHPShopGUI->setInputText(false, 'name_new', null, '100%'));
    
    
    if(count($countries)>0)
    $Tab.= $PHPShopGUI->setField("Страна", $PHPShopGUI->setSelect('country_id_new', $countries, '100%', false, false, false));
    else $Tab .= $PHPShopGUI->setField("Страна", $PHPShopGUI->setInputText(false, 'country_name_new', null, '100%'));

    
    
    $collapse = $PHPShopGUI->setCollapse('Информация', $Tab);

    // Запрос модуля на закладку
    $PHPShopModules->setAdmHandler(__FILE__, __FUNCTION__, null);

    $PHPShopGUI->setTab(["Основное", $collapse]);

    // Левый сайдбар дерева категорий
    $orm = new PHPShopOrm($GLOBALS['SysValue']['base']['citylist_region']);
    $regions = $orm->getList(['region_id', 'name']);

    $tree_array = [];
    foreach ($regions as $region) {
        $tree_array[$region['region_id']] = [
            'name' => $region['name'],
            'id'   => $region['region_id'],
        ];
    }

    $GLOBALS['tree_array'] = &$tree_array;

    $tree = '<table class="tree table table-hover">';

    $PHPShopGUI->dropdown_action_form = false;
    foreach ($regions as $region) {
        $link = [
            [
                'url'  => sprintf('?path=citylist.region&id=%s', $region['region_id']),
                'name' => __('Редактировать')
            ]
        ];

        $tree.='<tr class="treegrid-' . $region['region_id'] . ' data-tree">
                    <td>
                        <a href="?path=citylist&cat=' . $region['region_id'] . '">' . $region['name'] . '</a>
                        <span class="pull-right">' . $PHPShopGUI->setDropdownAction($link) . '</span>
                    </td>
	            </tr>';
    }
    $tree.='
        </table>';
    $PHPShopGUI->dropdown_action_form = true;

    $sidebarleft[] = [
        'title'      => 'Регионы',
        'content'    => $tree,
        'title-icon' => '<a href="/phpshop/admpanel/admin.php?path=citylist.region&action=new" style="color: #333;"><span class="glyphicon glyphicon-plus newregion" data-toggle="tooltip" data-placement="top" 
                        title="'.__('Добавить регион').'"></span></a>'
    ];


    $PHPShopGUI->setSidebarLeft($sidebarleft, 3);
    $PHPShopGUI->sidebarLeftCell = 3;

    // Вывод кнопок сохранить и выход в футер
    $ContentFooter = $PHPShopGUI->setInput("submit", "saveID", "ОК", "right", 70, "", "but", "actionInsert.citylist.create");

    // Футер
    $PHPShopGUI->setFooter($ContentFooter);
    return true;
}

// Функция записи
function actionInsert() {
    global $PHPShopModules;

    // Перехват модуля
    $PHPShopModules->setAdmHandler(__FILE__, __FUNCTION__, $_POST);

    $orm = new PHPShopOrm($GLOBALS['SysValue']['base']['citylist_region']);
    $action = $orm->insert($_POST);
    
    // Новая страна
    if(!empty($_POST['country_name_new'])){
       $orm = new PHPShopOrm($GLOBALS['SysValue']['base']['citylist_country']);
       $_POST['name_new'] = $_POST['country_name_new'];
       $action = $orm->insert($_POST); 
    }
        
        

    if ($_POST['saveID'] == 'Создать и редактировать')
        header('Location: ?path=' . $_GET['path'] . '&id=' . $action);
    else
        header('Location: ?path=citylist');

    return $action;
}


// Обработка событий
$PHPShopGUI->getAction();

// Вывод формы при старте
$PHPShopGUI->setLoader($_POST['saveID'], 'actionStart');
?>