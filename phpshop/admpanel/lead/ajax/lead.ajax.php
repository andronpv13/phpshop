<?php

session_start();
$_classPath = "../../../";
include($_classPath . "class/obj.class.php");
PHPShopObj::loadClass(array("base", "system", "admgui", "orm", "date", "xml", "security", "string", "parser", "mail", "lang", "order"));
$subpath[0] = 'order';

$PHPShopBase = new PHPShopBase($_classPath . "inc/config.ini", true, true);
$PHPShopBase->chekAdmin();

PHPShopObj::loadClass('valuta');
PHPShopObj::loadClass('category');
PHPShopObj::loadClass('sort');
PHPShopObj::loadClass('modules');

// Системные настройки
$PHPShopSystem = new PHPShopSystem();
$PHPShopLang = new PHPShopLang(array('locale' => $_SESSION['lang'], 'path' => 'admin'));

// Редактор GUI
$PHPShopInterface = new PHPShopInterface();

// Модули
$PHPShopModules = new PHPShopModules($_classPath . "modules/");

if (isset($_GET['start']))
    $limit = $_GET['start'] . ',' . $_GET['length'];
else
    $limit = 300;

// Статусы заказов
PHPShopObj::loadClass('order');
$PHPShopOrderStatusArray = new PHPShopOrderStatusArray();
$status_array = $PHPShopOrderStatusArray->getArray();
$status_array[0]['color'] = '#33A5E7';
$status[] = __('Новый');
$order_status_value[] = array(__('Новый'), 0, '');

if (is_array($status_array))
    foreach ($status_array as $status_val) {
        if (!empty($status_val['id']))
            $status[$status_val['id']] = mb_substr($status_val['name'], 0, 22);
    }

// Знак рубля
if ($PHPShopSystem->getDefaultValutaIso() == 'RUB' or $PHPShopSystem->getDefaultValutaIso() == 'RUR')
    $currency = ' <span class="rubznak hidden-xs">p</span>';
else
    $currency = $PHPShopSystem->getDefaultValutaCode();


// Таблица с данными
$PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['orders']);
$PHPShopInterface->path = 'lead';
$PHPShopOrm->Option['where'] = ' or ';
$PHPShopOrm->debug = false;
$PHPShopOrm->mysql_error = false;


// Учет модуля returncal
if (!empty($GLOBALS['SysValue']['base']['returncall']['returncall_jurnal'])) {
    $returncall = true;
}

if (empty($returncall)) {
    $PHPShopOrm->sql = 'SELECT id,uid,date,statusi,fio,tel,user,sum FROM ' . $GLOBALS['SysValue']['base']['orders'] . ' UNION SELECT id,2,date,status,null,tel,null,message FROM ' . $GLOBALS['SysValue']['base']['notes'] . ' order by date desc limit ' . $limit;
} else {
    $PHPShopOrm->sql = 'SELECT id,uid,date,statusi,fio,tel,user,sum FROM ' . $GLOBALS['SysValue']['base']['orders'] . ' UNION SELECT id,1,date,status,message,tel,null,message FROM ' . $GLOBALS['SysValue']['base']['returncall']['returncall_jurnal'] . ' UNION SELECT id,2,date,status,message,tel,null,message FROM ' . $GLOBALS['SysValue']['base']['notes'] . ' order by date desc limit ' . $limit;
}

// Отладка
//$PHPShopInterface->_AJAX["debug"] = PHPShopString::win_utf8($PHPShopOrm->sql);

$sum = $num = 0;
$data = $PHPShopOrm->select();
$PHPShopInterface->checkbox_action = false;

if (is_array($data))
    foreach ($data as $row) {

        // Библиотека заказа
        $PHPShopOrder = new PHPShopOrderFunction($row['id'], $row);

        // Дата
        $datas = PHPShopDate::get($row['date'], true);

        switch ($row['uid']) {

            // Обратный звонок
            case "1":
                $user_link = null;
                $type = __('Звонок') . ' ' . $row['id'];
                $link = '?path=modules.dir.returncall&return=lead&id=' . $row['id'];
                break;

            // Записка
            case "2":
                $user_link = null;
                $type = __('Событие') . ' ' . $row['id'];
                $link = '?path=lead.kanban&return=lead&id=' . $row['id'];
                break;

            // Заказ
            default:
                $user_link = '?path=shopusers&return=lead&id=' . $row['user'];
                $type = __('Заказ') . ' ' . $row['uid'];
                $link = '?path=order&return=lead&id=' . $row['id'];
        }

        $PHPShopInterface->setRow(
                array('name' => $type, 'link' => $link, 'align' => 'left', 'sort' => 'uid', 'order' => $row['id']), '<span style="color:' . $status_array[$row['statusi']]['color'] . '">' . $status[$row['statusi']] . '</span>', array('name' => $datas, 'order' => $row['date'], 'sort' => 'date'), array('name' => $row['fio'], 'sort' => 'fio', 'link' => $user_link), array('name' => $row['tel'], 'sort' => 'tel')
        );
    }


if (empty($returncall)) {
    $PHPShopOrm->sql = 'SELECT date FROM ' . $GLOBALS['SysValue']['base']['orders'] . ' UNION SELECT date FROM ' . $GLOBALS['SysValue']['base']['notes'] . ' limit 10000';
} else {
    $PHPShopOrm->sql = 'SELECT date FROM ' . $GLOBALS['SysValue']['base']['orders'] . ' UNION SELECT date FROM ' . $GLOBALS['SysValue']['base']['returncall']['returncall_jurnal'] . ' UNION SELECT date FROM ' . $GLOBALS['SysValue']['base']['notes'] . ' limit 10000';
}
$total = $PHPShopOrm->select();


if (is_array($total)) {

    $sum = $num = 0;
    foreach ($total as $row) {
        if(!empty($row['sum']))
        $sum += $row['sum'];
        $num++;
    }

    $PHPShopInterface->_AJAX["recordsFiltered"] = count($total);
    $PHPShopInterface->_AJAX["sum"] = number_format($sum, 0, '', ' ');
    $PHPShopInterface->_AJAX["num"] = $num;
} else {
    $PHPShopInterface->_AJAX["data"] = array();
    $PHPShopInterface->_AJAX["recordsFiltered"] = $PHPShopInterface->_AJAX["sum"] = $PHPShopInterface->_AJAX["num"] = 0;
}

$_SESSION['jsort'] = $PHPShopInterface->_AJAX["sort"];
unset($PHPShopInterface->_AJAX["sort"]);

header("Content-Type: application/json");
exit(json_encode($PHPShopInterface->_AJAX));
?>