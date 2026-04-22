<?php

$TitlePage = __("Журнал операций");

function actionStart() {
    global $PHPShopInterface, $TitlePage, $PHPShopSystem;

    $PHPShopInterface->addJSFiles('./js/bootstrap-datetimepicker.min.js', './js/bootstrap-datetimepicker.ru.js', 'report/gui/report.gui.js');
    $PHPShopInterface->addCSSFiles('./css/bootstrap-datetimepicker.min.css');
    $PHPShopInterface->checkbox_action = false;

    // Поиск
    $where = null;
    $limit = 300;

    // Дата
    if (!empty($_GET['date_start']) and !empty($_GET['date_end'])) {
        if ($where)
            $where.=' and ';
        else
            $where = ' where ';
        $where=array('datas'=>' between ' . (PHPShopDate::GetUnixTime($_GET['date_start']) - 1) . ' and ' . (PHPShopDate::GetUnixTime($_GET['date_end']) + 259200 / 2) );
        $TitlePage.=' с ' . $_GET['date_start'] . ' по ' . $_GET['date_end'];
        $limit = 1000;
    }
    
    $PHPShopInterface->setActionPanel($TitlePage, false, false,false);
    $PHPShopInterface->setCaption(array("Запрос", "40%"), array("Операция", "20%"), array("Дата", "20%"), array("Время", "10%"));

    // Пароль
    $pass_crm = PHPShopAdminRule::encodeCrm($_SESSION['pasPHPSHOP']);
    
    if ($PHPShopSystem->getSerilizeParam("1c_option.update_category") == 1)
        $create_category = 'create_category=true';
    else $create_category = 'create_category=false';

    // Таблица с данными
    $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['1c_jurnal']);
    $PHPShopOrm->debug = false;
    $data = $PHPShopOrm->select(array('*'), $where, array('order' => 'id DESC'), array('limit' => 300));
    if (is_array($data))
        foreach ($data as $row) {

            $PHPShopInterface->setRow(array('name' => $row['p_name'] . '/' . $row['f_name']), array('name' => '<span class="btn btn-default btn-xs"><span class="glyphicon glyphicon-play "></span> '.__('Выполнить').'</span>', 'align' => 'left', 'link' => '../../1cManager/result.php?date=' . $row['p_name'] . '&log=' . $_SESSION['logPHPSHOP'] . '&pas=' . $pass_crm . '&files=' . trim($row['f_name']) . '&create=true&'.$create_category.'&cml=true', 'target' => '_blank'), PHPShopDate::get($row['datas'], true), array('name' => $row['time'], 'align' => 'center'));
        }

    if (isset($_GET['date_start']))
        $date_start = $_GET['date_start'];
    else
        $date_start = PHPShopDate::get(time() - 2592000);

    if (isset($_GET['date_end']))
        $date_end = $_GET['date_end'];
    else
        $date_end = PHPShopDate::get(time() - 1);

    $PHPShopInterface->field_col = 1;
    $searchforma=$PHPShopInterface->setInputDate("date_start", $date_start, 'margin-bottom:10px', null, 'Дата начала отбора');
    $searchforma.=$PHPShopInterface->setInputDate("date_end", $date_end, false, null, 'Дата конца отбора');

    $searchforma.= $PHPShopInterface->setInputArg(array('type' => 'hidden', 'name' => 'path', 'value' => $_GET['path']));
    $searchforma.=$PHPShopInterface->setButton('Найти', 'search', 'btn-order-search pull-right');

    if ($where)
        $searchforma.=$PHPShopInterface->setButton('Сброс', 'remove', 'btn-order-cancel pull-left');

    // Правый сайдбар
    //$sidebarright[] = array('title' => 'Отчеты', 'content' => $PHPShopInterface->loadLib('tab_menu', false, './report/'));
    $sidebarright[] = array('title' => 'Расширенный поиск', 'content' => $PHPShopInterface->setForm($searchforma, false, "order_search", false, false, 'form-sidebar'));
    $PHPShopInterface->setSidebarRight($sidebarright, 2);
    $PHPShopInterface->Compile(2);
}

?>