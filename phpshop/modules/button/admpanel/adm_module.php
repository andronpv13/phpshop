<?php

// SQL
$PHPShopOrm = new PHPShopOrm($PHPShopModules->getParam("base.button.button_system"));

// Функция обновления
function actionUpdate() {
    global $PHPShopOrm,$PHPShopModules;
    
     // Настройки витрины
    $PHPShopModules->updateOption($_GET['id'], $_POST['servers']);
    
    if (empty($_POST['enabled_new']))
        $_POST['enabled_new'] = 0;
    
    if (empty($_POST['editor_new']))
        $_POST['editor_new'] = 0;
    
    $action = $PHPShopOrm->update($_POST);
    header('Location: ?path=modules&id='.$_GET['id']);
    return $action;
}


// Начальная функция загрузки
function actionStart() {
    global $PHPShopGUI, $PHPShopOrm;

    // Выборка
    $data = $PHPShopOrm->select();

    switch ($data['enabled']) {
        case 0: $s0 = 'selected';
            break;
        case 1: $s1 = 'selected';
            break;
        case 2: $s2 = 'selected';
            break;
        case 3: $s3 = 'selected';
            break;
    }

    $value[] = array(__('счетчики'), 0, $s0);
    $value[] = array(__('подвал'), 1, $s1);
    $value[] = array(__('слева'), 2, $s2);
    $value[] = array(__('справа'), 3, $s3);


    $info = 'Для произвольной вставки элемента, следует выбрать параметр вывода "Счетчики" и вставить переменную
        <kbd>@button@</kbd> в свой шаблон в нужное вам место.';

    $Tab1=$PHPShopGUI->setField('Расположение блока', $PHPShopGUI->setSelect('enabled_new', $value));
    $Tab1.=$PHPShopGUI->setField('Визуальный редактор', $PHPShopGUI->setCheckbox('editor_new', 1,null,$data['editor']));

    // Содержание закладки 2
    $Tab3 = $PHPShopGUI->setPay();

    // Вывод формы закладки
    $PHPShopGUI->setTab(array("Основное", $Tab1), array("О Модуле", $Tab3),array("Кнопки", null,'?path=modules.dir.button'));

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