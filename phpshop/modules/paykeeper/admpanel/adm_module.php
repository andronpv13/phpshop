<?php

// SQL
$PHPShopOrm = new PHPShopOrm($PHPShopModules->getParam("base.paykeeper.paykeeper_system"));

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
    global $PHPShopOrm, $PHPShopModules;

    // Настройки витрины
    $PHPShopModules->updateOption($_GET['id'], $_POST['servers']);

    $PHPShopOrm->debug = false;
    $action = $PHPShopOrm->update($_POST);
    header('Location: ?path=modules&id=' . $_GET['id']);
    return $action;
}

function actionStart() {
    global $PHPShopGUI, $PHPShopOrm;

    PHPShopObj::loadClass('order');

    // Выборка
    $data = $PHPShopOrm->select();

    $Tab1 = $PHPShopGUI->setField('Наименование типа оплаты', $PHPShopGUI->setInputText(false, 'title_new', $data['title']));
    $Tab1 .= $PHPShopGUI->setField('Адрес формы оплаты', $PHPShopGUI->setInputText(false, 'form_url_new', $data['form_url'], 500));
    $Tab1 .= $PHPShopGUI->setField('Секретное слово', $PHPShopGUI->setInputText(false, 'secret_new', $data['secret'], 500));

    // Доступые статусы заказов
    $PHPShopOrderStatusArray = new PHPShopOrderStatusArray();
    $OrderStatusArray = $PHPShopOrderStatusArray->getArray();
    $order_status_value[] = array(__('Новый заказ'), 0, $data['status']);
    if (is_array($OrderStatusArray))
        foreach ($OrderStatusArray as $order_status)
            $order_status_value[] = array($order_status['name'], $order_status['id'], $data['status']);


    // Принудительный учет скидок
    $value_arr = $data['forced_discount_check'] == 1 ? array(array('Вкл', 1, 'selected'), array('Выкл', 2, false)) : array(array('Вкл', 1, false), array('Выкл', 2, 'selected'));
    $Tab1 .= $PHPShopGUI->setField('Принудительный учет скидок', $PHPShopGUI->setSelect('forced_discount_check_new', $value_arr, 300));

    // Статус заказа
    //$Tab1.= $PHPShopGUI->setField('Оплата при статусе', $PHPShopGUI->setSelect('status_new', $order_status_value));
    $Tab1 .= $PHPShopGUI->setField('Описание оплаты', $PHPShopGUI->setTextarea('title_end_new', $data['title_end']));


    // Форма регистрации
    $Tab3 = $PHPShopGUI->setPay($data['serial'], false, $data['version'], true);

    // Инструкция
    $info = '
        <h4>Настройка модуля</h4>
        <ol>
        <li>Зарегистрироваться, заключить договор с <a href="https://paykeeper.ru/paykeeper/register/registerform/" target="_blank">PayKeeper</a>.</li>
        <li>Секретное слово необходимо сгенерировать в личном кабинете PayKeeper, скопировать и вставить в поле <kbd>Секретное поле</kbd> в настройках модуля. </li>
        <li>В поле Адрес формы оплаты укажите URL адрес следующего вида: <code>https://имя_сайта.server.paykeeper.ru/order/inline/cp1251</code></li>
        </ol>
        <h4>Настройка личного кабинета PayKeeper</h4>
        <ol>
         <li>Измените "Способ получения уведомления о платежах" на <kbd>POST-оповещения</kbd>.</li>
         <li>В поле "URL, на который будут отправляться POST-запросы" укажите URL-адрес вида: <code>http://имя_сайта.ru/success/</code></li>
         <li>Секретное слово можно придумать самостоятельно или сгенерировать с помощью кнопки <kbd>Сгенерировать</kbd>.</li>
         <li>В разделе "Адреса перенаправления клиента" в полях "URL страницы, на которую клиент переходит при успешном завершении оплаты" и "URL страницы, на которую клиент переходит при неудаче в процессе оплаты" укажите <code>http://имя_сайта.ru/</code></li>
        </ol>
';

    $Tab2 = $PHPShopGUI->setInfo($info);

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
