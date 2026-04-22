<?php

// SQL
$PHPShopOrm = new PHPShopOrm($PHPShopModules->getParam("base.panorama360.panorama360_system"));

// Обновление версии модуля
function actionBaseUpdate() {
    global $PHPShopModules, $PHPShopOrm;
    $PHPShopOrm->clean();
    $option = $PHPShopOrm->select();
    $new_version = $PHPShopModules->getUpdate($option['version']);
    $PHPShopOrm->clean();
    $action = $PHPShopOrm->update(array('version_new' => $new_version));
    header('Location: ?path=modules&id='.$_GET['id']);
    return $action;
}

// Функция обновления
function actionUpdate() {
    global $PHPShopOrm,$PHPShopModules;
    
    // Настройки витрины
    $PHPShopModules->updateOption($_GET['id'], $_POST['servers']);


    $action = $PHPShopOrm->update($_POST);
    header('Location: ?path=modules&id='.$_GET['id']);
    return $action;
}


function actionStart() {
    global $PHPShopGUI, $PHPShopOrm;

    $PHPShopGUI->field_col = 2;

    // Выборка
    $data = $PHPShopOrm->select();
    
    if(empty($data['frame']))
        $data['frame']=28;
    
    $Tab1 = $PHPShopGUI->setField('Фреймов в спрайтах', $PHPShopGUI->setInputText(false, 'frame_new', $data['frame'],50));

    $info = 'Изображения товара ('.$data['frame'].' шт.) должны быть собраны в общий спрайт (<a href="../modules/panorama360/sample/sample.jpg" target="_blank" >пример спрайта</a>) и загружены в карточку товара через закладку <kbd>Панорама</kbd>. <br>'
            . 'В шаблон подробной карточки товара необходимо разместить переменную <code>@panorama360@</code> в удобное для ее вывода место.';

    $Tab2 = $PHPShopGUI->setInfo($info);
    $Tab3 = $PHPShopGUI->setPay($serial = false, false, $data['version'], true);


    // Вывод формы закладки
    $PHPShopGUI->setTab(array("Основное", $Tab1, true),array("Инструкция", $Tab2), array("О Модуле", $Tab3));

    // Вывод кнопок сохранить и выход в футер
    $ContentFooter =
            $PHPShopGUI->setInput("hidden", "rowID", $data['id']) .
            $PHPShopGUI->setInput("submit", "saveID", "Применить", "right", 80, "", "but", "actionUpdate.modules.edit");

    $PHPShopGUI->setFooter($ContentFooter);
    return true;
}

// Обработка событий 
$PHPShopGUI->getAction();

// Вывод формы при старте
$PHPShopGUI->setLoader($_POST['saveID'], 'actionStart');
?>