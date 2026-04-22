<?php

$TitlePage = __("Товары");
PHPShopObj::loadClass('valuta');
PHPShopObj::loadClass('category');
PHPShopObj::loadClass('sort');
unset($_SESSION['jsort']);

/**
 * Вывод товаров
 */
function actionStart() {
    global $PHPShopInterface, $TitlePage, $PHPShopSystem, $PHPShopBase;

    // Права менеджеров
    if ($PHPShopSystem->ifSerilizeParam('admoption.rule_enabled', 1) and ! $PHPShopBase->Rule->CheckedRules('catalog', 'remove')) {
        $where = array('secure_groups' => " REGEXP 'i" . $_SESSION['idPHPSHOP'] . "i' or secure_groups = ''");
        $secure_groups = true;
    } else
        $where = $secure_groups = false;

    if (empty($_GET['cat']))
        $_GET['cat'] = null;

    if (empty($_GET['sub']))
        $_GET['sub'] = null;


    $where['id'] = '=' . intval($_GET['cat']);

    $PHPShopCategoryArray = new PHPShopCategoryArray($where);
    $PHPShopCategoryArray->order = array('order' => 'num, name');
    $CategoryArray = $PHPShopCategoryArray->getArray();

    if (!empty($CategoryArray[$_GET['cat']]['name']))
        $catname = '  &rarr;  <span id="catname">' . $CategoryArray[$_GET['cat']]['name'] . '</span>';
    elseif (!empty($CategoryArray[$_GET['sub']]['name']))
        $catname = '  &rarr;  <span id="catname">' . $CategoryArray[$_GET['sub']]['name'] . '</span>';
    elseif (isset($_GET['where']) and ! empty($_GET['where']['name']))
        $catname = '  &rarr;  <span id="catname">' . __('Поиск') . '</span>';
    else
        $catname = '  &rarr;  <span id="catname">' . __('Новые товары') . '</span>';

    // Права менеджеров
    if ($secure_groups and isset($_GET['cat']) and empty($CategoryArray[$_GET['cat']]['name'])) {
        $catname = " /  <span class='text-danger'><span class='glyphicon glyphicon-lock'></span> " . __('Доступ закрыт') . '</span>';
        $_GET['where']['disabled'] = true;
    }

    $PHPShopInterface->action_select['Предпросмотр'] = array(
        'name' => 'Предпросмотр',
        'class' => 'cat-view hide',
    );

    $PHPShopInterface->action_select['Редактировать выбранные'] = array(
        'name' => 'Редактировать выбранные',
        'action' => 'edit-select',
        'class' => 'disabled'
    );
    
    $PHPShopInterface->action_select['Объдинить выбранные'] = array(
        'name' => 'Объдинить выбранные',
        'action' => 'join-select',
        'class' => 'disabled'
    );

    $PHPShopInterface->action_select['Скопировать ID выбранных'] = array(
        'name' => 'Скопировать ID выбранных',
        'action' => 'copy-id-select',
        'class' => 'disabled'
    );

    $PHPShopInterface->action_select['Отложить выбранные'] = array(
        'name' => 'Отложить выбранные',
        'action' => 'id-select',
        'class' => 'disabled'
    );

    $PHPShopInterface->action_select['Убрать из отложенных выбранные'] = array(
        'name' => 'Убрать из отложенных выбранные',
        'action' => 'id-select-delete',
        'class' => 'disabled'
    );

    $PHPShopInterface->action_select['Настройка'] = array(
        'name' => 'Настройка полей',
        'action' => 'option enabled'
    );

    $PHPShopInterface->action_select['Поиск'] = array(
        'name' => '<span class=\'glyphicon glyphicon-search\'></span> Расширенный поиск',
        'action' => 'search enabled'
    );

    $PHPShopInterface->action_select['Редактировать каталог'] = array(
        'name' => 'Редактировать каталог',
        'action' => 'enabled',
        'class' => 'cat-select hide',
        'url' => '?path=' . $_GET['path'] . '&id=' . intval($_COOKIE['cat']) . '&return=catalog.' . intval($_COOKIE['cat'])
    );

    $PHPShopInterface->action_title['copy'] = 'Сделать копию';
    $PHPShopInterface->action_title['url'] = 'Открыть URL';

    $PHPShopInterface->action_button['Добавить товар'] = array(
        'name' => '',
        'action' => 'addNewModal',
        'class' => 'btn btn-default btn-sm navbar-btn',
        'type' => 'button',
        'icon' => 'glyphicon glyphicon-plus',
        'tooltip' => 'data-toggle="tooltip" data-placement="left" title="' . __('Добавить товар') . '" data-cat="' . $_GET['cat'] . '"'
    );

    // Отключение бытсрого просмотра
    if ($PHPShopSystem->getSerilizeParam('admoption.fast_view') == 1)
        $PHPShopInterface->action_button['Добавить товар']['action'] = 'addNew';

    // Убираем меню если много полей
    $count_view = 0;

    if (is_array($PHPShopInterface->getProductTableFields()['catalog.option']))
        foreach ($PHPShopInterface->getProductTableFields()['catalog.option'] as $view)
            if (!empty($view))
                $count_view++;

    if ($count_view > 8 and empty($_COOKIE['fullscreen']))
        $function_del = $function_pre_del = null;
    else {
        $function_del = 'Удалить выбранные';
        $function_pre_del = '|';
    }


    $PHPShopInterface->setActionPanel($TitlePage . $catname, array('Поиск', '|', 'Предпросмотр', 'Настройка', 'Редактировать каталог', 'Редактировать выбранные', 'CSV', '|', 'Объдинить выбранные', 'Скопировать ID выбранных', 'Отложить выбранные', 'Убрать из отложенных выбранные', $function_pre_del, $function_del), array('Добавить товар'));

    $PHPShopInterface->setCaption(
            ...getTableCaption()
    );

    $PHPShopInterface->addJSFiles('./catalog/gui/catalog.gui.js', './js/bootstrap-treeview.min.js', './js/bootstrap-colorpicker.min.js');
    $PHPShopInterface->addCSSFiles('./css/bootstrap-treeview.min.css', './css/bootstrap-colorpicker.min.css');
    $PHPShopInterface->path = 'catalog';

    // Прогрессбар
    $treebar = '<div class="progress">
  <div class="progress-bar progress-bar-striped active" role="progressbar" aria-valuenow="45" aria-valuemin="0" aria-valuemax="100" style="width: 45%">
    <span class="sr-only">' . __('Загрузка') . '..</span>
  </div>
</div>';

    // Поиск категорий
    $search = '<div class="none" id="category-search" style="padding-bottom:5px;"><div class="input-group input-sm">
                <input type="input" class="form-control input-sm" type="search" id="input-category-search" placeholder="' . __('Искать в категориях...') . '" value="">
                 <span class="input-group-btn">
                  <a class="btn btn-default btn-sm" id="btn-search" type="submit"><span class="glyphicon glyphicon-search"></span></a>
                 </span>
            </div></div>';

    $sidebarleft[] = array('title' => 'Категории', 'content' => $search . '<div id="tree">' . $treebar . '</div>', 'title-icon' => '<div class="hidden-xs"><span class="glyphicon glyphicon-plus addNewElement" data-toggle="tooltip" data-placement="top" title="' . __('Добавить каталог') . '"></span>&nbsp;<span class="glyphicon glyphicon-chevron-down" data-toggle="tooltip" data-placement="top" title="' . __('Развернуть все') . '"></span>&nbsp;<span class="glyphicon glyphicon-chevron-up" data-toggle="tooltip" data-placement="top" title="' . __('Свернуть') . '"></span>&nbsp;<span class="glyphicon glyphicon-search" id="show-category-search" data-toggle="tooltip" data-placement="top" title="' . __('Поиск') . '"></span></div>');

    $PHPShopInterface->setSidebarLeft($sidebarleft, 3);

    $PHPShopInterface->Compile(3);
}

function getTableCaption() {
    global $PHPShopInterface, $PHPShopModules, $PHPShopSystem;

    $memory = $PHPShopInterface->getProductTableFields();

    // Мобильная версия
    if (PHPShopString::is_mobile()) {
        $PHPShopInterface->mobile = true;
    }

    // Дополнительный склад
    $PHPShopOrmWarehouse = new PHPShopOrm($GLOBALS['SysValue']['base']['warehouses']);
    $Warehouses = $PHPShopOrmWarehouse->select(array('*'), array('enabled' => "='1'"), array('order' => 'num DESC'), array('limit' => 100));

    if (is_array($Warehouses))
        foreach ($Warehouses as $warehouse) {
            $dataWarehouse[$warehouse['id']] = $warehouse;
        }

    // Убираем меню если много полей
    $count_view = 0;

    if (is_array($memory['catalog.option']))
        foreach ($memory['catalog.option'] as $view)
            if (!empty($view))
                $count_view++;

    //if ($count_view > 8 and empty($_COOKIE['fullscreen']))
        //unset($memory['catalog.option']['menu']);

    // Режим каталога
    if ($PHPShopSystem->getParam("shop_type") == 1) {
        $memory['catalog.option']['item'] = 0;
    }

    $PHPShopInterface->productTableCaption = [
        [null, "2%"],
        ["Иконка", "5%", ['sort' => 'none', 'view' => (int) $memory['catalog.option']['icon']]],
        ["Название", "40%", ['view' => (int) $memory['catalog.option']['name']]],
        ["№", "10%", ['view' => (int) $memory['catalog.option']['num']]],
        ["ID", "10%", ['view' => (int) $memory['catalog.option']['id']]],
        ["Артикул", "15%", ['view' => (int) $memory['catalog.option']['uid']]],
        ["Цена", "10%", ['view' => (int) $memory['catalog.option']['price']]],
        ["Цена 2", "10%", ['view' => (int) $memory['catalog.option']['price2']]],
        ["Цена 3", "10%", ['view' => (int) $memory['catalog.option']['price3']]],
        ["Цена 4", "10%", ['view' => (int) $memory['catalog.option']['price4']]],
        ["Цена 5", "10%", ['view' => (int) $memory['catalog.option']['price5']]],
        ["Ст. цена", "10%", ['view' => (int) $memory['catalog.option']['price_n']]],
        ["Зак. цена", "10%", ['view' => (int) $memory['catalog.option']['price_purch']]],
        ["Кол-во", "10%", ['view' => (int) $memory['catalog.option']['item']]],
        [$dataWarehouse[1]['name'], "10%", ['view' => (int) $memory['catalog.option']['items1']]],
        [$dataWarehouse[2]['name'], "10%", ['view' => (int) $memory['catalog.option']['items2']]],
        [$dataWarehouse[3]['name'], "10%", ['view' => (int) $memory['catalog.option']['items3']]],
        ["Аналоги", "10%", ['view' => (int) $memory['catalog.option']['join']]],
        ["Характеристики", "25%", ['view' => (int) $memory['catalog.option']['sort']]]
    ];

    // Перехват модуля
    $PHPShopModules->setAdmHandler(__FILE__, __FUNCTION__, $PHPShopInterface->productTableCaption);

    $PHPShopInterface->productTableCaption[] = [PHPShopText::nbsp(15), "7%", ['view' => (int) $memory['catalog.option']['menu']]];
    $PHPShopInterface->productTableCaption[] = ["Статус", "7%", ['align' => 'right', 'view' => (int) $memory['catalog.option']['status']]];

    return $PHPShopInterface->productTableCaption;
}

// Обработка событий
$PHPShopGUI->getAction();
?>