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
$result = mysqli_query($link_db, $sql);
$row = mysqli_fetch_array($result);
$id = $row['id'];
$datas = $row['datas'];
$ouid = $row['uid'];
$order = unserialize($row['orders']);
$status = unserialize($row['status']);

if ($LoadItems['System']['nds_enabled']) {
    $nds = $LoadItems['System']['nds'];
}


$dis = $weight = $adr_info = null;
$sum = $num = $this_nds_summa = $total_summa_nds = $total_summa_nds_taxe = $total_summa = 0;
if (is_array($order['Cart']['cart']))
    foreach ($order['Cart']['cart'] as $val) {

        // Услуга
        if ($val['type'] == 2)
            continue;

        $this_price = number_format(($PHPShopOrder->returnSumma(number_format($val['price'], "2", ".", ""), (int) $order['Person']['discount'])), "2", ".", "");
        $this_nds = number_format($this_price * $nds / (100 + $nds), "2", ".", "");
        $this_price_bez_nds = number_format(($this_price - $this_nds) * $val['num'], "2", ".", "");
        $this_price_c_nds = number_format($this_price * $val['num'], "2", ".", "");
        $this_nds_summa += $this_nds * $val['num'];

        $dis .= '<tr class="tab2 tr-begin">
                  <td class="border_right"></td>
                  <td>' . $n . '</td>
                  <td>' . $val['name'] . '</td>
                  <td>--</td>
                  <td width="1%">--</td>
                  <td width="5%">--</td>
                  <td width="5%">--</td>
                  <td width="5%">--</td>
                  <td width="5%">' . $this_price_bez_nds . '</td>
                  <td width="5%">без акциза</td>
                  <td width="1%">'.$nds.'%</td>
                  <td width="5%">' . $this_nds . '</td>
                  <td width="5%">' . $this_price . '</td>
                  <td width="5%">--</td>
                  <td width="5%">--</td>
                  <td width="5%">--</td>
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
        <meta http-equiv="Content-Type" content="text/html; charset=windows-1251">
        <title>Унифицированный передаточный документ №<?php echo $ouid ?></title>
        <link rel="apple-touch-icon" href="../../apple-touch-icon.png">
        <link rel="icon" href="../../favicon.ico"> 
        <link href="style.css" type=text/css rel=stylesheet>
        <script src="../../../lib/templates/print/js/html2pdf.bundle.min.js"></script>
    </head>

    <body>
        <style>

            .view_container {
                font-family: arial, tahoma, verdana;
                /* print italic */
                font-size: 9.0pt;
            }

            .view_container .page-landscape-margin-narrow {
                width: 100%;
                margin: 1mm 0 0 0;
            }

            .view_container .page-content {
                width: 100%;
            }

            .view_container table {
                font-family: Tahoma, Geneva, sans-serif;
                border-collapse: collapse;
                font-size: 8.0pt;
                width: 100%;
                color: black;
            }

            .view_container .header_text {
                font-family: Tahoma, Geneva, sans-serif;
                font-weight: bold;
                font-size: 10.0pt;
                text-align: left;
                vertical-align: top;
            }

            .view_container .header_text_arial {
                font-family: arial, Tahoma, Geneva, sans-serif;
            }

            .view_container .tab {
                margin-top: 10px;
            }

            .view_container .tab tr td {
                border: 1px solid black;
            }

            .view_container .tab1 {
                color: black;
                font-size: 8.0pt;
            }

            .view_container .tab1 td {
                padding: 3px;
            }

            .view_container .tab2 {
                color: black;
                font-size: 8.0pt;
                line-height: 13px;
            }

            .view_container .tab2 td {
                padding: 3px;
                vertical-align: top;
                text-align: center;
            }

            .view_container .cell-podpis {
                vertical-align: top;
                font-size: 7.0pt;
                text-align: center;
                color: black;
            }

            .view_container .sum-words {
                font-size: 9.0pt;
                text-align: right;
                font-weight: bold;
                margin-top: 10px;
            }

            .view_container .fio {
                vertical-align: bottom;
                text-align: left;
                font-size: 8pt;
            }

            .view_container .text_m {
                width: 13%;
                color: black;
                text-align: right;
                font-family: Tahoma, Geneva, sans-serif;
                font-size: 7pt;
            }

            .view_container .cell-value {
                font-size: 8.0pt;
                border-bottom: 1px solid black;
                padding: 2px;
            }

            .view_container .cell-name {
                font-size: 8.0pt;
                white-space: nowrap;
                color: black;
                padding: 3px;
            }

            .view_container .border-cell-podpis {
                border-top: 1px solid black;
                font-size: 7.0pt;
                text-align: center;
                color: black;
                vertical-align: top;
            }

            .view_container .cell-primechanie {
                padding-left: 5px;
                word-wrap: break-word;
                word-break: break-word;
            }

            .view_container .break-word {
                word-break: break-word;
            }

            .view_container .border-cell-money {
                vertical-align: top;
                border-top: 1pt solid black;
                border-left: 1pt solid black;
                border-right: 1pt solid black;
                font-size: 8.0pt;
                white-space: nowrap;
                text-align: right;
            }

            /*Серые цвета при отображении*/
            @media SCREEN {
                .view_container .text_m {
                    color: #c0c0c0;
                }

                .view_container .cell-value {
                    border-bottom: 1px solid #dddddd;
                }

                .view_container .cell-name {
                    -color: #808080;
                }

                .view_container .cell-primechanie {
                    color: #808080;
                }

                .view_container .tab tr td {
                    -border: 1px solid #c0c0c0;
                }

                .view_container .tab2 {
                    -color: #999999;
                }

                .view_container .border-cell-podpis {
                    border-top: 1px solid #dddddd;
                    color: #808080;
                }

                .view_container .border-cell-money {
                    border-top: 1pt solid #c0c0c0;
                    border-left: 1pt solid #c0c0c0;
                    border-right: 1pt solid #c0c0c0;
                }

                .view_container .cell-podpis {
                    color: #808080;
                }
            }

            /*Показ изменений*/
            .view_container .change {
                position: relative;
                background-color: yellow;
            }

            .view_container .change em {
                display: none;
            }

            .view_container .change:hover em {
                display: block;
                position: absolute;
                z-index: 1;
                border: 1pt solid red;
                background-color: white;
                font-style: normal;
                font-weight: normal;
                padding: 2px 2px 2px 3px;
                bottom: -23px;
                left: -36px;
                white-space: nowrap;
                -webkit-box-shadow: 0 0 2px #000;
                /* красивости в виде тени */
                -moz-box-shadow: 0 0 2px #000;
                box-shadow: 0 0 2px #000;
            }

            .view_container .hover {
                display: block;
                position: absolute;
                z-index: 1;
                border: 1pt solid red;
                background-color: white;
                font-style: normal;
                font-weight: normal;
                padding: 2px 2px 2px 3px;
                top: 13px;
                left: -36px;
                white-space: nowrap;
                box-shadow: 0 0 2px #000;
            }

            /*Общее*/
            .view_container .explanation {
                font-size: 8pt;
            }

            .view_container .bold {
                font-weight: bold;
            }

            .view_container .nowrap {
                white-space: nowrap;
            }

            .view_container .text-right {
                text-align: right;
            }

            .view_container .text-center {
                text-align: center;
            }

            .view_container .text-left {
                text-align: left;
            }

            .view_container .vertical-align-top {
                vertical-align: top;
            }

            .view_container .vertical-align-middle {
                vertical-align: middle;
            }

            .view_container .vertical-align-bottom {
                vertical-align: bottom;
            }

            .view_container .italic {
                font-style: italic;
            }

            .view_container .tab1 {
                page-break-inside: avoid !important;
            }

            /*Доработка для печати документов по правилам бухгалтерии*/
            .view_container .table-begin .tr-end td span,
            .view_container .table-end .tr-begin td span,
            .view_container .table-end thead td span,
            .view_container .table-begin .tr-end td sup,
            .view_container .table-end .tr-begin td sup,
            .view_container .table-end thead td sup {
                display: block;
            }

            .view_container .table-begin .tr-end td,
            .view_container .table-end .tr-begin td,
            .view_container .table-end thead td {
                padding-top: 0 !important;
                padding-bottom: 0 !important;
                border-bottom: none !important;
                border-top: none !important;
                line-height: 0 !important;
                opacity: 0 !important;
                height: 0 !important;
                /* для ie */
                -ms-filter: "progid:DXImageTransform.Microsoft.Alpha(Opacity=0)";
                filter: alpha(opacity=0);
                /* правит косяк конвертера с границами при разрыве страницы */
                border-color: rgba(255, 255, 255, 0);
                border-color: #fff\9;
            }

            .view_container .table-end {
                margin-top: 0 !important;
            }

            @media PRINT {

                .page-break-after-avoid
                {
                    page-break-after: avoid;
                }

                .page-break-after-always
                {
                    page-break-after: always;
                }

                .page-break-before-avoid
                {
                    page-break-before: avoid;
                }

                .page-break-before-always
                {
                    page-break-before: always;
                }

                .page-break-before-auto
                {
                    page-break-before: auto;
                }

                .view_container .no-break-div {
                    page-break-inside: avoid;
                    position: relative;
                    display: run-in;
                }

                /* костыль для конвертера в pdf без .view_container т.к. договоры без этого класса */
                .pdffacsimile {
                    position: relative !important;
                }
            }
        </style>

        <style>
            .view_container .qrcode,
            .view_container .qrcode-invoice,
            .view_container .qrcode-vr,
            .view_container .qrcode-tn {
                height: 102px;
                width: 102px;
                display: inline-block;
                vertical-align: top;
            }

            .view_container .qrcode>img,
            .view_container .qrcode-invoice>img,
            .view_container .qrcode-vr>img,
            .view_container .qrcode-tn>img {
                width: 100%;
                height: 100%;
            }

            .view_container .withQR-invoice,
            .view_container .withQR {
                width: calc(100% - 106px)
            }

            .view_container .withoutQR {
                width: 100%;
            }

            @media screen {
                .view_container .buyer-block {
                    width: 100%;
                }
            }

            @media PRINT {
                .view_container .qrcode {
                    float: right;
                    padding-top: 15px;
                }

                .view_container .withQR {
                    vertical-align: top;
                    height: 100%;
                    width: calc(100% - 106px);
                    display: inline-block;
                }
            }
        </style>

        <style>
            .view_container .border-right {
                border-right: 1px solid black;
            }

            .view_container .border-cell {
                border: 1px solid black;
            }

            .noborder,
            .noborder td {
                border: none !important;
            }

            /* серые цвета при отображении */
            @media SCREEN {
                .view_container .border-cell {
                    border: 1px solid #c0c0c0;
                }

                .view_container .border-right {
                    border-right: 1px solid #c0c0c0;
                }

                .noborder .cell-value {
                    border-bottom: 1px solid #dddddd !important;
                }

                .border_bottom {
                    border-bottom: 2px solid #c0c0c0 !important;
                }

                .border_right {
                    border-right: 2px solid #000 !important;
                }

                .noborder .border-cell-podpis,
                .border-top {
                    border-top: 1px solid #dddddd !important;
                }

                .view_container .page-content {
                    width: 99%;
                }

                .page-landscape-margin-narrow {
                    margin-bottom: 50px;
                }
            }

            @media print {
                body {
                    margin: 0;
                    padding: 0;
                }

                .view_container .page-content td {
                    padding-top: 1px;
                    padding-bottom: 0px ! important;
                }

                .view_container .page-content table {
                    margin-top: 0px ! important;
                    margin-bottom: 0px ! important;
                }

                .view_container .tab1,
                .view_container .tab2,
                .view_container .cell-value,
                .view_container .cell-name,
                .view_container .border-cell-money {
                    font-size: 7pt !important;
                }

                .cell-name {
                    vertical-align: top;
                }

                .view_container .border-cell-podpis {
                    font-size: 6pt !important;
                }

                .noborder .cell-value {
                    border-bottom: 1px solid #000 !important;
                }

                .border_bottom {
                    border-bottom: 2px solid #000 !important;
                }

                .border_right {
                    border-right: 2px solid #000 !important;
                }

                .noborder .border-cell-podpis,
                .border-top {
                    border-top: 1px solid #000 !important;
                }

                .view_container table {
                    font-size: 7pt !important;
                }

                .view_container .page-content {
                    width: 99%;
                }
            }

            .view_container .table-begin .tr-end,
            .view_container .table-end .tr-begin {
                visibility: collapse;
            }

            .table-begin .tr-end *,
            .table-end .tr-begin * {
                margin-top: 0 !important;
                margin-bottom: 0 !important;
                padding-top: 0 !important;
                padding-bottom: 0 !important;
                border-top-width: 0 !important;
                border-bottom-width: 0 !important;
                height: 0 !important;
            }

            .remove_border {
                border: none !important;
            }

            .remove_borders_except_right {
                border-left: none !important;
                border-top: none !important;
                border-bottom: none !important;
            }

            .remove_top_bottom_borders {
                border-bottom: none !important;
                border-top: none !important;
            }

            .remove_border_top {
                border-top: none !important;
            }

            .remove_border_bottom {
                border-bottom: none !important;
            }

            .remove_border_right {
                border-right: none !important;
            }

            .remove_border_left {
                border-left: none !important;
            }

            .corr_wrap {
                word-break: break-word;
            }

            .min-width150 {
                min-width: 150px;
            }

            .width80 {
                min-width: 80px;
                max-width: 80px;
            }

            .width83 {
                width: 83px;
                min-width: 83px;
                max-width: 83px;
            }

            .width20 {
                width: 20px;
                min-width: 20px;
                max-width: 20px;
            }

            .vert_top {
                vertical-align: top;
            }



            .ws-is-desktop-safari .safari-remove-border-top {
                border-top: none !important;
            }

            .ws-is-mobile-safari .safari-remove-border-top {
                border-top: none !important;
            }
        </style>
        
        <div align="right" class="nonprint" style="min-width: fit-content; width: 100%;">
            <button onclick="html2pdf(document.getElementById('content'), {margin: 1, filename: 'ТОРГ-12 №<?php echo $ouid ?>.pdf', html2canvas: {dpi: 192, letterRendering: true}, jsPDF: {orientation: 'landscape'}});">Сохранить</button> 
            <button onclick="window.print();">Распечатать</button> 
            <br><br><hr><br><br>
        </div>

        <div id="content" style="min-width: fit-content; width: 100%;"
             class="view_container ws-is-chrome ws-is-windows-10 ws-is-desktop-platform ws-is-no-touch">
            <div sds-presentation-recordcount="15" sds-presentation-pagesize="20" sds-presentation-pagecurrent="0"
                 id="sds-presentation-paging"></div>
            <div class="page-landscape-margin-narrow">
                <div class="page-content">
                    <table>
                        <tbody>
                            <tr class="tr-begin">
                                <td class="noborder vertical-align-top border_right" width="85">
                                    Универсальный<br>
                                    передаточный<br>
                                    документ<br>
                                    <br>
                                    Статус:&nbsp;
                                    <span style="border: 2px solid #000;padding: 1px 8px 1px 10px;">
                                        1
                                    </span>
                                    <br>
                                    <p style="font-size:10px">
                                        1 – счет-фактура и
                                        передаточный документ
                                        (акт)<br>
                                        <span class="nowrap">2 – передаточный</span>
                                        документ (акт)
                                    </p>
                                </td>
                                <td class="noborder" colspan="15">
                                    <table>
                                        <tbody>
                                            <tr>
                                                <td>
                                                    <table>
                                                        <tbody>
                                                            <tr>
                                                                <td width="5%" style="font-size: 12px; padding-left: 4px"
                                                                    class="bold nowrap vertical-align-bottom">
                                                                    Счет-фактура № <?php echo $ouid ?> от <?php echo PHPShopDate::get($row['datas'], false, false, '.', false) ?></td>
                                                                <td width="95%"></td>
                                                            </tr>
                                                            <tr>
                                                                <td style="padding-left:4px">
                                                                    Исправление №
                                                                    -
                                                                    от
                                                                    -</td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </td>
                                                <td>
                                                    <table>
                                                        <tbody>
                                                            <tr>
                                                                <td style="white-space: nowrap;" class="text_m">
                                                                    Приложение N 1
                                                                    к постановлению Правительства Российской Федерации
                                                                    от 26.12.2011 №1137
                                                                    <br>
                                                                    (в ред. Постановления Правительства РФ от 02.04.2021 N
                                                                    534)
                                                                </td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                    <div style="display: inline-block;height:100%" class="withoutQR">
                                        <table>
                                            <tbody>
                                                <tr>
                                                    <td>
                                                        <table>
                                                            <tbody>
                                                                <tr>
                                                                    <td width="190mm" style="color:#000"
                                                                        class="cell-name bold">
                                                                        Продавец

                                                                        &nbsp;
                                                                    </td>
                                                                    <td class="cell-value bold"><?php echo $blank_org_name ?></td>
                                                                </tr>
                                                                <tr>
                                                                    <td class="cell-name">
                                                                        Адрес&nbsp;
                                                                    </td>
                                                                    <td class="cell-value"><?php echo $blank_org_ur_adres ?></td>
                                                                </tr>
                                                                <tr>
                                                                    <td class="cell-name">
                                                                        ИНН/КПП продавца&nbsp;
                                                                    </td>
                                                                    <td class="cell-value"><?php echo$blank_org_inn . '/' . $blank_org_kpp ?></td>
                                                                </tr>
                                                            </tbody>
                                                        </table>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <table>
                                                            <tbody>
                                                                <tr>
                                                                    <td width="190mm" class="cell-name">
                                                                        Грузоотправитель и его адрес:
                                                                    </td>
                                                                    <td class="cell-value"><?php echo $blank_org_ur_adres ?></td>
                                                                </tr>
                                                            </tbody>
                                                        </table>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <table>
                                                            <tbody>
                                                                <tr>
                                                                    <td width="190mm" class="cell-name">Грузополучатель и
                                                                        его адрес:&nbsp;
                                                                    </td>
                                                                    <td class="cell-value"><?php if (empty($orgData))
    echo $blank_person_user;
else
    echo $orgData;
?></td>
                                                                </tr>
                                                            </tbody>
                                                        </table>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="withoutQR buyer-block">
                                        <table>
                                            <tbody>
                                                <tr>
                                                    <td>
                                                        <table>
                                                            <tbody>
                                                                <tr>
                                                                    <td width="190mm" class="cell-name">
                                                                        К платежно-расчетному документу
                                                                    </td>
                                                                    <td class="cell-value">--</td>
                                                                </tr>
                                                            </tbody>
                                                        </table>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <table>
                                                            <tbody>
                                                                <tr>
                                                                    <td width="190mm" class="cell-name">Документ об
                                                                        отгрузке:&nbsp;</td>
                                                                    <td class="cell-value">Универсальный передаточный №<?php echo $ouid ?> от <?php echo PHPShopDate::get($row['datas'], false, false, '.', false) ?></td>
                                                                </tr>
                                                            </tbody>
                                                        </table>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <table>
                                                            <tbody>
                                                                <tr>
                                                                    <td width="190mm" style="color:#000"
                                                                        class="cell-name bold">
                                                                        Покупатель

                                                                        &nbsp;
                                                                    </td>
                                                                    <td class="cell-value bold"><?php echo $PHPShopOrder->getParam('org_name'); ?></td>
                                                                </tr>
                                                                <tr>
                                                                    <td class="cell-name">
                                                                        Адрес&nbsp;
                                                                    </td>
                                                                    <td class="cell-value"><?php echo $PHPShopOrder->getParam('org_yur_adres'); ?></td>
                                                                </tr>
                                                                <tr>
                                                                    <td class="cell-name">
                                                                        ИНН/КПП покупателя&nbsp;
                                                                    </td>
                                                                    <td class="cell-value"><?php
if (!empty($PHPShopOrder->getParam('org_inn')))
    echo $PHPShopOrder->getParam('org_inn') . '/' . $PHPShopOrder->getParam('org_kpp');
?></td>
                                                                </tr>
                                                            </tbody>
                                                        </table>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                    <table>
                                        <tbody>
                                            <tr>
                                                <td>
                                                    <table>
                                                        <tbody>
                                                            <tr>
                                                                <td width="190mm" class="cell-name">Валюта: наименование,
                                                                    код &nbsp; </td>
                                                                <td class="cell-value">Российский рубль, код - 643</td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <table>
                                                        <tbody>
                                                            <tr>
                                                                <td width="30%" class="cell-name">Идентификатор
                                                                    государственного контракта, договора
                                                                    (соглашения)&nbsp;(при наличии)</td>
                                                                <td></td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <table style="margin-top: 0" class="tab " class="page-break-after-avoid">
                        <thead>
                            <tr class="tab2 tr-begin">
                                <td width="80" rowspan="2" class="border_right width80">
                                    Код<br>товара/<br>работ,<br> услуг
                                </td>
                                <td width="2%" rowspan="2">
                                    №<br>п/п
                                </td>
                                <td rowspan="2" class="min-width150">
                                    Наименование товара<br>(описание выполненных работ, оказанных услуг),<br>имущественного
                                    права
                                </td>
                                <td width="5%" rowspan="2">Код вида товара</td>
                                <td colspan="2">Единица измерения</td>
                                <td rowspan="2">
                                    Количество<br>(объем)
                                </td>
                                <td rowspan="2">
                                    Цена (тариф)<br>за единицу<br>измерения
                                </td>
                                <td rowspan="2">
                                    Стоимость<br>товаров<br>(работ,услуг),<br>имущественных<br>прав без<br>налога - всего
                                </td>
                                <td rowspan="2">
                                    В том<br>числе<br>сумма<br>акциза
                                </td>
                                <td rowspan="2"><span class="nowrap">Налого-</span>
                                    <br>вая<br>ставка
                                </td>
                                <td rowspan="2">
                                    Сумма налога,<br>предъявляемая<br>покупателю
                                </td>
                                <td rowspan="2">
                                    Стоимость товаров<br>(работ, услуг),<br>имущественных прав с<br>налогом - всего
                                </td>
                                <td colspan="2"><span class="nowrap">Страна происхож-</span>
                                    <br>
                                    дения товара
                                </td>
                                <td width="10%" rowspan="2">
                                    Регистрационный номер декларации на товары или регистрационный номер партии товара,
                                    подлежащего прослеживаемости
                                </td>
                            </tr>
                            <tr class="tab2 tr-begin">
                                <td>
                                    код
                                </td>
                                <td>
                                    условное<br>обозначе-<br>
                                    <span class="nowrap">ние (нацио-</span>
                                    <br>нальное)
                                </td>
                                <td><span class="nowrap">Цифро-</span>
                                    <br>вой код
                                </td>
                                <td>
                                    Краткое<br>
                                    <span class="nowrap">наимено-</span>
                                    <br>вание
                                </td>
                            </tr>
                            <tr class="tab2 tr-begin">
                                <td class="border_right">
                                    А
                                </td>
                                <td>
                                    1
                                </td>
                                <td>
                                    1а
                                </td>
                                <td>
                                    1б
                                </td>
                                <td width="1%">
                                    2
                                </td>
                                <td width="5%">
                                    2а
                                </td>
                                <td width="5%">
                                    3
                                </td>
                                <td width="5%">
                                    4
                                </td>
                                <td width="5%">
                                    5
                                </td>
                                <td width="5%">
                                    6
                                </td>
                                <td width="1%">
                                    7
                                </td>
                                <td width="5%">
                                    8
                                </td>
                                <td width="5%">
                                    9
                                </td>
                                <td width="5%">
                                    10
                                </td>
                                <td width="5%">
                                    10а
                                </td>
                                <td width="5%">
                                    11
                                </td>
                            </tr>
                        </thead>
                        <tbody>
<?php echo $dis ?>
                            <tr class="tab2 tr-begin">
                                <td class="border_right">

                                </td>
                                <td colspan="7" style="text-align: left">
                                    <b>Всего к оплате (9)</b>
                                </td>
                                <td>
<?php echo $total_summa ?>
                                </td>

                                <td width="5%">

                                </td>
                                <td width="1%">

                                </td>
                                <td width="5%">
<?php echo $this_nds_summa ?>
                                </td>
                                <td width="5%">
<?php echo $total_summa_nds ?>
                                </td>
                                <td width="5%">

                                </td>
                                <td width="5%">

                                </td>
                                <td width="5%">

                                </td>
                            </tr>
                        </tbody>
                    </table>
                    
                    <table width="100%" border="0" cellspacing="0" cellpadding="0">
                        <tbody>
                            <tr>
                                <td width="77" style="padding: 5px">
                                    Документ составлен на 2 листах
                                </td>
                                <td style="border-left: 2px solid #000; border-bottom: 2px solid #000; padding-bottom: 5px">
                                    <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                        <tbody>
                                            <tr>
                                                <td width="49%">
                                                    <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                                        <tbody>
                                                            <tr>
                                                                <td width="170" style="padding: 5px">Руководитель организации или иное уполномоченное лицо</td>
                                                                <td style="border-bottom: 1px solid #000; padding: 5px" width="100"> </td>
                                                                <td width="10"> </td>
                                                                <td style="vertical-align: bottom !important; border-bottom: 1px solid #000; padding: 5px">
                                                                    <?php
                        if (!empty($LoadBanc['org_sig']))
                            echo '<img src="' . $LoadBanc['org_sig_buh'] . '">';
                        else
                            echo '<p class="P72"> </p>';
                        ?>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td> </td>
                                                                <td style="text-align: center; font-size:9px">(подпись)</td>
                                                                <td> </td>
                                                                <td style="text-align: center; font-size:9px">(ф.и.о.)</td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </td>
                                                <td width="2%"> </td>
                                                <td>
                                                    <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                                        <tbody>
                                                            <tr>
                                                                <td width="170" style="padding: 5px">Главный бухгалтер или иное уполномоченное лицо</td>
                                                                <td style="border-bottom: 1px solid #000; padding: 5px" width="100"> </td>
                                                                <td width="10"> </td>
                                                                <td style="vertical-align: bottom !important; border-bottom: 1px solid #000; padding: 5px">
                                                                    
                                                                    <?php
                        if (!empty($LoadBanc['org_sig']))
                            echo '<img src="' . $LoadBanc['org_sig_buh'] . '">';
                        else
                            echo '<p class="P72"> </p>';
                        ?>
                                                                    
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td> </td>
                                                                <td style="text-align: center; font-size:9px">(подпись)</td>
                                                                <td> </td>
                                                                <td style="text-align: center; font-size:9px">(ф.и.о.)</td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                    <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                        <tbody>
                                            <tr>
                                                <td width="170" style="padding: 5px">Индивидуальный предприниматель или иное уполномоченное лицо</td>
                                                <td style="border-bottom: 1px solid #000; padding: 5px" width="100"> </td>
                                                <td width="10"> </td>
                                                <td style="vertical-align: bottom !important; border-bottom: 1px solid #000; padding: 5px"></td>
                                                <td width="2%"> </td>
                                                <td style="vertical-align: bottom !important; border-bottom: 1px solid #000; padding: 5px" width="49%"></td>
                                            </tr>
                                            <tr>
                                                <td> </td>
                                                <td style="text-align: center; font-size:9px">(подпись)</td>
                                                <td> </td>
                                                <td style="text-align: center; font-size:9px">(ф.и.о.)</td>
                                                <td> </td>
                                                <td style="text-align: center; font-size:9px">(реквизиты свидетельства о государственной регистрации индивидуального предпринимателя)</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <div class="no-break-div page-break-before-avoid page-break-after-always" style="margin-top:150px">
                        <table>
                            <tbody>
                                <tr class="vertical-align-bottom">
                                    <td width="5%" style="padding-top: 5px;" class="cell-name nowrap">Основание передачи
                                        (сдачи)/получения приемки &nbsp;</td>
                                    <td>Заказ №<?php echo $ouid ?> от <?php echo PHPShopDate::get($row['datas'], false, false, '.', false) ?></td>
                                </tr>
                                <tr>
                                    <td></td>
                                    <td class="border-cell-podpis">(договор; доверенность и др.)</td>
                                </tr>
                            </tbody>
                        </table>
                        <table>
                            <tbody>
                                <tr class="vertical-align-bottom">
                                    <td width="5%" class="cell-name nowrap">
                                        Данные о транспортировке и грузе &nbsp;
                                    </td>
                                    <td class="corr_wrap"></td>
                                </tr>
                                <tr>
                                    <td></td>
                                    <td class="border-cell-podpis">(транспортная накладная, поручение экспедитору,
                                        экспедиторская/складская расписка и др./масса нетто/брутто груза, если не приведены
                                        ссылки на транспортные документы, содержащие эти сведения)</td>
                                </tr>
                            </tbody>
                        </table>
                        <table>
                            <tbody>
                                <tr class="vertical-align-top">
                                    <td width="50%" style="padding-right: 2mm;" class="border-right">
                                        <table>
                                            <tbody>
                                                <tr>
                                                    <td>
                                                        <table>
                                                            <tbody>
                                                                <tr>
                                                                    <td colspan="6" class="cell-name">
                                                                        Товар (груз) передал/услуги, результаты работ, права
                                                                        сдал
                                                                    </td>
                                                                </tr>
                                                                <tr height="16mm" class="vertical-align-bottom">
                                                                    <td width="40%" class="text-center">Генеральный директор
                                                                    </td>
                                                                    <td width="5%"></td>
                                                                    <td width="20%" class="text-center">
                                                                        <div class="pdffacsimile pdffacsimiledirector">
                                                                            <?php
                                                                            if (!empty($LoadBanc['org_sig']))
                                                                                echo '<img src="' . $LoadBanc['org_sig_buh'] . '">';
                                                                            ?>
                                                                        </div>
                                                                    </td>
                                                                    <td width="5%"></td>
                                                                    <td width="30%" class="text-center"></td>
                                                                </tr>
                                                                <tr>
                                                                    <td class="border-cell-podpis">(должность)</td>
                                                                    <td></td>
                                                                    <td class="border-cell-podpis">(подпись)</td>
                                                                    <td></td>
                                                                    <td class="border-cell-podpis">(ф.и.о.)</td>
                                                                </tr>
                                                            </tbody>
                                                        </table>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <table>
                                                            <tbody>
                                                                <tr>
                                                                    <td class="cell-name">
                                                                        Дата отгрузки, передачи (сдачи) &nbsp;
                                                                    </td>
                                                                    <td class="cell-name">
                                                                        <table>
                                                                            <tbody>
                                                                                <tr>
                                                                                    <td style="width: 1mm;">«</td>
                                                                                    <td style="width: 5mm;text-align: center;"
                                                                                        class="cell-value"><?php echo date("d", $row['datas']) ?></td>
                                                                                    <td style="width: 1mm;">»</td>
                                                                                    <td style="width: 20mm;text-align: center;"
                                                                                        class="cell-value"><?php echo date("m", $row['datas']) ?></td>
                                                                                    <td style="width: 1mm;"></td>
                                                                                    <td style="width: 5mm;text-align: center;"
                                                                                        class="cell-value"><?php echo date("Y", $row['datas']) ?></td>
                                                                                    <td>г.</td>
                                                                                </tr>
                                                                            </tbody>
                                                                        </table>
                                                                    </td>
                                                                </tr>
                                                            </tbody>
                                                        </table>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="cell-name">
                                                        Иные сведения об отгрузке, передаче
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <table>
                                                            <tbody>
                                                                <tr height="16mm" class="vertical-align-bottom">
                                                                    <td></td>
                                                                </tr>
                                                                <tr>
                                                                    <td class="border-cell-podpis">(ссылки на неотъемлемые
                                                                        приложения, сопутствующие документы, иные документы
                                                                        и т. п.)</td>
                                                                </tr>
                                                            </tbody>
                                                        </table>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <table>
                                                            <tbody>
                                                                <tr class="vertical-align-top">
                                                                    <td colspan="6" class="cell-name">
                                                                        Ответственный за правильность оформления факта
                                                                        хозяйственной жизни
                                                                    </td>
                                                                </tr>
                                                                <tr height="16mm" class="vertical-align-bottom">
                                                                    <td width="40%" class="text-center">Генеральный директор
                                                                    </td>
                                                                    <td width="5%"></td>
                                                                    <td width="20%" class="text-center"><?php
                                                                        if (!empty($LoadBanc['org_sig']))
                                                                            echo '<img src="' . $LoadBanc['org_sig'] . '">';
                                                                        ?></td>
                                                                    <td width="5%"></td>
                                                                    <td width="30%" class="text-center"></td>
                                                                </tr>
                                                                <tr>
                                                                    <td class="border-cell-podpis">(должность)</td>
                                                                    <td></td>
                                                                    <td class="border-cell-podpis">(подпись)</td>
                                                                    <td></td>
                                                                    <td class="border-cell-podpis nowrap">
                                                                        (ф.и.о.)
                                                                    </td>
                                                                </tr>
                                                            </tbody>
                                                        </table>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <table>
                                                            <tbody>
                                                                <tr>
                                                                    <td class="cell-name">
                                                                        Наименование экономического субъекта — составителя
                                                                        документа (в т. ч. комиссионера/агента)
                                                                    </td>
                                                                </tr>
                                                                <tr height="10mm" class="vertical-align-bottom">
                                                                    <td align="center"><?php echo $blank_org_name ?></td>
                                                                </tr>
                                                                <tr>
                                                                    <td class="border-cell-podpis">(может не заполняться при
                                                                        проставлении печати в М. П., может быть указан
                                                                        ИНН/КПП)</td>
                                                                </tr>
                                                            </tbody>
                                                        </table>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </td>
                                    <td width="50%" style="padding-left: 2mm;">
                                        <table>
                                            <tbody>
                                                <tr>
                                                    <td>
                                                        <table>
                                                            <tbody>
                                                                <tr>
                                                                    <td colspan="6" class="cell-name">
                                                                        Товар (груз) получил/услуги, результаты работ, права
                                                                        принял
                                                                    </td>
                                                                </tr>
                                                                <tr height="16mm" class="vertical-align-bottom">
                                                                    <td width="40%"></td>
                                                                    <td width="5%"></td>
                                                                    <td width="20%"></td>
                                                                    <td width="5%"></td>
                                                                    <td width="30%" class="text-center"></td>
                                                                </tr>
                                                                <tr>
                                                                    <td class="border-cell-podpis">(должность)</td>
                                                                    <td></td>
                                                                    <td class="border-cell-podpis">(подпись)</td>
                                                                    <td></td>
                                                                    <td class="border-cell-podpis">(ф.и.о.)</td>
                                                                </tr>
                                                            </tbody>
                                                        </table>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <table>
                                                            <tbody>
                                                                <tr>
                                                                    <td class="cell-name">
                                                                        Дата получения (приемки)&nbsp;
                                                                    </td>
                                                                    <td class="cell-name">
                                                                        <table>
                                                                            <tbody>
                                                                                <tr>
                                                                                    <td style="width: 1mm;">«</td>
                                                                                    <td style="width: 5mm;text-align: center;"
                                                                                        class="cell-value"></td>
                                                                                    <td style="width: 1mm;">»</td>
                                                                                    <td style="width: 20mm;text-align: center;"
                                                                                        class="cell-value"></td>
                                                                                    <td style="width: 1mm;">20</td>
                                                                                    <td style="width: 5mm;text-align: center;"
                                                                                        class="cell-value"></td>
                                                                                    <td>г.</td>
                                                                                </tr>
                                                                            </tbody>
                                                                        </table>
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td colspan="2" class="cell-name">
                                                                        Иные сведения о получении, приемке
                                                                    </td>
                                                                </tr>
                                                            </tbody>
                                                        </table>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <table>
                                                            <tbody>
                                                                <tr height="16mm" class="vertical-align-bottom">
                                                                    <td></td>
                                                                </tr>
                                                                <tr>
                                                                    <td class="border-cell-podpis">(информация о
                                                                        наличии/отсутствии претензии; ссылки на неотъемлемые
                                                                        приложения и другие документы и т. п.)</td>
                                                                </tr>
                                                            </tbody>
                                                        </table>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <table>
                                                            <tbody>
                                                                <tr>
                                                                    <td colspan="6" class="cell-name">
                                                                        Ответственный за правильность оформления факта
                                                                        хозяйственной жизни
                                                                    </td>
                                                                </tr>
                                                                <tr height="16mm" class="vertical-align-bottom">
                                                                    <td width="40%"></td>
                                                                    <td width="5%"></td>
                                                                    <td width="20%"></td>
                                                                    <td width="5%"></td>
                                                                    <td width="30%" class="text-center"></td>
                                                                </tr>
                                                                <tr>
                                                                    <td class="border-cell-podpis">(должность)</td>
                                                                    <td></td>
                                                                    <td class="border-cell-podpis">(подпись)</td>
                                                                    <td></td>
                                                                    <td class="border-cell-podpis">(ф.и.о.)</td>
                                                                </tr>
                                                            </tbody>
                                                        </table>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <table>
                                                            <tbody>
                                                                <tr>
                                                                    <td class="cell-name">
                                                                        Наименование экономического субъекта — составителя
                                                                        документа
                                                                    </td>
                                                                </tr>
                                                                <tr height="10mm" class="vertical-align-bottom">
                                                                    <td><?php echo $PHPShopOrder->getParam('org_name'); 
if (!empty($PHPShopOrder->getParam('org_inn')))
    echo ', ИНН/КПП '.$PHPShopOrder->getParam('org_inn') . '/' . $PHPShopOrder->getParam('org_kpp');
?></td>
                                                                </tr>
                                                                <tr>
                                                                    <td class="border-cell-podpis">(может не заполняться при
                                                                        проставлении печати в М. П., может быть указан
                                                                        ИНН/КПП)</td>
                                                                </tr>
                                                            </tbody>
                                                        </table>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        <table>
                            <tbody>
                                <tr>
                                    <td width="50%" style="padding-left:20mm" class="cell-name"><?php
                                        if (!empty($LoadBanc['org_stamp']))
                                            echo '<img src="' . $LoadBanc['org_stamp'] . '" align="left">';
                                        else
                                            echo " М. П.   ";
                                        ?></td>
                                    <td width="50%" style="padding-left:20mm" class="cell-name">М.П.</td>
                                </tr>
                                <tr>
                                    <td width="50%" style="padding-left:20mm" class="cell-name">
                                        <div style="margin-top: -20mm;">
                                            <div class="roundstamp"></div>
                                        </div>
                                    </td>
                                    <td width="50%" style="padding-left:20mm" class="cell-name"></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>