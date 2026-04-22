<?php

PHPShopObj::loadClass('order');

// SQL
$PHPShopOrm = new PHPShopOrm($PHPShopModules->getParam("base.mandarinhosted.mandarinhosted_system"));

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
    global $PHPShopOrm, $PHPShopModules;

    // Настройки витрины
    $PHPShopModules->updateOption($_GET['id'], $_POST['servers']);

    $PHPShopOrm->debug = false;
    $action = $PHPShopOrm->update($_POST);
    header('Location: ?path=modules&id=mandarinhosted');
    return $action;
}

function actionStart() {
    global $PHPShopGUI, $PHPShopOrm;

    // Выборка
    $data = $PHPShopOrm->select();

    // Доступые статусы заказов
    $PHPShopOrderStatusArray = new PHPShopOrderStatusArray();
    $OrderStatusArray = $PHPShopOrderStatusArray->getArray();
    $order_status_value[] = array(__('Новый заказ'), 0, $data['status']);
    if (is_array($OrderStatusArray))
        foreach ($OrderStatusArray as $order_status)
            $order_status_value[] = array($order_status['name'], $order_status['id'], $data['status']);

    $Tab1 .= $PHPShopGUI->setField('ID Мерчанта', $PHPShopGUI->setInputText(false, 'merchant_key_new', $data['merchant_key'], 250));
    $Tab1 .= $PHPShopGUI->setField('Секретный ключ', $PHPShopGUI->setInputText(false, 'merchant_skey_new', $data['merchant_skey'], 250));
    $Tab1 .= $PHPShopGUI->setField('Разрешить оплату при статусе заказа', $PHPShopGUI->setSelect('status_new', $order_status_value, 300));
    $Tab1 .= $PHPShopGUI->setField('Сообщение до разрешения оплаты', $PHPShopGUI->setTextarea('title_sub_new', $data['title_sub']));
    $Tab1 .= $PHPShopGUI->setField('Сообщение перед оплатой', $PHPShopGUI->setTextarea('title_new', $data['title']));

    $info = '<h4>Настройка модуля</h4>
      <ol>
<li>Предоставить необходимые документы и заключить договор с <a href="https://mandarin.io/partner_phpshop" target="_blank">MandarinPay</a>.</li>
<li>На закладке настройки ввести "Merchant ID" и "Secret key", который можно найти в <a href="https://admin.mandarinpay.com/user/" target="_blank">Личном кабинете</a> на вкладке <kbd>Продажи</kbd>.</li>
</ol>';

    $Tab2 = $PHPShopGUI->setInfo($info);

    // Форма регистрации
    $Tab3 = $PHPShopGUI->setPay();

    // Вывод формы закладки
    $PHPShopGUI->setTab(array("Основное", $Tab1, true), array("Инструкция", $Tab2), array("О Модуле", $Tab3));

    // Вывод кнопок сохранить и выход в футер
    $ContentFooter = $PHPShopGUI->setInput("hidden", "rowID", $data['id']) . $PHPShopGUI->setInput("submit", "saveID", "Применить", "right", 80, "", "but", "actionUpdate.modules.edit");

    $PHPShopGUI->setFooter($ContentFooter);
    return true;
}

// Обработка событий
$PHPShopGUI->getAction();
// Вывод формы при старте
$PHPShopGUI->setLoader($_POST['editID'], 'actionStart');
