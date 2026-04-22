<?php

$TitlePage = __('Создание новой кнопки');

// SQL
$PHPShopOrm = new PHPShopOrm($PHPShopModules->getParam("base.button.button_forms"));


// Функция записи
function actionInsert() {
    global $PHPShopOrm;
    if(empty($_POST['num_new'])) $_POST['num_new']=1;
    if(empty($_POST['enabled_new'])) $_POST['enabled_new']=0;

    $action = $PHPShopOrm->insert($_POST);
    
    header('Location: ?path=' . $_GET['path']);
    
    return $action;
}

// Начальная функция загрузки
function actionStart() {
    global $PHPShopGUI,$PHPShopOrm,$PHPShopModules,$PHPShopSystem;
    
    $PHPShopOrmOption = new PHPShopOrm($PHPShopModules->getParam("base.button.button_system"));
    $option = $PHPShopOrmOption->select();

    // Выборка
    $data['name']=__('Новая кнопка');
    $data['enabled']=1;
    $data['num']=1;
    

    $PHPShopGUI->field_col = 3;
    $Tab1 = $PHPShopGUI->setField('Название', $PHPShopGUI->setInputText(false, 'name_new', $data['name']));

    $Tab1.= $PHPShopGUI->setField('Приоритет', $PHPShopGUI->setInputText('№', 'num_new', $data['num'], '100'));
    $Tab1.= $PHPShopGUI->setField('Статус',  $PHPShopGUI->setCheckbox('enabled_new', 1, null, $data['enabled']));
    
    // Редактор 
    if(empty($option['editor']))
        $editor = 'ace';
    else $editor = $PHPShopSystem->getSerilizeParam("admoption.editor");
    
    $PHPShopGUI->setEditor($editor, true);
    $oFCKeditor = new Editor('content_new');
    $oFCKeditor->Height = '320';
    $oFCKeditor->Value = $data['content'];

    $Tab1.=$PHPShopGUI->setField('Содержание', $oFCKeditor->AddGUI());

    // Вывод формы закладки
    $PHPShopGUI->setTab(array("Основное",$Tab1,true,false,true));

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