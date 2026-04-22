<?php

// SQL
$PHPShopOrm = new PHPShopOrm($PHPShopModules->getParam("base.countcat.countcat_system"));

// Обновление версии модуля
function actionBaseUpdate() {
    global $PHPShopModules, $PHPShopOrm;
    $PHPShopOrm->clean();
    $option = $PHPShopOrm->select();
    $new_version = $PHPShopModules->getUpdate($option['version']);
    $PHPShopOrm->clean();
    $PHPShopOrm->update(array('version_new' => $new_version));
}

// Обновление цен
function actionUpdateCount() {

    // Безопасность
    $cron_secure = md5($GLOBALS['SysValue']['connect']['host'] . $GLOBALS['SysValue']['connect']['dbase'] . $GLOBALS['SysValue']['connect']['user_db'] . $GLOBALS['SysValue']['connect']['pass_db']);

    $protocol = 'http://';
    if (!empty($_SERVER['HTTPS']) && 'off' !== strtolower($_SERVER['HTTPS'])) {
        $protocol = 'https://';
    }

    $true_path = $protocol . $_SERVER['SERVER_NAME'] . $GLOBALS['SysValue']['dir']['dir'] . "/phpshop/modules/countcat/cron/count.php?s=" . $cron_secure;

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $true_path);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_exec($ch);
    curl_close($ch);
}

// Функция обновления
function actionUpdate() {
    global $PHPShopOrm, $PHPShopModules;

    // Настройки витрины
    $PHPShopModules->updateOption($_GET['id'], $_POST['servers']);

    if (empty($_POST['enabled_new']))
        $_POST['enabled_new'] = 0;
    $action = $PHPShopOrm->update($_POST);

    if (!empty($_POST['clean'])) {
        $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['categories']);
        $PHPShopOrm->update(array('count' => '0'), false, false);
    }

    header('Location: ?path=modules&id=' . $_GET['id']);
    return $action;
}

function actionStart() {
    global $PHPShopGUI, $PHPShopOrm,$TitlePage, $select_name;

    // Выборка
    $data = $PHPShopOrm->select();
    $PHPShopGUI->field_col = 1;

    $PHPShopGUI->action_button['Пересчитать'] = [
        'name' => __('Пересчитать'),
        'class' => 'btn btn-default btn-sm navbar-btn ',
        'type' => 'submit',
        'action' => 'exportID',
        'icon' => 'glyphicon glyphicon-refresh'
    ];
    $PHPShopGUI->setActionPanel($TitlePage, $select_name, ['Пересчитать', 'Сохранить и закрыть']);



    $Tab2 = '
    <h4>Настройка</h4>
   <ol>
    <li>При первом включении модуля будет произведен автоматический расчет товаров в подкаталогах с занесением в базу модуля. 
        Для дальнейшей корректировки этого параметра используйте одноименное поле в карточке редактировния каталога и подкаталога в закладке
        <kbd>Модули</kbd> - <kbd>Count</kbd>.</li>
        <li>Для автоматического подсчета товаров по расписанию следует добавить новую задачу в модуль <a href="https://docs.phpshop.ru/moduli/razrabotchikam/cron" target="_blank">Задачи</a> с адресом запускаемого файла <code>phpshop/modules/countcat/cron/count.php</code>.</li>
     </ol> ';

    $Tab1 = $PHPShopGUI->setField('Вывод', $PHPShopGUI->setCheckbox("enabled_new", 1, 'Добавить количество товара к имени каталога', $data['enabled']) . '<br>' .
            $PHPShopGUI->setCheckbox("clean", 1, 'Обнулить ранее установленные значения кол-ва товара в категориях', 0));

    $Tab3 = $PHPShopGUI->setPay(false, false, $data['version'], false);

    // Вывод формы закладки
    $PHPShopGUI->setTab(array("Настройки", $Tab1), array("Инструкция", $PHPShopGUI->setInfo($Tab2)), array("О Модуле", $Tab3));

    // Вывод кнопок сохранить и выход в футер
    $ContentFooter = $PHPShopGUI->setInput("hidden", "rowID", $data['id']) .
            $PHPShopGUI->setInput("submit", "exportID", "Применить", "right", 80, "", "but", "actionUpdateCount.modules.edit");
            $PHPShopGUI->setInput("submit", "saveID", "Применить", "right", 80, "", "but", "actionUpdate.modules.edit");

    $PHPShopGUI->setFooter($ContentFooter);
    return true;
}

// Обработка событий 
$PHPShopGUI->getAction();

// Вывод формы при старте
$PHPShopGUI->setLoader($_POST['saveID'], 'actionStart');
?>