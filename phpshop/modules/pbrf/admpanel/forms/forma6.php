<?
$_classPath = "../../../../";
include($_classPath . "class/obj.class.php");
PHPShopObj::loadClass("base");
PHPShopObj::loadClass("order");
PHPShopObj::loadClass("system");
PHPShopObj::loadClass("inwords");
PHPShopObj::loadClass("delivery");
PHPShopObj::loadClass("date");
PHPShopObj::loadClass("valuta");
PHPShopObj::loadClass("security");
include("../../class/pbrf.class.php");


$PHPShopBase = new PHPShopBase($_classPath."inc/config.ini");
$PHPShopBase->chekAdmin();


$PHPShopSystem = new PHPShopSystem();
$LoadItems['System'] = $PHPShopSystem->getArray();

$PHPShopOrder = new PHPShopOrderFunction($_GET['orderID']);

// Подключаем реквизиты
$SysValue['bank'] = unserialize($LoadItems['System']['bank']);
$pathTemplate = $SysValue['dir']['templates'] . chr(47) . $_SESSION['skin'];


$sql = "select * from " . $SysValue['base']['table_name1'] . " where id=" . intval($_GET['orderID']);
$n = 1;
@$result = mysqli_query($link_db,$sql);
$row = mysqli_fetch_array(@$result);
//Закрываем
if($_GET['datas']!=$row['datas']) {
  echo 'Запрещен доступ';
  exit();
}
$id = $row['id'];
$datas = $row['datas'];
$ouid = $row['uid'];
$order = unserialize($row['orders']);
$status = unserialize($row['status']);
$datas = PHPShopDate::dataV($datas, "false");
$user_id = $order['Person']['user_id'];

//select phshop_shopusers
$sql = "select * from " . $SysValue['base']['table_name27'] . " where id=" . intval($user_id);
@$result = mysqli_query($link_db,$sql);
$row = mysqli_fetch_array(@$result);
//Адреса
$data_adres = unserialize($row['data_adres']);
//Данные из адресов
$country_new = $data_adres['list'][0]['country_new'];
$state_new = $data_adres['list'][0]['state_new'];
$city_new = $data_adres['list'][0]['city_new'];
$index_new = $data_adres['list'][0]['index_new'];
$fio_new = $data_adres['list'][0]['fio_new'];
$tel_new = $data_adres['list'][0]['tel_new'];
$street_new = $data_adres['list'][0]['street_new'];
$house_new = $data_adres['list'][0]['house_new'];
$porch_new = $data_adres['list'][0]['porch_new'];
$door_phone_new = $data_adres['list'][0]['door_phone_new'];
$flat_new = $data_adres['list'][0]['flat_new'];
$delivtime_new = $data_adres['list'][0]['delivtime_new'];
//Данные из Person
$mail_new = $order['Person']['mail'];

//Реквизиты
$bank = $SysValue['bank'];
//Данные
$org_name = $bank['org_name'];
$org_ur_adres = $bank['org_ur_adres'];
$org_adres = $bank['org_adres'];
$org_inn = $bank['org_inn'];
$org_kpp = $bank['org_kpp'];
$org_schet = $bank['org_schet'];
$org_bank = $bank['org_bank'];
$org_bic = $bank['org_bic'];
$org_bank_schet = $bank['org_bank_schet'];

$cart = $order['Cart']['cart'];

$k = 0;
$n=1;
if(isset($cart)) {
    foreach ($cart as $key => $value) {
      $html_predmet .= '<tr class="object" rel="'.$n.'">
                  <td class="text-center" valign="middle"><b>'.$n.'</b></td>
                  <td><input placeholder="предмет" type="text" class="twelve validate[required],length[0,20]" name="F107[object]['.$k.'][name]" value="'.substr($value['name'], 0, 40).'" maxlength="40"></td>
                  <td><input placeholder="число" type="text" class="twelve validate[required,custom[integer]] count onlyNumber" name="F107[object]['.$k.'][count]" value="'.$value['num'].'"></td>
                  <td><input placeholder="0.99" type="text" class="twelve validate[required,custom[number]] price onlyNumber addPoint" name="F107[object]['.$k.'][price]" value="'.($value['price']*$value['num']).'"></td>
                </tr>';
      $n++;
      $k++;
      //итого кол-во
      $itNum = $itNum + $value['num'];
    }
}

for ($i=$n; $i<10 ; $i++) {
  $k++;
  $html_predmet .= '<tr class="object" rel="'.$i.'">
              <td class="text-center" valign="middle"><b>'.$i.'</b></td>
              <td><input placeholder="предмет" type="text" class="twelve validate[required],length[0,20]" name="F107[object]['.$k.'][name]" value="" maxlength="40"></td>
              <td><input placeholder="число" type="text" class="twelve validate[required,custom[integer]] count onlyNumber" name="F107[object]['.$k.'][count]" value=""></td>
              <td><input placeholder="0.99" type="text" class="twelve validate[required,custom[number]] price onlyNumber addPoint" name="F107[object]['.$k.'][price]" value=""></td>
            </tr>';
}

?>

<head>
    <title>Бланк Заказа №<?= $ouid ?></title>
    <META HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=windows-1251">
    <META HTTP-EQUIV="PRAGMA" CONTENT="NO-CACHE">
    <meta http-equiv="Content-Type" content="text/html; charset=windows-1251">

    <style media="print" type="text/css">

        <!-- 
        .nonprint {
            display: none;
        }
        -->
    </style>
    <link rel="stylesheet" type="text/css" href="../../css/foundation.css" />
    <link rel="stylesheet" type="text/css" href="../../css/default.css" />
    <script src="//code.jquery.com/jquery-1.11.0.min.js"></script>
    <script src="../../js/pbrf.js"></script>

</head>
<body onload="window.focus()" bgcolor="#FFFFFF" text="#000000" marginwidth=5 leftmargin=5 style="padding: 2px;">
    
    <?
        //Получаем тип бланка
        $type = $_POST['type_send'];
        //Результат
        if( isset($type) ):
            //Бланк
            $blank = 'F107';
            //Запрос данных
            pbrf_get($_POST['F107'], $type, $blank);
        endif;
    ?>

    <div class="row blank" id="blank">
        <div align="center"><table align="center" width="100%">
            <tr>
                <td align="center"></td>
                <td align="right"><h4 align=center>Заказ&nbsp;№&nbsp;<?= $ouid ?>&nbsp;от&nbsp;<?= $datas ?></h4></td>
            </tr>
        </table>
    </div>
    
    <h2 class="blank_name">Заполнение бланка Описи Ф.107</h2>
        <div class="twelve columns">


<form class="twelve columns" method="post" action="" id="blanks">
  <fieldset>
    <fieldset id="recipient" class="validationEngineContainer">
      <legend>Данные получателя</legend>
      <div class="row">
        <div class="two columns">
          <label class="inline right">Кому</label>
        </div>
        <div class="six columns left">
          <input placeholder="Фамилия Имя Отчеств" id="whom" class="twelve validate[required] capitalize" type="text" name="F107[whom]" value="<?=$fio_new?>" maxlength="40">
        </div>
        <div class="four columns left">
          <label class="inline left">Фамилия Имя Отчество</label>
        </div>
      </div>
      <div class="row">
        <div class="two columns">
          <label class="inline right">Страна</label>
        </div>
        <div class="four columns left end">
          <input placeholder="Название страны" id="whom_country" class="twelve validate[required] capitalize" type="text" name="F107[whom_country]" value="<?=$country_new?>">
        </div>
      </div>
      <div class="row">
        <div class="two columns">
          <label class="inline right">Город</label>
        </div>
        <div class="four columns left end">
          <input placeholder="Название города" id="whom_city" class="twelve validate[required]" type="text" name="F107[whom_city]" value="<?=$city_new?>">
        </div>
      </div>
    </fieldset>
    <div id="other" class="validationEngineContainer">
    <fieldset>
      <legend>Опись</legend>
      <div class="row">
        <div class="two columns">
          <label class="inline right">вложения в</label>
        </div>
        <div class="six columns left end">
          <input placeholder="девять предметов как они есть" id="investment" class="twelve validate[required,custom[forbiddenItems]]" type="text" name="F107[investment]" value="<?=$itNum?> предмет(ов) как они есть" maxlength="40">
        </div>
      </div>
    </fieldset>
    <fieldset id="subject">
      <legend>Список предметов</legend>
      <table>
        <thead>
          <tr>
            <td class="text-center one">№</td>
            <td class="text-center seven">Наименование предмета</td>
            <td class="text-center two">Кол-во предметов</td>
            <td class="text-center two">Объявленная ценность<br>(руб.)</td>
          
        </tr></thead>
        <tbody>
        <?=$html_predmet?>
        </tbody>       
      </table>
    </fieldset>
    </div><!--other-->
                
    <!-- закрытие тегов после кнопок
  </fieldset>
</form> -->     <div class="but-send-pbrf">
            <input type="hidden" name="type_send" id="type_send">
            <!--button class="blank_button" onclick="print_pbrf()"><img src="/phpshop/admpanel/img/action_print.gif"> Для печати</button--> 
            <button class="blank_button" onclick="pdf_pbrf()">Скачать PDF</button>
        </div>
      </fieldset><!--начало в шаблоне отдельного бланка-->
    </form>
  </div>
    
  </div>
    
</body>
</html>