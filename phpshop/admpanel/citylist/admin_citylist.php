<?php
$TitlePage = __("Города и регионы");

function actionStart() {
    global $PHPShopInterface;

    $orm = new PHPShopOrm($GLOBALS['SysValue']['base']['citylist_region']);
    $regions = array_column($orm->getList(['region_id', 'name']), 'name','region_id');

    $PHPShopInterface->setActionPanel(__("Города и регионы"), ['Удалить выбранные'], ['Добавить']);
    $PHPShopInterface->setCaption([null, "2%"], ["Город", "40%"], ["Регион", "40%"], ["", "10%"]);

    $PHPShopInterface->addJSFiles('./js/jquery.treegrid.js', './citylist/gui/citylist.gui.js');

    if (!empty($_GET['cat'])) {
        $where = ['region_id' => sprintf('="%s"', (int) $_GET['cat'])];
        $limit=null;
    }
    else $limit=['limit'=>300];

    // Таблица с данными
    $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['citylist_city']);
    $PHPShopOrm->debug = false;
    $data = $PHPShopOrm->getList(['*'], $where, ['order' => 'city_id DESC'],$limit);

    foreach ($data as $row) {
        $PHPShopInterface->setRow(
            $row['id'],
            ['name'   => $row['name'], 'link' => '?path=citylist&id=' . $row['city_id'], 'align' => 'left'],
            ['name'   => $regions[$row['region_id']], 'link' => '?path=citylist.region&id=' . $row['region_id'], 'align' => 'left'],
            ['action' => ['edit', '|', 'delete', 'id' => $row['city_id']], 'align' => 'center']
        );
    }

    // Левый сайдбар дерева категорий
    $tree_array = [];
    foreach ($regions as $id => $name) {
        $tree_array[$id] = [
            'name' => $name,
            'id'   => $id,
        ];
    }

    $GLOBALS['tree_array'] = &$tree_array;

    $tree = '<table class="tree table table-hover">';

    $PHPShopInterface->dropdown_action_form = false;
    foreach ($regions as $id => $name) {
        $link = [
            [
                'url'  => sprintf('?path=citylist.region&id=%s', $id),
                'name' => __('Редактировать')
            ]
        ];
        $tree.='<tr class="treegrid-' . $id . ' data-tree">
                    <td>
                        <a href="?path=citylist&cat=' . $id . '">' . $name . '</a>
                        <span class="pull-right">' . $PHPShopInterface->setDropdownAction($link) . '</span>
                    </td>
	            </tr>';
        }
    $PHPShopInterface->dropdown_action_form = true;

    $tree.='
        </table>';

    $sidebarleft[] = [
        'title'      => 'Регионы',
        'content'    => $tree,
        'title-icon' => '<a href="/phpshop/admpanel/admin.php?path=citylist.region&action=new" style="color: #333;"><span class="glyphicon glyphicon-plus newregion" data-toggle="tooltip" data-placement="top" 
                        title="'.__('Добавить регион').'"></span></a>'
    ];

    $PHPShopInterface->setSidebarLeft($sidebarleft, 3);

    $PHPShopInterface->Compile(3);
}

?>