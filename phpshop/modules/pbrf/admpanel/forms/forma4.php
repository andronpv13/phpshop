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
PHPShopObj::loadClass("modules");
PHPShopObj::loadClass("security");
include("../../class/pbrf.class.php");


$PHPShopBase = new PHPShopBase($_classPath."inc/config.ini");
$PHPShopBase->chekAdmin();

$PHPShopSystem = new PHPShopSystem();
$LoadItems['System'] = $PHPShopSystem->getArray();

$PHPShopOrder = new PHPShopOrderFunction($_GET['orderID']);
// Настройки модуля
$PHPShopModules = new PHPShopModules($_classPath."modules/");

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
//Данные админа
$PHPShopOrm = new PHPShopOrm($PHPShopModules->getParam("base.pbrf.pbrf_system"));
$pbrf_system = $PHPShopOrm->select();
$data_person = unserialize($pbrf_system['data']);

?>

<head>
    <title>Бланк Заказа №<?= $ouid ?></title>
    <META HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=windows-1251">
    <META HTTP-EQUIV="PRAGMA" CONTENT="NO-CACHE">
    <meta http-equiv="Content-Type" content="text/html; charset=windows-1251">
    <link href="style.css" type=text/css rel=stylesheet>
    <style media="screen" type="text/css">
        a.save{
            display: none;
        }

        * HTML a.save{ /* Только для браузера IE */
            display: inline;
        }
    </style>
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
            $blank = 'F112EK';
            //Запрос данных
            pbrf_get($_POST['F112ek'], $type, $blank);
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
    
    <h2 class="blank_name">Заполнение бланка Ф112ЭК для федеральных клиентов Почты России</h2>
        <div class="twelve columns">


<form class="twelve columns" method="post" action="" id="blanks">
<fieldset>
  <fieldset id="recipient" class="validationEngineContainer">
    <legend>Данные Получателя Денежных Средств</legend>
    <div class="row">
      <div class="two columns">
        <label class="inline right">Кому</label>
      </div>
      <div class="ten columns left">
        <input placeholder="Наименование юридического лица" id="to_name" class="twelve validate[required] capitalize" type="text" name="F112ek[to_name]" value='<?=$org_name?>' maxlength="40">
      </div>
    </div>
    <div class="row">
      <div class="two columns">
        <label class="inline right">Тип клиента</label>
      </div>
      <div class="ten columns left">
        <input placeholder="например, Федеральный клиент" id="to_type" class="twelve validate[required] capitalize" type="text" name="F112ek[to_type]" value="" maxlength="40">
      </div>
    </div>
    <div class="row">
      <div class="three columns">
        <label class="inline right">Почтовый индекс</label>
      </div>
      <div class="three columns left end">
        <input placeholder="000000" id="to_zip" class="twelve validate[required, custom[integer]]" type="text" name="F112ek[to_zip]" value="" maxlength="6">
      </div>
    </div>
    <div class="row">
      <div class="three columns">
        <label class="inline right">Телефон <b>+7</b></label>
      </div>
      <div class="three columns left end">
        <input placeholder="7777777777" id="to_phone" class="twelve" type="text" name="F112ek[to_phone]" value="<?=$data_person['tel']?>" maxlength="15">
      </div>
    </div>
  </fieldset>
  <fieldset id="sender" class="validationEngineContainer">
    <legend>Данные Отправителя Денежных Средств</legend>
    <div class="row">
      <div class="two columns">
        <label class="inline right">От кого</label>
      </div>
      <div class="four columns left">
        <input placeholder="Фамилия" id="from_surname" class="twelve validate[required] capitalize" type="text" name="F112ek[from_surname]" value="<?=$fio_new?>" maxlength="20">
      </div>
      <div class="six columns">
        <label class="inline left">Фамилия</label>
      </div>
    </div>
    <div class="row">
      <div class="two columns">
        <label class="inline right">&nbsp;</label>
      </div>
      <div class="six columns left">
        <input placeholder="Имя Отчество" id="from_name" class="twelve validate[required] capitalize" type="text" name="F112ek[from_name]" value="" maxlength="30">
      </div>
      <div class="four columns">
        <label class="inline left">Имя Отчество</label>
      </div>
    </div>
    <div class="row">
      <div class="three columns">
        <label class="inline right">Область, район</label>
      </div>
      <div class="five columns left end">
        <input placeholder="Название области, района" id="from_region" class="twelve" type="text" name="F112ek[from_region]" value="<?=$state_new?>">
      </div>
      <div class="one columns">
        <label class="inline right">Город</label>
      </div>
      <div class="three columns left end">
        <input placeholder="Название города" id="from_city" class="twelve validate[required]" type="text" name="F112ek[from_city]" value="<?=$city_new?>">
      </div>
    </div>
    <div class="row">
      <div class="two columns">
        <label class="inline right">Улица</label>
      </div>
      <div class="three columns left end">
        <input placeholder="Варшавское шоссе" id="from_street" class="twelve validate[required]" type="text" name="F112ek[from_street]" value="<?=$street_new?>">
      </div>
      <div class="one columns">
        <label class="inline right">Дом</label>
      </div>
      <div class="two columns left end">
        <input placeholder="номер" id="from_build" class="twelve validate[required,custom[numAfterLetter]]" type="text" name="F112ek[from_build]" value="<?=$house_new?>" maxlength="6">
      </div>
      <div class="two columns">
        <label class="inline right">Квартира</label>
      </div>
      <div class="two columns left end">
        <input placeholder="номер" id="from_appartment" class="twelve validate[custom[numAfterLetter]]" type="text" name="F112ek[from_appartment]" value="<?=$flat_new?>" maxlength="7">
      </div>
    </div>
    <div class="row">
      <div class="three columns">
        <label class="inline right">Почтовый индекс</label>
      </div>
      <div class="three columns left end">
        <input placeholder="000000" id="from_zip" class="twelve validate[required, custom[integer]]" type="text" name="F112ek[from_zip]" value="<?=$index_new?>" maxlength="6">
      </div>
    </div>
  </fieldset>
  <div id="other" class="validationEngineContainer">
  <fieldset>
    <legend>Почтовый перевод</legend>
    <div class="row">
      <div class="three columns">
        <label class="inline right">на сумму</label>
      </div>
      <div class="nine columns left">
        <div class="row">
          <div class="three columns">
            <input placeholder="0.99" id="sum_num" class="twelve validate[required,custom[number]] onlyNumber addPoint" type="text" name="F112ek[sum_num]" value="" rel="">
          </div>
          <div class="nine columns">
            <label class="inline left">цифрами (руб.)</label>
          </div>
        </div>
      </div>
    </div>
  </fieldset> 
    <fieldset>
    <legend>Kод идентификатора Федерального получателя</legend>
    <div class="row">
      <div class="four columns">
        <label class="inline right">Штриховой код</label>
      </div>
      <div class="eight columns left">
        <div class="row">
          <div class="seven columns">
            <input placeholder="000000" id="barcode" class="twelve validate[custom[number]]" type="text" name="F112ek[barcode]" maxlength="6" value="">
          </div>
        </div>
      </div>
    </div>
  </fieldset>
  
      <div class="but-send-pbrf">
            <input type="hidden" name="type_send" id="type_send">
            <button class="blank_button" onclick="pdf_pbrf()">Скачать PDF</button>
        </div>
      </fieldset><!--начало в шаблоне отдельного бланка-->
    </form>
  </div>
  </div>
</body>
</html>