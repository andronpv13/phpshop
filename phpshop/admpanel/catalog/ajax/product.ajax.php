<?php

session_start();
$_classPath = "../../../";
include($_classPath . "class/obj.class.php");
PHPShopObj::loadClass(array("base", "system", "admgui", "orm", "date", "xml", "security", "string", "parser", "mail", "lang"));
$subpath[0] = 'catalog';

$PHPShopBase = new PHPShopBase($_classPath . "inc/config.ini", true, true);
$PHPShopBase->chekAdmin();

PHPShopObj::loadClass('valuta');
PHPShopObj::loadClass('category');
PHPShopObj::loadClass('sort');

// Системные настройки
$PHPShopSystem = new PHPShopSystem();
$PHPShopLang = new PHPShopLang(array('locale' => $_SESSION['lang'], 'path' => 'admin'));

// Редактор GUI
$PHPShopInterface = new PHPShopInterface();

$PHPShopModules = new PHPShopModules($_classPath . "modules/");

/**
 * Вывод товаров
 */
// Настройка полей
$memory = $PHPShopInterface->getProductTableFields();

// Мобильная версия
if (PHPShopString::is_mobile()) {
    $PHPShopInterface->mobile = true;
    $product_class = null;
} elseif ($PHPShopSystem->getSerilizeParam('admoption.fast_view') != 1)
    $product_class = ' adminModal';

// Характеристики
if (!empty($memory['catalog.option']['sort'])) {
    $PHPShopSortArray = new PHPShopSortArray();
    $PHPShopSort = $PHPShopSortArray->getArray();
    $PHPShopSortCategoryArray = new PHPShopSortCategoryArray();
    $PHPShopSortCategory = $PHPShopSortCategoryArray->getArray();
} else {
    $PHPShopSort = array();
    $PHPShopSortCategory = array();
}

if (isset($_GET['where']['category']))
    unset($_GET['cat']);

if (empty($_GET['core']))
    $_GET['core'] = null;

// Тип поиска
switch ($_GET['core']) {
    case 'reg':
        $core = 'REGEXP';
        break;
    case 'eq':
        $core = ' = ';
        break;
    default: $core = 'REGEXP';
}

// ID всегда eq
if (!empty($_GET['where']['id']))
    $core = ' = ';

if (empty($_GET['sub']))
    $_GET['sub'] = null;

$where = false;

if (isset($_GET['start']))
    $limit = array('limit' => $_GET['start'] . ',' . $_GET['length']);
else
    $limit = array('limit' => 300);

if (isset($_GET['cat']) or isset($_GET['sub'])) {

    if (!empty($_GET['cat']) or $_GET['sub'] == 'csv' or isset($_GET['sub'])) {
        $where['(category'] = "=" . intval($_GET['cat']) . ' OR category=1000002  OR dop_cat LIKE \'%#' . intval($_GET['cat']) . '#%\') ';
    }

    // Отложенные
    if ($_GET['cat'] == 1000005 and ! empty($_COOKIE['idselect'])) {
        $idselect = json_decode($_COOKIE['idselect'], true);

        if (is_array($idselect)) {
            unset($where['(category']);
            $where['id'] = sprintf(' IN (%s)', implode(',', $idselect));
        }
    }

    // Направление сортировки из настроек каталога
    $PHPShopCategory = new PHPShopCategory(intval($_GET['cat']));
    switch ($PHPShopCategory->getParam('order_to')) {
        case(1): $order_direction = "";
            break;
        case(2): $order_direction = " desc";
            break;
        default: $order_direction = "";
            break;
    }
    switch ($PHPShopCategory->getParam('order_by')) {
        case(1): $order = array('order' => 'name' . $order_direction);
            break;
        case(2):
            $order = array('order' => 'price' . $order_direction);
            break;
        case(3): $order = array('order' => 'num' . $order_direction . ', items' . $order_direction . ', enabled' . $order_direction . ', datas desc');
            break;
        default: $order = array('order' => 'num' . $order_direction . ', items' . $order_direction . ', enabled' . $order_direction . ', datas desc');
            break;
    }
} else {

    $order = array('order' => 'enabled desc, datas desc');
}

// Расширенная сортировка из JSON
if (is_array($_GET['order']) and ! empty($_SESSION['jsort'][$_GET['order']['0']['column']])) {
    $order = array('order' => $_SESSION['jsort'][$_GET['order']['0']['column']] . ' ' . $_GET['order']['0']['dir']);
}

// Расширенный поиск
if (!empty($_GET['where']) and is_array($_GET['where'])) {
    foreach ($_GET['where'] as $k => $v) {

        if (isset($v) and $v != '') {

            if ($v == 'null')
                $v = '';

            // Характеристики
            if (is_array($v)) {
                $v = array_values(array_diff($v, [''])); // очистка пустых значений и сброс ключей.
                $vendor = null;
                foreach ($v as $kk => $vv) {
                    if ($kk == 0 and ! empty($vv))
                        $vendor .= "  LIKE '%" . PHPShopSecurity::TotalClean($vv) . "%'";
                    elseif (!empty($vv))
                        $vendor .= " and " . PHPShopSecurity::TotalClean($k) . " LIKE '%" . PHPShopSecurity::TotalClean($vv) . "%'";
                }

                if (!empty($vendor))
                    $where[PHPShopSecurity::TotalClean($k)] = $vendor;
            }

            // Категория
            else if ($k == "category") {
                $where['(category'] = "=" . intval($v) . ' OR dop_cat LIKE \'%#' . intval($v) . '#%\') ';
            } else
                $where[PHPShopSecurity::TotalClean($k)] = " " . $core . " '" . PHPShopSecurity::TotalClean($v) . "'";
        }
    }
}

// Сквозные характеристики
if (!empty($_GET['sort'])) {
    $sort_array = explode(":", $_GET['sort']);
    $PHPShopSortSearch = new PHPShopSortSearch($sort_array[0]);

    if (is_array($PHPShopSortSearch->sort_array))
        foreach ($PHPShopSortSearch->sort_array as $k => $v) {
            if ($v == $sort_array[1])
                $where['vendor'] = " REGEXP 'i" . $PHPShopSortSearch->sort_category . '-' . $k . "i'";
        }
}

// Постфикс
if (!empty($_GET['cat']))
    $postfix = '&cat=' . intval($_GET['cat']);
else
    $postfix = null;


// Таблица с данными
$PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['products']);
//$PHPShopOrm->debug = true;

if (empty($_GET['from']))
    $_GET['from'] = null;

// Быстрый поиск
if ($_GET['from'] == 'header') {
    $where['parent_enabled'] = "='0'";

    // Учет модуля ProductOption
    if (!empty($GLOBALS['SysValue']['base']['productoption']['productoption_system']))
        $where['parent_enabled'] .= " and (name " . $where['name'] . " or uid " . $where['name'] . " or id " . $where['name'] . " or option1 " . $where['name'] . " or option2 " . $where['name'] . " or option3 " . $where['name'] . " or option4 " . $where['name'] . " or option5 " . $where['name'] . ")";
    else
        $where['parent_enabled'] .= " and (name " . $where['name'] . " or uid " . $where['name'] . " or id " . $where['name'] . ")";

    unset($where['name']);
} elseif ($_GET['from'] != 'search') {

    // Убираем подтипы
    $where['parent_enabled'] = "='0'";
}

// Поиск размеров
if (!empty($_GET['parent'])) {
    $where['parent'] = "='" . $_GET['parent'] . "'";
    $where['parent_enabled'] = "='1'";
}

// Права менеджеров
if ($PHPShopSystem->ifSerilizeParam('admoption.rule_enabled', 1)) {
    $categoriesOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['categories']);
    $PHPShopOrm->debug = false;
    $categories = $categoriesOrm->getList(array('id'), array('secure_groups' => " REGEXP 'i" . $_SESSION['idPHPSHOP'] . "i' or secure_groups = ''"));
    $categoryIds = array();
    foreach ($categories as $category) {
        $categoryIds[] = $category['id'];
    }

    if (count($categoryIds) > 0 && !isset($where['category']) && !isset($where['(category'])) {
        $where['category'] = sprintf(' IN (%s)', implode(',', $categoryIds));
    }
}

// import id
if (!empty($_GET['import']))
    $where['import_id'] = '="' . PHPShopSecurity::TotalClean($_GET['import']) . '"';

// Поиск на странице JSON
if (!empty($_GET['search']['value'])) {
    $where['parent_enabled'] .= " and (name LIKE '%" . PHPShopString::utf8_win1251(PHPShopSecurity::TotalClean($_GET['search']['value'])) . "%' or uid LIKE '%" . PHPShopString::utf8_win1251(PHPShopSecurity::TotalClean($_GET['search']['value'])) . "%')";
}

// Вывод подтипов
if (!empty($_GET['parents'])) {
    $data = $PHPShopOrm->getOne(array('pic_small,parent'), array('id' => '=' . intval($_GET['parents'])));

    $parent_array = @explode(",", $data['parent']);
    if (is_array($parent_array))
        foreach ($parent_array as $v)
            if (!empty($v))
                $parent_array_true[] = $v;

    if (!empty($data['parent'])) {

        $PHPShopInterface->tr_id = 'data-parent="' . $_GET['parents'] . '"';
        $parent_pic = $data['pic_small'];

        // Подтипы из 1С
        if ($PHPShopSystem->ifSerilizeParam('1c_option.update_option'))
            $where = array('uid' => ' IN ("' . @implode('","', $parent_array_true) . '")', 'parent_enabled' => "='1'");
        else
            $where = array('id' => ' IN ("' . @implode('","', $parent_array_true) . '")', 'parent_enabled' => "='1'");
    }
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

$PHPShopOrm->mysql_error = false;
$sklad_enabled = $PHPShopSystem->getSerilizeParam('admoption.sklad_enabled');
//$PHPShopOrm->debug=true;

$data = $PHPShopOrm->select(array('*'), $where, $order, $limit);
if (is_array($data))
    foreach ($data as $row) {

        $PHPShopInterface->productTableRowLabels = [];

        // Картинка родителя
        if (empty($row['pic_small']) and ! empty($parent_pic))
            $row['pic_small'] = $parent_pic;

        if (!empty($row['pic_small']))
            $icon = '<img src="' . $row['pic_small'] . '" onerror="this.onerror = null;this.src = \'./images/no_photo.gif\'" class="media-object">';
        else
            $icon = '<img class="media-object" src="./images/no_photo.gif">';

        $PHPShopInterface->path = 'product&return=catalog';

        // Артикул
        if (!empty($row['uid']) and empty($memory['catalog.option']['uid']))
            $uid = '<div class="text-muted">' . __('Арт') . ' ' . $row['uid'] . '</div>';
        else
            $uid = null;

        if (!empty($memory['catalog.option']['label']) and ( !empty($row['newtip']) or ! empty($row['spec']) or ! empty($row['sklad']) or isset($row['yml'])) and empty($_GET['parents'])) {
            $uid .= '<div class="text-muted">';

            // Новинка
            if (!empty($row['newtip']))
                $PHPShopInterface->productTableRowLabels[] = '<a class="label label-info" title="' . __('Новинка') . '" href="?path=catalog' . $postfix . '&where[newtip]=1">' . __('Н') . '</a> ';

            // Спецпредложение
            if (!empty($row['spec']))
                $PHPShopInterface->productTableRowLabels[] = '<a class="label label-success" title="' . __('Спецпредложение') . '" href="?path=catalog' . $postfix . '&where[spec]=1">' . __('С') . '</a> ';

            // Отсутствует
            if (!empty($row['sklad']))
                $PHPShopInterface->productTableRowLabels[] = '<a class="label label-success" title="' . __('Отсутствует') . '" href="?path=catalog' . $postfix . '&where[sklad]=1">' . __('О') . '</a> ';

            // Перехват модуля
            $PHPShopModules->setAdmHandler(__FILE__, 'labels', $row);

            $uid .= implode('', $PHPShopInterface->productTableRowLabels);

            // Подтип
            if (!empty($row['parent']))
                $uid .= '<a class="label label-default view-parent" title="' . __('Подтипы') . '" href="#" data-id="' . $row['id'] . '">' . __('П') . '<span class="caret"></span></a> ';

            $uid .= '</div>';
        }

        // Enabled
        if (empty($row['enabled']))
            $enabled = 'text-muted';
        else
            $enabled = null;

        if ($row['items'] < 0 and $sklad_enabled)
            $row['items'] = 0;

        // Характеристики
        $sort_list = null;
        $sort = unserialize($row['vendor_array']);
        if (is_array($sort))
            foreach ($sort as $scat => $sorts) {
                if (!empty($PHPShopSortCategory[$scat]) and is_array($PHPShopSortCategory[$scat])) {
                    if (is_array($sorts))
                        foreach ($sorts as $s)
                            if (!empty($PHPShopSort[$s]['name']))
                                $sort_list .= '<a href="?path=sort&id=' . $scat . '" class="text-muted">' . $PHPShopSort[$s]['name'] . '</a>, ';
                }
            }

        $sort_list = substr($sort_list, 0, strlen($sort_list) - 2);
        
        if(strstr($row['odnotip'],",")){
            $odnotip = explode(',', $row['odnotip']);
            if(is_array($odnotip))
                 $join=count($odnotip);
            else $join=0;
        }
       else $join=0;

        $PHPShopInterface->productTableRow = [
            $row['id'],
            array(
                'name' => $icon,
                'link' => '../../shop/UID_' . $row['id'] . '.html',
                'target' => '_blank',
                'align' => 'left',
                'view' => intval($memory['catalog.option']['icon'])
            ),
            array(
                'name' => $row['name'],
                'sort' => 'name',
                'link' => '?path=product&return=catalog.' . $row['category'] . '&id=' . $row['id'],
                'align' => 'left',
                'addon' => $uid,
                'class' => $enabled . $product_class,
                'id' => $row['id'],
                'view' => intval($memory['catalog.option']['name'])
            ),
            array(
                'name' => $row['num'],
                'sort' => 'num',
                'align' => 'center',
                'editable' => 'num_new',
                'view' => intval($memory['catalog.option']['num'])
            ),
            array(
                'name' => $row['id'],
                'sort' => 'id',
                'view' => intval($memory['catalog.option']['id'])
            ),
            array(
                'name' => $row['uid'],
                'sort' => 'uid',
                'view' => intval($memory['catalog.option']['uid'])
            ),
            array(
                'name' => $row['price'],
                'sort' => 'price',
                'editable' => 'price_new',
                'view' => intval($memory['catalog.option']['price'])
            ),
            array(
                'name' => $row['price2'],
                'sort' => 'price2',
                'editable' => 'price2_new',
                'view' => intval($memory['catalog.option']['price2'])
            ),
            array(
                'name' => $row['price3'],
                'sort' => 'price3',
                'editable' => 'price3_new',
                'view' => intval($memory['catalog.option']['price3'])
            ),
            array(
                'name' => $row['price4'],
                'sort' => 'price4',
                'editable' => 'price4_new',
                'view' => intval($memory['catalog.option']['price4'])
            ),
            array(
                'name' => $row['price5'],
                'sort' => 'price5',
                'editable' => 'price5_new',
                'view' => intval($memory['catalog.option']['price5'])
            ),
            array(
                'name' => $row['price_n'],
                'sort' => 'price_n',
                'editable' => 'price_n_new',
                'view' => intval($memory['catalog.option']['price_n'])
            ),
            array(
                'name' => $row['price_purch'],
                'sort' => 'price_purch',
                'editable' => 'price_purch_new',
                'view' => intval($memory['catalog.option']['price_purch'])
            ),
            array(
                'name' => $row['items'],
                'sort' => 'items',
                'align' => 'center',
                'editable' => 'items_new',
                'view' => intval($memory['catalog.option']['item'])
            ),
            array(
                'name' => @$row['items1'],
                'sort' => 'items1',
                'align' => 'center',
                'editable' => 'items1_new',
                'view' => intval($memory['catalog.option']['items1'])
            ),
            array(
                'name' => @$row['items2'],
                'sort' => 'items2',
                'align' => 'center',
                'editable' => 'items2_new',
                'view' => intval($memory['catalog.option']['items2'])
            ),
            array(
                'name' => @$row['items3'],
                'sort' => 'items3',
                'align' => 'center',
                'editable' => 'items3_new',
                'view' => intval($memory['catalog.option']['items3'])
            ),
            array(
                'name' => $join,
                'view' => intval($memory['catalog.option']['join']),
                'sort' => 'odnotip',
            ),
            array(
                'name' => $sort_list . "",
                'view' => intval($memory['catalog.option']['sort']),
                'sort' => 'vendor_array',
            )
        ];

        // Перехват модуля
        $PHPShopModules->setAdmHandler(__FILE__, 'grid', $row);

        $PHPShopInterface->productTableRow[] = array(
            'action' => array('edit', 'copy', 'url', '|', 'delete', 'id' => $row['id']),
            'align' => 'center',
            'view' => intval($memory['catalog.option']['menu'])
        );
        $PHPShopInterface->productTableRow[] = array(
            'status' => array('enable' => $row['enabled'], 'align' => 'right', 'caption' => array('Выкл', 'Вкл')),
            'sort' => 'enabled',
            'view' => intval($memory['catalog.option']['status'])
        );

        $PHPShopInterface->setRow(...$PHPShopInterface->productTableRow);
    }

$total = $PHPShopOrm->select(array("COUNT('id') as count"), $where, $order);

if (isset($_GET['cat'])) {

    if ($_GET['cat'] == 1000001)
        $catname = __('Загруженные CRM');
    elseif ($_GET['cat'] == 0)
        $catname = __('Загруженные CSV');
    elseif ($_GET['cat'] == 1000004)
        $catname = __('Удаленные товары');
    elseif ($_GET['cat'] == 1000005)
        $catname = __('Отложенные товары');
    else
        $catname = $PHPShopCategory->getName();
}
elseif (isset($_GET['where']))
    $catname = __('Поиск');
else
    $catname = __('Новые товары');

if (empty($_GET['search']['value']))
    $PHPShopInterface->_AJAX["catname"] = PHPShopString::win_utf8($catname) . ' [' . $total['count'] . ']';
else
    $PHPShopInterface->_AJAX["catname"] =PHPShopString::win_utf8(__('Поиск')) . ' "' .PHPShopSecurity::true_search($_GET['search']['value']) . '"';

if (!empty($total['count'])) {
    $PHPShopInterface->_AJAX["recordsFiltered"] = $total['count'];
} else {
    $PHPShopInterface->_AJAX["data"] = array();
    $PHPShopInterface->_AJAX["recordsFiltered"] = 0;
}

$_SESSION['jsort'] = $PHPShopInterface->_AJAX["sort"];
unset($PHPShopInterface->_AJAX["sort"]);

if (!empty($_GET['parents']))
    exit($PHPShopInterface->_CODE);
else {
    header("Content-Type: application/json");
    exit(json_encode($PHPShopInterface->_AJAX));
}
?>