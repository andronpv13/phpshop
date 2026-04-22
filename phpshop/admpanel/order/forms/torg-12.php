<?php
$_classPath = "../../../";
include($_classPath . "class/obj.class.php");
PHPShopObj::loadClass("base");
PHPShopObj::loadClass("order");
PHPShopObj::loadClass("system");
PHPShopObj::loadClass("inwords");
PHPShopObj::loadClass("delivery");
PHPShopObj::loadClass("date");
PHPShopObj::loadClass("valuta");

$PHPShopBase = new PHPShopBase($_classPath . "inc/config.ini", true, true);

// Авторизация
if (strstr($_GET['orderID'], '-')) {
    $id = (int) explode("-", $_GET['orderID'])[0];
    $s = (string) explode("-", $_GET['orderID'])[1];
}
if (empty($s) or ! $PHPShopBase->checkFile($s, $id))
    $PHPShopBase->chekAdmin();

$PHPShopSystem = new PHPShopSystem();
$LoadItems['System'] = $PHPShopSystem->getArray();
$PHPShopOrder = new PHPShopOrderFunction($_GET['orderID']);

// Юридические лица
$company = $PHPShopOrder->getParam('company');
$PHPShopSystem->setCompany($company);

$blank_org_name = $PHPShopSystem->getSerilizeParam('bank.org_name');
$blank_org_inn = $PHPShopSystem->getSerilizeParam('bank.org_inn');
$blank_org_kpp = $PHPShopSystem->getSerilizeParam('bank.org_kpp');
$blank_org_ur_adres = $PHPShopSystem->getSerilizeParam('bank.org_ur_adres');
$blank_org_adres = $PHPShopSystem->getSerilizeParam('bank.org_adres');

$LoadBanc = unserialize($LoadItems['System']['bank']);
$LoadBanc['org_sig'] = $PHPShopSystem->getSerilizeParam('bank.org_sig');
$LoadBanc['org_sig_buh'] = $PHPShopSystem->getSerilizeParam('bank.org_sig_buh');
$LoadBanc['org_stamp'] = $PHPShopSystem->getSerilizeParam('bank.org_stamp');
$LoadBanc['org_stamp'] = $PHPShopSystem->getSerilizeParam('bank.org_stamp');
$LoadBanc['org_adres'] = $PHPShopSystem->getSerilizeParam('bank.org_adres');
$LoadBanc['org_inn'] = $PHPShopSystem->getSerilizeParam('bank.org_inn');
$LoadBanc['org_kpp'] = $PHPShopSystem->getSerilizeParam('bank.org_kpp');
$LoadItems['System']['company'] = $PHPShopSystem->getParam('company');

$fio = $PHPShopOrder->getParam('fio');
if (!empty($fio))
    $blank_person_user = $PHPShopOrder->getParam('fio');
else
    $blank_person_user = $PHPShopOrder->getSerilizeParam('orders.Person.name_person');

$orgData = $PHPShopOrder->getSerilizeParam('orders.Person.org_name');
if (empty($orgData)) {
    $orgData = $PHPShopOrder->getParam('org_name');
}
$inn = $PHPShopOrder->getSerilizeParam('orders.Person.org_inn');
if (empty($inn)) {
    $inn = $PHPShopOrder->getParam('org_inn');
}
if (!empty($inn)) {
    $orgData .= ' ИНН ' . $inn;
}

$kpp = $PHPShopOrder->getSerilizeParam('orders.Person.org_kpp');
if (empty($kpp)) {
    $kpp = $PHPShopOrder->getParam('org_kpp');
}
if (!empty($kpp)) {
    $orgData .= ' КПП ' . $kpp;
}

if (!empty($PHPShopOrder->getParam('org_yur_adres'))) {
    $orgData .= ' Юр. адрес ' . $PHPShopOrder->getParam('org_yur_adres');
}

// Подключаем реквизиты
$SysValue['bank'] = unserialize($LoadItems['System']['bank']);
$pathTemplate = $SysValue['dir']['templates'] . chr(47) . $_SESSION['skin'];


$sql = "select * from " . $SysValue['base']['table_name1'] . " where id=" . intval($_GET['orderID']);
$n = 1;
@$result = mysqli_query($link_db, $sql);
$row = mysqli_fetch_array(@$result);
$id = $row['id'];
$datas = $row['datas'];
$ouid = $row['uid'];
$order = unserialize($row['orders']);
$status = unserialize($row['status']);
$nds = $PHPShopOrder->getParam('nds');
$dis = $weight = $adr_info = null;
$sum = $num = $this_nds_summa = $total_summa_nds = $total_summa_nds_taxe = $total_summa = 0;
if (is_array($order['Cart']['cart']))
    foreach ($order['Cart']['cart'] as $val) {

        // Услуга
        if ($val['type'] == 2)
            continue;

        $this_price = ($PHPShopOrder->returnSumma(number_format($val['price'], "2", ".", ""), (int) $order['Person']['discount']));
        $this_nds = number_format($this_price * $nds / (100 + $nds), "2", ".", "");
        $this_price_bez_nds = ($this_price - $this_nds) * $val['num'];
        $this_price_c_nds = number_format($this_price * $val['num'], "2", ".", "");
        $this_nds_summa += $this_nds * $val['num'];

        $dis .= '<tr class="Таблица113">   
        <td style="text-align:center;width:0.924cm; " class="Таблица1_A34"><p class="P41" style="text-align:center;">' . $n . '</p></td>
        <td colspan="4" style="text-align:left;width:1.513cm; " class="Таблица1_A34"><p class="P42">' . $val['name'] . '</p></td>
        <td style="text-align:left;width:0.716cm; " class="Таблица1_A34"><p class="P40"></p></td>
        <td style="text-align:left;width:0.93cm; " class="Таблица1_A34"><p class="P40">' . $val['ed_izm'] . '</p></td>
        <td style="text-align:left;width:0.877cm; " class="Таблица1_A34"><p class="P40"> </p></td>
        <td colspan="2" style="text-align:left;width:0.665cm; " class="Таблица1_A34"><p class="P42"> </p></td>
        <td colspan="2" style="text-align:left;width:0.162cm; " class="Таблица1_A34"><p class="P41"> </p></td>
        <td style="text-align:left;width:0.903cm; " class="Таблица1_A34"><p class="P42"> </p></td>
        <td style="text-align:left;width:0.903cm; " class="Таблица1_A34"><p class="P42"> </p></td>
        <td colspan="2" style="text-align:left;width:0.24cm; " class="Таблица1_A34"><p class="P41">' . $val['num'] . '</p></td>
        <td colspan="3" style="text-align:left;width:1.619cm; " class="Таблица1_A34"><p class="P41">' . $this_price . '</p></td>
        <td colspan="4" style="text-align:left;width:0.22cm; " class="Таблица1_A34"><p class="P41">' . $this_price_bez_nds . '</p></td>
        <td colspan="2" style="text-align:left;width:1.171cm; " class="Таблица1_A34"><p class="P40">' . $nds . '</p></td>
        <td colspan="4" style="text-align:left;width:0.459cm; " class="Таблица1_A34"><p class="P47">' . $this_nds_summa . '</p></td>
        <td colspan="2" style="text-align:left;width:0.132cm; " class="Таблица1_A34"><p class="P47">' . $this_price_c_nds . '</p></td>
      </tr>';

        $total_summa_nds += $this_price_bez_nds;
        $total_summa_nds_taxe += $this_nds_summa;
        $total_summa += $PHPShopOrder->returnSumma(($val['price'] * $val['num']), (int) $order['Person']['discount']);

        //Определение и суммирование веса
        $goodid = $val['id'];
        $goodnum = $val['num'];
        $wsql = 'select weight from ' . $SysValue['base']['table_name2'] . ' where id=\'' . $goodid . '\'';
        $wresult = mysqli_query($link_db, $wsql);
        $wrow = mysqli_fetch_array($wresult);
        $cweight = $wrow['weight'] * $goodnum;
        if (!$cweight) {
            $zeroweight = 1;
        } //Один из товаров имеет нулевой вес!
        $weight += $cweight;


        $sum += $val['price'] * $val['num'];
        $num += $val['num'];
        $n++;
    }
//Обнуляем вес товаров, если хотя бы один товар был без веса
if ($zeroweight) {
    $weight = 0;
}

$total_summa_nds = number_format($sum, "2", ".", "");
$total_summa = $total_summa_nds;

$PHPShopDelivery = new PHPShopDelivery($order['Person']['dostavka_metod']);
$PHPShopDelivery->checkMod($order['Cart']['dostavka']);
$deliveryPrice = $PHPShopDelivery->getPrice($sum, $weight);

$summa_nds_dos = number_format($deliveryPrice * $nds / (100 + $nds), "2", ".", "");

//$sum = $row['sum'];

if ($LoadItems['System']['nds_enabled']) {
    $nds = $LoadItems['System']['nds'];
    $nds = number_format($sum * ($nds / (100 + $nds)), "2", ".", "");
}



if ($row['org_name'] or ! empty($order['Person']['org_name']))
    $org_name = $order['Person']['org_name'] . $row['org_name'];
else
    $org_name = $row['fio'];

$datas = PHPShopDate::dataV($datas, "false");

// время доставки под старый формат данных в заказе
if (!empty($order['Person']['dos_ot']) OR ! empty($order['Person']['dos_do']))
    $dost_ot = " От: " . $order['Person']['dos_ot'] . ", до: " . $order['Person']['dos_do'];

if (!empty($row['fio']))
    $user = $row['fio'];
else
    $user = $order['Person']['name_person'];

// формируем адрес доставки с учётом старого формата данных в заказах
if ($row['org_name'])
    $adr_info .= ", " . $row['org_name'];
elseif ($row['fio'] OR $order['Person']['name_person'])
    $adr_info .= ", " . $user;
if ($row['country'])
    $adr_info .= ", страна: " . $row['country'];
if ($row['state'])
    $adr_info .= ", регион/штат: " . $row['state'];
if ($row['city'])
    $adr_info .= ", город: " . $row['city'];
if ($row['index'])
    $adr_info .= ", индекс: " . $row['index'];
if ($row['street'] OR $order['Person']['adr_name'])
    $adr_info .= ", улица: " . $row['street'] . @$order['Person']['adr_name'];
if ($row['house'])
    $adr_info .= ", дом: " . $row['house'];
if ($row['porch'])
    $adr_info .= ", подъезд: " . $row['porch'];
if ($row['door_phone'])
    $adr_info .= ", код домофона: " . $row['door_phone'];
if ($row['flat'])
    $adr_info .= ", квартира: " . $row['flat'];

$adr_info = substr($adr_info, 2);
?>

<!doctype html>
<html>
    <head>
        <title>Унифицированная форма ТОРГ-12 №<?php echo @$ouid ?></title>
        <meta http-equiv="Content-Type" content="text/html; charset=windows-1251">
        <link rel="apple-touch-icon" href="../../apple-touch-icon.png">
        <link rel="icon" href="../../favicon.ico"> 
        <link href="style.css" type=text/css rel=stylesheet>
        <script src="../../../lib/templates/print/js/html2pdf.bundle.min.js"></script>
        <style type="text/css">
            @page {  }
            table { border-collapse:collapse; border-spacing:0; empty-cells:show }
            td, th { vertical-align:top; font-size:12pt;}
            h1, h2, h3, h4, h5, h6 { clear:both }
            ol, ul { margin:0; padding:0;}
            li { list-style: none; margin:0; padding:0;}
            <!-- "li span.odfLiEnd" - IE 7 issue-->
            li span. { clear: both; line-height:0; width:0; height:0; margin:0; padding:0; }
            span.footnodeNumber { padding-right:1em; }
            span.annotation_style_by_filter { font-size:95%; font-family:Arial; background-color:#fff000;  margin:0; border:0; padding:0;  }
            * { margin:0;}
            .P1 { font-size:10pt; line-height:100%; margin:100%; margin-bottom:0.055cm; margin-left:0.055cm; margin-right:0.055cm; margin-top:0.055cm; text-align:left ! important; text-indent:0.055cm; font-family:Tahoma; vertical-align:top; writing-mode:lr-tb; color:#000000; }
            .P10 { font-size:4.5pt; line-height:100%; margin:100%; margin-bottom:0.055cm; margin-left:0.055cm; margin-right:0.055cm; margin-top:0.055cm; text-align:left ! important; text-indent:0.055cm; font-family:Tahoma; vertical-align:top; writing-mode:lr-tb; }
            .P11 { font-size:2.5pt; line-height:100%; margin:100%; margin-bottom:0.055cm; margin-left:0.055cm; margin-right:0.055cm; margin-top:0.055cm; text-align:left ! important; text-indent:0.055cm; font-family:Tahoma; vertical-align:top; writing-mode:lr-tb; }
            .P12 { font-size:5pt; line-height:100%; margin:100%; margin-bottom:0.055cm; margin-left:0.055cm; margin-right:0.055cm; margin-top:0.055cm; text-align:center ! important; text-indent:0.055cm; font-family:Tahoma; vertical-align:top; writing-mode:lr-tb; }
            .P13 { font-size:5pt; line-height:100%; margin:100%; margin-bottom:0.055cm; margin-left:0.055cm; margin-right:0.055cm; margin-top:0.055cm; text-align:left ! important; text-indent:0.055cm; font-family:Tahoma; vertical-align:top; writing-mode:lr-tb; }
            .P14 { font-size:3.5pt; line-height:100%; margin:100%; margin-bottom:0.055cm; margin-left:0.055cm; margin-right:0.055cm; margin-top:0.055cm; text-align:left ! important; text-indent:0.055cm; font-family:Tahoma; vertical-align:top; writing-mode:lr-tb; }
            .P15 { font-size:3.5pt; line-height:100%; margin:100%; margin-bottom:0.055cm; margin-left:0.055cm; margin-right:0.055cm; margin-top:0.055cm; text-align:center ! important; text-indent:0.055cm; font-family:Tahoma; vertical-align:top; writing-mode:lr-tb; }
            .P16 { font-size:8.5pt; line-height:100%; margin:100%; margin-bottom:0.055cm; margin-left:0.055cm; margin-right:0.055cm; margin-top:0.055cm; text-align:left ! important; text-indent:0.055cm; font-family:Tahoma; vertical-align:top; writing-mode:lr-tb; }
            .P17 { font-size:7.5pt; line-height:100%; margin:100%; margin-bottom:0.055cm; margin-left:0.055cm; margin-right:0.055cm; margin-top:0.055cm; text-align:left ! important; text-indent:0.055cm; font-family:Tahoma; vertical-align:top; writing-mode:lr-tb; }
            .P18 { font-size:10pt; line-height:100%; margin:100%; margin-bottom:0.055cm; margin-left:0.055cm; margin-right:0.055cm; margin-top:0.055cm; text-align:left ! important; text-indent:0.055cm; font-family:Tahoma; vertical-align:top; writing-mode:lr-tb; }
            .P19 { font-size:10pt; line-height:100%; margin:100%; margin-bottom:0.055cm; margin-left:0.055cm; margin-right:0.055cm; margin-top:0.055cm; text-align:center ! important; text-indent:0.055cm; font-family:Tahoma; vertical-align:top; writing-mode:lr-tb; }
            .P2 { font-size:9.5pt; line-height:100%; margin:100%; margin-bottom:0.055cm; margin-left:0.055cm; margin-right:0.055cm; margin-top:0.055cm; text-align:left ! important; text-indent:0.055cm; font-family:Tahoma; vertical-align:top; writing-mode:lr-tb; }
            .P20 { font-size:4pt; line-height:100%; margin:100%; margin-bottom:0.055cm; margin-left:0.055cm; margin-right:0.055cm; margin-top:0.055cm; text-align:left ! important; text-indent:0.055cm; font-family:Tahoma; vertical-align:top; writing-mode:lr-tb; }
            .P21 { font-size:5.5pt; line-height:100%; margin:100%; margin-bottom:0.055cm; margin-left:0.055cm; margin-right:0.055cm; margin-top:0.055cm; text-align:left ! important; text-indent:0.055cm; font-family:Tahoma; vertical-align:top; writing-mode:lr-tb; }
            .P22 { font-size:3pt; line-height:100%; margin:100%; margin-bottom:0.055cm; margin-left:0.055cm; margin-right:0.055cm; margin-top:0.055cm; text-align:left ! important; text-indent:0.055cm; font-family:Tahoma; vertical-align:top; writing-mode:lr-tb; }
            .P23 { font-size:10pt; line-height:0.259cm; margin:100%; margin-bottom:0.055cm; margin-left:0.126cm; margin-right:0.055cm; margin-top:0.053cm; text-align:left ! important; text-indent:0.055cm; font-family:Tahoma; vertical-align:top; writing-mode:lr-tb; color:#000000; }
            .P24 { font-size:10pt; line-height:0.328cm; margin:100%; margin-bottom:0.055cm; margin-left:0.126cm; margin-right:0.055cm; margin-top:0.053cm; text-align:left ! important; text-indent:0.055cm; font-family:Tahoma; vertical-align:top; writing-mode:lr-tb; color:#000000; }
            .P25 { font-size:10pt; line-height:0.467cm; margin:100%; margin-bottom:0.055cm; margin-left:0.126cm; margin-right:0.055cm; margin-top:0.053cm; text-align:left ! important; text-indent:0.055cm; font-family:Tahoma; vertical-align:top; writing-mode:lr-tb; color:#000000; }
            .P26 { font-size:6pt; line-height:0.259cm; margin:100%; margin-bottom:0.055cm; margin-left:0.126cm; margin-right:0.055cm; margin-top:0.053cm; text-align:right ! important; text-indent:0.055cm; font-family:Verdana; vertical-align:top; writing-mode:lr-tb; color:#000000; }
            .P27 { font-size:10pt; line-height:0.328cm; margin:100%; margin-bottom:0.055cm; margin-left:0.126cm; margin-right:0.055cm; margin-top:0.053cm; text-align:center ! important; text-indent:0.055cm; font-family:Verdana; vertical-align:top; writing-mode:lr-tb; color:#000000; }
            .P28 { font-size:10pt; line-height:0.328cm; margin:100%; margin-bottom:0.055cm; margin-left:0.126cm; margin-right:0.055cm; margin-top:0.053cm; text-align:right ! important; text-indent:0.055cm; font-family:Verdana; vertical-align:top; writing-mode:lr-tb; color:#000000; }
            .P29 { font-size:10pt; line-height:0.328cm; margin:100%; margin-bottom:0.055cm; margin-left:0.126cm; margin-right:0.055cm; margin-top:0.053cm; text-align:left ! important; text-indent:0.055cm; font-family:Verdana; vertical-align:top; writing-mode:lr-tb; color:#000000; }
            .P3 { font-size:2pt; line-height:100%; margin:100%; margin-bottom:0.055cm; margin-left:0.055cm; margin-right:0.055cm; margin-top:0.055cm; text-align:left ! important; text-indent:0.055cm; font-family:Tahoma; vertical-align:top; writing-mode:lr-tb; }
            .P30 { font-size:10pt; line-height:0.328cm; margin:100%; margin-bottom:0.055cm; margin-left:0.126cm; margin-right:0.055cm; margin-top:0.053cm; text-align:left ! important; text-indent:0.055cm; font-family:Verdana; vertical-align:top; writing-mode:lr-tb; color:#000000; }
            .P31 { font-size:10pt; line-height:0.328cm; margin:100%; margin-bottom:0.055cm; margin-left:0.126cm; margin-right:0.055cm; margin-top:0.053cm; text-align:center ! important; text-indent:0.055cm; font-family:Verdana; vertical-align:top; writing-mode:lr-tb; color:#000000; }
            .P32 { font-size:10pt; line-height:0.467cm; margin:100%; margin-bottom:0.055cm; margin-left:0.126cm; margin-right:0.055cm; margin-top:0.053cm; text-align:center ! important; text-indent:0.055cm; font-family:Verdana; vertical-align:top; writing-mode:lr-tb; color:#000000; }
            .P33 { font-size:7pt; line-height:0.328cm; margin:100%; margin-bottom:0.055cm; margin-left:0.126cm; margin-right:0.055cm; margin-top:0.053cm; text-align:left ! important; text-indent:0.055cm; font-family:Verdana; vertical-align:top; writing-mode:lr-tb; color:#000000; }
            .P34 { font-size:9pt; line-height:0.328cm; margin:100%; margin-bottom:0.055cm; margin-left:0.126cm; margin-right:0.055cm; margin-top:0.053cm; text-align:center ! important; text-indent:0.055cm; font-family:Verdana; vertical-align:top; writing-mode:lr-tb; color:#000000; }
            .P35 { font-size:12pt; line-height:0.467cm; margin:100%; margin-bottom:0.055cm; margin-left:0.126cm; margin-right:0.055cm; margin-top:0.053cm; text-align:center ! important; text-indent:0.055cm; font-family:Arial; vertical-align:top; writing-mode:lr-tb; color:#000000; font-weight:bold; }
            .P36 { font-size:11pt; line-height:0.328cm; margin:100%; margin-bottom:0.055cm; margin-left:0.126cm; margin-right:0.055cm; margin-top:0.053cm; text-align:center ! important; text-indent:0.055cm; font-family:Calibri; vertical-align:top; writing-mode:lr-tb; }
            .P37 { font-size:10pt; line-height:0.259cm; margin:100%; margin-bottom:0.055cm; margin-left:0.126cm; margin-right:0.055cm; margin-top:0.055cm; text-align:left ! important; text-indent:0.055cm; font-family:Tahoma; vertical-align:top; writing-mode:lr-tb; color:#000000; }
            .P38 { font-size:10pt; line-height:0.259cm; margin:100%; margin-bottom:0.055cm; margin-left:0.126cm; margin-right:0.055cm; margin-top:0.055cm; text-align:left ! important; text-indent:0.055cm; font-family:Verdana; vertical-align:top; writing-mode:lr-tb; color:#000000; }
            .P39 { font-size:10pt; line-height:0.259cm; margin:100%; margin-bottom:0.055cm; margin-left:0.126cm; margin-right:0.055cm; margin-top:0.055cm; text-align:center ! important; text-indent:0.055cm; font-family:Verdana; vertical-align:top; writing-mode:lr-tb; color:#000000; }
            .P4 { font-size:2pt; line-height:100%; margin:100%; margin-bottom:0.055cm; margin-left:0.055cm; margin-right:0.055cm; margin-top:0.055cm; text-align:center ! important; text-indent:0.055cm; font-family:Tahoma; vertical-align:top; writing-mode:lr-tb; }
            .P40 { font-size:10pt; line-height:0.277cm; margin:100%; margin-bottom:0.055cm; margin-left:0.126cm; margin-right:0.055cm; margin-top:0.055cm; text-align:center ! important; text-indent:0.055cm; font-family:Verdana; vertical-align:top; writing-mode:lr-tb; color:#000000; }
            .P41 { font-size:10pt; line-height:0.277cm; margin:100%; margin-bottom:0.055cm; margin-left:0.126cm; margin-right:0.055cm; margin-top:0.055cm; text-align:right; text-indent:0.055cm; font-family:Verdana; vertical-align:top; writing-mode:lr-tb; color:#000000; }
            .P42 { font-size:10pt; line-height:0.277cm; margin:100%; margin-bottom:0.055cm; margin-left:0.126cm; margin-right:0.055cm; margin-top:0.055cm; text-align:left ! important; text-indent:0.055cm; font-family:Verdana; vertical-align:top; writing-mode:lr-tb; color:#000000; }
            .P43 { font-size:10pt; line-height:0.268cm; margin:100%; margin-bottom:0.055cm; margin-left:0.126cm; margin-right:0.055cm; margin-top:0.055cm; text-align:center ! important; text-indent:0.055cm; font-family:Verdana; vertical-align:top; writing-mode:lr-tb; color:#000000; }
            .P44 { font-size:10pt; line-height:0.268cm; margin:100%; margin-bottom:0.055cm; margin-left:0.126cm; margin-right:0.055cm; margin-top:0.055cm; text-align:right ! important; text-indent:0.055cm; font-family:Verdana; vertical-align:top; writing-mode:lr-tb; color:#000000; }
            .P45 { font-size:10pt; line-height:0.268cm; margin:100%; margin-bottom:0.055cm; margin-left:0.126cm; margin-right:0.055cm; margin-top:0.055cm; text-align:left ! important; text-indent:0.055cm; font-family:Verdana; vertical-align:top; writing-mode:lr-tb; color:#000000; }
            .P46 { font-size:10pt; line-height:0.277cm; margin:100%; margin-bottom:0.055cm; margin-left:0.126cm; margin-right:0.055cm; margin-top:0.055cm; text-align:center ! important; text-indent:0.055cm; font-family:Verdana; vertical-align:top; writing-mode:lr-tb; color:#000000; }
            .P47 { font-size:10pt; line-height:0.277cm; margin:100%; margin-bottom:0.055cm; margin-left:0.126cm; margin-right:0.055cm; margin-top:0.055cm; text-align:right ! important; text-indent:0.055cm; font-family:Verdana; vertical-align:top; writing-mode:lr-tb; color:#000000; }
            .P48 { font-size:10pt; line-height:0.268cm; margin:100%; margin-bottom:0.055cm; margin-left:0.126cm; margin-right:0.055cm; margin-top:0.055cm; text-align:center ! important; text-indent:0.055cm; font-family:Verdana; vertical-align:top; writing-mode:lr-tb; color:#000000; }
            .P49 { font-size:10pt; line-height:0.268cm; margin:100%; margin-bottom:0.055cm; margin-left:0.126cm; margin-right:0.055cm; margin-top:0.055cm; text-align:right ! important; text-indent:0.055cm; font-family:Verdana; vertical-align:top; writing-mode:lr-tb; color:#000000; }
            .P5 { font-size:1.5pt; line-height:100%; margin:100%; margin-bottom:0.055cm; margin-left:0.055cm; margin-right:0.055cm; margin-top:0.055cm; text-align:left ! important; text-indent:0.055cm; font-family:Tahoma; vertical-align:top; writing-mode:lr-tb; }
            .P50 { font-size:10pt; line-height:0.277cm; margin:100%; margin-bottom:0.055cm; margin-left:0.126cm; margin-right:0.055cm; margin-top:0.055cm; text-align:center ! important; text-indent:0.055cm; font-family:Verdana; vertical-align:top; writing-mode:lr-tb; color:#000000; letter-spacing:-0.126cm; }
            .P51 { font-size:10pt; line-height:0.268cm; margin:100%; margin-bottom:0.055cm; margin-left:0.126cm; margin-right:0.055cm; margin-top:0.055cm; text-align:center ! important; text-indent:0.055cm; font-family:Verdana; vertical-align:top; writing-mode:lr-tb; color:#000000; letter-spacing:-0.126cm; }
            .P52 { font-size:6pt; line-height:0.259cm; margin:100%; margin-bottom:0.055cm; margin-left:0.126cm; margin-right:0.055cm; margin-top:0.055cm; text-align:center ! important; text-indent:0.055cm; font-family:Verdana; vertical-align:top; writing-mode:lr-tb; color:#000000; }
            .P53 { font-size:10pt; line-height:0.318cm; margin:100%; margin-bottom:0.055cm; margin-left:0.126cm; margin-right:0.055cm; margin-top:0.051cm; text-align:left ! important; text-indent:0.055cm; font-family:Verdana; vertical-align:top; writing-mode:lr-tb; color:#000000; }
            .P54 { font-size:10pt; line-height:0.318cm; margin:100%; margin-bottom:0.055cm; margin-left:0.126cm; margin-right:0.055cm; margin-top:0.051cm; text-align:center ! important; text-indent:0.055cm; font-family:Verdana; vertical-align:top; writing-mode:lr-tb; color:#000000; }
            .P55 { font-size:10pt; line-height:0.318cm; margin:100%; margin-bottom:0.055cm; margin-left:0.126cm; margin-right:0.055cm; margin-top:0.051cm; text-align:right ! important; text-indent:0.055cm; font-family:Verdana; vertical-align:top; writing-mode:lr-tb; color:#000000; }
            .P56 { font-size:10pt; line-height:0.318cm; margin:100%; margin-bottom:0.055cm; margin-left:0.126cm; margin-right:0.055cm; margin-top:0.051cm; text-align:center ! important; text-indent:0.055cm; font-family:Verdana; vertical-align:top; writing-mode:lr-tb; color:#000000; }
            .P57 { font-size:10pt; line-height:0.318cm; margin:100%; margin-bottom:0.055cm; margin-left:0.126cm; margin-right:0.055cm; margin-top:0.051cm; text-align:left ! important; text-indent:0.055cm; font-family:Tahoma; vertical-align:top; writing-mode:lr-tb; color:#000000; }
            .P58 { font-size:11pt; line-height:0.318cm; margin:100%; margin-bottom:0.055cm; margin-left:0.126cm; margin-right:0.055cm; margin-top:0.051cm; text-align:center ! important; text-indent:0.055cm; font-family:Calibri; vertical-align:top; writing-mode:lr-tb; }
            .P59 { font-size:10pt; line-height:0.318cm; margin:100%; margin-bottom:0.055cm; margin-left:0.126cm; margin-right:0.055cm; margin-top:0.025cm; text-align:left ! important; text-indent:0.055cm; font-family:Tahoma; vertical-align:top; writing-mode:lr-tb; color:#000000; }
            .P6 { font-size:9pt; line-height:100%; margin:100%; margin-bottom:0.055cm; margin-left:0.055cm; margin-right:0.055cm; margin-top:0.055cm; text-align:left ! important; text-indent:0.055cm; font-family:Tahoma; vertical-align:top; writing-mode:lr-tb; }
            .P60 { font-size:10pt; line-height:0.217cm; margin:100%; margin-bottom:0.055cm; margin-left:0.126cm; margin-right:0.055cm; margin-top:0.025cm; text-align:left ! important; text-indent:0.055cm; font-family:Tahoma; vertical-align:top; writing-mode:lr-tb; color:#000000; }
            .P61 { font-size:10pt; line-height:0.318cm; margin:100%; margin-bottom:0.055cm; margin-left:0.126cm; margin-right:0.055cm; margin-top:0.025cm; text-align:left ! important; text-indent:0.055cm; font-family:Verdana; vertical-align:top; writing-mode:lr-tb; color:#000000; }
            .P62 { font-size:10pt; line-height:0.318cm; margin:100%; margin-bottom:0.055cm; margin-left:0.126cm; margin-right:0.055cm; margin-top:0.025cm; text-align:right ! important; text-indent:0.055cm; font-family:Verdana; vertical-align:top; writing-mode:lr-tb; color:#000000; }
            .P63 { font-size:10pt; line-height:0.318cm; margin:100%; margin-bottom:0.055cm; margin-left:0.126cm; margin-right:0.055cm; margin-top:0.025cm; text-align:center ! important; text-indent:0.055cm; font-family:Verdana; vertical-align:top; writing-mode:lr-tb; color:#000000; }
            .P64 { font-size:10pt; line-height:0.318cm; margin:100%; margin-bottom:0.055cm; margin-left:0.126cm; margin-right:0.055cm; margin-top:0.025cm; text-align:left ! important; text-indent:0.055cm; font-family:Verdana; vertical-align:top; writing-mode:lr-tb; color:#000000; }
            .P65 { font-size:10pt; line-height:0.318cm; margin:100%; margin-bottom:0.055cm; margin-left:0.126cm; margin-right:0.055cm; margin-top:0.025cm; text-align:center ! important; text-indent:0.055cm; font-family:Verdana; vertical-align:top; writing-mode:lr-tb; color:#000000; }
            .P66 { font-size:10pt; line-height:0.217cm; margin:100%; margin-bottom:0.055cm; margin-left:0.126cm; margin-right:0.055cm; margin-top:0.025cm; text-align:left ! important; text-indent:0.055cm; font-family:Verdana; vertical-align:top; writing-mode:lr-tb; color:#000000; }
            .P67 { font-size:5pt; line-height:0.217cm; margin:100%; margin-bottom:0.055cm; margin-left:0.126cm; margin-right:0.055cm; margin-top:0.025cm; text-align:center ! important; text-indent:0.055cm; font-family:Verdana; vertical-align:top; writing-mode:lr-tb; color:#000000; }
            .P68 { font-size:1pt; line-height:0.217cm; margin:100%; margin-bottom:0.055cm; margin-left:0.126cm; margin-right:0.055cm; margin-top:0.025cm; text-align:left ! important; text-indent:0.055cm; font-family:Arial; vertical-align:top; writing-mode:lr-tb; color:#000000; }
            .P69 { font-size:11pt; line-height:0.318cm; margin:100%; margin-bottom:0.055cm; margin-left:0.126cm; margin-right:0.055cm; margin-top:0.025cm; text-align:left ! important; text-indent:0.055cm; font-family:Calibri; vertical-align:top; writing-mode:lr-tb; }
            .P7 { font-size:1pt; line-height:100%; margin:100%; margin-bottom:0.055cm; margin-left:0.055cm; margin-right:0.055cm; margin-top:0.055cm; text-align:left ! important; text-indent:0.055cm; font-family:Tahoma; vertical-align:top; writing-mode:lr-tb; }
            .P70 { font-size:10pt; line-height:0.293cm; margin:100%; margin-bottom:0.055cm; margin-left:0.126cm; margin-right:0.055cm; margin-top:0.078cm; text-align:left ! important; text-indent:0.055cm; font-family:Verdana; vertical-align:top; writing-mode:lr-tb; color:#000000; }
            .P71 { font-size:10pt; line-height:0.367cm; margin:100%; margin-bottom:0.055cm; margin-left:0.126cm; margin-right:0.055cm; margin-top:0.078cm; text-align:left ! important; text-indent:0.055cm; font-family:Verdana; vertical-align:top; writing-mode:lr-tb; color:#000000; }
            .P72 { font-size:10pt; line-height:0.367cm; margin:100%; margin-bottom:0.055cm; margin-left:0.126cm; margin-right:0.055cm; margin-top:0.078cm; text-align:left ! important; text-indent:0.055cm; font-family:Verdana; vertical-align:top; writing-mode:lr-tb; color:#000000; }
            .P73 { font-size:10pt; line-height:0.367cm; margin:100%; margin-bottom:0.055cm; margin-left:0.126cm; margin-right:0.055cm; margin-top:0.078cm; text-align:left ! important; text-indent:0.055cm; font-family:Tahoma; vertical-align:top; writing-mode:lr-tb; color:#000000; }
            .P74 { font-size:11pt; line-height:115%; margin:100%; margin-bottom:0.353cm; margin-left:0.055cm; margin-right:0.055cm; margin-top:0.055cm; text-align:left ! important; text-indent:0.055cm; font-family:Calibri; vertical-align:top; writing-mode:lr-tb; }
            .P8 { font-size:1pt; line-height:100%; margin:100%; margin-bottom:0.055cm; margin-left:0.055cm; margin-right:0.055cm; margin-top:0.055cm; text-align:center ! important; text-indent:0.055cm; font-family:Tahoma; vertical-align:top; writing-mode:lr-tb; }
            .P9 { font-size:4.5pt; line-height:100%; margin:100%; margin-bottom:0.055cm; margin-left:0.055cm; margin-right:0.055cm; margin-top:0.055cm; text-align:center ! important; text-indent:0.055cm; font-family:Tahoma; vertical-align:top; writing-mode:lr-tb; }
            .Таблица1 { width:100%; writing-mode:lr-tb; }
            .Таблица2 { width:100%; writing-mode:lr-tb; margin-top: 50px;}
            .Таблица1_A1 { padding-left:0.126cm; padding-right:0.126cm; padding-top:0.055cm; padding-bottom:0.055cm; border-style:none; }
            .Таблица1_A34 { vertical-align:middle; padding-left:0.126cm; padding-right:0.126cm; padding-top:0.055cm; padding-bottom:0.055cm; border-left-width:0.035cm; border-left-style:solid; border-left-color:#000000; border-right-width:0.035cm; border-right-style:solid; border-right-color:#000000; border-top-style:none; border-bottom-width:0.035cm; border-bottom-style:solid; border-bottom-color:#000000; }
            .Таблица1_A43 { vertical-align:middle; padding-left:0.126cm; padding-right:0.126cm; padding-top:0.055cm; padding-bottom:0.055cm; border-left-style:none; border-right-style:none; border-top-width:0.035cm; border-top-style:solid; border-top-color:#000000; border-bottom-style:none; }
            .Таблица1_A5 { vertical-align:bottom; padding-left:0.126cm; padding-right:0.126cm; padding-top:0.055cm; padding-bottom:0.055cm; border-style:none; }
            .Таблица1_A7 { padding-left:0.126cm; padding-right:0.126cm; padding-top:0.055cm; padding-bottom:0.055cm; border-left-style:none; border-right-style:none; border-top-width:0.035cm; border-top-style:solid; border-top-color:#000000; border-bottom-style:none; }
            .Таблица1_A8 { vertical-align:bottom; padding-left:0.126cm; padding-right:0.126cm; padding-top:0.055cm; padding-bottom:0.055cm; border-left-style:none; border-right-style:none; border-top-style:none; border-bottom-width:0.035cm;}
            .Таблица1_A9 { padding-left:0.126cm; padding-right:0.126cm; padding-top:0.055cm; padding-bottom:0.055cm; border-left-style:none; border-right-style:none; border-top-style:none; border-bottom-width:0.035cm; border-bottom-style:solid; border-bottom-color:#000000; }
            .Таблица1_B33 { vertical-align:middle; padding-left:0.126cm; padding-right:0.126cm; padding-top:0.055cm; padding-bottom:0.055cm; border-left-style:none; border-right-width:0.0333cm; border-right-style:solid; border-right-color:#000000; border-top-width:0.0333cm; border-top-style:solid; border-top-color:#000000; border-bottom-width:0.0333cm; border-bottom-style:solid; border-bottom-color:#000000; }
            .Таблица1_D15 { padding-left:0.126cm; padding-right:0.126cm; padding-top:0.055cm; padding-bottom:0.055cm; border-left-style:none; border-right-style:none; border-top-width:0.0333cm; border-top-style:solid; border-top-color:#000000; border-bottom-style:none; }
            .Таблица1_M43 { vertical-align:middle; padding-left:0.126cm; padding-right:0.126cm; padding-top:0.055cm; padding-bottom:0.055cm; border-width:0.035cm; border-style:solid; border-color:#000000; }
            .Таблица1_Y4 { vertical-align:middle; padding-left:0.126cm; padding-right:0.126cm; padding-top:0.055cm; padding-bottom:0.055cm; border-style:none; }
            .Таблица1_b21 { padding-left:0.126cm; padding-right:0.126cm; padding-top:0.055cm; padding-bottom:0.055cm; border-width:0.0333cm; }
            .Таблица1_e3 { vertical-align:middle; padding-left:0.126cm; padding-right:0.126cm; padding-top:0.055cm; padding-bottom:0.055cm; border-width:0.0333cm; border-style:solid; border-color:#000000; }
            .Таблица1_e4 { vertical-align:middle; padding-left:0.126cm; padding-right:0.126cm; padding-top:0.055cm; padding-bottom:0.055cm; border-left-width:0.0333cm; border-left-style:solid; border-left-color:#000000; border-right-width:0.0333cm; border-right-style:solid; border-right-color:#000000; border-top-style:none; border-bottom-width:0.0333cm; border-bottom-style:solid; border-bottom-color:#000000; }
            .Таблица1_e6 { padding-left:0.126cm; padding-right:0.126cm; padding-top:0.055cm; padding-bottom:0.055cm; border-left-width:0.0333cm; border-left-style:solid; border-left-color:#000000; border-right-width:0.0333cm; border-right-style:solid; border-right-color:#000000; border-top-style:none; border-bottom-width:0.0333cm; border-bottom-style:solid; border-bottom-color:#000000; }
            .Таблица1_e7 { vertical-align:bottom; padding-left:0.126cm; padding-right:0.126cm; padding-top:0.055cm; padding-bottom:0.055cm; border-left-width:0.0333cm; border-left-style:solid; border-left-color:#000000; border-right-width:0.0333cm; border-right-style:solid; border-right-color:#000000; border-top-style:none; border-bottom-width:0.0333cm; border-bottom-style:solid; border-bottom-color:#000000; }
            .Таблица2_A1 { vertical-align:middle; padding-left:0.126cm; padding-right:0.126cm; padding-top:0.055cm; padding-bottom:0.055cm; border-width:0.0333cm; border-style:solid; border-color:#000000; }
            .Таблица2_A2 { padding-left:0.126cm; padding-right:0.126cm; padding-top:0.055cm; padding-bottom:0.055cm; border-width:0.0333cm; border-style:solid; border-color:#000000; }
            .Таблица2_A4 { vertical-align:middle; padding-left:0.126cm; padding-right:0.126cm; padding-top:0.055cm; padding-bottom:0.055cm; border-left-width:0.035cm; border-left-style:solid; border-left-color:#000000; border-right-width:0.035cm; border-right-style:solid; border-right-color:#000000; border-top-style:none; border-bottom-width:0.035cm; border-bottom-style:solid; border-bottom-color:#000000; }
            .Таблица2_A6 { vertical-align:middle; padding-left:0.126cm; padding-right:0.126cm; padding-top:0.055cm; padding-bottom:0.055cm; border-left-style:none; border-right-style:none; border-top-width:0.035cm; border-top-style:solid; border-top-color:#000000; border-bottom-style:none; }
            .Таблица2_A7 { vertical-align:middle; padding-left:0.126cm; padding-right:0.126cm; padding-top:0.055cm; padding-bottom:0.055cm; border-style:none; }
            .Таблица2_A8 { padding-left:0.126cm; padding-right:0.126cm; padding-top:0.055cm; padding-bottom:0.055cm; border-style:none; }
            .Таблица2_C24 { vertical-align:bottom; padding-left:0.126cm; padding-right:0.126cm; padding-top:0.055cm; padding-bottom:0.055cm; border-style:none; }
            .Таблица2_E3 { vertical-align:middle; padding-left:0.126cm; padding-right:0.126cm; padding-top:0.055cm; padding-bottom:0.055cm; border-left-style:none; border-right-width:0.0333cm; border-right-style:solid; border-right-color:#000000; border-top-width:0.0333cm; border-top-style:solid; border-top-color:#000000; border-bottom-width:0.0333cm; border-bottom-style:solid; border-bottom-color:#000000; }
            .Таблица2_J28 { vertical-align:bottom; padding-left:0.126cm; padding-right:0.126cm; padding-top:0.055cm; padding-bottom:0.055cm; border-left-style:none; border-right-style:none; border-top-style:none; border-bottom-width:0.035cm; border-bottom-style:solid; border-bottom-color:#000000; }
            .Таблица2_J37 { padding-left:0.126cm; padding-right:0.126cm; padding-top:0.055cm; padding-bottom:0.055cm; border-left-style:none; border-right-style:none; border-top-width:0.035cm; border-top-style:solid; border-top-color:#000000; border-bottom-style:none; }
            .Таблица2_N19 { padding-left:0.126cm; padding-right:0.126cm; padding-top:0.055cm; padding-bottom:0.055cm; border-left-style:none; border-right-style:none; border-top-style:none; border-bottom-width:0.035cm; border-bottom-style:solid; border-bottom-color:#000000; }
            .Таблица2_a17 { padding-left:0.126cm; padding-right:0.126cm; padding-top:0.055cm; padding-bottom:0.055cm; border-left-width:0.035cm; border-left-style:solid; border-left-color:#000000; border-right-style:none; border-top-style:none; border-bottom-style:none; }
            .Таблица2_a6 { vertical-align:middle; padding-left:0.126cm; padding-right:0.126cm; padding-top:0.055cm; padding-bottom:0.055cm; border-width:0.035cm; border-style:solid; border-color:#000000; }
            .Таблица2_f11 { vertical-align:middle; padding-left:0.126cm; padding-right:0.126cm; padding-top:0.055cm; padding-bottom:0.055cm; border-left-style:none; border-right-style:none; border-top-style:none; border-bottom-width:0.035cm; border-bottom-style:solid; border-bottom-color:#000000; }

            .T2 { color:#000000; font-family:Verdana; font-size:10pt; }
            <!-- ODF styles with no properties representable as CSS -->
            .Таблица1.1 .Таблица1.10 .Таблица1.11 .Таблица1.13 .Таблица1.14 .Таблица1.16 .Таблица1.2 .Таблица1.21 .Таблица1.26 .Таблица1.31 .Таблица1.32 .Таблица1.5 .Таблица1.6 .Таблица1.7 .Таблица1.8 .Таблица1.9 .Таблица2.1 .Таблица2.10 .Таблица2.12 .Таблица2.16 .Таблица2.17 .Таблица2.18 .Таблица2.19 .Таблица2.2 .Таблица2.20 .Таблица2.22 .Таблица2.23 .Таблица2.24 .Таблица2.27 .Таблица2.29 .Таблица2.3 .Таблица2.30 .Таблица2.36 .Таблица2.39 .Таблица2.4 .Таблица2.8 .Таблица2.9 { }
        </style>
    </head>
    <body style="margin:10px">

        <div align="right" class="nonprint">
            <button onclick="html2pdf(document.getElementById('content'), {margin: 1, filename: 'ТОРГ-12 №<?php echo $ouid ?>.pdf', html2canvas: {dpi: 192, letterRendering: true}, jsPDF: {orientation: 'landscape'}});">Сохранить</button> 
            <button onclick="window.print();">Распечатать</button> 
            <br><br><hr><br><br>
        </div>
        <div id="content">
            <table border="0" cellspacing="0" cellpadding="0" class="Таблица1">
                <colgroup>
                    <col width="40"/>
                    <col width="66"/>
                    <col width="9"/>
                    <col width="213"/>
                    <col width="94"/>
                    <col width="31"/>
                    <col width="41"/>
                    <col width="38"/>
                    <col width="29"/>
                    <col width="6"/>
                    <col width="7"/>
                    <col width="38"/>
                    <col width="39"/>
                    <col width="39"/>
                    <col width="10"/>
                    <col width="59"/>
                    <col width="71"/>
                    <col width="9"/>
                    <col width="18"/>
                    <col width="10"/>
                    <col width="57"/>
                    <col width="7"/>
                    <col width="25"/>
                    <col width="51"/>
                    <col width="8"/>
                    <col width="20"/>
                    <col width="5"/>
                    <col width="14"/>
                    <col width="58"/>
                    <col width="6"/>
                    <col width="93"/>
                </colgroup>
                <tr class="Таблица11">
                    <td colspan="17" style="text-align:left;width:0.924cm; " class="Таблица1_A1"><p class="P1"> </p></td>
                    <td colspan="14" style="text-align:left;width:0.213cm; " class="Таблица1_A1"><p class="P26">Унифицированная форма № ТОРГ-12<br/>Утверждена постановлением Госкомстата России от 25.12.98 № 132</p></td>
                </tr>
                <tr class="Таблица12">  <td colspan="17" style="text-align:left;width:0.924cm; " class="Таблица1_A1"><p class="P3"> </p></td>
                    <td colspan="14" style="text-align:left;width:0.213cm; " class="Таблица1_A1"><p class="P23"> </p></td>
                </tr>
                <tr class="Таблица11">    <td colspan="17" style="text-align:left;width:0.924cm; " class="Таблица1_A1"><p class="P2"> </p></td>
                    <td colspan="13" style="text-align:left;width:0.213cm; " class="Таблица1_A1"><p class="P23"> </p></td>
                    <td style="text-align:left;width:2.124cm; " class="Таблица1_e3"><p class="P27">Код</p></td>
                </tr>
                <tr class="Таблица11">    <td colspan="17" style="text-align:left;width:0.924cm; " class="Таблица1_A1"><p class="P2"> </p></td>
                    <td colspan="7" style="text-align:left;width:0.213cm; " class="Таблица1_A1"><p class="P24"> </p></td>
                    <td colspan="6" style="text-align:left;width:0.185cm; " class="Таблица1_Y4"><p class="P28">Форма по ОКУД </p></td>
                    <td style="text-align:left;width:2.124cm; " class="Таблица1_e4"><p class="P27">0310001</p></td>
                </tr>
                <tr class="Таблица15">    <td colspan="25" style="text-align:left;width:0.924cm; " class="Таблица1_A5"><p class="P30"><?php echo $blank_org_name ?>, &nbsp;ИНН&nbsp;<?php echo$blank_org_inn ?>, КПП&nbsp;<?php echo $blank_org_kpp ?>, Юр. адрес:&nbsp;<?php echo $blank_org_ur_adres ?>, Почтовый адрес:&nbsp;<?php echo $blank_org_adres ?></p></td>
                    <td colspan="5" style="text-align:left;width:1.326cm; " class="Таблица1_Y4"><p class="P28">по ОКПО </p></td>
                    <td style="text-align:left;width:2.124cm; " class="Таблица1_e4"><p class="P31"> </p></td>
                </tr>
                <tr class="Таблица17">    
                    <td colspan="25" style="text-align:left;width:0.924cm; " class="Таблица1_A7"><p class="P52">(организация-грузоотправитель, адрес, телефон, факс, банковские реквизиты)</p></td>
                    <td colspan="3" style="text-align:left;width:0.459cm; " class="Таблица1_A1"><p class="P9"> </p></td>
                    <td colspan="2" style="text-align:left;width:1.326cm; " class="Таблица1_A1"><p class="P37"> </p></td>
                    <td rowspan="2" style="text-align:left;width:2.124cm; " class="Таблица1_e7"><p class="P39"> </p></td>
                </tr>
                <tr class="Таблица19">
                    <td colspan="30" style="text-align:left;width:0.924cm; " class="Таблица1_A9"><p class="P6"> </p></td>
                </tr>
                <tr class="Таблица110">   <td colspan="3" style="text-align:left;width:0.924cm; " class="Таблица1_A1"><p class="P37"> </p></td>
                    <td colspan="18" style="text-align:left;width:4.884cm; " class="Таблица1_A1"><p class="P52">структурное подразделение</p></td>
                    <td style="text-align:left;width:0.159cm; " class="Таблица1_A1"><p class="P37"> </p></td>
                    <td colspan="8" style="text-align:left;width:0.582cm; " class="Таблица1_A5"><p class="P28">Вид деятельности по ОКДП </p></td>
                    <td style="text-align:left;width:2.124cm; " class="Таблица1_e7"><p class="P31"> </p></td>
                </tr>
                <tr class="Таблица113">   <td colspan="3" style="text-align:left;width:0.924cm; " class="Таблица1_A1"><p class="P29">Грузополучатель</p></td>
                    <td colspan="22" style="text-align:left;width:4.884cm; " class="Таблица1_A5"><p class="P29"><?php if (empty($orgData)) echo $blank_person_user;
else echo $orgData; ?></p></td>
                    <td style="text-align:left;width:0.319cm; " class="Таблица1_A1"><p class="P24"></p></td>
                    <td colspan="4" style="text-align:left;width:1.326cm; " class="Таблица1_Y4"><p class="P28">по ОКПО </p></td>
                    <td style="text-align:left;width:2.124cm; " class="Таблица1_e4"><p class="P31"> </p></td>
                </tr>

                <tr class="Таблица110">   <td colspan="2" style="text-align:left;width:0.924cm; " class="Таблица1_A1"></td>
                    <td style="text-align:left;width:0.212cm; " class="Таблица1_A1"><p class="P24"> </p></td>
                    <td colspan="22" style="text-align:left;width:4.884cm; " class="Таблица1_D15"><p class="P52">(организация, адрес, телефон, факс, банковские реквизиты)</p></td>
                    <td style="text-align:left;width:0.319cm; " class="Таблица1_A1"><p class="P12"> </p></td>
                    <td colspan="4" style="text-align:left;width:1.326cm; " class="Таблица1_Y4"><p class="P28">по ОКПО </p></td>
                    <td style="text-align:left;width:2.124cm; " class="Таблица1_e4"><p class="P31"> </p></td>
                </tr>

                <tr class="Таблица110">   <td colspan="2" style="text-align:left;width:0.924cm; " class="Таблица1_A1" valign="top"><p class="P29">Поставщик</p></td>
                    <td style="text-align:left;width:0.212cm; " class="Таблица1_A1"><p class="P24"> </p></td>
                    <td colspan="22" style="text-align:left;width:4.884cm; " class="Таблица1_A1"><p class="P29"><?php echo $blank_org_name ?>, &nbsp;ИНН&nbsp;<?php echo$blank_org_inn ?>, КПП&nbsp;<?php echo $blank_org_kpp ?>, Юр. адрес:&nbsp;<?php echo $blank_org_ur_adres ?>, Почтовый адрес:&nbsp;<?php echo $blank_org_adres ?></p></td>
                    <td style="text-align:left;width:0.319cm; " class="Таблица1_A1"><p class="P12"> </p></td>
                    <td colspan="4" style="text-align:left;width:1.326cm; " class="Таблица1_Y4"><p class="P28"></p></td>
                    <td style="text-align:left;width:2.124cm; " class="Таблица1_e4"><p class="P31"> </p></td>
                </tr>

                <tr class="Таблица110">   <td colspan="2" style="text-align:left;width:0.924cm; " class="Таблица1_A1"></td>
                    <td style="text-align:left;width:0.212cm; " class="Таблица1_A1"><p class="P13"> </p></td>
                    <td colspan="22" style="text-align:left;width:4.884cm; " class="Таблица1_D15"><p class="P52">(организация, адрес, телефон, факс, банковские реквизиты)</p></td>
                    <td style="text-align:left;width:0.319cm; " class="Таблица1_A1"><p class="P12"> </p></td>
                    <td colspan="4" style="text-align:left;width:1.326cm; " class="Таблица1_Y4"><p class="P28">по ОКПО </p></td>
                    <td style="text-align:left;width:2.124cm; " class="Таблица1_e4"><p class="P31"> </p></td>
                </tr>

                <tr class="Таблица110">   <td colspan="2" style="text-align:left;width:0.924cm; " class="Таблица1_A1"><p class="P29">Плательщик</p></td>
                    <td style="text-align:left;width:0.212cm; " class="Таблица1_A1"><p class="P13"> </p></td>
                    <td colspan="22" style="text-align:left;width:4.884cm; " class="Таблица1_A1"><p class="P29"><?php if (empty($orgData)) echo $blank_person_user;
else echo $orgData; ?></p></td>
                    <td style="text-align:left;width:0.319cm; " class="Таблица1_A1"><p class="P12"> </p></td>
                    <td colspan="4" style="text-align:left;width:1.326cm; " class="Таблица1_Y4"><p class="P28"></p></td>
                    <td style="text-align:left;width:2.124cm; " class="Таблица1_e4"><p class="P31"> </p></td>
                </tr>


                <tr class="Таблица110">   <td colspan="2" style="text-align:left;width:0.924cm; " class="Таблица1_A1"><p class="P24"> </p></td>
                    <td style="text-align:left;width:0.212cm; " class="Таблица1_A1"><p class="P13"> </p></td>
                    <td colspan="22" style="text-align:left;width:4.884cm; " class="Таблица1_D15"><p class="P52">(организация, адрес, телефон, факс, банковские реквизиты)</p></td>
                    <td colspan="2" style="text-align:left;width:1.326cm; " class="Таблица1_Y4"><p class="P28"></p></td>
                    <td colspan="3" style="text-align:left;width:0.319cm; " class="Таблица1_e3"><p class="P28">номер </p></td>
                    <td style="text-align:left;width:2.124cm; " class="Таблица1_e4"><p class="P31"> </p></td>
                </tr>
                <tr class="Таблица113">   <td colspan="2" style="text-align:left;width:0.924cm; " class="Таблица1_A1"><p class="P29">Основание</p></td>
                    <td style="text-align:left;width:0.212cm; " class="Таблица1_A1"><p class="P16"> </p></td>
                    <td colspan="24" style="text-align:left;width:4.884cm; " class="Таблица1_A1"><p class="P16"> </p></td>
                    <td colspan="3" style="text-align:left;width:0.319cm; " class="Таблица1_e3"><p class="P28">дата </p></td>
                    <td style="text-align:left;width:2.124cm; " class="Таблица1_e4"><p class="P31"> </p></td>
                </tr>
                <tr class="Таблица110">   <td colspan="2" style="text-align:left;width:0.924cm; " class="Таблица1_A1"><p class="P13"> </p></td>
                    <td style="text-align:left;width:0.212cm; " class="Таблица1_A1"><p class="P13"> </p></td>
                    <td colspan="24" style="text-align:left;width:4.884cm; " class="Таблица1_D15"><p class="P52">(договор, заказ-наряд)</p></td>
                    <td colspan="3" style="text-align:left;width:0.319cm; " class="Таблица1_e3"><p class="P28">номер </p></td>
                    <td style="text-align:left;width:2.124cm; " class="Таблица1_e4"><p class="P31"> </p></td>
                </tr>
                <tr class="Таблица126">   <td colspan="2" style="text-align:left;width:0.924cm; " class="Таблица1_A1"><p class="P17"> </p></td>
                    <td style="text-align:left;width:0.212cm; " class="Таблица1_A1"><p class="P17"> </p></td>
                    <td colspan="3" style="text-align:left;width:4.884cm; " class="Таблица1_A1"><p class="P24"> </p></td>
                    <td colspan="5" style="text-align:left;width:0.873cm; " class="Таблица1_e3"><p class="P36"><span class="T2">Номер документа</span></p></td>
                    <td colspan="4" style="text-align:left;width:1.353cm; " class="Таблица1_e3"><p class="P36"><span class="T2">Дата составления</span></p></td>
                    <td colspan="1" style="text-align:left;width:0.423cm; " class="Таблица1_A1"><p class="P24"> </p></td>
                    <td colspan="10" style="text-align:left;width:1.295cm; " class="Таблица1_Y4"><p class="P28">Транспортная накладная </p></td>
                    <td style="text-align:left;width:0.125cm; " class="Таблица1_A1"><p class="P24"> </p></td>
                    <td colspan="3" style="text-align:left;width:0.319cm; " class="Таблица1_e3"><p class="P28">дата </p></td>
                    <td style="text-align:left;width:2.124cm; " class="Таблица1_e4"><p class="P31"> </p></td>
                </tr>
                <tr class="Таблица12">
                    <td colspan="6" style="text-align:left;width:2.148cm; " class="Таблица1_Y4"><p class="P35">ТОВАРНАЯ НАКЛАДНАЯ</p></td>
                    <td colspan="5" style="text-align:left;width:0.873cm; " class="Таблица1_e3">
                        <p class="P32"><input title="Изменить" style="font-size: 14px;font-weight: normal;" value="<?php echo $ouid; ?>"></p></td>
                    <td colspan="4" style="text-align:left;width:1.353cm; " class="Таблица1_e3">
                        <p class="P32"><input title="Изменить" style="font-size: 14px;font-weight: normal;" value="<?php echo PHPShopDate::get($row['datas'], false, false, '.', false) ?>"></p>
                    </td>
                    <td colspan="9" style="text-align:left;width:0.423cm; " class="Таблица1_A1"><p class="P4"> </p></td>
                    <td colspan="1" style="text-align:left;width:1.295cm; " class="Таблица1_A1"><p class="P4"> </p></td>
                    <td colspan="5" style="text-align:left;width:0.459cm; " class="Таблица1_Y4"><p class="P28">Вид операции </p></td>
                    <td style="text-align:left;width:2.124cm; " class="Таблица1_e7"><p class="P34"> </p></td>
                </tr>
                <tr class="Таблица126">   <td colspan="2" style="text-align:left;width:0.924cm; " class="Таблица1_A1"><p class="P17"> </p></td>
                    <td style="text-align:left;width:0.212cm; " class="Таблица1_A1"><p class="P17"> </p></td>
                    <td style="text-align:left;width:4.884cm; " class="Таблица1_A1"><p class="P17"> </p></td>
                    <td colspan="5" style="text-align:left;width:2.148cm; " class="Таблица1_A1"><p class="P17"> </p></td>
                    <td colspan="2" style="text-align:left;width:0.131cm; " class="Таблица1_A1"><p class="P17"> </p></td>
                    <td colspan="4" style="text-align:left;width:0.873cm; " class="Таблица1_b21"><p class="P17"> </p></td>
                    <td colspan="3" style="text-align:left;width:1.353cm; " class="Таблица1_b21"><p class="P17"> </p></td>
                    <td colspan="2" style="text-align:left;width:0.423cm; " class="Таблица1_A1"><p class="P17"> </p></td>
                    <td colspan="5" style="text-align:left;width:1.295cm; " class="Таблица1_A1"><p class="P25"> </p></td>
                    <td colspan="5" style="text-align:left;width:0.459cm; " class="Таблица1_A1"><p class="P25"> </p></td>
                    <td style="text-align:left;width:2.124cm; " class="Таблица1_A1"><p class="P34"> </p></td>
                </tr>
                <tr class="Таблица17">    <td colspan="2" style="text-align:left;width:0.924cm; " class="Таблица1_A1"><p class="P10"> </p></td>
                    <td style="text-align:left;width:0.212cm; " class="Таблица1_A1"><p class="P10"> </p></td>
                    <td style="text-align:left;width:4.884cm; " class="Таблица1_A1"><p class="P10"> </p></td>
                    <td colspan="5" style="text-align:left;width:2.148cm; " class="Таблица1_A1"><p class="P10"> </p></td>
                    <td colspan="2" style="text-align:left;width:0.131cm; " class="Таблица1_A1"><p class="P10"> </p></td>
                    <td colspan="7" style="text-align:left;width:0.873cm; " class="Таблица1_A1"><p class="P10"> </p></td>
                    <td colspan="2" style="text-align:left;width:0.423cm; " class="Таблица1_A1"><p class="P10"> </p></td>
                    <td colspan="5" style="text-align:left;width:1.295cm; " class="Таблица1_A1"><p class="P10"> </p></td>
                    <td colspan="6" style="text-align:left;width:0.459cm; " class="Таблица1_A1"><p class="P24"> </p></td>
                </tr>
                <tr class="Таблица131">   
                    <td rowspan="2" style="text-align:left;width:0.924cm; " class="Таблица1_e3"><p class="P29"> Но-<br/> мер<br/> по<br/> по-<br/> ряд-<br/> ку</p></td>
                    <td colspan="5" style="text-align:left;width:1.513cm; " class="Таблица1_e3"><p class="P27">Товар</p></td>
                    <td colspan="2" style="text-align:left;width:0.93cm; " class="Таблица1_e3"><p class="P36"><span class="T2">Ед. изм.</span></p></td>
                    <td colspan="2" rowspan="2" style="text-align:left;width:0.665cm; " class="Таблица1_e3"><p class="P36"><span class="T2">Вид<br/>упа-<br/>ков-<br/>ки</span></p></td>
                    <td colspan="3" style="text-align:left;width:0.162cm; " class="Таблица1_e3"><p class="P27">Количество</p></td>
                    <td rowspan="2" style="text-align:left;width:0.903cm; " class="Таблица1_e3"><p class="P36"><span class="T2">Мас-<br/>са<br/>брут-<br/>то</span></p></td>
                    <td rowspan="2" colspan="2" style="text-align:left;width:0.24cm; " class="Таблица1_e3"><p class="P36"><span class="T2">Коли-<br/>чество<br/>(масса<br/>нетто)</span></p></td>
                    <td rowspan="2" colspan="3" style="text-align:left;width:1.619cm; " class="Таблица1_e3"><p class="P36"><span class="T2">Цена руб.<br/>коп.</span></p></td>
                    <td rowspan="2" colspan="4" style="text-align:left;width:0.22cm; " class="Таблица1_e3"><p class="P36"><span class="T2">Сумма без<br/>учета НДС<br/>руб. коп.</span></p></td>
                    <td colspan="6" style="text-align:left;width:1.171cm; " class="Таблица1_e3"><p class="P27">НДС</p></td>
                    <td rowspan="2" colspan="2" style="text-align:left;width:0.132cm; " class="Таблица1_e3"><p class="P36"><span class="T2">Сумма с<br/>учетом НДС,<br/>руб. коп.</span></p></td>
                </tr>
                <tr class="Таблица132">   
                    <td colspan="4" style="text-align:left;width:1.513cm; " class="Таблица1_e3"><p class="P36"><span class="T2">наименование, характеристика,<br/>сорт, артикул товара</span></p></td>
                    <td style="text-align:left;width:0.716cm; " class="Таблица1_e3"><p class="P27">код</p></td>
                    <td style="text-align:left;width:0.93cm; " class="Таблица1_e3"><p class="P36"><span class="T2">наиме-<br/>нова-<br/>ние</span></p></td>
                    <td style="text-align:left;width:0.877cm; " class="Таблица1_e3"><p class="P36"><span class="T2">код<br/>по<br/>ОКЕИ</span></p></td>
                    <td colspan="2" style="text-align:left;width:0.162cm; " class="Таблица1_e3"><p class="P36"><span class="T2">в<br/>одном<br/>месте</span></p></td>
                    <td style="text-align:left;width:0.903cm; " class="Таблица1_e3"><p class="P36"><span class="T2">мест,<br/>штук</span></p></td>
                    <td colspan="2" style="text-align:left;width:1.171cm; " class="Таблица1_e3"><p class="P36"><span class="T2">став-<br/>ка, %</span></p></td>
                    <td colspan="4" style="text-align:left;width:0.459cm; " class="Таблица1_e3"><p class="P36"><span class="T2">сумма руб.<br/>коп.</span></p></td>
                </tr>
                <tr class="Таблица126">   <td style="text-align:left;width:0.924cm; " class="Таблица1_e3"><p class="P46">1</p></td>
                    <td colspan="4" style="text-align:left;width:1.513cm; " class="Таблица1_B33"><p class="P46">2</p></td>
                    <td style="text-align:left;width:0.716cm; " class="Таблица1_B33"><p class="P50">3</p></td>
                    <td style="text-align:left;width:0.93cm; " class="Таблица1_B33"><p class="P46">4</p></td>
                    <td style="text-align:left;width:0.877cm; " class="Таблица1_B33"><p class="P46">5</p></td>
                    <td colspan="2" style="text-align:left;width:0.665cm; " class="Таблица1_B33"><p class="P46">6</p></td>
                    <td colspan="2" style="text-align:left;width:0.162cm; " class="Таблица1_B33"><p class="P46">7</p></td>
                    <td style="text-align:left;width:0.903cm; " class="Таблица1_B33"><p class="P46">8</p></td>
                    <td style="text-align:left;width:0.903cm; " class="Таблица1_B33"><p class="P46">9</p></td>
                    <td colspan="2" style="text-align:left;width:0.24cm; " class="Таблица1_B33"><p class="P46">10</p></td>
                    <td colspan="3" style="text-align:left;width:1.619cm; " class="Таблица1_B33"><p class="P46">11</p></td>
                    <td colspan="4" style="text-align:left;width:0.22cm; " class="Таблица1_B33"><p class="P46">12</p></td>
                    <td colspan="2" style="text-align:left;width:1.171cm; " class="Таблица1_B33"><p class="P46">13</p></td>
                    <td colspan="4" style="text-align:left;width:0.459cm; " class="Таблица1_B33"><p class="P46">14</p></td>
                    <td colspan="2" style="text-align:left;width:0.132cm; " class="Таблица1_B33"><p class="P46">15</p></td>
                </tr>
<?php echo $dis ?>
                <tr class="Таблица131">   <td colspan="12" style="text-align:left;width:0.924cm; " class="Таблица1_A43"><p class="P28">Итого </p></td>
                    <td style="text-align:left;width:0.903cm; " class="Таблица1_M43"><p class="P31"> </p></td>
                    <td style="text-align:left;width:0.903cm; " class="Таблица1_M43"><p class="P31"> </p></td>
                    <td colspan="2" style="text-align:left;width:0.24cm; " class="Таблица1_M43"><p class="P28"> </p></td>
                    <td colspan="3" style="text-align:left;width:1.619cm; " class="Таблица1_M43"><p class="P27">X</p></td>
                    <td colspan="4" style="text-align:left;width:0.22cm; " class="Таблица1_M43"><p class="P28"><?php echo $total_summa_nds ?></p></td>
                    <td colspan="2" style="text-align:left;width:1.171cm; " class="Таблица1_M43"><p class="P27">X</p></td>
                    <td colspan="4" style="text-align:left;width:0.459cm; " class="Таблица1_M43"><p class="P28"><?php echo $total_summa_nds_taxe ?></p></td>
                    <td colspan="2" style="text-align:left;width:0.132cm; " class="Таблица1_M43"><p class="P28"><?php echo $total_summa ?></p></td>
                </tr>
                <tr class="Таблица131">   <td colspan="12" style="text-align:left;width:0.924cm; "><p class="P28">Всего по накладной </p></td>
                    <td style="text-align:left;width:0.903cm; " class="Таблица1_M43"><p class="P31"> </p></td>
                    <td style="text-align:left;width:0.903cm; " class="Таблица1_M43"><p class="P31"> </p></td>
                    <td colspan="2" style="text-align:left;width:0.24cm; " class="Таблица1_M43"><p class="P28"> </p></td>
                    <td colspan="3" style="text-align:left;width:1.619cm; " class="Таблица1_M43"><p class="P27">X</p></td>
                    <td colspan="4" style="text-align:left;width:0.22cm; " class="Таблица1_M43"><p class="P28"><?php echo $total_summa_nds ?></p></td>
                    <td colspan="2" style="text-align:left;width:1.171cm; " class="Таблица1_M43"><p class="P27">X</p></td>
                    <td colspan="4" style="text-align:left;width:0.459cm; " class="Таблица1_M43"><p class="P28"><?php echo $total_summa_nds_taxe ?></p></td>
                    <td colspan="2" style="text-align:left;width:0.132cm; " class="Таблица1_M43"><p class="P28"><?php echo $total_summa ?></p></td>
                </tr>
            </table>
            <table border="0" cellspacing="0" cellpadding="0" class="Таблица2">
                <colgroup>
                    <col width="4"/>
                    <col width="15"/>
                    <col width="20"/>
                    <col width="2"/>
                    <col width="35"/>
                    <col width="30"/>
                    <col width="4"/>
                    <col width="52"/>
                    <col width="4"/>
                    <col width="125"/>
                    <col width="8"/>
                    <col width="48"/>
                    <col width="4"/>
                    <col width="43"/>
                    <col width="8"/>
                    <col width="25"/>
                    <col width="31"/>
                    <col width="29"/>
                    <col width="11"/>
                    <col width="4"/>
                    <col width="28"/>
                    <col width="7"/>
                    <col width="35"/>
                    <col width="11"/>
                    <col width="4"/>
                    <col width="32"/>
                    <col width="4"/>
                    <col width="13"/>
                    <col width="6"/>
                    <col width="9"/>
                    <col width="3"/>
                    <col width="6"/>
                    <col width="10"/>
                    <col width="29"/>
                    <col width="14"/>
                    <col width="6"/>
                    <col width="50"/>
                    <col width="9"/>
                    <col width="7"/>
                    <col width="56"/>
                    <col width="27"/>
                    <col width="78"/>
                    <col width="4"/>
                    <col width="18"/>
                    <col width="2"/>
                    <col width="4"/>
                    <col width="7"/>
                    <col width="12"/>
                    <col width="14"/>
                    <col width="7"/>
                    <col width="4"/>
                    <col width="12"/>
                    <col width="99"/>
                    <col width="3"/>
                    <col width="35"/>
                    <col width="15"/>
                    <col width="15"/>
                    <col width="30"/>
                </colgroup>
                <tr class="Таблица29">    <td colspan="58" style="text-align:left;width:0.088cm; " class="Таблица2_A7"><p class="P61">Товарная накладная имеет приложение на ______________________  листах и содержит  ________________________________ порядковых номеров записей</p></td>
                </tr>
                <tr class="Таблица210">   <td colspan="58" style="text-align:left;width:0.088cm; " class="Таблица2_A8"><p class="P59"> </p></td>
                </tr>
                <tr class="Таблица29">    <td colspan="20" style="text-align:left;width:0.088cm; " class="Таблица2_A8"><p class="P59"> </p></td>
                    <td colspan="9" style="text-align:left;width:0.639cm; " class="Таблица2_A7"><p class="P69"><span class="T2">Масса груза (нетто)</span></p></td>
                    <td colspan="2" style="text-align:left;width:0.213cm; " class="Таблица2_A8"><p class="P59"> </p></td>
                    <td colspan="17" style="text-align:left;width:0.132cm; " class="Таблица2_f11"><p class="P64"> </p></td>
                    <td colspan="10" style="text-align:left;width:0.319cm; " class="Таблица2_A8"><p class="P59"> </p></td>
                </tr>
                <tr class="Таблица212">   <td colspan="20" style="text-align:left;width:0.088cm; " class="Таблица2_A8"><p class="P14"> </p></td>
                    <td colspan="9" style="text-align:left;width:0.639cm; " class="Таблица2_A8"><p class="P59"> </p></td>
                    <td colspan="2" style="text-align:left;width:0.213cm; " class="Таблица2_A8"><p class="P14"> </p></td>
                    <td colspan="17" style="text-align:left;width:0.132cm; " class="Таблица2_A7"><p class="P67">прописью</p></td>
                    <td colspan="10" style="text-align:left;width:0.319cm; " class="Таблица2_A8"><p class="P15"> </p></td>
                </tr>
                <tr class="Таблица29">
                    <td colspan="6" style="text-align:left;width:0.452cm; " class="Таблица2_A7"><p class="P69"><span class="T2">Всего мест</span></p></td>
                    <td style="text-align:left;width:0.088cm; " class="Таблица2_A8"><p class="P59"> </p></td>
                    <td colspan="11" style="text-align:left;width:1.194cm; " class="Таблица2_f11"><p class="P64"> </p></td>
                    <td colspan="2" style="text-align:left;width:0.263cm; " class="Таблица2_A8"><p class="P59"> </p></td>
                    <td colspan="10" style="text-align:left;width:0.639cm; " class="Таблица2_A7"><p class="P69"><span class="T2">Масса груза (брутто)</span></p></td>
                    <td style="text-align:left;width:0.079cm; " class="Таблица2_A8"><p class="P59"> </p></td>
                    <td colspan="17" style="text-align:left;width:0.132cm; " class="Таблица2_f11"><p class="P64"> </p></td>
                    <td colspan="10" style="text-align:left;width:0.319cm; " class="Таблица2_A8"><p class="P59"> </p></td>
                </tr>
                <tr class="Таблица212">   <td colspan="2" style="text-align:left;width:0.088cm; " class="Таблица2_A8"><p class="P14"> </p></td>
                    <td colspan="4" style="text-align:left;width:0.452cm; " class="Таблица2_A8"><p class="P59"> </p></td>
                    <td style="text-align:left;width:0.088cm; " class="Таблица2_A8"><p class="P14"> </p></td>
                    <td colspan="11" style="text-align:left;width:1.194cm; " class="Таблица2_A7"><p class="P67">прописью</p></td>
                    <td colspan="2" style="text-align:left;width:0.263cm; " class="Таблица2_A8"><p class="P15"> </p></td>
                    <td colspan="10" style="text-align:left;width:0.639cm; " class="Таблица2_A8"><p class="P60"> </p></td>
                    <td style="text-align:left;width:0.079cm; " class="Таблица2_A8"><p class="P14"> </p></td>
                    <td colspan="17" style="text-align:left;width:0.132cm; " class="Таблица2_A7"><p class="P67">прописью</p></td>
                    <td colspan="10" style="text-align:left;width:0.319cm; " class="Таблица2_A8"><p class="P15"> </p></td>
                </tr>
                <tr class="Таблица217">   <td colspan="2" style="text-align:left;width:0.088cm; " class="Таблица2_A8"><p class="P7"> </p></td>
                    <td colspan="4" style="text-align:left;width:0.452cm; " class="Таблица2_A8"><p class="P7"> </p></td>
                    <td style="text-align:left;width:0.088cm; " class="Таблица2_A8"><p class="P7"> </p></td>
                    <td colspan="11" style="text-align:left;width:1.194cm; " class="Таблица2_A8"><p class="P7"> </p></td>
                    <td colspan="2" style="text-align:left;width:0.263cm; " class="Таблица2_A8"><p class="P7"> </p></td>
                    <td colspan="6" style="text-align:left;width:0.639cm; " class="Таблица2_A8"><p class="P60"> </p></td>
                    <td style="text-align:left;width:0.088cm; " class="Таблица2_a17"><p class="P68"> </p></td>
                    <td colspan="3" style="text-align:left;width:0.293cm; " class="Таблица2_A8"><p class="P60"> </p></td>
                    <td style="text-align:left;width:0.079cm; " class="Таблица2_A8"><p class="P7"> </p></td>
                    <td colspan="17" style="text-align:left;width:0.132cm; " class="Таблица2_A8"><p class="P7"> </p></td>
                    <td colspan="10" style="text-align:left;width:0.319cm; " class="Таблица2_A8"><p class="P7"> </p></td>
                </tr>
                <tr class="Таблица218">
                    <td colspan="12" style="text-align:left;width:0.452cm; " class="Таблица2_A7"><p class="P69"><span class="T2">Приложение (паспорта, сертификаты, и т. п.) на</span></p></td>
                    <td style="text-align:left;width:0.088cm; " class="Таблица2_A8"><p class="P59"> </p></td>
                    <td colspan="8" style="text-align:left;width:0.982cm; " class="Таблица2_f11"><p class="P64"> </p></td>
                    <td colspan="4" style="text-align:left;width:0.157cm; " class="Таблица2_A7"><p class="P62">листах</p></td>
                    <td style="text-align:left;width:0.743cm; " class="Таблица2_A8"><p class="P59"> </p></td>

                    <td style="text-align:left;width:0.088cm; " class="Таблица2_a17"><p class="P20"> </p></td>
                    <td colspan="13" style="text-align:right;width:0.132cm; " class="Таблица2_A7"><p class="P69"><span class="T2">По доверенности №</span></p></td>
                    <td colspan="8" style="text-align:left;width:1.274cm; " class="Таблица2_f11"><p class="P65"> </p></td>
                    <td colspan="2" style="text-align:left;width:0.423cm; " class="Таблица2_A7"><p class="P63">от</p></td>
                    <td style="text-align:left;width:0.159cm; " class="Таблица2_A7"><p class="P63">"</p></td>
                    <td style="text-align:left;width:0.159cm; " class="Таблица2_A7"><p class="P63">"</p></td>
                    <td colspan="5" style="text-align:left;width:0.266cm; " class="Таблица2_f11"><p class="P65"> </p></td>
                    <td style="text-align:left;width:0.344cm; " class="Таблица2_A7"><p class="P62">г.</p></td>
                </tr>
                <tr class="Таблица218">   <td colspan="2" style="text-align:left;width:0.088cm; " class="Таблица2_A8"><p class="P7"> </p></td>
                    <td colspan="10" style="text-align:left;width:0.452cm; " class="Таблица2_A8"><p class="P59"> </p></td>
                    <td style="text-align:left;width:0.088cm; " class="Таблица2_A8"><p class="P7"> </p></td>
                    <td colspan="8" style="text-align:left;width:0.982cm; " class="Таблица2_A7"><p class="P67">прописью</p></td>
                    <td colspan="4" style="text-align:left;width:0.157cm; " class="Таблица2_A8"><p class="P8"> </p></td>
                    <td style="text-align:left;width:0.743cm; " class="Таблица2_A8"><p class="P8"> </p></td>
                    <td style="text-align:left;width:0.088cm; " class="Таблица2_a17"><p class="P8"> </p></td>
                    <td style="text-align:left;width:0.293cm; " class="Таблица2_A8"><p class="P8"> </p></td>
                    <td colspan="10" style="text-align:left;width:0.132cm; " class="Таблица2_A8"><p class="P8"> </p></td>
                    <td style="text-align:left;width:0.161cm; " class="Таблица2_A8"><p class="P8"> </p></td>
                    <td colspan="3" style="text-align:left;width:1.274cm; " class="Таблица2_A8"><p class="P8"> </p></td>
                    <td style="text-align:left;width:0.088cm; " class="Таблица2_A8"><p class="P8"> </p></td>
                    <td colspan="2" style="text-align:left;width:0.423cm; " class="Таблица2_A8"><p class="P8"> </p></td>
                    <td style="text-align:left;width:0.088cm; " class="Таблица2_A8"><p class="P8"> </p></td>
                    <td style="text-align:left;width:0.159cm; " class="Таблица2_A8"><p class="P8"> </p></td>
                    <td colspan="2" style="text-align:left;width:0.265cm; " class="Таблица2_A8"><p class="P8"> </p></td>
                    <td style="text-align:left;width:0.159cm; " class="Таблица2_A8"><p class="P8"> </p></td>
                    <td style="text-align:left;width:0.088cm; " class="Таблица2_A8"><p class="P8"> </p></td>
                    <td colspan="3" style="text-align:left;width:0.266cm; " class="Таблица2_A8"><p class="P8"> </p></td>
                    <td style="text-align:left;width:0.797cm; " class="Таблица2_A8"><p class="P8"> </p></td>
                    <td style="text-align:left;width:0.344cm; " class="Таблица2_A8"><p class="P8"> </p></td>
                    <td colspan="2" style="text-align:left;width:0.346cm; " class="Таблица2_A8"><p class="P8"> </p></td>
                </tr>
                <tr class="Таблица222"> 
                    <td colspan="12" style="text-align:left;width:0.452cm; " class="Таблица2_A8"><p class="P11"> </p></td>
                    <td style="text-align:left;width:0.088cm; " class="Таблица2_A8"><p class="P11"> </p></td>
                    <td colspan="8" style="text-align:left;width:0.982cm; " class="Таблица2_A8"><p class="P11"> </p></td>
                    <td colspan="4" style="text-align:left;width:0.157cm; " class="Таблица2_A8"><p class="P11"> </p></td>
                    <td style="text-align:left;width:0.743cm; " class="Таблица2_A8"><p class="P11"> </p></td>
                    <td style="text-align:left;width:0.088cm; " class="Таблица2_a17"><p class="P11"> </p></td>
                    <td style="text-align:left;width:0.293cm; " class="Таблица2_A8"><p class="P11"> </p></td>
                    <td colspan="10" style="text-align:left;width:0.132cm; " class="Таблица2_A8"><p class="P60"> </p></td>
                    <td style="text-align:left;width:0.161cm; " class="Таблица2_A8"><p class="P11"> </p></td>
                    <td colspan="3" style="text-align:left;width:1.274cm; " class="Таблица2_A8"><p class="P60"> </p></td>
                    <td style="text-align:left;width:0.088cm; " class="Таблица2_A8"><p class="P11"> </p></td>
                    <td colspan="2" style="text-align:left;width:0.423cm; " class="Таблица2_A8"><p class="P60"> </p></td>
                    <td style="text-align:left;width:0.088cm; " class="Таблица2_A8"><p class="P11"> </p></td>
                    <td colspan="4" style="text-align:left;width:0.159cm; " class="Таблица2_A8"><p class="P60"> </p></td>
                    <td style="text-align:left;width:0.088cm; " class="Таблица2_A8"><p class="P11"> </p></td>
                    <td colspan="5" style="text-align:left;width:0.266cm; " class="Таблица2_A8"><p class="P60"> </p></td>
                    <td colspan="2" style="text-align:left;width:0.346cm; " class="Таблица2_A8"><p class="P11"> </p></td>
                </tr>
                <tr class="Таблица29">    <td colspan="2" style="text-align:left;width:0.088cm; " class="Таблица2_A8"><p class="P21"> </p></td>
                    <td colspan="23" style="text-align:left;width:0.452cm; " class="Таблица2_A8"><p class="P70">Всего отпущено на сумму:</p</td>
                    <td style="text-align:left;width:0.743cm; " class="Таблица2_A8"><p class="P21"> </p></td>
                    <td style="text-align:left;width:0.088cm; " class="Таблица2_a17"><p class="P21"> </p></td>
                    <td colspan="8" style="text-align:left;width:0.132cm; " class="Таблица2_A7"><p class="P61">Выданной</p></td>
                    <td style="text-align:left;width:0.134cm; " class="Таблица2_A8"><p class="P59"> </p></td>
                    <td colspan="22" style="text-align:left;width:1.141cm; " class="Таблица2_f11"><p class="P64"> </p></td>
                </tr>
                <tr class="Таблица218">   <td colspan="2" style="text-align:left;width:0.088cm; " class="Таблица2_A8"><p class="P7"> </p></td>
                    <td colspan="23" style="text-align:left;width:0.452cm; " class="Таблица2_A8"><p class="P7"> </p></td>
                    <td style="text-align:left;width:0.743cm; " class="Таблица2_A8"><p class="P7"> </p></td>
                    <td style="text-align:left;width:0.088cm; " class="Таблица2_a17"><p class="P7"> </p></td>
                    <td style="text-align:left;width:0.293cm; " class="Таблица2_A8"><p class="P7"> </p></td>
                    <td colspan="7" style="text-align:left;width:0.132cm; " class="Таблица2_A8"><p class="P59"> </p></td>
                    <td style="text-align:left;width:0.134cm; " class="Таблица2_A8"><p class="P7"> </p></td>
                    <td colspan="20" style="text-align:left;width:1.141cm; " class="Таблица2_A7"><p class="P67">кем, кому (организация, должность, фамилия, и. о.)</p></td>
                    <td colspan="2" style="text-align:left;width:0.346cm; " class="Таблица2_A8"><p class="P8"> </p></td>
                </tr>
                <tr class="Таблица230">
                    <td colspan="8" style="text-align:left;width:0.452cm; " class="Таблица2_A7"><p class="P62">Отпуск разрешил</p></td>
                    <td style="text-align:left;width:0.088cm; " class="Таблица2_A8"><p class="P59"> </p></td>
                    <td style="text-align:left;width:2.866cm; " class="Таблица2_N19"><p class="P10"> </p></td>
                    <td style="text-align:left;width:0.187cm; " class="Таблица2_A8"><p class="P59"> </p></td>
                    <td colspan="3" style="text-align:left;width:1.088cm; " class="Таблица2_f11"><p class="P64"> </p></td>
                    <td style="text-align:left;width:0.185cm; " class="Таблица2_A8"><p class="P59"> </p></td>
                    <td colspan="10" style="text-align:left;width:0.582cm; " class="Таблица2_A7"><p class="P64"> </p></td>
                    <td style="text-align:left;width:0.743cm; " class="Таблица2_A8"><p class="P10"> </p></td>
                    <td style="text-align:left;width:0.088cm; " class="Таблица2_a17"><p class="P10"> </p></td>
                    <td colspan="9" style="text-align:left;width:0.132cm; " class="Таблица2_A7"><p class="P61">Груз принял</p></td>
                    <td colspan="22" style="text-align:left;width:1.141cm; " class="Таблица2_f11"><p class="P61"> </p></td>
                </tr>
                <tr class="Таблица218">   <td colspan="2" style="text-align:left;width:0.088cm; " class="Таблица2_A8"><p class="P7"> </p></td>
                    <td colspan="6" style="text-align:left;width:0.452cm; " class="Таблица2_A8"><p class="P7"> </p></td>
                    <td style="text-align:left;width:0.088cm; " class="Таблица2_A8"><p class="P7"> </p></td>
                    <td style="text-align:left;width:2.866cm; " class="Таблица2_A7"><p class="P67">должность</p></td>
                    <td style="text-align:left;width:0.187cm; " class="Таблица2_A8"><p class="P7"> </p></td>
                    <td colspan="3" style="text-align:left;width:1.088cm; " class="Таблица2_A7"><p class="P67">подпись</p></td>
                    <td style="text-align:left;width:0.185cm; " class="Таблица2_A8"><p class="P7"> </p></td>
                    <td colspan="10" style="text-align:left;width:0.582cm; " class="Таблица2_A6"><p class="P67">расшифровка подписи</p></td>
                    <td style="text-align:left;width:0.743cm; " class="Таблица2_A8"><p class="P7"> </p></td>
                    <td style="text-align:left;width:0.088cm; " class="Таблица2_a17"><p class="P7"> </p></td>
                    <td style="text-align:left;width:0.293cm; " class="Таблица2_A8"><p class="P7"> </p></td>
                    <td colspan="7" style="text-align:left;width:0.132cm; " class="Таблица2_A8"><p class="P10"> </p></td>
                    <td style="text-align:left;width:0.134cm; " class="Таблица2_A8"><p class="P10"> </p></td>
                    <td colspan="20" style="text-align:left;width:1.141cm; " class="Таблица2_A8"><p class="P10"> </p></td>
                    <td colspan="2" style="text-align:left;width:0.346cm; " class="Таблица2_A8"><p class="P10"> </p></td>
                </tr>
                <tr class="Таблица29">
                    <td style="text-align:left;width:0.452cm; " class="Таблица2_A8"><p class="P60"> </p></td>
                    <td colspan="9" style="text-align:left;width:0.053cm; " class="Таблица2_A7"><p class="P71">Главный (страший) бухгалтер</p></td>
                    <td style="text-align:left;width:0.187cm; " class="Таблица2_A8"><p class="P21"> </p></td>

                    <td colspan="3" style="text-align:left;width:1.088cm; " class="Таблица2_f11">
                        <?php
                        if (!empty($LoadBanc['org_sig']))
                            echo '<img src="' . $LoadBanc['org_sig_buh'] . '">';
                        else
                            echo '<p class="P72"> </p>';
                        ?>

                    </td>
                    <td style="text-align:left;width:0.185cm; " class="Таблица2_A8"><p class="P21"> </p></td>
                    <td colspan="10" style="text-align:left;width:0.582cm; " class="Таблица2_A7"><p class="P72"> </p></td>
                    <td style="text-align:left;width:0.743cm; " class="Таблица2_A8"><p class="P21"> </p></td>
                    <td style="text-align:left;width:0.088cm; " class="Таблица2_a17"><p class="P21"> </p></td>
                    <td style="text-align:left;width:0.293cm; " class="Таблица2_A8"><p class="P21"> </p></td>
                    <td colspan="28" style="text-align:left;width:0.132cm; " class="Таблица2_A8"><p class="P21"> </p></td>
                    <td colspan="2" style="text-align:left;width:0.346cm; " class="Таблица2_A8"><p class="P21"> </p></td>
                </tr>
                <tr class="Таблица236">   <td colspan="2" style="text-align:left;width:0.088cm; " class="Таблица2_A8"><p class="P3"> </p></td>
                    <td style="text-align:left;width:0.452cm; " class="Таблица2_A8"><p class="P3"> </p></td>
                    <td colspan="6" style="text-align:left;width:0.053cm; " class="Таблица2_A8"><p class="P73"> </p></td>
                    <td style="text-align:left;width:2.866cm; " class="Таблица2_A6"><p class="P67">должность</p></td>
                    <td style="text-align:left;width:0.187cm; " class="Таблица2_A8"><p class="P4"> </p></td>
                    <td colspan="3" style="text-align:left;width:1.088cm; " class="Таблица2_A7"><p class="P67">подпись</p></td>
                    <td style="text-align:left;width:0.185cm; " class="Таблица2_A8"><p class="P4"> </p></td>
                    <td colspan="10" style="text-align:left;width:0.582cm; " class="Таблица2_A6"><p class="P67">расшифровка подписи</p></td>
                    <td style="text-align:left;width:0.743cm; " class="Таблица2_A8"><p class="P4"> </p></td>
                    <td style="text-align:left;width:0.088cm; " class="Таблица2_a17"><p class="P4"> </p></td>
                    <td style="text-align:left;width:0.293cm; " class="Таблица2_A8"><p class="P4"> </p></td>
                    <td colspan="28" style="text-align:left;width:0.132cm; " class="Таблица2_A8"><p class="P4"> </p></td>
                    <td colspan="2" style="text-align:left;width:0.346cm; " class="Таблица2_A8"><p class="P4"> </p></td>
                </tr>

                <tr class="Таблица239">
                    <td colspan="9" style="text-align:left;width:0.346cm; " class="Таблица2_A7"><p class="P62">Отпуск груза произвел</p></td>
                    <td style="text-align:left;width:2.866cm; " class="Таблица2_f11"><p class="P64"> </p></td>
                    <td style="text-align:left;width:0.187cm; " class="Таблица2_A8"><p class="P7"> </p></td>
                    <td colspan="3" style="text-align:left;width:1.088cm; " class="Таблица2_f11">
<?php
if (!empty($LoadBanc['org_sig']))
    echo '<img src="' . $LoadBanc['org_sig'] . '">';
else
    echo '<p class="P64"> </p>';
?>

                    </td>
                    <td style="text-align:left;width:0.185cm; " class="Таблица2_A8"><p class="P7"> </p></td>
                    <td colspan="10" style="text-align:left;width:0.582cm; " class="Таблица2_A7"><p class="P64"> </p></td>
                    <td style="text-align:left;width:0.743cm; " class="Таблица2_A8"><p class="P7"> </p></td>
                    <td style="text-align:left;width:0.088cm; " class="Таблица2_a17"><p class="P7"> </p></td>
                    <td colspan="19" style="text-align:left;width:0.132cm; " class="Таблица2_A7"><p class="P61">Груз получил грузополучатель</p></td>
                    <td colspan="13" style="text-align:left;width:0.61cm; " class="Таблица2_f11"><p class="P61"> </p></td>
                </tr>
                <tr class="Таблица219">   <td style="text-align:left;width:0.088cm; " class="Таблица2_A8"><p class="P20"> </p></td>
                    <td colspan="7" style="text-align:left;width:0.346cm; " class="Таблица2_A8"><p class="P20"> </p></td>
                    <td style="text-align:left;width:0.088cm; " class="Таблица2_A8"><p class="P20"> </p></td>
                    <td style="text-align:left;width:2.866cm; " class="Таблица2_A7"><p class="P67">должность</p></td>
                    <td style="text-align:left;width:0.187cm; " class="Таблица2_A8"><p class="P20"> </p></td>
                    <td colspan="3" style="text-align:left;width:1.088cm; " class="Таблица2_A7"><p class="P67">подпись</p></td>
                    <td style="text-align:left;width:0.185cm; " class="Таблица2_A8"><p class="P20"> </p></td>
                    <td colspan="10" style="text-align:left;width:0.582cm; " class="Таблица2_A6"><p class="P67">расшифровка подписи</p></td>
                    <td style="text-align:left;width:0.743cm; " class="Таблица2_A8"><p class="P20"> </p></td>
                    <td style="text-align:left;width:0.088cm; " class="Таблица2_a17"><p class="P20"> </p></td>
                    <td style="text-align:left;width:0.293cm; " class="Таблица2_A8"><p class="P20"> </p></td>
                    <td colspan="28" style="text-align:left;width:0.132cm; " class="Таблица2_A8"><p class="P59"> </p></td>
                    <td colspan="2" style="text-align:left;width:0.346cm; " class="Таблица2_A8"><p class="P20"> </p></td>
                </tr>
                <tr class="Таблица229">   <td style="text-align:left;width:0.088cm; " class="Таблица2_A8"><p class="P22"> </p></td>
                    <td colspan="7" style="text-align:left;width:0.346cm; " class="Таблица2_A8"><p class="P22"> </p></td>
                    <td style="text-align:left;width:0.088cm; " class="Таблица2_A8"><p class="P22"> </p></td>
                    <td style="text-align:left;width:2.866cm; " class="Таблица2_A8"><p class="P60"> </p></td>
                    <td style="text-align:left;width:0.187cm; " class="Таблица2_A8"><p class="P22"> </p></td>
                    <td colspan="3" style="text-align:left;width:1.088cm; " class="Таблица2_A8"><p class="P60"> </p></td>
                    <td style="text-align:left;width:0.185cm; " class="Таблица2_A8"><p class="P22"> </p></td>
                    <td colspan="10" style="text-align:left;width:0.582cm; " class="Таблица2_A8"><p class="P60"> </p></td>
                    <td style="text-align:left;width:0.743cm; " class="Таблица2_A8"><p class="P22"> </p></td>
                    <td style="text-align:left;width:0.088cm; " class="Таблица2_a17"><p class="P22"> </p></td>
                    <td style="text-align:left;width:0.293cm; " class="Таблица2_A8"><p class="P22"> </p></td>
                    <td colspan="28" style="text-align:left;width:0.132cm; " class="Таблица2_A8"><p class="P22"> </p></td>
                    <td colspan="2" style="text-align:left;width:0.346cm; " class="Таблица2_A8"><p class="P22"> </p></td>
                </tr>
                <tr class="Таблица230">   <td style="text-align:left;width:0.088cm; " class="Таблица2_A8"><p class="P10"> </p></td>
                    <td colspan="4" style="text-align:left;width:0.346cm; " class="Таблица2_A8"><p class="P59"> </p></td>
                    <td colspan="19" style="text-align:left;width:0.691cm; " class="Таблица2_A7"><p class="P61">
<?php
if (!empty($LoadBanc['org_stamp']))
    echo '<img src="' . $LoadBanc['org_stamp'] . '" align="left">';
else
    echo " М. П.   ";
?></p>
                        <p class="P61" style="padding-top:70px;padding-left:200px"> <?php echo PHPShopDate::dataV(false, false, false, ' ', true); ?> года</p></td>
                    <td style="text-align:left;width:0.088cm; " class="Таблица2_A8"><p class="P59"> </p></td>
                    <td style="text-align:left;width:0.743cm; " class="Таблица2_A8"><p class="P10"> </p></td>
                    <td style="text-align:left;width:0.088cm; " class="Таблица2_a17"><p class="P10"> </p></td>
                    <td style="text-align:left;width:0.293cm; " class="Таблица2_A8"><p class="P10"> </p></td>
                    <td colspan="30" style="text-align:left;width:0.663cm; " class="Таблица2_A7"><p class="P61">М. П.                         "          " _______________ 20____ года</p></td>
                </tr>
            </table>
        </div>
    </body>
</html>