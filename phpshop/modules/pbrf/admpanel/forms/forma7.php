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
          //Улица дом и квартира, меняем данные
            if($_POST['F116']['from_street']!='' or $_POST['F116']['from_build']!='' or $_POST['F116']['from_appartment']!='')
              $_POST['F116']['from_city'] = $_POST['F116']['from_city'].' '.$_POST['F116']['from_street'].' д.'.$_POST['F116']['from_build'].' кв.'.$_POST['F116']['from_appartment'];

            if($_POST['F116']['whom_build']!='' or $_POST['F116']['whom_appartment']!='')
              $_POST['F116']['whom_street'] = $_POST['F116']['whom_street'].' д.'.$_POST['F116']['whom_build'].' кв.'.$_POST['F116']['whom_appartment'];

            //Бланк
            $blank = 'F116';
            //Запрос данных
            pbrf_get($_POST['F116'], $type, $blank);
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
    
    <h2 class="blank_name">Заполнение бланка Посылки Ф.116</h2>
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
          <input placeholder="Фамилия Имя Отчество" id="whom" type="text" class="twelve validate[required] capitalize" name="F116[whom]" value="<?=$fio_new?>" maxlength="36">
        </div>
        <div class="four columns">
          <label class="inline left">Фамилия Имя Отчество</label>
        </div>
      </div>
      <div class="row">
        <div class="two columns">
          <label class="inline right">Город</label>
        </div>
        <div class="ten columns">
          <input placeholder="Название города" id="whom_city" type="text" class="twelve" name="F116[whom_city]" value="<?=$city_new?>">
        </div>
      </div>
      <div class="row">
        <div class="two columns">
          <label class="inline right">Улица</label>
        </div>
        <div class="ten columns">
          <input placeholder="Название улицы" id="whom_street" type="text" class="twelve" name="F116[whom_street]" value="<?=$street_new?>">
        </div>
      </div>
      <div class="row">
        <div class="two columns">
          <label class="inline right">Дом</label>
        </div>
        <div class="two columns">
          <input placeholder="номер" id="whom_build" type="text" name="F116[whom_build]" class="twelve validate[custom[numAfterLetter]]" value="<?=$house_new?>" maxlength="10">
        </div>
        <div class="two columns">
          <label class="inline right">Корпус</label>
        </div>
        <div class="two columns">
          <input id="whom_house" type="text" name="F116[whom_house]" class="twelve validate[custom[numAfterLetter],custom[onlyLetterNumber]]" value="" maxlength="4">
        </div>
        <div class="two columns">
          <label class="inline right">Квартира</label>
        </div>
        <div class="two columns end">
          <input placeholder="номер" id="whom_appartment" type="text" name="F116[whom_appartment]" class="twelve validate[custom[numAfterLetter]]" value="<?=$flat_new?>" maxlength="7">
        </div>
      </div>
      <div class="row">
        <div class="two columns">
          <label class="inline right">Страна</label>
        </div>
        <div class="four columns left end">
          <input placeholder="Название страны" id="whom_country" class="twelve validate[required] capitalize" type="text" name="F116[whom_country]" value="<?=$country_new?>">
        </div>
      </div>
      <div class="row">
        <div class="three columns">
          <label class="inline right">Почтовый индекс</label>
        </div>
        <div class="three columns left end">
          <input id="whom_zip" placeholder="000000" type="text" name="F116[whom_zip]" class="twelve left validate[required,custom[integer]]" maxlength="6" value="<?=$index_new?>">
        </div>
    </div></fieldset>
    <fieldset id="sender" class="validationEngineContainer">
      <legend>Данные отправителя</legend>
      <div class="row">
        <div class="two columns">
          <label class="inline right">От кого</label>
        </div>
        <div class="six columns left">
          <input placeholder="Фамилия Имя Отчество" id="from_surname" type="text" class="twelve validate[required] capitalize" name="F116[from_surname]" value="<?=$data_person['surname']?> <?=$data_person['name']?> <?=$data_person['name2']?>" maxlength="50">
        </div>
        <div class="four columns">
          <label class="inline left">Фамилия Имя Отчество</label>
        </div>
      </div>
      <div class="row">
        <div class="two columns">
          <label class="inline right">Город</label>
        </div>
        <div class="ten columns">
          <input placeholder="Название города" id="from_city" type="text" class="twelve" name="F116[from_city]" value="<?=$data_person['city']?>">
        </div>
      </div>
      <div class="row">
        <div class="two columns">
          <label class="inline right">Улица</label>
        </div>
        <div class="ten columns">
          <input placeholder="Название улицы" id="from_street" type="text" class="twelve" name="F116[from_street]" value="<?=$data_person['street']?>">
        </div>
      </div>
      <div class="row">
        <div class="two columns">
          <label class="inline right">Дом</label>
        </div>
        <div class="two columns">
          <input placeholder="номер" id="from_build" type="text" name="F116[from_build]" class="twelve validate[custom[numAfterLetter]]" value="<?=$data_person['build']?>" maxlength="10">
        </div>
        <div class="two columns">
          <label class="inline right">Корпус</label>
        </div>
        <div class="two columns">
          <input id="from_house" type="text" name="F116[from_house]" class="twelve validate[custom[numAfterLetter],custom[onlyLetterNumber]]" value="" maxlength="4">
        </div>
        <div class="two columns">
          <label class="inline right">Квартира</label>
        </div>
        <div class="two columns end">
          <input placeholder="номер" id="from_appartment" type="text" name="F116[from_appartment]" class="twelve validate[custom[numAfterLetter]]" value="<?=$data_person['appartment']?>" maxlength="7">
        </div>
      </div>

      <div class="row">
        <div class="two columns">
          <label class="inline right">Страна</label>
        </div>
        <div class="four columns left end">
          <input placeholder="Название страны" id="from_country" class="twelve validate[required] capitalize" type="text" name="F116[from_country]" value="<?=$data_person['country']?>">
        </div>
      </div>

      <div class="row">
        <div class="three columns">
          <label class="inline right">Почтовый индекс</label>
        </div>
        <div class="three columns left end">
          <input placeholder="000000" id="from_zip" type="text" name="F116[from_zip]" class="twelve left validate[required,custom[integer]]" maxlength="6" value="<?=$data_person['zip']?>">
        </div>
      </div>
    </fieldset>
    <div id="other" class="validationEngineContainer">
    <fieldset id="money" class="validationEngineContainer">
      <div class="row">
        <div class="five columns">
          <label class="inline right">Сумма объявленной ценности</label>
        </div>
        <div class="three columns left end">
          <input placeholder="0.99" id="declared_value" class="twelve validate[custom[number]] onlyNumber addPoint" type="text" name="F116[declared_value]" value="">
        </div>
      </div>

      <div class="row">
        <div class="five columns">
          <label class="inline right">Сумма наложенного платежа</label>
        </div>
        <div class="three columns left end">
          <input placeholder="0.99" id="COD_amount" class="twelve validate[custom[number]] onlyNumber addPoint" type="text" name="F116[COD_amount]" value="">
        </div>
      </div>
    </fieldset>
    <fieldset id="document" class="validationEngineContainer">
      <legend>Предъявленный документ</legend>
      <div class="row">
        <div class="four columns">
          <label class="inline right">Наименование документа</label>
        </div>
        <div class="two columns left">
          <input placeholder="наименование" id="document" class="twelve" type="text" name="F116[document]" maxlength="7" value="<?=$data_person['document']?>">
        </div>
        <div class="one columns">
          <label class="inline right">Серия</label>
        </div>
        <div class="two columns left">
          <input placeholder="ЕВ" id="document_serial" class="twelve" type="text" name="F116[document_serial]" maxlength="4" value="<?=$data_person['document_serial']?>">
        </div>
        <div class="one columns">
          <label class="inline right">№</label>
        </div>
        <div class="two columns left">
          <input placeholder="234523" id="document_number" class="twelve left" type="text" name="F116[document_number]" maxlength="6" value="<?=$data_person['document_number']?>">
        </div>
      </div>
      <div class="row">
        <div class="four columns">
          <label class="inline right">Выдан</label>
        </div>
        <div class="two columns left">
          <input placeholder="00.00" id="document_day" type="text" class="twelve" name="F116[document_day]" value="<?=$data_person['document_day']?>">
        </div>
        <div class="one columns">
          <label class="inline right">20</label>
        </div>
        <div class="two columns left">
          <input placeholder="99" id="document_year" type="text" class="twelve" name="F116[document_year]" maxlength="2" value="<?=$data_person['document_year']?>">
        </div>
        <div class="one columns end">
          <label class="inline left">г.</label>
        </div>
      </div>
      <div class="row">
        <div class="twelve columns">
          <label>Наименование учреждения выдающего документ</label>
          <input placeholder="Наименование учреждения" id="document_issued_by" type="text" name="F116[document_issued_by]" class="twelve" value="<?=$data_person['document_issued_by']?>" maxlength="100">
        </div>
      </div>
    </fieldset>
    </div><!--other-->
        

<!-- закрытие тегов после кнопок
  </fieldset>
</form> -->     
    <div class="but-send-pbrf">
            <input type="hidden" name="type_send" id="type_send">
            <button class="blank_button" onclick="print_pbrf()">Для печати</button> 
            <button class="blank_button" onclick="pdf_pbrf()">Скачать PDF</button>
        </div>
      </fieldset><!--начало в шаблоне отдельного бланка-->
    </form>
  </div>
  </div>
    
</body>
</html>