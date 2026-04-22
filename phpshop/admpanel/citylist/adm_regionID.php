<?php

$TitlePage = __('Редактирование региона');

/**
 * Экшен загрузки форм редактирования
 */
function actionStart() {
    global $PHPShopGUI, $PHPShopModules;

    // Размер названия поля
    $PHPShopGUI->field_col = 3;
    $PHPShopGUI->action_select['Удалить регион'] = array(
        'name' => 'Удалить <span class="glyphicon glyphicon-trash"></span>',
        'locale' => true,
        'action' => 'deleteRegion',
        'url' => '#'
    );

    $orm = new PHPShopOrm($GLOBALS['SysValue']['base']['citylist_region']);
    $data = $orm->getOne(['*'], ['region_id' => sprintf('="%s"', (int) $_REQUEST['id'])]);

    $ormCountry = new PHPShopOrm($GLOBALS['SysValue']['base']['citylist_country']);
    $countries = array_map(function ($country) use ($data){
        return [
            $country['name'],
            $country['country_id'],
            $data['country_id']
        ];
    }, $ormCountry->getList(['country_id', 'name']));

    // Начальные данные
    $PHPShopGUI->setActionPanel(__("Редактирование региона"), ['Создать', '|', 'Удалить регион'], ['Сохранить', 'Сохранить и закрыть']);

    $PHPShopGUI->addJSFiles('./js/jquery.treegrid.js', './citylist/gui/citylist.gui.js');

    // Наименование
    $Tab = $PHPShopGUI->setField("Наименование региона", $PHPShopGUI->setInputText(false, 'name_new', $data['name'], '100%'));
    $Tab.= $PHPShopGUI->setField("Страна", $PHPShopGUI->setSelect('country_id_new', $countries, '100%', false, false, false));
    $collapse = $PHPShopGUI->setCollapse('Информация', $Tab);

    // Запрос модуля на закладку
    $PHPShopModules->setAdmHandler(__FILE__, __FUNCTION__, $data);

    $PHPShopGUI->setTab(["Основное", $collapse]);

    // Левый сайдбар дерева категорий
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
    $ContentFooter = $PHPShopGUI->setInput("hidden", "rowID", $data['region_id'], "right", 70, "", "but") .
        $PHPShopGUI->setInput("button", "delID", "Удалить", "right", 70, "", "but", "actionDelete.citylist.edit") .
        $PHPShopGUI->setInput("submit", "editID", "Сохранить", "right", 70, "", "but", "actionUpdate.citylist.edit") .
        $PHPShopGUI->setInput("submit", "saveID", "Применить", "right", 80, "", "but", "actionSave.citylist.edit");

    // Футер
    $PHPShopGUI->setFooter($ContentFooter);
    return true;
}

/**
 * Экшен сохранения
 */
function actionSave() {

    // Сохранение данных
    actionUpdate();

    header('Location: ?path=citylist');
}

/**
 * Экшен обновления
 * @return array
 */
function actionUpdate() {
    global $PHPShopModules;

    // Перехват модуля
    $PHPShopModules->setAdmHandler(__FILE__, __FUNCTION__, $_POST);
    $orm = new PHPShopOrm($GLOBALS['SysValue']['base']['citylist_region']);
    $orm->debug = false;

    $action = $orm->update($_POST, ['region_id' => '=' . (int) $_POST['rowID']]);

    return ["success" => $action];
}

// Функция удаления
function actionDelete() {
    global $PHPShopModules;

    // Перехват модуля
    $PHPShopModules->setAdmHandler(__FILE__, __FUNCTION__, $_POST);

    $orm = new PHPShopOrm($GLOBALS['SysValue']['base']['citylist_region']);
    $action = $orm->delete(['region_id' => '=' . (int) $_POST['rowID']]);
    
    // Удаление городов
    $orm = new PHPShopOrm($GLOBALS['SysValue']['base']['citylist_city']);
    $action = $orm->delete(['region_id' => '=' . (int) $_POST['rowID']]);

    return ['success' => $action];
}

// Обработка событий
$PHPShopGUI->getAction();

// Вывод формы при старте
$PHPShopGUI->setAction($_GET['id'], 'actionStart', 'none');
?>