<?php

// SQL
$PHPShopOrm = new PHPShopOrm($PHPShopModules->getParam("base.1c.1c_system"));

// Обновление версии модуля
function actionBaseUpdate() {
    global $PHPShopModules, $PHPShopOrm;
    $PHPShopOrm->clean();
    $option = $PHPShopOrm->select();
    $new_version = $PHPShopModules->getUpdate(number_format($option['version'], 1, '.', false));
    $PHPShopOrm->clean();
    $PHPShopOrm->update(array('version_new' => $new_version));
}

function actionStart() {
    global $PHPShopGUI, $PHPShopOrm;
    
    // Выборка
    $data = $PHPShopOrm->select();

    // Интструкция
    $info='<p>
        <p><h4>Как подключиться к 1С?</h4>
        <ol>
        <li>Воспользуйтесь <a href="https://docs.phpshop.ru/sinkhronizaciya-s-1s/commerceml" target="_blank">инструкцией по подключению</a>
        </ol>
        </p>
        <p><h4>Как подключиться к МойСклад?</h4>
        <ol>
        <li>Воспользуйтесь <a href="https://docs.phpshop.ru/sinkhronizaciya-s-1s/sinkhronizaciya-s-moi-sklad" target="_blank">инструкцией по подключению</a>
        </ol>
        </p>
';
    
    $Tab3 = $PHPShopGUI->setPay(false, false, $data['version'], false);

    // Вывод формы закладки
    $PHPShopGUI->setTab(array("Инструкция", $PHPShopGUI->setCollapse('',$info),true), array("Обмен данными", null, '?path=system.sync'), array("Журнал операций", null, '?path=report.crm'));

    // Вывод кнопок сохранить и выход в футер
    $ContentFooter =
            $PHPShopGUI->setInput("hidden", "rowID", $data['id']) .
            $PHPShopGUI->setInput("submit", "saveID", "Применить", "right", 80, "", "but", "actionUpdate.modules.edit");

    $PHPShopGUI->setFooter($ContentFooter);
    return true;
}

// Функция обновления
function actionUpdate() {
    global $PHPShopOrm,$PHPShopModules;
    
    // Настройки витрины
    $PHPShopModules->updateOption($_GET['id'], $_POST['servers']);

    $PHPShopOrm->debug = false;
    $_POST['region_data_new']=1;

    if (empty($_POST["manual_control_new"]))
        $_POST["manual_control_new"] = 0;

    $action = $PHPShopOrm->update($_POST);
    header('Location: ?path=modules&id=' . $_GET['id']);
    return $action;
}

// Обработка событий
$PHPShopGUI->getAction();

// Вывод формы при старте
$PHPShopGUI->setLoader($_POST['saveID'], 'actionStart');
?>