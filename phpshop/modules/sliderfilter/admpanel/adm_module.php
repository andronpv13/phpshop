<?php

// SQL
$PHPShopOrm = new PHPShopOrm($PHPShopModules->getParam("base.sliderfilter.sliderfilter_system"));

// Обновление версии модуля
function actionBaseUpdate() {
    global $PHPShopModules, $PHPShopOrm;
    $PHPShopOrm->clean();
    $option = $PHPShopOrm->select();
    $new_version = $PHPShopModules->getUpdate($option['version']);
    $PHPShopOrm->clean();
    $PHPShopOrm->update(array('version_new' => $new_version));
}

// Функция обновления
function actionUpdate() {
    global $PHPShopOrm, $PHPShopModules;

    // Настройки витрины
    $PHPShopModules->updateOption($_GET['id'], $_POST['servers']);

    $_POST['sort_new'] = serialize($_POST['sort_new']);

    $PHPShopOrm->debug = false;
    $action = $PHPShopOrm->update($_POST);
    header('Location: ?path=modules&id=' . $_GET['id']);
    return $action;
}


function actionStart() {
    global $PHPShopGUI, $PHPShopOrm;

    // Выборка
    $data = $PHPShopOrm->select();

    $info = 'Для вставки элемента следует в ручном режиме вставить переменную
        <kbd>@sliderFilter@</kbd> в файл вывода штатного фильтра <code>phpshop/templates/имя_шаблона/main/shop.tpl</code> или <code>phpshop/templates/имя_шаблона/product/product_page_list.tpl</code> своего шаблона вместо штатного кода вывода фильтра:
        <p>
        <pre>&lt;!-- Фасетный фильтр --&gt;
... html код фильтра ...    
&lt;!--/ Фасетный фильтр --&gt; </pre>
</p>
        <p>В настройках системы, меню <kbd>Настройки</kbd> - <kbd>Основные</kbd> должна стоять логика отображения фильтра - "Перекрестная с  множественным выбором".<br>Для персонализации формы вывода отредактируйте шаблоны в папке <code>phpshop/templates/имя_шаблона/modules/sliderfilter/templates/</code></p>';

    $Tab2 = $PHPShopGUI->setInfo($info);

    // Форма регистрации
    $Tab3 = $PHPShopGUI->setPay($data['serial'], false, $data['version'], true);

    // Вывод формы закладки
    $PHPShopGUI->setTab(array("Инструкция", $Tab2), array("О Модуле", $Tab3));

    // Вывод кнопок сохранить и выход в футер
    $ContentFooter = $PHPShopGUI->setInput("hidden", "rowID", $data['id']) .
            $PHPShopGUI->setInput("submit", "saveID", "Применить", "right", 80, "", "but", "actionUpdate.modules.edit");

    $PHPShopGUI->setFooter($ContentFooter);
    return true;
}

// Обработка событий
$PHPShopGUI->getAction();

// Вывод формы при старте
$PHPShopGUI->setLoader($_POST['saveID'], 'actionStart');
?>