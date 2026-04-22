<?php

// SQL
$PHPShopOrm = new PHPShopOrm($PHPShopModules->getParam("base.productlastview.productlastview_system"));

// Функция обновления
function actionUpdate() {
    global $PHPShopOrm,$PHPShopModules;
    
    // Настройки витрины
    $PHPShopModules->updateOption($_GET['id'], $_POST['servers']);

    if (empty($_POST['memory_new']))
        $_POST['memory_new'] = 0;
    $PHPShopOrm->debug = false;
    $action = $PHPShopOrm->update($_POST);
    header('Location: ?path=modules&install=check');
    return $action;
}

function actionStart() {
    global $PHPShopGUI, $PHPShopOrm;

    // Выборка
    $data = $PHPShopOrm->select();


    $e_value[] = array('метка вывода', 0, $data['enabled']);
    $e_value[] = array('слева', 2, $data['enabled']);
    $e_value[] = array('справа', 1, $data['enabled']);


    $Tab1 = $PHPShopGUI->setField('Заголовок блока', $PHPShopGUI->setInputText(false, 'title_new', $data['title']));
    $Tab1.=$PHPShopGUI->setField('Память товаров', $PHPShopGUI->setCheckbox('memory_new', 1, 'Хранить информацию по товарам в базе', $data['memory']));
    $Tab1.=$PHPShopGUI->setField('Место вывода', $PHPShopGUI->setSelect('enabled_new', $e_value, 150,true));
    $Tab1.=$PHPShopGUI->setField('Ширина иконки товара', $PHPShopGUI->setInputText(false, 'pic_width_new', $data['pic_width'], 100, 'px'));
    $Tab1.=$PHPShopGUI->setField('Количество товаров в блоке', $PHPShopGUI->setInputText(false, 'num_new', $data['num'], 100));

    $info = 'Для произвольной вставки элемента следует выбрать параметр вывода "Не выводить" и в ручном режиме вставить переменную
        <kbd>@productlastview@</kbd> в свой шаблон. Или через панель управления создайте текстовый блок, переключитесь в режим исходного кода (Система - Настройка - Режимы - Визуальный редактор),
        внесите метку @productlastview@ - теперь блок будет выводить список товаров в нужном вам месте.
        <p>Для персонализации формы вывода отредактируйте шаблоны <code>phpshop/modules/productlastview/templates/</code> или <code>phpshop/templates/имя_шаблона/modules/productlastview/templates/</code></p>';

    $Tab2 = $PHPShopGUI->setInfo($info);

    // Форма регистрации
    $Tab3 = $PHPShopGUI->setPay();

    // Вывод формы закладки
    $PHPShopGUI->setTab(array("Основное", $Tab1, true), array("Инструкция", $Tab2), array("О Модуле", $Tab3));

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