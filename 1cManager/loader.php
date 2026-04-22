<?php

/**
 * Автономная синхронизация заказов с 1С
 * @package PHPShopExchange
 * @author PHPShop Software
 * @version 2.3
 */
// Функции авторизации
include_once("login.php");

// Функции работы с почтой
include_once("mailer.php");

// Время записи документа
$curent_time = time();

// Заводим статус обработанного заказа
function CheckStatusReady() {
    global $link_db;
    $result = mysqli_query($link_db, "select id from " . $GLOBALS['SysValue']['base']['order_status'] . " where id=100 limit 1");
    $num = mysqli_num_rows($result);

    // Запись нового статуса
    if (empty($num))
        mysqli_query($link_db, "INSERT INTO " . $GLOBALS['SysValue']['base']['order_status'] . " (`id`, `name`, `color`) VALUES (100, 'Передано в бухгалтерию', '#EC971F')");
    return 100;
}

/**
 * Обработка комманд [check_f | update_f | check | new | update | list | optimize]
 */
switch ($_REQUEST['command']) {

    // Оптимизация базы перед загрузкой склада
    case("optimize"):
        mysqli_query($link_db, "OPTIMIZE TABLE " . $GLOBALS['SysValue']['base']['categories'] . ", " . $GLOBALS['SysValue']['base']['products']) or die("Optimize fail: " . mysqli_error($link_db));
        break;

    // Выводим список всех заказов
    // command=list&date1=123456&date2=24255
    case("list"):
        PHPShopObj::loadClass(array("order", "delivery"));
        $csv = $adr_info = null;

        // Безопасность
        if (PHPShopSecurity::true_num($_REQUEST['date1']) and PHPShopSecurity::true_num($_REQUEST['date2']) and PHPShopSecurity::true_num($_REQUEST['num'])) {

            $PHPShopSystem = new PHPShopSystem();
            $load_status = $PHPShopSystem->getSerilizeParam('1c_option.1c_load_status');
            $where = "where seller!='1'";

            if (!empty($load_status))
                $where .= " and statusi=" . intval($load_status);

            $sql = "select * from " . $GLOBALS['SysValue']['base']['orders'] . " " . $where . " and datas BETWEEN " . $_REQUEST['date1'] . " AND " . $_REQUEST['date2'] . " order by id desc  limit " . $_REQUEST['num'];

            $result = mysqli_query($link_db, $sql);
            while ($row = mysqli_fetch_array($result)) {

                $csv1 = "Начало личных данных\n";
                $csv2 = "Начало заказанных товаров\n";
                $csv3 = "Начало данных доставки\n";
                $id = $row['id'];
                $datas = $row['datas'];
                $uid = $row['uid'];
                $adr_info = $dop_info = null;

                // Подключаем класс заказа
                if (class_exists('PHPShopOrder'))
                    $PHPShopOrder = new PHPShopOrder($id);
                else
                    $PHPShopOrder = new PHPShopOrderFunction($id);

                $order = unserialize($row['orders']);
                $mail = $order['Person']['mail'];
                $user = $row['user'];

                // ФИО
                if (empty($row['fio']))
                    $name = $order['Person']['name_person'];
                else
                    $name = $row['fio'];

                // Реквизиты старого образца
                if (empty($row['org_inn'])) {
                    $company = str_replace("&quot;", '"', $order['Person']['org_name']);
                    $inn = $order['Person']['org_inn'];
                    $kpp = $order['Person']['org_kpp'];
                }
                // Реквизиты нового образца
                else {
                    $company = str_replace("&quot;", '"', $row['org_name']);
                    $inn = $row['org_inn'];
                    $kpp = $row['org_kpp'];
                }

                if (empty($row['tel'])) {
                    $tel = $order['Person']['tel_code'] . " " . $order['Person']['tel_name'];
                } else {
                    $tel = str_replace(array('&#43;', '&#43'), '+', $row['tel']);
                }

                // формируем адрес для новой структуры таблицы заказов
                if ($row['country'])
                    $adr_info .= ", страна: " . $row['country'];
                if ($row['state'])
                    $adr_info .= ", регион/штат: " . $row['state'];
                if ($row['city'])
                    $adr_info .= ", город: " . $row['city'];
                if ($row['index'])
                    $adr_info .= ", индекс: " . $row['index'];
                if ($row['street'])
                    $adr_info .= ", улица: " . $row['street'];
                if ($row['house'])
                    $adr_info .= ", дом: " . $row['house'];
                if ($row['porch'])
                    $adr_info .= ", подъезд: " . $row['porch'];
                if ($row['door_phone'])
                    $adr_info .= ", код домофона: " . $row['door_phone'];
                if ($row['flat'])
                    $adr_info .= ", квартира: " . $row['flat'];
                if ($row['delivtime'])
                    $dop_info = "время доставки: " . $row['delivtime']." ";
                if ($row['dop_info'])
                    $dop_info .=  PHPShopSecurity::CleanOut($row['dop_info']);

                $adres = PHPShopSecurity::CleanOut(str_replace("&quot;", '"', $adr_info . $order['Person']['adr_name']));
                $oplata = $PHPShopOrder->getOplataMetodName();
                $sum = $row['sum'];
                //$weight = $order['Cart']['weight'];
                $discount = $order['Person']['discount'];

                // формируем реквизитыдля новой структуры таблицы заказов
                $org_yur_adres = $row['org_yur_adres'];
                $org_fakt_adres = $row['org_fakt_adres'];
                $org_ras = $row['org_ras'];
                $org_bank = $row['org_bank'];
                $org_kor = $row['org_kor'];
                $org_bik = $row['org_bik'];
                $org_city = $row['org_city'];

                // Ver 1.8
                $csv1 .= "$id;$uid;$datas;$mail;$name;$company;$tel;$oplata;$sum;$discount;$inn;$adres;$kpp;$user;$org_yur_adres;$org_fakt_adres;$org_ras;$org_bank;$org_kor;$org_bik;$org_city;$dop_info\n";

                if (is_array($order['Cart']['cart']))
                    foreach ($order['Cart']['cart'] as $val) {
                        $id = $val['id'];
                        $uid = $val['uid'];
                        $num = $val['num'];
                        $sum = $val['price'] * $num - ($val['price'] * $num * $order['Person']['discount'] / 100);

                        // Валюта
                        $valuta = $PHPShopOrder->getValutaIso($id);
                        $csv2 .= "$id;$uid;$num;$sum;$valuta\n";
                    }

                // Доставка
                $PHPShopDelivery = new PHPShopDelivery($order['Person']['dostavka_metod']);
                $csv3 .= $PHPShopDelivery->getCity() . ";" . $order['Cart']['dostavka'] . ";" . $valuta . "\n";

                $csv .= $csv1 . $csv2 . $csv3;
            }
            echo $csv;
        } else
            exit('Ошибка проверки параметров блока list');
        break;



    // обновление статуса заказа
    // command=update&id=63[ид заказа]&cid=12345[номер счета 1с]&accounts=true
    case("update"):
        $CheckStatusReady = CheckStatusReady();
        mysqli_query($link_db, "UPDATE " . $GLOBALS['SysValue']['base']['orders'] . " SET seller='1', statusi=" . intval($CheckStatusReady) . " where id=" . intval($_REQUEST['id']));

        if (!empty($_REQUEST['cid'])) {
            mysqli_query($link_db, "INSERT INTO " . $GLOBALS['SysValue']['base']['1c_docs'] . " (`uid`, `cid`, `datas`, `year`) VALUES (" . $_REQUEST['id'] . ", '" . $_REQUEST['cid'] . "'," . $curent_time . "," . date('Y') . ")");
        }

            // Сообщение пользователю
            SendMailUser($_REQUEST['id'], "accounts", $curent_time);
        
        break;


    // кол-во новых заказов
    // command=new&date1=123456&date2=24255
    case("new"):
        @$result = mysqli_query($link_db, "select id from " . $GLOBALS['SysValue']['base']['orders'] . " where seller!='1' and datas<'$_REQUEST[date2]' and datas>'$_REQUEST[date1]'");
        $new_order = mysqli_num_rows($result);
        echo $new_order;
        break;

    // Проверка для Счет-фактур
    // command=check&date1=123456&date2=24255
    case("check"):
        $csv = null;
        @$result = mysqli_query($link_db, "select * from " . $GLOBALS['SysValue']['base']['1c_docs'] . " where datas<'" . $_REQUEST[date2] . "' and datas>'" . $_REQUEST[date1] . "'");
        while ($row = mysqli_fetch_array($result)) {
            $cid = $row['cid'];
            $csv .= "$cid;";
        }
        echo $csv;
        break;

    // Обновление даты для Счет-фактур
    // command=update_f&cid=1234[номер счета 1с]&date=123456
    case("update_f"):

        mysqli_query($link_db, "UPDATE " . $GLOBALS['SysValue']['base']['1c_docs'] . " SET datas_f='" . $_REQUEST[date] . "' where cid='" . $_REQUEST[cid] . "' and year='" . date('Y', $_REQUEST[date]) . "'");

        // Сообщение пользователю
        SendMailUser($_REQUEST['cid'], "invoice");
        break;

    // Проверка загрузки Счет-фактур
    // command=check_f&cid=123[номер счета 1с]
    case("check_f"):

        @$result = mysqli_query($link_db, "select datas_f from " . $GLOBALS['SysValue']['base']['1c_docs'] . " where cid='" . $_REQUEST[cid] . "' and year='" . date('Y') . "' limit 1");
        $row = mysqli_fetch_array($result);
        $datas_f = $row['datas_f'];
        echo $datas_f;
        break;

    default: echo "Нет комманды<br>
	 loader.php?command=[check_f | update_f | check | new | update | list | optimize]";
}
?>