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
$LoadItems['System'] = $PHPShopSystem->getArray();
$PHPShopLang = new PHPShopLang(array('locale' => $_SESSION['lang'], 'path' => 'admin'));

$PHPShopOrder = new PHPShopOrderFunction($_GET['orderID']);

// Юридические лица
$company = $PHPShopOrder->getParam('company');
$PHPShopSystem->setCompany($company);
$LoadItems['System']['nds'] = $PHPShopOrder->PHPShopSystem->getParam('nds');

// Подключаем реквизиты
$SysValue['bank'] = $LoadBanc = unserialize($LoadItems['System']['bank']);

$sql = "select * from " . $SysValue['base']['orders'] . " where id=" . intval($_GET['orderID']);
$n = 1;
$sum=$num=0;
@$result = mysqli_query($link_db, $sql);
$row = mysqli_fetch_array(@$result);
$id = $row['id'];
$datas = $row['datas'];
$ouid = $row['uid'];
$order = unserialize($row['orders']);
$status = unserialize($row['status']);
$nds = $LoadItems['System']['nds'];
$dis = $this_nds_summa = $sum = $num = $weight = $adr_info = null;
foreach ($order['Cart']['cart'] as $val) {

    // Услуга
    if ($val['type'] == 2)
        continue;

    $this_price = ($PHPShopOrder->returnSumma(number_format($val['price'], "2", ".", ""), $order['Person']['discount']));
    $this_nds = number_format($this_price * $nds / (100 + $nds), "2", ".", "");
    $this_price_bez_nds = ($this_price - $this_nds) * $val['num'];
    $this_price_c_nds = number_format($this_price * $val['num'], "2", ".", "");
    $this_nds_summa += $this_nds * $val['num'];

    $dis .= "
  <tr>
    <td >" . $val['name'] . "</td>
    <td align=\"center\">796</td>
    <td align=\"center\">" . $val['ed_izm'] . "</td>
    <td align=\"right\">" . $val['num'] . "</td>
    <td align=\"right\">" . $this_price . "</td>
    <td align=\"right\">" . $this_price_bez_nds . "</td>
    <td align=\"right\">--</td>
    <td align=\"right\">" . $LoadItems['System']['nds'] . "%</td>
    <td align=\"right\">" . $this_nds * $val['num'] . "</td>
    <td align=\"right\">" . $this_price_c_nds . "</td>
    <td align=\"center\">---</td>
    <td align=\"center\">---</td>
    <td align=\"center\">---</td>
  </tr>
  ";

    $total_summa = $row['sum'];

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

$summa_nds_dos = number_format($deliveryPrice * $nds / (100 + $nds), "2", ".", "");

$dis .= "
  <tr>
    <td >" . __("Доставка") . " " . $PHPShopDelivery->getCity() . "</td>
    <td align=\"right\">----</td>
    <td align=\"center\">----</td>
    <td align=\"right\">1</td>
    <td align=\"right\">" . $deliveryPrice . "</td>
    <td align=\"right\">" . $deliveryPrice . "</td>
    <td align=\"right\">--</td>
    <td align=\"right\">" . $LoadItems['System']['nds'] . "%</td>
    <td align=\"right\">" . $summa_nds_dos . "</td>
    <td align=\"right\">" . $deliveryPrice . "</td>
    <td align=\"center\">---</td>
    <td align=\"center\">---</td>
    <td align=\"center\">---</td>
  </tr>
  ";

if ($LoadItems['System']['nds_enabled']) {
    $nds = $LoadItems['System']['nds'];
    $nds = number_format($sum * ($nds / (100 + $nds)), "2", ".", "");
}
$sum = number_format($sum+$deliveryPrice, "2", ".", "");

$name_person = $order['Person']['name_person'];

if ($row['org_name'] or @ $order['Person']['org_name'])
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
if ($row['country'])
    $adr_info .= ", " . __("страна") . ": " . $row['country'];
if ($row['state'])
    $adr_info .= ", " . __("регион/штат") . ": " . $row['state'];
if ($row['city'])
    $adr_info .= ", " . __("город") . ": " . $row['city'];
if ($row['index'])
    $adr_info .= ", " . __("индекс") . ": " . $row['index'];
if ($row['street'] OR ! empty($order['Person']['adr_name']))
    $adr_info .= ", " . __("улица") . ": " . $row['street'] . @$order['Person']['adr_name'];
if ($row['house'])
    $adr_info .= ", " . __("дом") . ": " . $row['house'];
if ($row['porch'])
    $adr_info .= ", " . __("подъезд") . ": " . $row['porch'];
if ($row['door_phone'])
    $adr_info .= ", " . __("код домофона") . ": " . $row['door_phone'];
if ($row['flat'])
    $adr_info .= ", " . __("квартира") . ": " . $row['flat'];

$adr_info = substr($adr_info, 2);

// Генерим номер товарного чека
$chek_num = substr(abs(crc32(uniqid(rand(), true))), 0, 5);

$LoadBanc = unserialize($LoadItems['System']['bank']);
$LoadBanc['org_sig'] = $PHPShopSystem->getSerilizeParam('bank.org_sig');
$LoadBanc['org_sig_buh'] = $PHPShopSystem->getSerilizeParam('bank.org_sig_buh');
$LoadBanc['org_stamp'] = $PHPShopSystem->getSerilizeParam('bank.org_stamp');
$LoadBanc['org_stamp'] = $PHPShopSystem->getSerilizeParam('bank.org_stamp');
$LoadBanc['org_adres'] = $PHPShopSystem->getSerilizeParam('bank.org_adres');
$LoadBanc['org_inn'] = $PHPShopSystem->getSerilizeParam('bank.org_inn');
$LoadBanc['org_kpp'] = $PHPShopSystem->getSerilizeParam('bank.org_kpp');
$LoadItems['System']['company'] = $PHPShopSystem->getParam('company');
?>
<!doctype html>
<head>
    <title><?php _e("Счет - Фактура") ?> &#8470;<?php echo @$ouid ?></title>
    <meta http-equiv="Content-Type" content="text/html; charset=windows-1251">
    <link rel="apple-touch-icon" href="../../apple-touch-icon.png">
    <link rel="icon" href="../../favicon.ico"> 
    <link href="style.css" type=text/css rel=stylesheet>
    <script src="../../../lib/templates/print/js/html2pdf.bundle.min.js"></script>
</head>
<body>
    <div align="right" class="nonprint">
        <button onclick="html2pdf(document.getElementById('content'), {margin: 1, filename: '<?php _e("Счет-фактура") ?> &#8470;<?php echo $ouid ?>.pdf', html2canvas: {dpi: 192, letterRendering: true}, jsPDF: {orientation: 'landscape'}});"><?php _e("Сохранить") ?></button> 
        <button onclick="window.print();"><?php _e("Распечатать") ?></button> 
        <hr>
    </div>
    <div id="content">
        <table align="center" width="100%" border="0" cellspacing="0" cellpadding="0">
            <tr>
                <td valign="top" align="right">
                    <?php
                    $GetIsoValutaOrder = $PHPShopOrder->default_valuta_code;
                    if (preg_match("/руб/", $GetIsoValutaOrder)) {
                        echo '
	<div>' . _e("Приложение № 1<br>к постановлению Правительства Российской Федерации<br>от 26 декабря 2011 г. № 1137") . ' 
 </div>
';
                    }
                    ?>
                </td>
            </tr>
            <tr>
                <td valign="top" id="d2"><?php _e("СЧЕТ-ФАКТУРА") ?> &#8470;<input title="<?php _e("Изменить") ?>" value="<?php echo @$ouid ?> / <?php echo PHPShopDate::get($row['datas']) ?> "> <br>
                    <?php _e("Исправление № --  от --") ?></td>
            </tr>
            <tr>
                <td valign="top"><?php
                    _e("Продавец");
                    echo ": " . $LoadItems['System']['company']
                    ?><br />					
                    <?php
                    _e("Адрес");
                    echo ": " . $LoadBanc['org_adres']
                    ?>, <?php echo $LoadItems['System']['tel'] ?> <br />							
                    <?php _e("Идентификационный номер продавца (ИНН)") ?> <?php echo $LoadBanc['org_inn'] ?>\<?php echo $LoadBanc['org_kpp'] ?> <br />							
                    <?php _e("Грузоотправитель и его адрес: Он же") ?>	<br />						
                    <?php
                    _e("Грузополучатель и его адрес");
                    echo @$adr_info
                    ?>	<br />						
                    <?php _e("К платежно-расчетному документу") ?>       <br />							
<?php echo __("Покупатель") . ": " . $org_name ?>	<br />						
<?php echo __("Адрес") . ": " . @$adr_info ?> <br />							
<?php echo __("Идентификационный номер покупателя (ИНН)") . ": " . $order['Person']['org_inn'] . $row['org_inn'] . "/ " . __("КПП") . ":" . @$order['Person']['org_kpp'] . $row['org_kpp'] ?> <br />							
                </td>
            </tr>
            <tr>
                <td valign="top" >
                    <table style="margin-top:10px;" bordercolor="#000000"  border="1" cellspacing="0" cellpadding="0">

                        <tr>
                            <td width="200" align="center" rowspan="2"><?php _e("Наименование товара (описание выполненных 
                                работ, оказанных услуг), имущественного права") ?></td>
                            <td  align="center" colspan="2"><?php _e("Единица измерения") ?></td>
                            <td  align="center" rowspan="2"><?php _e("Количество (объем)") ?></td>
                            <td  align="center" rowspan="2"><?php _e("Цена (тариф) за единицу измерения") ?></td>
                            <td  align="center" rowspan="2"><?php _e("Стоимость товаров (работ, услуг),имущественных прав без налога всего") ?></td>
                            <td  align="center" rowspan="2"><?php _e("В том числе сумма акциза") ?></td>
                            <td  align="center" rowspan="2"><?php _e("Налоговая ставка") ?></td>
                            <td  align="center" rowspan="2"><?php _e("Сумма налога, предъявляемая покупателю") ?></td>
                            <td  align="center" rowspan="2"><?php _e("Стоимость товаров (работ, услуг), имущественных прав с налогом всего") ?></td>
                            <td  align="center" colspan="2"><?php _e("Страна<br> происхождения товара") ?>
                            </td>
                            <td  align="center"  rowspan="2"><?php _e("Номер таможенной декларации") ?></td>
                        </tr>
                        <tr>
                            <td align="center"><?php _e("код") ?></td>
                            <td align="center"><?php _e("условное обозначение (национальное)") ?></td>
                            <td align="center"><?php _e("цифровой код") ?></td>
                            <td align="center"><?php _e("краткое наименование") ?></td>
                        </tr>
                        <tr>
                        <tr>
                            <td align="center">1</td>
                            <td align="center">2</td>
                            <td align="center">2a</td>
                            <td align="center">3</td>
                            <td align="center">4</td>
                            <td align="center">5</td>
                            <td align="center">6</td>
                            <td align="center">7</td>
                            <td align="center">8</td>
                            <td align="center">9</td>
                            <td align="center">10</td>
                            <td align="center">10a</td>
                            <td align="center">11</td>
                        </tr>
<?php echo $dis; ?>

                        <tr>
                            <td colspan="8"><b><?php _e("Всего к оплате") ?></b></td>

                            <td align="right"><?php echo $this_nds_summa + $summa_nds_dos; ?></td>
                            <td align="right"><?php echo $sum; ?></td>
                            <td colspan="3">&nbsp;</td>

                        </tr>

                    </table>
                </td>
            </tr>
            <tr>
                <td valign="top" >&nbsp;</td>


            </tr>
            <tr>
                <td valign="top" >&nbsp;</td>


            </tr>
            <tr>
                <td valign="top" ><table width="100%" border="0" cellspacing="0" cellpadding="0">
                        <tr>
                            <td width="50%">
                                <table  border="0" cellspacing="3" cellpadding="0">
                                    <tr>
                                        <td><?php _e("Руководитель организации<br>или иное уполномоченное лицо") ?>
                                        </td>
                                        <td><?php
                                            if (!empty($LoadBanc['org_sig']))
                                                echo '<img src="' . $LoadBanc['org_sig'] . '">';
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
                            </td>
                            <td width="50%" valign="top" align="right">
                                <table  border="0" cellspacing="3" cellpadding="0">
                                    <tr>
                                        <td><?php _e("Главный бухгалтер<br>или иное уполномоченное лицо") ?>
                                        </td>
                                        <td><?php
                                            if (!empty($LoadBanc['org_sig_buh']))
                                                echo '<img src="' . $LoadBanc['org_sig_buh'] . '">';
                                            else
                                                echo '<u>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</u>';
                                            ?></td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
            <!--
            <tr>
                <td valign="top" >
                    <p><br></p>
                    <table width="100%" border="0" cellspacing="0" cellpadding="0">
                        <tr>
                            <td width="70%">
                                <table  border="0" cellspacing="3" cellpadding="0">
                                    <tr>
                                        <td>Индивидуальный предприниматель</td>                                
                                        <td>____________________</td>
                                        <td>&nbsp;&nbsp;&nbsp;&nbsp;</td>
                                        <td>____________________</td>
                                    </tr>
                                    <tr>
                                        <td></td>
                                        <td id="center">(подпись)</td>
                                        <td></td>
                                        <td id="center" >(ф.и.о.)</td>
                                    </tr>
                                </table>
                            </td>
                            <td width="10%"></td>
                            <td width="20%" valign="top" align="right">
                                <table  border="0" cellspacing="3" cellpadding="0">
                                    <tr>
                                        <td>______________________________________________</td>
                                    <tr>                             
                                        <td id="center">(реквизиты свидетельства о государственной регистрации индивидуального предпринимателя)</td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>-->
        </table>
    </div>
</body>
</html>