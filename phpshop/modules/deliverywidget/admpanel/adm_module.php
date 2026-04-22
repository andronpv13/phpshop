<?php

// SQL
$PHPShopOrm = new PHPShopOrm($PHPShopModules->getParam("base.deliverywidget.deliverywidget_system"));

// Функция обновления
function actionUpdate() {
    global $PHPShopOrm, $PHPShopModules;

    // Настройки витрины
    $PHPShopModules->updateOption($_GET['id'], $_POST['servers']);

    $action = $PHPShopOrm->update($_POST);
    header('Location: ?path=modules&id=' . $_GET['id']);
    return $action;
}

// Обновление версии модуля
function actionBaseUpdate() {
    global $PHPShopModules, $PHPShopOrm;
    $PHPShopOrm->clean();
    $option = $PHPShopOrm->select();
    $new_version = $PHPShopModules->getUpdate($option['version']);
    $PHPShopOrm->clean();
    $PHPShopOrm->update(array('version_new' => $new_version));
}

// Функция очистки кеша
function actionClean() {
    global $PHPShopModules;

    if ($_POST['cache_new'] == 0) {
        $PHPShopOrm = new PHPShopOrm($PHPShopModules->getParam("base.deliverywidget.deliverywidget_cache"));
        $PHPShopOrm->delete(null);
    } else if ($_POST['cache_new'] == 1 and class_exists('Memcached')) {
        $cache = new Memcached();
        $cache->addServer($_POST['server_new'], $_POST['port_new']);
        $cache->flush();
    }
}

function actionStart() {
    global $PHPShopGUI, $PHPShopOrm, $TitlePage, $select_name;

    // Выборка
    $data = $PHPShopOrm->select();

    if ($data['cache'] != 2)
        $PHPShopGUI->action_button['Очистить кеш'] = [
            'name' => __('Очистить кеш'),
            'locale' => true,
            'action' => 'cleanID',
            'class' => 'btn btn-default btn-sm navbar-btn',
            'type' => 'submit',
            'icon' => 'glyphicon glyphicon-erase'
        ];
    $PHPShopGUI->setActionPanel($TitlePage, $select_name, ['Очистить кеш', 'Сохранить и закрыть']);



    if (class_exists('Memcached') or class_exists('Memcache')) {
        $disabled = false;
    } else {
        $disabled = 'disabled="disabled"';
    }

    $Tab1 .= $PHPShopGUI->setField('Хранение кеша', $PHPShopGUI->setSelect('cache_new', [
                ['База данных MySQL', 0, $data['cache']],
                ['Сервер кеширования Memcached', 1, $data['cache'], $disabled],
                ['Без кеша', 2, $data['cache']],
                    ], 300));

    $Tab1 .= $PHPShopGUI->setField("Вес по умолчанию:", $PHPShopGUI->setInputText('гр', 'weight_new', (int) $data['weight'], 100));
    $Tab1 .= $PHPShopGUI->setField("Почтовый индекс города отправителя:", $PHPShopGUI->setInputText(false, 'index_from_new', (int) $data['index_from'], 100));
    $Tab1 .= $PHPShopGUI->setField("Кол-во дней хранения кеша:", $PHPShopGUI->setInputText(false, 'time_new', (int) $data['time'], 50, false, false, false, '1'));

    // Форма регистрации
    $Tab3 = $PHPShopGUI->setPay(false, false, $data['version'], true);

    // Инструкция
    $Tab2 = $PHPShopGUI->loadLib('tab_info', $data, '../modules/' . $_GET['id'] . '/admpanel/');

    // Вывод формы закладки
    $PHPShopGUI->setTab(array("Основное", $Tab1, true), array("Инструкция", $Tab2), array("О Модуле", $Tab3));

    // Вывод кнопок сохранить и выход в футер
    $ContentFooter = $PHPShopGUI->setInput("hidden", "rowID", $data['id']) .
            $PHPShopGUI->setInput("submit", "saveID", "Применить", "right", 80, "", "but", "actionUpdate.modules.edit") .
            $PHPShopGUI->setInput("submit", "cleanID", "Применить", "right", 80, "", "but", "actionClean.modules.edit");

    $PHPShopGUI->setFooter($ContentFooter);
    return true;
}

// Обработка событий
$PHPShopGUI->getAction();

// Вывод формы при старте
$PHPShopGUI->setLoader($_POST['saveID'], 'actionStart');
?>