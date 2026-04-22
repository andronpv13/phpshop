<?php

include_once dirname(__DIR__) . '/class/NovaPay.php';

// SQL
$PHPShopOrm = new PHPShopOrm($PHPShopModules->getParam("base.novapay.novapay_system"));

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

    if (empty($_POST["dev_mode_new"]))
        $_POST["dev_mode_new"] = 0;

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

    $Tab1 = $PHPShopGUI->setField('Merchant Id', $PHPShopGUI->setInputText(false, 'merchant_id_new', $data['merchant_id'], 300));
    $Tab1 .= $PHPShopGUI->setField('Публичный ключ', $PHPShopGUI->setTextarea('public_key_new', $data['public_key'], true, 300, 200));
    $Tab1 .= $PHPShopGUI->setField('Приватный ключ', $PHPShopGUI->setTextarea('private_key_new', $data['private_key'], true, 300, 200));
    $Tab1 .= $PHPShopGUI->setField('Ссылка на оплату', $PHPShopGUI->setInputText(false, 'title_new', $data['title'], 300));
    $Tab1 .= $PHPShopGUI->setField('Оплата при статусе', $PHPShopGUI->setSelect('status_new', NovaPay::getOrderStatuses($data['status']) , 300));
    $Tab1 .= $PHPShopGUI->setField('Описание оплаты', $PHPShopGUI->setTextarea('title_end_new', $data['title_end'], true, 300));
    $Tab1 .= $PHPShopGUI->setField('Режим разработки', $PHPShopGUI->setCheckbox("dev_mode_new", 1, "Отправка данных на тестовую среду NovaPay", $data["dev_mode"]));

    // Форма регистрации
    $Tab3 = $PHPShopGUI->setPay(false, false, $data['version']);

    $info = '<h4>Как подключиться к NovaPay?</h4>
        <ol>
<li>Зарегистрироваться, заключить договор с <a href="https://novapay.ua/" target="_blank">NovaPay</a>.</li>
<li>Полученные от NovaPay "Merchant Id", "Публичный ключ", "Приватный ключ" ввести в соответствующие поля в настройках модуля.</li>
<li>Для режима разработки необходимо использовать Merchant Id и ключи указанные в документации NovaPay.</li>
<li>В используемых способах доставки, закладка <kbd>Адреса пользователя</kbd> отметить <kbd>ФИО</kbd> "Вкл." и "Обязательное"</li>
<li>В используемых способах доставки, закладка <kbd>Адреса пользователя</kbd> отметить <kbd>Телефон</kbd> "Вкл." и "Обязательное"</li>
</ol>';

    $Tab2 = $PHPShopGUI->setInfo($info);

    // Вывод формы закладки
    $PHPShopGUI->setTab(array("Основное", $Tab1,true), array("Инструкция", $Tab2), array("О Модуле", $Tab3));

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