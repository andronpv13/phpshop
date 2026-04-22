<?php

// Библиотека работы с почтой
include_once('../phpshop/class/system.class.php');
include_once('../phpshop/class/parser.class.php');
include_once('../phpshop/class/mail.class.php');

// Номер заказа для счет-фактуры
function GetNumOrders($cid) {
    global $link_db;

    $sql = "select uid,datas from " . $GLOBALS['SysValue']['base']['1c_docs'] . " where cid='$cid'";
    $result = mysqli_query($link_db, $sql);
    @$row = mysqli_fetch_array(@$result);
    return $row['uid'];
}

// Сообщение пользователю
function SendMailUser($id, $flag, $datas = false) {
    global $link_db, $PHPShopSystem;

    // Счет-фактура, поиск номера заказа
    if ($flag == "invoice") {
        $order_info = GetNumOrders($id);
        $id = $order_info['id'];
        $datas = $order_info['datas'];
        $name_doc = "Счет-фактуры";
    }
    else
        $name_doc = "Счета на оплату";

    $sql = "select * from " . $GLOBALS['SysValue']['base']['orders'] . " where id=" . intval($id);
    $result = mysqli_query($link_db, $sql);
    @$row = mysqli_fetch_array(@$result);
    $order = unserialize($row['orders']);
    $mail = $order['Person']['mail'];
    $name = $order['Person']['name_person'];
    $uid = $row['uid'];

    $PHPShopSystem = new PHPShopSystem();
    if ($PHPShopSystem->ifSerilizeParam('1c_option.1c_load_status_email')) {

        $content = "Уважаемый(ая) пользователь " . $name . ", по заказу №" . $uid . " стала доступна печатная форма \"" . $name_doc . "\".

Вы можете проверить статус заказа, загрузить файлы, распечатать платежные документы он-лайн в \"Личном кабинете\" или скачать по ссылке: http://" . $_SERVER['SERVER_NAME'] . $GLOBALS['SysValue']['dir']['dir'] . "/files/docsSave.php?orderId=" . $id . "&list=" . $flag . "&datas=" . $datas . "&user=" . $row['user'] . "
       
Дата: " . date("d-m-y H:s a") . "
    
";

        // Отправление сообщения
        new PHPShopMail($mail, $PHPShopSystem->getParam('adminmail2'), "Бухгалтерские документы по заказу №" . $row['uid'], $content);
    }
}

?>