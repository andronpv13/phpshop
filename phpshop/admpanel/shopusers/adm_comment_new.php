<?php

$TitlePage = __('Создание комментария');
$PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['comment']);
PHPShopObj::loadClass('user');

// Стартовый вид
function actionStart() {
    global $PHPShopGUI, $PHPShopOrm, $PHPShopModules, $PHPShopSystem,$TitlePage;


    // Размер названия поля
    $PHPShopGUI->field_col = 3;

    $PHPShopGUI->addJSFiles('./js/bootstrap-datetimepicker.min.js',  './shopusers/gui/shopusers.gui.js');
    $PHPShopGUI->addCSSFiles('./css/bootstrap-datetimepicker.min.css');

    $PHPShopGUI->setActionPanel(__("Покупатели") . ' / ' .$TitlePage, null, array('Сохранить и закрыть'), false);

    $PHPShopGUI->setEditor($PHPShopSystem->getSerilizeParam("admoption.editor"));
    $oFCKeditor = new Editor('content_new');
    $oFCKeditor->Height = '300';
    $oFCKeditor->Value = null;
    
     $user='<div class="form-group form-group-sm ">
        <label class="col-sm-3 control-label">'.__('ФИО').':</label><div class="col-sm-9">
        <input data-set="3" name="name_new" maxlength="50" class="search_user form-control input-sm" type="search" data-trigger="manual" data-container="body" data-toggle="popover" data-placement="bottom" data-html="true"  data-content="" placeholder="'.__('Найти...').'" value="" required="">
        <input name="user_new" type="hidden">
     </div></div> ';
     
    $data['rate']=5;
    $rate_value[] = array(1, 1, $data['rate']);
    $rate_value[] = array(2, 2, $data['rate']);
    $rate_value[] = array(3, 3, $data['rate']);
    $rate_value[] = array(4, 4, $data['rate']);
    $rate_value[] = array(5, 5, $data['rate']);

    // Содержание закладки 1
    $Tab1 = $PHPShopGUI->setCollapse('Информация', $user .
            $PHPShopGUI->setField("Дата", $PHPShopGUI->setInputDate("datas_new", PHPShopDate::get($data['datas']), 'width:200px')) .
            $PHPShopGUI->setField("Товар", $PHPShopGUI->setInput('text.required', "product_name", null,null,false,false,false,false, false, '<a href="#" data-target="#product_name"  class="product-search"><span class="glyphicon glyphicon-search"></span> ' . __('Выбрать') . '</a>')) .
            $PHPShopGUI->setField("Статус", $PHPShopGUI->setCheckbox("enabled_new", 1, null, 1)) .
            $PHPShopGUI->setField("Рейтинг", $PHPShopGUI->setSelect('rate_new', $rate_value, 50)) .
            $PHPShopGUI->setField("Комментарий", $oFCKeditor->AddGUI(). $PHPShopGUI->setAIHelpButton('content_new', 300, 'product_comment', 'product_name')) 
          
    );


    // Запрос модуля на закладку
    $PHPShopModules->setAdmHandler(__FILE__, __FUNCTION__, $data);

    // Вывод формы закладки
    $PHPShopGUI->setTab(array("Основное", $Tab1));

    // Вывод кнопок сохранить и выход в футер
    $ContentFooter = 
            $PHPShopGUI->setInput("hidden", "parent_id_new", null, "right", 70, "", "but") .
            $PHPShopGUI->setInput("hidden", "user_id_new", null, "right", 70, "", "but") .
            $PHPShopGUI->setInput("submit", "saveID", "Применить", "right", 80, "", "but", "actionInsert.shopusers.create");


    // Футер
    $PHPShopGUI->setFooter($ContentFooter);
    return true;
}


// Функция обновления
function actionInsert() {
    global $PHPShopOrm, $PHPShopModules;
    
    if(empty($_POST['parent_id_new']) or empty($_POST['user_id_new']))
       header('Location: ?path=' . $_GET['path'].'&action=new');

    $_POST['datas_new'] = PHPShopDate::GetUnixTime($_POST['datas_new']);

           
    // Перехват модуля
    $PHPShopModules->setAdmHandler(__FILE__, __FUNCTION__, $_POST);

    $action = $PHPShopOrm->insert($_POST);
    header('Location: ?path=' . $_GET['path']);
    return $action;
}

/**
 * Пересчет рейтинга товара
 */
function ratingUpdate() {

    if (empty($_POST['parentID'])) {
        return false;
    }

    $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['comment']);
    $PHPShopOrm->debug = false;

    $result = $PHPShopOrm->query("select avg(rate) as rate, count(id) as num from " . $GLOBALS['SysValue']['base']['comment'] . " WHERE parent_id=" . intval($_POST['parentID']) . " AND enabled='1' AND rate>0 group by parent_id LIMIT 1");
    if (mysqli_num_rows($result)) {
        $row = mysqli_fetch_array($result);
        $rate = round($row['rate'], 1);
        $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['products']);
        $PHPShopOrm->debug = false;
        $PHPShopOrm->update(array('rate_new' => $rate, 'rate_count_new' => $row['num']), array('id' => '=' . $_POST['parentID']));
    } else {
        $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['products']);
        $PHPShopOrm->debug = false;
        $PHPShopOrm->update(array('rate_new' => 0, 'rate_count_new' => 0), array('id' => '=' . $_POST['parentID']));
    }
}


// Обработка событий
$PHPShopGUI->getAction();

// Вывод формы при старте
$PHPShopGUI->setLoader($_POST['saveID'], 'actionStart');
?>