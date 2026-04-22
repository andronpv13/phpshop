<?php

// SQL
$PHPShopOrm = new PHPShopOrm($PHPShopModules->getParam("base.sortproduct.sortproduct_forms"));

// Функция записи
function actionInsert() {
    global $PHPShopOrm;
    if(empty($_POST['num_new'])) $_POST['num_new']=1;
    if(empty($_POST['enabled_new'])) $_POST['enabled_new']=0;

    $action = $PHPShopOrm->insert($_POST);
    header('Location: ?path=' . $_GET['path']);
    return $action;
}

/**
 * Выбор характеристики
 */
function getSortValue($n) {
    global $PHPShopGUI;
    $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['sort_categories']);
    $PHPShopOrm->debug=false;
    $data = $PHPShopOrm->select(array('*'),array('filtr'=>"='1'",'goodoption'=>"!='1'"),array('order'=>'num'),array('limit'=>100));
    if(is_array($data))
        foreach($data as $row) {

            if($n == $row['id']) $sel='selected';
            else $sel=false;

            $value[]=array($row['name'],$row['id'],$sel);
        }

    return $PHPShopGUI->setSelect('sort_new',$value,300);
}

// Начальная функция загрузки
function actionStart() {
    global $PHPShopGUI, $PHPShopOrm;

    // Выборка
    $data = array();


    $Tab1 = $PHPShopGUI->setField('Порядок:', $PHPShopGUI->setInputText(null, 'num_new', $data['num'], '100'));
    $Tab1.=$PHPShopGUI->setField('Количество ссылок:', $PHPShopGUI->setInputText(null, 'items_new', $data['items'], '100'));
    $Tab1.=$PHPShopGUI->setField('Статус:', $PHPShopGUI->setCheckbox('enabled_new', 1, 'Включить', $data['enabled']));
    $Tab1.=$PHPShopGUI->setField('Характеристика', getSortValue($data['sort']));
    $Tab1.=$PHPShopGUI->setField('Значение', $PHPShopGUI->setInputText(false, 'value_name_new', $data['value_name'], 300) . $PHPShopGUI->setHelp(__('Введите значение или ID выбранной').' <a href="?path=sort"><span class="glyphicon glyphicon-share-alt"></span>'.__('Характеристики').'</a>',false,false));

    // Вывод формы закладки
    $PHPShopGUI->setTab(array("Основное", $Tab1, true));

    // Вывод кнопок сохранить и выход в футер
    $ContentFooter=$PHPShopGUI->setInput("submit","saveID","Сохранить","right",false,false,false,"actionInsert.modules.create");

    $PHPShopGUI->setFooter($ContentFooter);
    return true;
}



// Обработка событий 
$PHPShopGUI->getAction();

// Вывод формы при старте
$PHPShopGUI->setLoader($_POST['saveID'], 'actionStart');

?>


