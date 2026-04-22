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

// Поиск
$where = null;

if (isset($_GET['start']))
    $limit = $_GET['start'] . ',' . $_GET['length'];
else
    $limit = 300;

// Статусы заказов
PHPShopObj::loadClass('order');
$status_array[0]['name'] = 'Новый';
$status_array[0]['color'] = '#33A5E7';
$status_array[0]['id'] = 0;
$status_array_name[0]['name'] = 'Новый';
$PHPShopOrderStatusArray = new PHPShopOrm($GLOBALS['SysValue']['base']['order_status']);
$data = $PHPShopOrderStatusArray->select(array('*'), false, array('order' => 'num'), array('limit' => 100));
if (is_array($data))
    foreach ($data as $key => $row) {
        $status_array[$row['id']]['name'] = $row['name'];
        $status_array[$row['id']]['color'] = $row['color'];
        $status_array[$row['id']]['id'] = $row['id'];
        $status_array_name[$row['id']]['name'] = $row['name'];
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
    $PHPShopOrm->sql = 'SELECT id,uid,date,statusi,fio,tel,user,sum FROM ' . $GLOBALS['SysValue']['base']['orders'] . ' UNION SELECT id,2,date,status,name,tel,null,message FROM ' . $GLOBALS['SysValue']['base']['notes'] . ' order by date desc limit 50';
} else {
    $PHPShopOrm->sql = 'SELECT id,uid,date,statusi,fio,tel,user,sum FROM ' . $GLOBALS['SysValue']['base']['orders'] . ' UNION SELECT id,1,date,status,name,tel,null,message FROM ' . $GLOBALS['SysValue']['base']['returncall']['returncall_jurnal'] . ' UNION SELECT id,2,date,status,name,tel,mail,message FROM ' . $GLOBALS['SysValue']['base']['notes'] . ' order by date desc limit 50';
}

// Статусы
$n = 1;
foreach ($status_array as $row) {
    $ajax[$row['name']] = array(
        "id" => "board-id-" . $row['id'],
        "uid" => $row['id'],
        "title" => '<a href="?path=order.status&return=lead.kanban&id=' . $row['id'] . '" class="kanban-title-board" data-toggle="tooltip" data-placement="top" title="' . PHPShopString::win_utf8('Редактировать') . '">' . PHPShopString::win_utf8(substr($row['name'], 0, 17), true) . '</a>',
        "color" => PHPShopString::win_utf8($row['color']),
    );
    $n++;
}

$ajax['Новый']['title'] = PHPShopString::win_utf8('Новый');
$count=[];
$data = $PHPShopOrm->select();
if (is_array($data))
    foreach ($data as $row) {

        $sum = null;

        // Вывод не более 15
        if (!empty($count[$status_array_name[$row['statusi']]['name']]) and $count[$status_array_name[$row['statusi']]['name']] > 15)
            continue;

        switch ($row['uid']) {

            // Обратный звонок
            case "1":
                $user_link = null;
                $type = __('Звонок') . ' ' . $row['id'];
                $pref = 'c_';
                $ico = 'glyphicon-phone-alt';
                $sum = '<span class="text-muted">' . PHPShopString::win_utf8($row['sum']) . '</span>';
                $link = '?path=modules.dir.returncall&id=';
                break;

            // Записка
            case "2":
                $user_link = null;
                if (empty($row['fio']))
                    $type = __('Событие') . ' ' . $row['id'];
                else
                    $type = $row['fio'];
                $pref = 'c_';
                $ico = 'glyphicon-bookmark';
                
                if(empty($row['tel']))
                    $row['tel']=$row['user'];
                
                $sum = '<span class="text-muted">' . PHPShopString::win_utf8($row['sum']) . '</span>';
                $link = '?path=lead.kanban&id=';
                break;

            // Заказ
            default:

                $user_link = '?path=shopusers&id=' . $row['user'];
                $type = __('Заказ') . ' ' . $row['uid'];
                $pref = 'o_';
                $ico = 'glyphicon-shopping-cart';

                if ($row['sum'] > 0)
                    $sum = '<span class="text-primary strong">' . $row['sum'] . PHPShopString::win_utf8($currency) . '</span>';

                $link = '?path=order&id=';
        }


        // Дата
        $datas = PHPShopDate::get($row['date'], true);

        $info = '<div class="text-muted">' . $datas . '<span class="glyphicon ' . $ico . ' pull-right" style="color:' . $status_array[$row['statusi']]['color'] . '"></span></div>' .
                PHPShopString::win_utf8($type) .
                '<div>' . PHPShopString::win_utf8($row['tel']) . '</div>' .
                '<div>' . PHPShopString::win_utf8($row['fio']) . '</div>' .
                '<div>' . PHPShopString::win_utf8($row['mail']) . '</div>' .
                $sum;

        $ajax[$status_array_name[$row['statusi']]['name']]['item'][] = array(
            "id" => "item-id-" . $row['id'],
            "title" => $info,
            "uid" => $row['id'],
            "link" => $link,
            "user" => $user_link,
            "date" => @$row['datas'] . $row['date']
        );

        @$count[@$status_array_name[@$row['statusi']]['name']] ++;
    }

if (is_array($ajax))
    foreach ($ajax as $list) {
        $AJAX[] = $list;
    }

header("Content-Type: application/json");
exit(json_encode($AJAX));
