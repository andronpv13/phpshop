<?php
session_start();
$_classPath = "../../../";
include($_classPath . "class/obj.class.php");
PHPShopObj::loadClass(array("base", "order", "system", "inwords", "delivery", "date", "valuta", "lang"));

$PHPShopBase = new PHPShopBase($_classPath . "inc/config.ini");

// Авторизация
if (strstr($_GET['orderID'], '-')) {
    $id = (int) explode("-", $_GET['orderID'])[0];
    $s = (string) explode("-", $_GET['orderID'])[1];
}
if (empty($s) or !$PHPShopBase->checkFile($s, $id))
    $PHPShopBase->chekAdmin();

$PHPShopSystem = new PHPShopSystem();
$PHPShopBase->checkMultibase("../../../../");

$LoadItems['System'] = $PHPShopSystem->getArray();

$PHPShopOrder = new PHPShopOrderFunction($_GET['orderID']);
$PHPShopLang = new PHPShopLang(array('locale' => $_SESSION['lang'], 'path' => 'admin'));

// Подключаем реквизиты
$SysValue['bank'] = $LoadBanc = unserialize($LoadItems['System']['bank']);

$sql = "select * from " . $SysValue['base']['orders'] . " where id=" . intval($_GET['orderID']);
$n = 1;
$result = mysqli_query($link_db, $sql);
$row = mysqli_fetch_array($result);
$ouid = $row['uid'];
$order = unserialize($row['orders']);
$dis = null;
$weight = $sum = $num = 0;
if (is_array($order['Cart']['cart']))
    foreach ($order['Cart']['cart'] as $val) {

        if (!empty($val['parent_uid']))
            $val['uid'] = $val['parent_uid'];

        if (!empty($val['uid']))
            $val['name'] .= ' (' . $val['uid'] . ')';

        $dis .= "<tr class=tablerow>
		<td class=tablerow>" . $n . "</td>
		<td class=tablerow>" . $val['name'] . "</td>
		<td class=tablerow align=center>" . $val['ed_izm'] . "</td>
		<td align=right class=tablerow>" . $val['num'] . "</td>
		<td align=right class=tablerow nowrap>" . $PHPShopOrder->returnSumma($val['price'], 0) . "</td>
		<td class=tableright>" . $PHPShopOrder->returnSumma($val['price'] * $val['num'], 0) . "</td>
	</tr>";

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

$PHPShopDelivery = new PHPShopDelivery($order['Person']['dostavka_metod']);
$PHPShopDelivery->checkMod($order['Cart']['dostavka']);
$deliveryPrice = $PHPShopDelivery->getPrice($sum, $weight);
$deliveryName = unserialize($PHPShopDelivery->getParam('data_fields'));
$dis .= "
  <tr class=tablerow>
		<td class=tablerow>" . $n . "</td>
		<td class=tablerow>" . __("Доставка") . " - " . $PHPShopDelivery->getCity() . "</td>
		<td class=tablerow align=center>&nbsp;" . __("шт.") . "&nbsp;</td>
		<td align=right class=tablerow>1</td>
		<td align=right class=tablerow nowrap>" . $deliveryPrice . "</td>
		<td class=tableright>" . $deliveryPrice . "</td>
	</tr>
  ";
if ($LoadItems['System']['nds_enabled']) {
    $nds = $LoadItems['System']['nds'];
    $nds = number_format($sum * $nds / (100 + $nds), "2", ".", "");
}
$sum = number_format($sum, "2", ".", "");

$summa_nds_dos = number_format($deliveryPrice * $nds / (100 + $nds), "2", ".", "");


$name_person = @$order['Person']['name_person'];
$org_name = @$order['Person']['org_name'];
$datas = PHPShopDate::dataV($row['datas'], "false");

// время доставки под старый формат данных в заказе
if (!empty($order['Person']['dos_ot']) OR ! empty($order['Person']['dos_do']))
    $dost_ot = " От: " . $order['Person']['dos_ot'] . ", до: " . $order['Person']['dos_do'];
else
    $dost_ot = null;

// формируем адрес доставки с учётом старого формата данных в заказах
$adr_info = null;

if (is_array($deliveryName['enabled']))
    foreach ($deliveryName['enabled'] as $k => $v) {
        if (!empty($row[$k]) and $v['name'] != 'ФИО') {
            $adr_info .= ", " . $v['name'] . ': ' . $row[$k];
        }
    }

$adr_info = substr($adr_info, 2);

$PERSON = $order['Person'];
if (@$PERSON['discount'] > 0) {
    $discount = @$PERSON['discount'] . '%';
} else {
    $discount = (@$PERSON['tip_disc'] == 1 ? @$PERSON['discount_promo'] . '%' : @$PERSON['discount_promo']);
}

if (!empty($row['bonus_minus']))
    $discount = $row['bonus_minus'];
?>
<!doctype html>
<head>
    <title><?php _e("Бланк Заказа") ?> &#8470;<?php echo $ouid ?></title>
    <meta http-equiv="Content-Type" content="text/html; charset=windows-1251">
    <link rel="apple-touch-icon" href="../../apple-touch-icon.png">
    <link rel="icon" href="../../favicon.ico"> 
    <link href="style.css" type=text/css rel=stylesheet>
    <script src="../../../lib/templates/print/js/html2pdf.bundle.min.js"></script>
</head>
<body>
    <div align="right" class="nonprint">
        <button onclick="html2pdf(document.getElementById('content'), {margin: 1, filename: '<?php _e("Бланк Заказа") ?> &#8470;<?php echo $ouid ?>.pdf', html2canvas: {dpi: 192, letterRendering: true}});"><?php _e("Сохранить") ?></button> 
        <button onclick="window.print();"><?php _e("Распечатать") ?></button> 
        <hr><br><br>
    </div>
    <div id="content">
        <div align="center"><table align="center" width="100%">
                <tr>
                    <td align="center"><img src="<?php echo $PHPShopSystem->getLogo(true); ?>" alt="" border="0" style="max-width: 200px;height: auto;"></td>
                    <td align="right"><h2><?php _e("Заказ") ?>&nbsp;&#8470;&nbsp;<?php echo $ouid ?>&nbsp;<?php _e("от") ?>&nbsp;<?php echo $datas ?></h2></td>
                </tr>
            </table>
        </div>

        <table width=99% cellpadding=2 cellspacing=0 align=center>
            <tr class=tablerow>
                <td class=tablerow width="150"><?php _e("Заказчик") ?>:</td>
                <td class=tableright><?php
                    if (!empty($row['fio']))
                        echo $row['fio'];
                    else
                        echo @$order['Person']['name_person'];
                    ?></td>
            </tr>
            <tr class=tablerow>
                <td class=tablerow><?php _e("Компания") ?>:</td>
                <td class=tableright>&nbsp;<?php echo @$order['Person']['org_name'] . $row['org_name'] ?></td>
            </tr>
            <tr class=tablerow>
                <td class=tablerow><?php _e("Почта") ?>:</td>
                <td class=tableright><a href="mailto:<?php echo $order['Person']['mail'] ?>"><?php echo $order['Person']['mail'] ?></a></td>
            </tr>
            <tr class=tablerow>
                <td class=tablerow><?php _e("ИНН") ?>:</td>
                <td class=tableright>&nbsp;<?php echo @$order['Person']['org_inn'] . $row['org_inn'] ?></td>
            </tr>
            <tr class=tablerow>
                <td class=tablerow><?php _e("КПП") ?>:</td>
                <td class=tableright>&nbsp;<?php echo @$order['Person']['org_kpp'] . $row['org_kpp'] ?></td>
            </tr>
            <tr class=tablerow>
                <td class=tablerow><?php _e("Тел") ?>:</td>
                <td class=tableright><?php echo @$order['Person']['tel_code'] . " " . @$order['Person']['tel_name'] . $row['tel'] ?></td>
            </tr>
            <tr class=tablerow>
                <td class=tablerow><?php _e("Адрес") ?>:</td>
                <td class=tableright><?php echo @$adr_info ?></td>
            </tr>
            <tr class=tablerow>
                <td class=tablerow><?php _e("Грузополучатель") ?>:</td>
                <td class=tableright><?php echo $PHPShopDelivery->getCity() ?></td>
            </tr>
            <tr class=tablerow>
                <td class=tablerow><?php _e("Время доставки") ?>:</td>
                <td class=tableright><?php echo $dost_ot . $row['delivtime'] ?></td>
            </tr>
            <tr class=tablerow >
                <td class=tablerow><?php _e("Тип оплаты") ?>:</td>
                <td class=tableright><?php echo $PHPShopOrder->getOplataMetodName() ?></td>
            </tr>
            <tr class=tablerow >
                <td class=tablerow style="border-bottom: 1px solid #000000;"><?php _e("Комментарии") ?>:</td>
                <td class=tableright style="border-bottom: 1px solid #000000;">&nbsp;<?php echo $row['dop_info']; ?></td>
            </tr>
        </table>
        <p><br></p>
        <table width=99% cellpadding=2 cellspacing=0 align=center>
            <tr class=tablerow>
                <td class=tablerow>&#8470;</td>
                <td width=50% class=tablerow><?php _e("Наименование") ?></td>
                <td class=tablerow><?php _e("Единица измерения") ?>&nbsp;</td>
                <td class=tablerow><?php _e("Количество") ?></td>
                <td class=tablerow><?php _e("Цена") ?></td>
                <td class=tableright><?php _e("Сумма") ?></td>
            </tr>
            <?php
            echo $dis;
            $my_total = $row['sum'];
            $my_nds = number_format($my_total * $LoadItems['System']['nds'] / (100 + $LoadItems['System']['nds']), "2", ".", "");
            ?>
            <tr>
                <td colspan=5 align=right style="border-top: 1px solid #000000;border-left: 1px solid #000000;"><?php _e("Скидка") ?>:</td>
                <td class=tableright nowrap><b><?php echo $discount ?></b></td>
            </tr>
            <tr>
                <td colspan=5 align=right style="border-top: 1px solid #000000;border-left: 1px solid #000000;"><?php _e("Итого") ?>:</td>
                <td class=tableright nowrap><b><?php echo $my_total ?></b></td>
            </tr>
<?php if ($LoadItems['System']['nds_enabled']) { ?>
                <tr>
                    <td colspan=5 align=right style="border-top: 1px solid #000000;border-left: 1px solid #000000;"><?php _e("В т.ч. НДС") ?>: <?php echo $LoadItems['System']['nds'] ?>%</td>
                    <td class=tableright nowrap><b><?php echo $my_nds ?></b></td>
                </tr>
<?php } ?>
            <tr><td colspan=6 style="border: 0px; border-top: 1px solid #000000;">&nbsp;</td></tr>
        </table>

        <p><b><?php echo __("Всего наименований") . " " . ($num + 1) . " " . __("на сумму") . " " . $row['sum'] . " " . $PHPShopOrder->default_valuta_code; ?>
                <br />
                <?php
                $iw = new inwords;
                $s = $iw->get($my_total);
                $v = $PHPShopOrder->default_valuta_code;
                if (preg_match("/руб/i", $v))
                    echo $s;
                ?>
            </b></p><br>

        <table>
            <tr>
                <td><?php _e("Руководитель") ?>:</td>
                <td><?php
                    if (!empty($LoadBanc['org_sig']))
                        echo '<img src="' . $LoadBanc['org_sig'] . '">';
                    else
                        echo '<u>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</u>';
                    ?></td>
            </tr>
            <tr>
                <td><?php _e("Главный бухгалтер") ?>:</td>
                <td><?php
                    if (!empty($LoadBanc['org_sig_buh']))
                        echo '<img src="' . $LoadBanc['org_sig_buh'] . '">';
                    else
                        echo '<u>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</u>';
                    ?></td>
            </tr>
            <tr>
                <td colspan="2">
                    <?php
                    if (!empty($LoadBanc['org_stamp']))
                        echo '<img src="' . $LoadBanc['org_stamp'] . '">';
                    else
                        echo '<div style="padding:50px;border-bottom: 1px solid #000000;border-top: 1px solid #000000;border-left: 1px solid #000000;border-right: 1px solid #000000;" align="center">' . __('М.П.') . '</div>';
                    ?>
                </td>
            </tr>
        </table>
    </div>
</body>
</html>