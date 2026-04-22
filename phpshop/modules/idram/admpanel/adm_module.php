<?php

include_once dirname(__DIR__) . '/class/Idram.php';

// SQL
$PHPShopOrm = new PHPShopOrm($PHPShopModules->getParam("base.idram.idram_system"));

// Обновление версии модуля
function actionBaseUpdate() {
    global $PHPShopModules, $PHPShopOrm;
    $PHPShopOrm->clean();
    $option = $PHPShopOrm->select();
    $new_version = $PHPShopModules->getUpdate($option['version']);
    $PHPShopOrm->clean();
    $action = $PHPShopOrm->update(['version_new' => $new_version]);
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
    
    PHPShopObj::loadClass('order');

    // Выборка
    $data = $PHPShopOrm->select();

    $Tab1 = $PHPShopGUI->setField('IdramID', $PHPShopGUI->setInputText(false, 'idram_id_new', $data['idram_id'], 300));
    $Tab1 .= $PHPShopGUI->setField('Секретный ключ', $PHPShopGUI->setInputText(false, 'secret_key_new', $data['secret_key'], 300));
    $Tab1 .= $PHPShopGUI->setField('Язык платежной формы', $PHPShopGUI->setSelect('language_new', Idram::getAvailableLanguages($data['language']) , 300));
    $Tab1 .= $PHPShopGUI->setField('Ссылка на оплату', $PHPShopGUI->setInputText(false, 'title_new', $data['title'], 300));
    $Tab1 .= $PHPShopGUI->setField('Оплата при статусе', $PHPShopGUI->setSelect('status_new', Idram::getOrderStatuses($data['status']) , 300));
    $Tab1 .= $PHPShopGUI->setField('Описание оплаты', $PHPShopGUI->setTextarea('payment_description_new', $data['payment_description'], true, 300, 100));
    $Tab1 .= $PHPShopGUI->setField('Сообщение предварительной проверки заказа', $PHPShopGUI->setTextarea('payment_status_new', $data['payment_status'], true, 300, 100));

    // Форма регистрации
    $Tab3 = $PHPShopGUI->setPay(false, false, $data['version']);

    $info = '<h4>Как подключиться к Idram?</h4>
        <ol>
<li>Зарегистрироваться, заключить договор с <a href="https://web.idram.am/new/am" target="_blank">Idram</a>.</li>
<li>Полученный <kbd>IdramID</kbd> вписать в поле <code>IdramID</code> в настройках модуля.</li>
<li>Полученный <kbd>Secret Key</kbd> вписать в поле <code>Секретный ключ</code> в настройках модуля.</li>
<li>В системе Idram указать SUCCESS_URL: <code>https://' . $_SERVER['SERVER_NAME'] . '/success/?status=success</code></li>
<li>В системе Idram указать FAIL_URL: <code>https://' . $_SERVER['SERVER_NAME'] . '/success/?status=fail</code></li>
<li>В системе Idram указать RESULT_URL: <code>https://' . $_SERVER['SERVER_NAME'] . '/phpshop/modules/idram/payment/check.php</code></li>
<li>Выбрать язык, на котором будет отображаться платежная форма покупателю.</li>
<li>Настроить статус заказа, при котором будет доступна оплата.</li>
</ol>';

    $Tab2 = $PHPShopGUI->setInfo($info);

    // Вывод формы закладки
    $PHPShopGUI->setTab(["Основное", $Tab1,true], ["Инструкция", $Tab2], ["О Модуле", $Tab3]);

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