<?php

// SQL
$PHPShopOrm = new PHPShopOrm($PHPShopModules->getParam("base.partner.partner_system"));

// Обновление версии модуля
function actionBaseUpdate() {
    global $PHPShopModules, $PHPShopOrm;
    $PHPShopOrm->clean();
    $option = $PHPShopOrm->select();
    $new_version = $PHPShopModules->getUpdate($option['version']);
    $PHPShopOrm->clean();
    $action = $PHPShopOrm->update(array('version_new' => $new_version));
}

// Функция обновления
function actionUpdate() {
    global $PHPShopOrm,$PHPShopModules;
    
    // Настройки витрины
    $PHPShopModules->updateOption($_GET['id'], $_POST['servers']);
    
    if (empty($_POST['enabled_new']))
        $_POST['enabled_new'] = 0;
    if (empty($_POST['key_enabled_new']))
        $_POST['key_enabled_new'] = 0;
    $action = $PHPShopOrm->update($_POST);
    header('Location: ?path=modules&id='.$_GET['id']);
    return $action;
}

function getStatus($status_id) {
    global $PHPShopGUI;
    $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['order_status']);
    $data = $PHPShopOrm->select(array('*'), false, false, array('limit' => 100));
    if (is_array($data))
        foreach ($data as $row) {
            if ($row['id'] == $status_id)
                $sel = 'selected';
            else
                $sel = null;
            $value[] = array($row['name'], $row['id'], $sel);
        }

    return $PHPShopGUI->setSelect('order_status_new', $value);
}

// Начальная функция загрузки
function actionStart() {
    global $PHPShopGUI, $PHPShopSystem, $PHPShopOrm;

    // Выборка
    $data = $PHPShopOrm->select();

    $Tab1 = $PHPShopGUI->setField('Логика учета', $PHPShopGUI->setCheckbox('enabled_new', 1, 'Учет рефералов партнеров', $data['enabled']));
    
    $Tab1.=$PHPShopGUI->setField('Начисление партнерам', $PHPShopGUI->setInputText('%', 'percent_new', $data['percent'], '150', 'от заказа'));
    $Tab1.=$PHPShopGUI->setField('Хранение cookies', $PHPShopGUI->setInputText(null, 'cookies_day_new', $data['cookies_day'], '150', 'дней'));
    $Tab1.=$PHPShopGUI->setField('Рейтинг', $PHPShopGUI->setInputText(null, 'stat_day_new', $data['stat_day'], '150', 'дней'));
    $Tab1.=$PHPShopGUI->setField('Статус заказа выплаты',getStatus($data['order_status']));
    $Info = 'Страница входа в партнерский раздел находится по адресу: <a href="../../partner/" target="_blank">http://' . $_SERVER['SERVER_NAME'] . '/partner/</a>. Необходимо на своем сайте добавить эту ссылку для пользователей.
        <p>Правила регистрации в партнерской программе доступны по ссылке
        <a href="../../rulepartner/" target="_blank">http://' . $_SERVER['SERVER_NAME'] . '/rulepartner/</a>.
     <p>
     Шаблоны оформления находятся в папке <code>/phpshop/modules/partner/templates/</code><br>
     Языковой файл по адресу <code>/phpshop/modules/partner/inc/config.ini</code> в блоке <kbd>[lang]</kbd>
     </p>
     <p>Для обновления статусов заказов из <kbd>CSV</kbd> файла по <kbd>URL</kbd> с учетом начисления вознаграждения нужно использовать файл обработчик для модуля "Задачи" по адресу: <code>phpshop/modules/partner/cron/status.php</code></p>
     ';

    // Редактор 
    $PHPShopGUI->setEditor($PHPShopSystem->getSerilizeParam("admoption.editor"));
    $oFCKeditor = new Editor('rule_new');
    $oFCKeditor->Height = '520';
    $oFCKeditor->Value = $data['rule'];
    
    $Tab4=$PHPShopGUI->setInfo($Info);

    // Содержание закладки 2
    $Tab2 = $PHPShopGUI->setPay($data['serial'], false, $data['version'], true);

    $Tab3 = $oFCKeditor->AddGUI();


    // Вывод формы закладки
    $PHPShopGUI->setTab(array("Настройки", $Tab1,true), array("Текст правила участия", $Tab3), array("Инструкция", $Tab4), array("О Модуле", $Tab2));

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


