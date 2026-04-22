<?php

PHPShopObj::loadClass('order');

// SQL
$PHPShopOrm = new PHPShopOrm($PHPShopModules->getParam("base.robokassa.robokassa_system"));

// Обновление версии модуля
function actionBaseUpdate() {
    global $PHPShopModules, $PHPShopOrm;
    $PHPShopOrm->clean();
    $option = $PHPShopOrm->select();
    $new_version = $PHPShopModules->getUpdate($option['version']);
    $PHPShopOrm->clean();
    $action = $PHPShopOrm->update(array('version_new' => $new_version));
    header('Location: ?path=modules&id=' . $_GET['id']);
    return $action;
}

// Функция обновления
function actionUpdate() {
    global $PHPShopOrm, $PHPShopModules;

    // Настройки витрины
    $PHPShopModules->updateOption($_GET['id'], $_POST['servers']);

    if (empty($_POST["dev_mode_new"]))
        $_POST["dev_mode_new"] = 0;

    $PHPShopOrm->debug = false;
    $action = $PHPShopOrm->update($_POST);
    header('Location: ?path=modules&id=' . $_GET['id']);
    return $action;
}

function actionStart() {
    global $PHPShopGUI, $PHPShopOrm;

    // Выборка
    $data = $PHPShopOrm->select();

    $Tab1 = $PHPShopGUI->setField('Ссылка на оплату', $PHPShopGUI->setInputText(false, 'title_new', $data['title'], 300));
    $Tab1 .= $PHPShopGUI->setField('Идентификатор магазина', $PHPShopGUI->setInputText(false, 'merchant_login_new', $data['merchant_login'], 300));
    $Tab1 .= $PHPShopGUI->setField('Пароль #1', $PHPShopGUI->setInputText(false, 'merchant_key_new', $data['merchant_key'], 300));
    $Tab1 .= $PHPShopGUI->setField('Пароль #2', $PHPShopGUI->setInputText(false, 'merchant_skey_new', $data['merchant_skey'], 300));
    $Tab1 .= $PHPShopGUI->setField('Режим разработки', $PHPShopGUI->setCheckbox("dev_mode_new", 1, "Отправка данных на тестовую среду", $data["dev_mode"]));

    $merchant_country_value[] = array('Россия', 'Россия', $data['merchant_country']);
    $merchant_country_value[] = array('Казахстан', 'Казахстан', $data['merchant_country']);
    $Tab1 .= $PHPShopGUI->setField('Страна', $PHPShopGUI->setSelect('merchant_country_new', $merchant_country_value, 300,true));

    // Доступые статусы заказов
    $PHPShopOrderStatusArray = new PHPShopOrderStatusArray();
    $OrderStatusArray = $PHPShopOrderStatusArray->getArray();
    $order_status_value[] = array(__('Новый заказ'), 0, $data['status']);
    if (is_array($OrderStatusArray))
        foreach ($OrderStatusArray as $order_status)
            $order_status_value[] = array($order_status['name'], $order_status['id'], $data['status']);

    // Статус заказа
    $Tab1 .= $PHPShopGUI->setField('Оплата при статусе', $PHPShopGUI->setSelect('status_new', $order_status_value, 300));
    $Tab1 .= $PHPShopGUI->setField('Сообщение предварительной проверки', $PHPShopGUI->setTextarea('title_sub_new', $data['title_sub']));

    $info = '<h4>Настройка Robokassa</h4>
       <ol>
        <li>Зарегистрироваться в <a href="https://partner.robokassa.ru/Reg/Register?PromoCode=01phpshop&culture=ru" target="_blank">Robokassa.ru</a>
        <li>Result Url: <code>http://' . $_SERVER['SERVER_NAME'] . '/phpshop/modules/robokassa/payment/result.php</code>
        <li>Метод отсылки данных по Result Url: POST
        <li>Success Url: <code>http://' . $_SERVER['SERVER_NAME'] . '/success/</code>
        <li>Метод отсылки данных по Success Url: POST
        <li>Fail Url: <code>http://' . $_SERVER['SERVER_NAME'] . '/fail/</code>
        <li>Метод отсылки данных по Fail Url: POST
        </ol>
        
       <h4>Настройка модуля</h4>
       <ol>
        <li>"Идентификатор магазина", "Пароль #1" и "Пароль #2" (выдаются при подключении к Robokassa) скопировать в одноименные поля настроек модуля.
        <li>Оплата при статусе "Новый заказ" будет происходить сразу после создания заказа. Все остальные статусы дают возможность оплатить только при ручном назначении статуса администратором в личном кабинете покупателя.
        </ol>
        
        <h4>Настройка доставки</h4>
        <ol>
        <li>Параметр ставки НДС для доставки в онлайн-кассе можно настроить в карточке редактирования доставки.
        </ol>
        
        <h4>Настройка онлайн-кассы</h4>
        <ol>
        <li>Robokassa позволяет <a href="https://fiscal.robokassa.ru/" target="_blank">предоставлять онлайн-чеки</a> покупателям после оплаты по закону 54-ФЗ на email. Для этого в личном кабинете нужно выбрать вариант работы по "54-ФЗ - Облачное", после чего станут доступны дополнительные настройки по кассе.
        <li>Robokassa позволяет работать без использования онлайн-кассы. Оплата покупок в магазине будет производиться переводом без открытия банковского счета (т.е. безналичным перечислением денежных средств на ваш расчетный счет), по распоряжению покупателя.
        </ol>
';

    $Tab2 = $PHPShopGUI->setInfo($info);

    // Вывод формы закладки
    $PHPShopGUI->setTab(array("Основное", $Tab1, true), array("Инструкция", $Tab2), array("О Модуле", $PHPShopGUI->setPay(false, false, $data['version'], true)));

    // Вывод кнопок сохранить и выход в футер
    $ContentFooter = $PHPShopGUI->setInput("hidden", "rowID", $data['id']) .
            $PHPShopGUI->setInput("submit", "saveID", "Применить", "right", 80, "", "but", "actionUpdate.modules.edit");

    $PHPShopGUI->setFooter($ContentFooter);
    return true;
}

// Обработка событий
$PHPShopGUI->getAction();

// Вывод формы при старте
$PHPShopGUI->setLoader($_POST['editID'], 'actionStart');
?>