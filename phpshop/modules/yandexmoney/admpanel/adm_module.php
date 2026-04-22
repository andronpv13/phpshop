<?php

// SQL
$PHPShopOrm = new PHPShopOrm($PHPShopModules->getParam("base.yandexmoney.yandexmoney_system"));

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

    $PHPShopOrm->debug = false;
    $action = $PHPShopOrm->update($_POST);
    header('Location: ?path=modules&id='.$_GET['id']);
    return $action;
}

function actionStart() {
    global $PHPShopGUI,  $PHPShopOrm;
    
    PHPShopObj::loadClass('order');

    // Выборка
    $data = $PHPShopOrm->select();

    $Tab1 = $PHPShopGUI->setField('Наименование типа оплаты', $PHPShopGUI->setInputText(false, 'title_new', $data['title']));
    $Tab1.=$PHPShopGUI->setField('Счет №', $PHPShopGUI->setInputText(false, 'merchant_id_new', $data['merchant_id'], 300));
    $Tab1.=$PHPShopGUI->setField('Секретное слово', $PHPShopGUI->setInputText(false, 'merchant_sig_new', $data['merchant_sig'], 300));

    // Доступые статусы заказов
    $PHPShopOrderStatusArray = new PHPShopOrderStatusArray();
    $OrderStatusArray = $PHPShopOrderStatusArray->getArray();
    $order_status_value[] = array(__('Новый заказ'), 0, $data['status']);
    if (is_array($OrderStatusArray))
        foreach ($OrderStatusArray as $order_status)
            $order_status_value[] = array($order_status['name'], $order_status['id'], $data['status']);

    // Статус заказа
    $Tab1.= $PHPShopGUI->setField('Оплата при статусе', $PHPShopGUI->setSelect('status_new', $order_status_value,300));

    $Tab1.=$PHPShopGUI->setField('Описание оплаты', $PHPShopGUI->setTextarea('title_end_new', $data['title_end']));


    // Форма регистрации
    $Tab3 = $PHPShopGUI->setPay($data['serial'], false, $data['version'],false);
    
            $info='Для работы модуля требуется настроить форму HTTP-уведомления по ссылке: <a href="https://yoomoney.ru/transfer/myservices/http-notification" target="_blank">https://yoomoney.ru/transfer/myservices/http-notification</a>. В качестве адреса для уведомлений нужно указать <code>http://'.$_SERVER['SERVER_NAME'].'/phpshop/modules/yandexmoney/payment/result.php</code>. 
                <p>
В поле "Секрет" нужно указать свое любое значение или оставить предложенное системой. Этот секрет нужно скопировать в закладку "Основное" настройки модуля в одноименное поле.</p>';

    $Tab2=$PHPShopGUI->setInfo($info);

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