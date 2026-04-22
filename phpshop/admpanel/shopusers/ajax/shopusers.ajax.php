<?php

session_start();
$_classPath = "../../../";
include($_classPath . "class/obj.class.php");
PHPShopObj::loadClass(array("base", "system", "admgui", "orm", "date", "xml", "security", "string", "parser", "mail", "lang", "user"));
$subpath[0] = 'order';

$PHPShopBase = new PHPShopBase($_classPath . "inc/config.ini", true, true);
$PHPShopBase->chekAdmin();


// Системные настройки
$PHPShopSystem = new PHPShopSystem();
$PHPShopLang = new PHPShopLang(array('locale' => $_SESSION['lang'], 'path' => 'admin'));

// Редактор GUI
$PHPShopInterface = new PHPShopInterface();

// Статусы пользователей
$PHPShopUserStatus = new PHPShopUserStatusArray();
$PHPShopUserStatusArray = $PHPShopUserStatus->getArray();
$PHPShopUserStatusArray[0]['name'] = __('Пользователь');
$PHPShopUserStatusArray[0]['discount'] = 0;

if (isset($_GET['start']))
    $limit = array('limit' => $_GET['start'] . ',' . $_GET['length']);
else
    $limit = array('limit' => 30000);

// Мобильная версия
if (PHPShopString::is_mobile()) {
    $memory['shopusers.option']['name'] = 1;
    $memory['shopusers.option']['mail'] = 0;
    $memory['shopusers.option']['menu'] = 0;
    $memory['shopusers.option']['status'] = 1;
    $memory['shopusers.option']['discount'] = 1;
    $memory['shopusers.option']['action'] =0;
    $memory['shopusers.option']['date'] = 1;
    $memory['shopusers.option']['tel'] =0;
    $PHPShopInterface->mobile=true;
} else {
    $memory['shopusers.option']['name'] = 1;
    $memory['shopusers.option']['mail'] = 1;
    $memory['shopusers.option']['menu'] = 1;
    $memory['shopusers.option']['status'] = 1;
    $memory['shopusers.option']['discount'] = 1;
    $memory['shopusers.option']['date'] = 1;
    $memory['shopusers.option']['action'] =1;
    $memory['shopusers.option']['tel'] =1; 
}


$where['id'] = '>0';

// Расширенная сортировка из JSON
if (is_array($_GET['order']) and ! empty($_SESSION['jsort'][$_GET['order']['0']['column']])) {
    $order = array('order' => $_SESSION['jsort'][$_GET['order']['0']['column']] . ' ' . $_GET['order']['0']['dir']);
    $test = $_SESSION['jsort'][$_GET['order']['0']['column']] . ' ' . $_GET['order']['0']['dir'];
} else
    $order = array('order' => 'id desc');

// Поиск на странице JSON
if (!empty($_GET['search']['value'])) {
    $where['id'] .= " and (login LIKE '%" . PHPShopString::utf8_win1251(PHPShopSecurity::TotalClean($_GET['search']['value'])) . "%' or name LIKE '%" . PHPShopString::utf8_win1251(PHPShopSecurity::TotalClean($_GET['search']['value'])) . "%')";
}

// Таблица с данными
$PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['shopusers']);
$PHPShopOrm->debug = false;
$PHPShopOrm->mysql_error = false;
$PHPShopOrm->Option['where'] = ' or ';
$data = $PHPShopOrm->select(array('*'), $where, $order, $limit);
if (is_array($data))
    foreach ($data as $row) {

        // Enabled
        if (empty($row['enabled']))
            $enabled = 'text-muted';
        else
            $enabled = null;

        // Накопительная скидка
        $discount_td = $PHPShopUserStatusArray[$row['status']]['discount'];
        if ($row['cumulative_discount'] > $discount_td) {
            $discount_td = $row['cumulative_discount'];
        }

        $PHPShopInterface->setRow(
                ['id'=>$row['id'],'select'=>$row['mail']], array('name' => $row['name'], 'link' => '?path=shopusers&id=' . $row['id'], 'align' => 'left', 'sort' => 'name', 'class' => $enabled,'view' => intval($memory['shopusers.option']['name'])), array('name' => $row['login'], 'sort' => 'login', 'link' => 'mailto:' . $row['login'], 'class' => $enabled,'view' => intval($memory['shopusers.option']['mail'])),array('name' => $row['tel'], 'sort' => 'tel', 'class' => $enabled,'view' => intval($memory['shopusers.option']['tel'])), array('name' => $PHPShopUserStatusArray[$row['status']]['name'], 'sort' => 'status','view' => intval($memory['shopusers.option']['status'])), array('name' => $discount_td, 'sort' => 'cumulative_discount','view' => intval($memory['shopusers.option']['discount'])), array('name' => '<span class="hide">' . $row['datas'] . '</span>' . PHPShopDate::get($row['datas']), 'sort' => 'datas','view' => intval($memory['shopusers.option']['date'])), array('action' => array('edit', 'order', '|', 'delete', 'id' => $row['id']), 'align' => 'center','view' => intval($memory['shopusers.option']['menu'])), array('view' => intval($memory['shopusers.option']['action']),'status' => array('enable' => $row['enabled'], 'align' => 'right', 'sort' => 'enabled', 'caption' => array('Выкл', 'Вкл'))));
    }



$total = $PHPShopOrm->select(array("COUNT('id') as count"), $where, $order);

if (!empty($total['count'])) {
    $PHPShopInterface->_AJAX["recordsFiltered"] = $total['count'];
} else {
    $PHPShopInterface->_AJAX["data"] = array();
    $PHPShopInterface->_AJAX["recordsFiltered"] = 0;
}

$_SESSION['jsort'] = $PHPShopInterface->_AJAX["sort"];
unset($PHPShopInterface->_AJAX["sort"]);

header("Content-Type: application/json");
exit(json_encode($PHPShopInterface->_AJAX));
?>