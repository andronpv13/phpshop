<?php

// SQL
$PHPShopOrm = new PHPShopOrm($PHPShopModules->getParam("base.bitrix24.bitrix24_system"));

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

    $_POST['statuses_new'] = serialize($_POST['statuses']);

    // Настройки витрины
    $PHPShopModules->updateOption($_GET['id'], $_POST['servers']);

    $PHPShopOrm->debug = false;
    $action = $PHPShopOrm->update($_POST);
    header('Location: ?path=modules&id=' . $_GET['id']);
    return $action;
}

function actionStart() {
    global $PHPShopGUI, $PHPShopOrm;

    include_once '../modules/bitrix24/class/Bitrix24.php';
    $Bitrix24 = new Bitrix24();
    $data = $PHPShopOrm->select();

    $statusesOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['order_status']);
    $statusesResult = $statusesOrm->select(array('*'));
    $statuses = array(
        array('id' => 0, 'name' => 'Новый заказ')
    );
    if (isset($statusesResult['id']))
        $statuses[] = $statusesResult;
    else
        $statuses = array_merge($statuses, $statusesResult);

    if (!empty($data['statuses']))
        $statusSettings = unserialize($data['statuses']);
    else
        $statusSettings = array();

    $dealStages = $Bitrix24->getDealStages();

    $fieldStatuses = '';
    if (is_array($dealStages['result']))
        foreach ($dealStages['result'] as $dealStage) {
            $selectStatuses = array();

            foreach ($statuses as $status) {
                $selectStatuses[] = array($status['name'], $status['id'], $statusSettings[$dealStage['STATUS_ID']]);
            }

            $fieldStatuses .= $PHPShopGUI->setField(PHPShopString::utf8_win1251($dealStage['NAME']), $PHPShopGUI->setSelect('statuses[' . $dealStage['STATUS_ID'] . ']', $selectStatuses));
        }

    $Tab1 = $PHPShopGUI->setField('URL вебхука Битрикс24', $PHPShopGUI->setInputText(false, 'webhook_url_new', $data['webhook_url'], 500));
    $Tab1 .= $PHPShopGUI->setField('Код авторизации обновления сделки', $PHPShopGUI->setInputText(false, 'update_delivery_token_new', $data['update_delivery_token'], 500));
    $Tab1 .= $PHPShopGUI->setField('Код авторизации удаления товара', $PHPShopGUI->setInputText(false, 'delete_product_token_new', $data['delete_product_token'], 500));
    $Tab1 .= $PHPShopGUI->setField('Код авторизации удаления контакта', $PHPShopGUI->setInputText(false, 'delete_contact_token_new', $data['delete_contact_token'], 500));
    $Tab1 .= $PHPShopGUI->setField('Код авторизации удаления компании', $PHPShopGUI->setInputText(false, 'delete_company_token_new', $data['delete_company_token'], 500));

    if (empty($data['webhook_url']))
        $Tab1 .= $PHPShopGUI->setCollapse('Статусы', $PHPShopGUI->setAlert('Для сопоставления статусов заказа и этапов сделки введите "URL вебхука Битрикс24" и нажмите "Сохранить"', 'warning'));
    else
        $Tab1 .= $PHPShopGUI->setCollapse('Статусы', $fieldStatuses);

    $info = '
<h4>Как подключиться к Битрикс24?</h4>
<ol>
 <li>Зарегистрироваться на сайте <a href="https://www.bitrix24.ru/create.php?p=9003557" target="_blank">Битрикс24</a>
</li></ol> 

<h4>Создание входящего вебхука в Битрикс24</h4>
        <ol>
<li>Откройте Приложения, вкладка Вебхуки URL адрес: <code>https://адрес_вашего_Битрикс24/marketplace/hook/</code></li>
<li>Нажмите кнопку "Добавить Вебхук", в выпадающем списке выберите "Входящий вебхук".</li>
<li>Название введите "Синхронизация заказов".</li>
<li>Права доступа отметьте галочкой "CRM".</li>
<li>Скопируйте "Пример URL для вызова REST" до "/profile/", нажмите "Сохранить".</li>
<li>Скопированный URL вставьте в поле "URL вебхука Битрикс24" в настройках модуля.</a></li>
</ol>
<h4>Создание исходящих вебхуков в Битрикс24</h4>
        <ol>
<li>Откройте Приложения, вкладка Вебхуки URL адрес: <code>https://адрес_вашего_Битрикс24/marketplace/hook/</code></li>
<li>Нажмите кнопку "Добавить Вебхук", в выпадающем списке выберите "Исходящий вебхук".</li>
<li>Адрес обработчика введите <code>https://' . $_SERVER['SERVER_NAME'] . '/phpshop/modules/bitrix24/api/api.php</code>.</li>
<li>Название введите "Обновление статуса заказа".</li>
<li>Тип события отметьте галочкой "Обновление сделки", нажмите кнопку "Сохранить".</li>
<li>Полученный "Код авторизации" внести в поле "Код авторизации обновления сделки" в настройках модуля.</li>
<li>Нажмите кнопку "Добавить Вебхук", в выпадающем списке выберите "Исходящий вебхук".</li>
<li>Адрес обработчика введите <code>https://' . $_SERVER['SERVER_NAME'] . '/phpshop/modules/bitrix24/api/api.php</code>.</li>
<li>Название введите "Удаление товара в Битрикс24".</li>
<li>Тип события отметьте галочкой "Удаление товара", нажмите кнопку "Сохранить".</li>
<li>Полученный "Код авторизации" внести в поле "Код авторизации удаления товара" в настройках модуля.</li>
<li>Нажмите кнопку "Добавить Вебхук", в выпадающем списке выберите "Исходящий вебхук".</li>
<li>Адрес обработчика введите <code>https://' . $_SERVER['SERVER_NAME'] . '/phpshop/modules/bitrix24/api/api.php</code>.</li>
<li>Название введите "Удаление контакта в Битрикс24".</li>
<li>Тип события отметьте галочкой "Удаление контакта", нажмите кнопку "Сохранить".</li>
<li>Полученный "Код авторизации" внести в поле "Код авторизации удаления контакта" в настройках модуля.</li>
<li>Нажмите кнопку "Добавить Вебхук", в выпадающем списке выберите "Исходящий вебхук".</li>
<li>Адрес обработчика введите <code>https://' . $_SERVER['SERVER_NAME'] . '/phpshop/modules/bitrix24/api/api.php</code>.</li>
<li>Название введите "Удаление компании в Битрикс24".</li>
<li>Тип события отметьте галочкой "Удаление компании", нажмите кнопку "Сохранить".</li>
<li>Полученный "Код авторизации" внести в поле "Код авторизации удаления компании" в настройках модуля.</li>
</ol>';

    $Tab2 = $PHPShopGUI->setInfo($info);

    // Форма регистрации
    $Tab3 = $PHPShopGUI->setPay($data['serial'], false, $data['version'], true);

    // Вывод формы закладки
    $PHPShopGUI->setTab(array("Основное", $Tab1, true), array("Инструкция", $Tab2), array("О Модуле", $Tab3));

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