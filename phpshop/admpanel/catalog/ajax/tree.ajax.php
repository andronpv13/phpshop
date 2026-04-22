<?php

session_start();
$_classPath = "../../../";
include($_classPath . "class/obj.class.php");
PHPShopObj::loadClass(array("base", "category", "string", "array", "system", "lang"));

$PHPShopBase = new PHPShopBase($_classPath . "inc/config.ini", true, true);
$PHPShopBase->chekAdmin();
$PHPShopSystem = new PHPShopSystem();

$PHPShopLang = new PHPShopLang(array('locale' => $_SESSION['lang'], 'path' => 'admin'));

// Права менеджеров
if ($PHPShopSystem->ifSerilizeParam('admoption.rule_enabled', 1) and ! $PHPShopBase->Rule->CheckedRules('catalog', 'remove')) {
    $where = array('secure_groups' => " REGEXP 'i" . $_SESSION['idPHPSHOP'] . "i' or secure_groups = ''");
} else
    $where = false;


$PHPShopCategoryArray = new PHPShopCategoryArray($where);
$CategoryArray = $PHPShopCategoryArray->getArray();

$CategoryArray[0]['name'] = '- ' . __('Корневой уровень') . ' -';
$tree_array = array();
$i = 0;

$getKey = $PHPShopCategoryArray->getKey('parent_to.id', true);
if (is_array($getKey))
    foreach ($getKey as $k => $v) {
        foreach ($v as $cat) {

            if ($CategoryArray[$cat]['skin_enabled'] == 1)
                $CategoryArray[$cat]['name'] .= ' <span class="glyphicon glyphicon-minus-sign" title="' . __('Скрыт') . '"></span>';

            $tree_array[$k]['sub'][$cat] = $CategoryArray[$cat]['name'];
        }
        $tree_array[$k]['name'] = $CategoryArray[$k]['name'];
        $tree_array[$k]['id'] = $k;
    }

// Ссылки
if (is_numeric($_GET['id']) or $_GET['action'] == 'new' or $_GET['path'] == 'catalog.list') {
    $currentId = $_GET['id'];
    $prePath = '?path=catalog&id=';
    $addNodes = false;
} else {
    $currentId = $_GET['cat'];
    $prePath = '?path=catalog&cat=';
    $addNodes = true;
}

if (is_array($tree_array[0]['sub']))
    foreach ($tree_array[0]['sub'] as $k => $v) {

        $result[$i] = array(
            'text' => PHPShopString::win_utf8($v)
        );

        $nodes = treegenerator(@$tree_array[$k], 100);

        if (is_array($nodes)) {
            $result[$i]['nodes'] = $nodes;

            if (!empty($addNodes))
                $result[$i]['selectable'] = false;
            else
                $result[$i]['selectable'] = true;
        }

        if ($k == $currentId) {
            $result[$i]['state']['expanded'] = true;
            $result[$i]['state']['selected'] = true;
        }

        //$result[$i]['href'] = $prePath . $k;
        $result[$i]['tags'] = $k;
        $i++;
    }

// Построение дерева категорий
function treegenerator($array, $m) {
    global $tree_array, $currentId, $prePath, $addNodes;
    static $i;

    if (empty($i))
        $i = $m;

    $result = null;

    if (!empty($array['sub']) and is_array($array['sub'])) {
        foreach ($array['sub'] as $k => $v) {

            $nodes = treegenerator(@$tree_array[$k], $i);

            $result[$i] = array(
                'text' => PHPShopString::win_utf8($v) . '<span class="hide">' . $k . '</span>'
            );

            if (is_array($nodes)) {
                $result[$i]['nodes'] = $nodes;
                $result[$i]['selectable'] = false;

                if (!empty($addNodes))
                    $result[$i]['selectable'] = false;
                else
                    $result[$i]['selectable'] = true;
            }

            if ($k == $currentId) {
                $result[$i]['state']['expanded'] = true;
                $result[$i]['state']['selected'] = true;
            }

            //$result[$i]['href'] = $prePath . $k;
            $result[$i]['tags'] = $k;
            $i++;
        }
    }
    return $result;
}

if (!empty($addNodes)) {

    $result[] = array(
        'text' => PHPShopString::win_utf8(__('Неопределенные товары')),
        'selectable' => false,
        'nodes' => array(
            1000001 => array(
                'text' => PHPShopString::win_utf8(__('Загруженные CRM')),
                'icon' => 'glyphicon glyphicon-hdd',
                'tags' => '1000001',
                'editable' => 0
            ),
            1000002 => array(
                'text' => PHPShopString::win_utf8(__('Загруженные CSV')),
                'icon' => 'glyphicon glyphicon-import',
                'tags' => '0&sub=csv',
                'editable' => 0
            )
            ,
            1000004 => array(
                'text' => PHPShopString::win_utf8(__('Удаленные')),
                'icon' => 'glyphicon glyphicon-trash',
                'tags' => '1000004',
                'editable' => 0
            ),
            1000005 => array(
                'text' => PHPShopString::win_utf8(__('Отложенные')),
                'icon' => 'glyphicon glyphicon-copy',
                'tags' => '1000005',
                'editable' => 0
            )
        )
    );

    // Журнал загрузок
    $PHPShopOrmImport = new PHPShopOrm($GLOBALS['SysValue']['base']['exchanges_log']);
    $data = $PHPShopOrmImport->getList(['*'], ['import_id' => '!=""', 'status' => "='1'", 'import_id' => '!=""'], ['order' => 'date desc'], ['limit' => 100]);

    $i = 0;
    if (count($data) > 0) {
        foreach ($data as $row) {

            $file = PHPShopString::win_utf8(pathinfo($row['file'])['basename']);
            $info = unserialize($row['info']);
            $count = $info[2];

            if (empty($count))
                continue;
            else
                $i++;

            if ($i < 10)
                $exchanges['200000' . $i] = array(
                    'text' => PHPShopDate::get($row['date'], true) . ': ' . $file . '<span class="badge">' . $count . '</span>',
                    'icon' => 'glyphicon glyphicon-import',
                    'tags' => '0&import=' . $row['import_id'],
                    'editable' => 0
                );
        }


        $result[] = array(
            'text' => PHPShopString::win_utf8(__('История импортов')),
            'selectable' => false,
            'nodes' => $exchanges
        );
    }
}

if (empty($_GET['sub']))
    $_GET['sub'] = null;


if (in_array($_GET['cat'], array(1000001, 1000002, 1000004)) or $_GET['sub'] == 'csv') {
    if ($_GET['sub'] == 'csv')
        $_GET['cat'] = 1000002;
    $result[count($result) - 1]['nodes'][$_GET['cat']]['state']['expanded'] = true;
    $result[count($result) - 1]['nodes'][$_GET['cat']]['state']['selected'] = true;
}

header("Content-Type: application/json");
echo json_encode($result);
