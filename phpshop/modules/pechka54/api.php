<?php

$_classPath = "../../";
include($_classPath . "class/obj.class.php");
PHPShopObj::loadClass("base");
$PHPShopBase = new PHPShopBase($_classPath . "inc/config.ini", true, true);
PHPShopObj::loadClass("orm");
PHPShopObj::loadClass("system");
PHPShopObj::loadClass("text");
PHPShopObj::loadClass("string");
PHPShopObj::loadClass("product");
PHPShopObj::loadClass("valuta");
PHPShopObj::loadClass("mail");
PHPShopObj::loadClass("parser");
PHPShopObj::loadClass("modules");
PHPShopObj::loadClass("security");


$PHPShopModules = new PHPShopModules($_classPath . "modules/");
$PHPShopModules->checkInstall('pechka54');

$PHPShopSystem = new PHPShopSystem();
$PHPShopValutaArray = new PHPShopValutaArray();

include_once($_classPath . 'modules/pechka54/class/pechka54.class.php');
$Pechka54Rest = new Pechka54Rest();

$PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['orders']);
$PHPShopOrm->debug = false;
$PHPShopOrm->mysql_error = false;


// Проверочный ключ
if ($Pechka54Rest->option['password'] == $_REQUEST['key'])
    switch ($_REQUEST['action']) {

        // Синхронизация
        case "sync":

            $data = $_REQUEST['data'];

            if (is_array($data)) {

                $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['pechka54']['pechka54_taxes']);
                foreach ($data as $kkm_list) {

                    // Касса есть в списке
                    if ($Pechka54Rest->option['kkm'] == $kkm_list['kkm']) {
                        $PHPShopOrm->delete(array('id' => '>0'));
                        $PHPShopOrm->clean();
                        foreach ($kkm_list['taxes'] as $val) {
                            $PHPShopOrm->insert(array('tax_name_new' => PHPShopString::utf8_win1251($val['title']), 'tax_id_new' => $val['id']));
                        }
                    }
                }
            }

            break;

        // Запрос чека
        case "order":

            // Касса есть в списке
            if ($Pechka54Rest->option['kkm'] == $_REQUEST['kkm']) {

                $data = $PHPShopOrm->select(array('*'), array('statusi'=>'=101','ofd_status' => "='0'"), array('order' => 'id'), array('limit' => 1));

                // Есть заказы
                if (is_array($data)) {
                    $data['kkm'] = $_REQUEST['kkm'];
                    $result = $Pechka54Rest->OFDStart($data);
                }
                else
                    $result = array(
                        "resultCode" => 0,
                        "resultInfo" => "Чеков нет"
                    );
            }
            break;


        // Печать чека
        case "orderConfirm":

            // Касса есть в списке
            if ($Pechka54Rest->option['kkm'] == $_REQUEST['kkm']) {

                //  Поиск заказа
                $data = $PHPShopOrm->select(array('*'), array('uid' => '="' . $_REQUEST['orderId'] . '"', 'ofd_status' => "='1'"), false, array('limit' => 1));
                if (is_array($data)) {

                    // Ошибок нет
                    if (empty($_REQUEST['resultCode'])) {

                        $check = $Pechka54Rest->OFDStart($data);

                        $ofd_status = 2;

                        // Запись лога
                        $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['pechka54']['pechka54_log']);
                        $log = array(
                            'message_new' => serialize($check),
                            'order_id_new' => $data['id'],
                            'order_uid_new' => $_REQUEST['orderId'],
                            'sum_new' => $data['sum'],
                            'date_new' => time(),
                            'operation_new' => $check['operation'],
                            'link' => $_REQUEST['OFDLink'],
                            'fiscal_new' => $_REQUEST['fpd']
                        );

                        $status['log_id'] = $PHPShopOrm->insert($log);
                        $status['payload']['fn_number'] = $_REQUEST['fpd'];
                        $status['payload']['OFDLink'] = $_REQUEST['OFDLink'];
                        $status['payload']['receipt_datetime'] = date('d.m.Y H:m:s');
                        $status['payload']['ecr_registration_number'] = $_REQUEST['kkm'];
                        $status['payload']['total'] = $data['sum'];

                        // Статус заказа
                        $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['orders']);
                        $PHPShopOrm->update(array('ofd_new' => serialize($status), 'ofd_status_new' => $ofd_status), array('id' => '="' . $data['id'] . '"'));
                    }
                    // Ошибки
                    else {
                        $ofd_status = 3;

                        // Запись лога
                        $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['pechka54']['pechka54_log']);
                        $log = array(
                            'message_new' => serialize($check),
                            'order_id_new' => $data['id'],
                            'order_uid_new' => $_REQUEST['orderId'],
                            'date_new' => time(),
                            'sum_new' => $data['sum'],
                            'operation_new' => $check['operation'],
                            'link' => $_REQUEST['OFDLink'],
                            'fiscal_new' => $_REQUEST['fpd']
                        );

                        $status['log_id'] = $PHPShopOrm->insert($log);
                        $status['operation'] = 'error';
                        $status['payload']['fn_number'] = $_REQUEST['fpd'];
                        $status['payload']['OFDLink'] = $_REQUEST['OFDLink'];
                        $status['payload']['receipt_datetime'] = date('d.m.Y H:m:s');
                        $status['payload']['ecr_registration_number'] = $_REQUEST['kkm'];
                        $status['payload']['total'] = $data['sum'];

                        $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['orders']);
                        $PHPShopOrm->update(array('ofd_new' => serialize($status), 'ofd_status_new' => $ofd_status), array('id' => '="' . $data['id'] . '"'));
                        // Уведомление на Email
                    }
                }
            }


            break;
    } else {

    $result = array(
        "resultCode" => 0,
        "resultInfo" => "OK"
    );
}

// Вывод JSON
$Pechka54Rest->compile($result);
?>