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

// Системные настройки
$PHPShopSystem = new PHPShopSystem();
$PHPShopLang = new PHPShopLang(array('locale' => $_SESSION['lang'], 'path' => 'admin'));

// Редактор GUI
$PHPShopInterface = new PHPShopInterface();

// Поиск
$where = null;

if (isset($_GET['start']))
    $limit = $_GET['start'] . ',' . $_GET['length'];
else
    $limit = 300;

// Статусы заказов
PHPShopObj::loadClass('order');
$PHPShopOrderStatusArray = new PHPShopOrderStatusArray();
$status_array = $PHPShopOrderStatusArray->getArray();
$status[] = __('Новый заказ');
$order_status_value[] = array(__('Новый заказ'), 0, '');

mb_internal_encoding($GLOBALS['PHPShopBase']->codBase);

if (!isset($_GET['where']['statusi']))
    $_GET['where']['statusi'] = null;

if (is_array($status_array))
    foreach ($status_array as $status_val) {

        $status[$status_val['id']] = mb_substr($status_val['name'], 0, 22);
        $order_status_value[] = array($status_val['name'], $status_val['id'], $_GET['where']['statusi']);
    }


if (!empty($_GET['where']) and is_array($_GET['where'])) {
    foreach ($_GET['where'] as $k => $v) {
        if ($v != '' and $v != 'none')
            if ($k == 'a.user' || $k == 'statusi' || $k == 'a.servers' || $k == 'a.admin')
                $where .= ' ' . PHPShopSecurity::TotalClean($k) . ' = "' . PHPShopSecurity::TotalClean($v) . '" or';
            else
                $where .= ' ' . PHPShopSecurity::TotalClean($k) . ' like "%' . PHPShopSecurity::TotalClean($v) . '%" or';
    }

    if ($where)
        $where = 'where' . substr($where, 0, strlen($where) - 2);

    // Дата
    if (!empty($_GET['date_start']) and ! empty($_GET['date_end'])) {
        if ($where)
            $where .= ' and ';
        else
            $where = ' where ';
        $fromArr = $array = explode('-', $_GET['date_start']);
        $toArr = $array = explode('-', $_GET['date_end']);

        $start = mktime(0, 0, 0, $fromArr[1], $fromArr[0], $fromArr[2]);
        $end = mktime(23, 59, 59, $toArr[1], $toArr[0], $toArr[2]);

        $where .= ' a.datas between ' . $start . ' and ' . $end . '  ';
    }
}

// Знак рубля
if ($PHPShopSystem->getDefaultValutaIso() == 'RUB' or $PHPShopSystem->getDefaultValutaIso() == 'RUR')
    $currency = ' <span class="rubznak hidden-xs">p</span>';
else
    $currency = $PHPShopSystem->getDefaultValutaCode();


// Настройка полей
if (!empty($_COOKIE['check_memory'])) {
    $memory = json_decode($_COOKIE['check_memory'], true);
} else
    $memory = null;

if (empty($memory) or ! is_array($memory['order.option'])) {

    // Мобильная версия
    if (PHPShopString::is_mobile()) {
        $memory['order.option']['uid'] = 1;
        $memory['order.option']['id'] = 0;
        $memory['order.option']['statusi'] = 1;
        $memory['order.option']['datas'] = 1;
        $memory['order.option']['fio'] = 0;
        $memory['order.option']['menu'] = 0;
        $memory['order.option']['tel'] = 0;
        $memory['order.option']['sum'] = 1;
        $memory['order.option']['city'] = 0;
        $memory['order.option']['adres'] = 0;
        $memory['order.option']['org'] = 0;
        $memory['order.option']['comment'] = 0;
        $memory['order.option']['cart'] = 0;
        $memory['order.option']['tracking'] = 0;
        $memory['order.option']['admin'] = 0;
        $memory['order.option']['discount'] = 0;
        $memory['order.option']['company'] = 0;
        $memory['order.option']['company'] = 0;
        $PHPShopInterface->mobile = true;
    } else {
        $memory['order.option']['uid'] = 1;
        $memory['order.option']['id'] = 0;
        $memory['order.option']['statusi'] = 1;
        $memory['order.option']['datas'] = 1;
        $memory['order.option']['fio'] = 1;
        $memory['order.option']['menu'] = 1;
        $memory['order.option']['tel'] = 1;
        $memory['order.option']['sum'] = 1;
        $memory['order.option']['city'] = 0;
        $memory['order.option']['adres'] = 0;
        $memory['order.option']['org'] = 0;
        $memory['order.option']['comment'] = 0;
        $memory['order.option']['cart'] = 0;
        $memory['order.option']['tracking'] = 0;
        $memory['order.option']['admin'] = 0;
        $memory['order.option']['discount'] = 0;
        $memory['order.option']['company'] = 0;
        $memory['order.option']['company'] = 0;
    }
} else if (PHPShopString::is_mobile()) {
    $PHPShopInterface->mobile = true;
}

// Расширенная сортировка из JSON
if (is_array($_GET['order']) and ! empty($_SESSION['jsort'][$_GET['order']['0']['column']])) {
    $order = 'a.' . $_SESSION['jsort'][$_GET['order']['0']['column']] . ' ' . $_GET['order']['0']['dir'];
} else {
    $order = 'a.id desc';
}

// Поиск на странице JSON
if (!empty($_GET['search']['value'])) {
    if (empty($where))
        $where = ' where ';
    else
        $where .= ' and ';

    $where .= "(a.uid LIKE '%" . PHPShopString::utf8_win1251(PHPShopSecurity::TotalClean($_GET['search']['value'])) . "%' or a.fio LIKE '%" . PHPShopString::utf8_win1251(PHPShopSecurity::TotalClean($_GET['search']['value'])) . "%' or a.tel LIKE '%" . PHPShopString::utf8_win1251(PHPShopSecurity::TotalClean($_GET['search']['value'])) . "%'";

    // Сумма
    if (is_numeric($_GET['search']['value']))
        $where .= ' or a.sum = ' . $_GET['search']['value'];

    $where .= ')';
}

// Права
if (!$PHPShopBase->Rule->CheckedRules('order', 'remove')) {
    if (empty($where))
        $where2 = ' where ';
    else
        $where2 = ' and ';
    $where .= $where2 . ' a.admin=' . $_SESSION['idPHPSHOP'];
}

// Память отбора
if (!empty($where))
    $_SESSION['search_memory'] = $_SERVER['QUERY_STRING'];
else
    unset($_SESSION['search_memory']);

// Таблица с данными
$PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['orders']);
$PHPShopInterface->path = 'order';
$PHPShopOrm->Option['where'] = ' or ';
$PHPShopOrm->debug = false;
$PHPShopOrm->mysql_error = false;
$PHPShopOrm->sql = 'SELECT a.*, b.mail, b.name FROM ' . $GLOBALS['SysValue']['base']['orders'] . ' AS a  LEFT JOIN ' . $GLOBALS['SysValue']['base']['shopusers'] . ' AS b ON a.user = b.id  ' . $where . ' order by ' . $order . ' limit ' . $limit;

// Отладка
//$PHPShopInterface->_AJAX["debug"] = PHPShopString::win_utf8($PHPShopOrm->sql);
// Менеджеры
if ($PHPShopBase->Rule->CheckedRules('order', 'rule')) {
    $PHPShopOrmAdmin = new PHPShopOrm($GLOBALS['SysValue']['base']['users']);
    $data_manager = $PHPShopOrmAdmin->select(array('*'), array('enabled' => "='1'"), array('order' => 'id DESC'), array('limit' => 100));
    $manager_status_value[0] = '';
    if (is_array($data_manager))
        foreach ($data_manager as $manager_status)
            $manager_status_value[$manager_status['id']] = $manager_status['name'];
}

// Юридические лица
$PHPShopCompany = new PHPShopCompanyArray();
$PHPShopCompanyArray = $PHPShopCompany->getArray();
$company_value[] = $PHPShopSystem->getSerilizeParam("bank.org_name");
if (is_array($PHPShopCompanyArray)) {
    foreach ($PHPShopCompanyArray as $company)
        $company_value[$company['id']] = $company['name'];
}

$sum = $num = 0;
$data = $PHPShopOrm->select();
if (is_array($data))
    foreach ($data as $row) {

        // Библиотека заказа
        $PHPShopOrder = new PHPShopOrderFunction($row['id'], $row);

        $mail = $row['mail'];
        if (empty($mail))
            $mail = $PHPShopOrder->getSerilizeParam('orders.Person.mail');
        $comment = $PHPShopOrder->getSerilizeParam('status.maneger');

        if (empty($row['fio']) and ! empty($row['name']))
            $row['fio'] = $row['name'];
        elseif (empty($row['fio']) and empty($row['name']))
            $row['fio'] = $mail;

        // Скидка
        $datas = PHPShopDate::get($row['datas'], false);
        $discount = $PHPShopOrder->getDiscount();

        // Адрес
        $adres = $row['street'];
        if (!empty($row['house']))
            $adres .= ', д. ' . $row['house'];
        if (!empty($row['flat']))
            $adres .= ', кв. ' . $row['flat'];

        // Корзина
        $order = unserialize($row['orders']);
        $cart_list = $order['Cart']['cart'];
        $carts = $search_product = null;

        if (is_array($cart_list))
            if (sizeof($cart_list) != 0)
                if (is_array($cart_list))
                    foreach ($cart_list as $key => $val) {

                        if (!empty($val['id'])) {

                            // Проверка подтипа товара
                            if (!empty($val['parent']))
                                $val['id'] = $val['parent'];
                            if (!empty($val['parent_uid']))
                                $val['uid'] = $val['parent_uid'];

                            $carts .= '<a href="?path=product&id=' . $val['id'] . '&return=order.' . $row['id'] . '" title="Артикул: ' . $val['uid'] . '">' . $val['name'] . '</a><br>';

                            // Поиск товара
                            if (!empty($_GET['search']['name'])) {

                                if ($val['id'] == trim($_GET['search']['name']) or $val['uid'] == trim($_GET['search']['name']) or stristr(mb_strtolower($val['name'], 'windows-1251'), mb_strtolower(trim($_GET['search']['name']), 'windows-1251')))
                                    $search_product = true;
                                else
                                    continue;
                            }
                        }
                    }

        // Поиск товара
        if (!empty($_GET['search']['name']) and empty($search_product))
            continue;

        // Имя
        if (!empty($row['user']))
            $user_link = '?path=shopusers&id=' . $row['user'];
        else
            $user_link = null;

        // Сумма
        if (empty($row['sum']))
            $row['sum'] = 0;

        $sum += $row['sum'];
        $num++;

        $PHPShopInterface->setRow(['id'=>$row['id'],'select'=>$row['mail']], array('name' => '<span class="hidden-xs">' . __('Заказ') . '</span> ' . $row['uid'], 'link' => '?path=order&id=' . $row['id'], 'align' => 'left', 'sort' => 'uid', 'order' => $row['id'], 'view' => intval($memory['order.option']['uid'])), array('name' => $row['id'], 'sort' => 'id', 'view' => intval($memory['order.option']['id']), 'link' => '?path=order&id=' . $row['id']), array('dropdown' => array('enable' => $row['statusi'], 'caption' => $status, 'passive' => true, 'color' => $PHPShopOrder->getStatusColor()), 'sort' => 'statusi', 'block_locale' => true, 'view' => intval($memory['order.option']['statusi'])), array('name' => $carts, 'order' => $row['datas'], 'sort' => 'datas', 'view' => intval($memory['order.option']['cart'])), array('name' => $datas, 'order' => $row['datas'], 'sort' => 'datas', 'view' => intval($memory['order.option']['datas'])), array('name' => $row['fio'], 'sort' => 'fio', 'link' => $user_link, 'view' => intval($memory['order.option']['fio'])), array('name' => '<span class="hidden" id="order-' . $row['id'] . '-email">' . $row['mail'] . '</span>' . $row['tel'], 'sort' => 'tel', 'view' => intval($memory['order.option']['tel'])), array('action' => array('edit', 'email', 'copy', '|', 'delete', 'id' => $row['id']), 'align' => 'center', 'view' => intval($memory['order.option']['menu'])), array('name' => $discount . '%', 'order' => $discount, 'view' => intval($memory['order.option']['discount'])), array('name' => $row['city'], 'sort' => 'city', 'view' => intval($memory['order.option']['city'])), array('name' => $adres, 'view' => intval($memory['order.option']['adres'])), array('name' => $row['org_name'], 'sort' => 'org_name', 'view' => intval($memory['order.option']['org'])), array('name' => $comment, 'view' => intval($memory['order.option']['comment'])), array('name' => $row['tracking'], 'view' => intval($memory['order.option']['tracking'])), array('name' => $manager_status_value[$row['admin']], 'view' => intval($memory['order.option']['admin'])), array('name' => $company_value[$row['company']], 'view' => intval($memory['order.option']['company'])), array('name' => $PHPShopOrder->getTotal(false, ' ') . $currency, 'align' => 'right', 'order' => $row['sum'], 'sort' => 'sum', 'view' => intval($memory['order.option']['sum']), 'link' => '?path=order&id=' . $row['id'],));
    }



$PHPShopOrm->sql = 'SELECT a.sum FROM ' . $GLOBALS['SysValue']['base']['orders'] . ' AS a 
        LEFT JOIN ' . $GLOBALS['SysValue']['base']['shopusers'] . ' AS b ON a.user = b.id  ' . $where . ' order by a.id desc 
            limit 10000';
$total = $PHPShopOrm->select();


if (is_array($total)) {

    $sum = $num = 0;
    foreach ($total as $row) {
        if (empty($row['sum']))
            $row['sum'] = 0;
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

if (!empty($PHPShopInterface->_AJAX["sort"])) {
    $_SESSION['jsort'] = $PHPShopInterface->_AJAX["sort"];
    unset($PHPShopInterface->_AJAX["sort"]);
}

if (!is_array($PHPShopInterface->_AJAX["data"])) {
    $PHPShopInterface->_AJAX["recordsFiltered"] = 0;
    $PHPShopInterface->_AJAX["sum"] = 0;
    $PHPShopInterface->_AJAX["num"] = 0;
    $PHPShopInterface->_AJAX["data"] = array();
}

header("Content-Type: application/json");
exit(json_encode($PHPShopInterface->_AJAX));
?>