<?php

$TitlePage = __('Редактирование записи').' #' . intval($_GET['id']);

// SQL
$PHPShopOrm = new PHPShopOrm($PHPShopModules->getParam("base.admlog.admlog_log"));

// Функция обновления
function actionUpdate() {
    global $PHPShopModules;

    // Карта таблиц БД
    $baseMap = array(
        'banner' => 'baners',
        'product' => 'products',
        'catalog' => 'categories'
    );

    // Выборка
    $PHPShopOrm = new PHPShopOrm($PHPShopModules->getParam("base.admlog.admlog_log"));
    $data = $PHPShopOrm->select(array('*'), array('id' => '=' . intval($_POST['rowID'])));

    if (is_array($data)) {

        if (!empty($baseMap[$data['file']]))
            $baseName = $baseMap[$data['file']];
        else
            $baseName = $data['file'];

        $contentCode = unserialize($data['content']);

        if (is_array($contentCode)) {

            $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base'][$baseName]);
            $PHPShopOrm->debug = false;
            //$PHPShopOrm->trace($contentCode);

            if (!empty($contentCode['delID'])) {
                $action = $PHPShopOrm->insert($contentCode);
                $nameHandler = 'Откат удаления';
            } else {

                $action = $PHPShopOrm->update($contentCode, array('id' => '=' . intval($_POST['rowID'])));
                $nameHandler = 'Откат изменения от '.PHPShopDate::dataV($data['date'], true);
            }

            // Пишем лог
            include_once('writelog.php');
            setLog(false, $nameHandler);
        }
    }
    
    header('Location: ?path='.$_GET['path']);
}

function unserializeArray(&$value) {
    $value = unserialize($value);
}

// Начальная функция загрузки
function actionStart() {
    global $PHPShopGUI, $PHPShopOrm, $PHPShopModules;
    
    // Настройки модуля
    $PHPShopOrmOption = new $PHPShopOrm($PHPShopModules->getParam("base.admlog.admlog_system"));
    $option = $PHPShopOrmOption->getOne();


    $PHPShopGUI->action_button['Восстановить данные'] = array(
        'name' => __('Восстановить данные'),
        'action' => 'saveID',
        'class' => 'btn btn-default btn-sm navbar-btn',
        'type' => 'submit',
        'icon' => 'glyphicon glyphicon-refresh'
    );


    // Выборка
    $data = $PHPShopOrm->select(array('*'), array('id' => '=' . intval($_GET['id'])));
    $contentTemp = unserialize($data['content']);
    
    array_walk_recursive($contentTemp, 'unserializeArray');
    
    
    $PHPShopGUI->setActionPanel(__('Запись журнала изменений от').' '.PHPShopDate::dataV($data['date'], true), false, $button);

    // Содержание закладки 1
    //$Tab1 = $PHPShopGUI->setField("Дата:", $PHPShopGUI->setInput("text", "name_new", PHPShopDate::dataV($data['date'], true), "left", 150));
    $Tab1.=$PHPShopGUI->setField("Пользователь:", $PHPShopGUI->setInput("text", "name_new", $data['user']));
    $Tab1.=$PHPShopGUI->setField("Действие:", $PHPShopGUI->setInput("text", "name_new", $data['title']));

    // Отладка
    ob_start();
    $PHPShopOrm->trace($contentTemp);
    $Tab3 = ob_get_clean();
     

    // Вывод формы закладки
    $PHPShopGUI->setTab(array("Данные", $Tab3));

    $ContentFooter.=$PHPShopGUI->setInput("hidden", "rowID", $data['id'], "right", 70, "", "but") .
            $PHPShopGUI->setInput("submit", "saveID", "Откатить", "right", 70, "", "but", "actionUpdate.modules.edit");

    $PHPShopGUI->setFooter($ContentFooter);
    return true;
}

// Обработка событий
$PHPShopGUI->getAction();

// Вывод формы при старте
$PHPShopGUI->setLoader($_POST['editID'], 'actionStart');
?>