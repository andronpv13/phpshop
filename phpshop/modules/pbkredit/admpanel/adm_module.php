<?php

// SQL
$PHPShopOrm = new PHPShopOrm($PHPShopModules->getParam("base.pbkredit.pbkredit_system"));

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

    $PHPShopOrm->debug = false;
    $action = $PHPShopOrm->update($_POST);
    header('Location: ?path=modules&id='.$_GET['id']);
    return $action;
}

function actionStart() {
    global $PHPShopGUI, $PHPShopOrm;

    // Выборка
    $data = $PHPShopOrm->select();
    $PHPShopGUI->field_col = 3;

    $Tab1 = $PHPShopGUI->setField('Идентификатор торговой точки', $PHPShopGUI->setInputText(false, 'tt_code_new', $data['tt_code'],300));
    $Tab1.=$PHPShopGUI->setField('Адрес пункта выдачи товара', $PHPShopGUI->setInputText(false, 'tt_name_new', $data['tt_name'], 300));

    $info = '<h4>Настройка модуля</h4>
       <ol>
        <li>Заключить договор с <a href="https://www.pochtabank.ru" target="_blank">Почта Банк</a>.
        <li>Внести Идентификатор торговой точки полученный от Банка.</li>
        <li>Заполнить поле "Адрес пункта выдачи товара".</li>
        <li>Для вывода кнопки покупки в кредит добавьте переменную <kbd>@pbkreditUid@</kbd> в файл своего шаблона <mark>phpshop/templates/имя_шаблона/product/main_product_forma_full.tpl</mark>.</li>
        <li>Шаблон кнопки заявки в кредит редактируется в файле <mark>phpshop/modules/pbkredit/templates/template.tpl</mark></li>
        </ol>';

    $Tab2 = $PHPShopGUI->setInfo($info);

    // Вывод формы закладки
    $PHPShopGUI->setTab(array("Основное", $Tab1, true), array("Инструкция", $Tab2), array("О Модуле", $PHPShopGUI->setPay(false, false, $data['version'], false)));

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
$PHPShopGUI->setLoader($_POST['editID'], 'actionStart');
?>