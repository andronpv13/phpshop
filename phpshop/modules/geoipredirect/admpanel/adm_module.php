<?php

// SQL
$PHPShopOrm = new PHPShopOrm($PHPShopModules->getParam("base.geoipredirect.geoipredirect_system"));

// Обновление версии модуля
function actionBaseUpdate() {
    global $PHPShopModules, $PHPShopOrm;
    $PHPShopOrm->clean();
    $option = $PHPShopOrm->select();
    $new_version = $PHPShopModules->getUpdate($option['version']);
    $PHPShopOrm->clean();
    $action = $PHPShopOrm->update(array('version_new' => $new_version));
    return $action;
}

// Функция обновления
function actionUpdate() {
    global $PHPShopOrm,$PHPShopModules;

    // Настройки витрины
    $PHPShopModules->updateOption($_GET['id'], $_POST['servers']);

    $PHPShopOrm->debug = false;
    $action = $PHPShopOrm->update($_POST);
    header('Location: ?path=modules&id=' . $_GET['id']);
    return $action;
}

function actionStart() {
    global $PHPShopGUI, $PHPShopOrm;

    // Выборка
    $data = $PHPShopOrm->select();

    $info = '<p>Для определения города по IP адресу используется бесплатная версия библиотеки <a href="http://sypexgeo.net" target="_blank">Sypex Geo</a>. Для более точного определения города рекомендуется приобрести коммерческую версию этой библиотеки. Файл данных городов <kbd>SxGeoCity.dat</kbd> должен находиться в  <code>phpshop/modules/geoipredirect/class/SxGeoCity.dat</code>. По причине большого размера <code>SxGeoCity.dat</code> в сборку этот файл не включен и должен быть скачен самостоятельно. При отсутствии файла <code>SxGeoCity.dat</code> работа модуля <kbd>GeoIP Redirect</kbd> невозможна.</p>';

    $Tab2 = $PHPShopGUI->setInfo($info);

    // Форма регистрации
    $Tab3 = $PHPShopGUI->setPay($data['serial'], false, $data['version'], true);

    // Вывод формы закладки
    $PHPShopGUI->setTab(array("Инструкция", $Tab2), array("О Модуле", $Tab3), array("Обзор адресов", null, '?path=modules.dir.geoipredirect'));

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