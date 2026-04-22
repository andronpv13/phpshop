<?php

// SQL
$PHPShopOrm = new PHPShopOrm($PHPShopModules->getParam("base.paypal.paypal_system"));

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
    global $PHPShopOrm;

    $PHPShopOrm->debug = false;
    $action = $PHPShopOrm->update($_POST);
    header('Location: ?path=modules&install=check');
    return $action;
}

function actionStart() {
    global $PHPShopGUI, $PHPShopOrm;
    
    PHPShopObj::loadClass('order');
    PHPShopObj::loadClass('valuta');


    // Выборка
    $data = $PHPShopOrm->select();

    $Tab1 = $PHPShopGUI->setField('Наименование типа оплаты', $PHPShopGUI->setInputText(false, 'title_new', $data['title'],300));
    $Tab1.=$PHPShopGUI->setField('Пользователь', $PHPShopGUI->setInputText(false, 'merchant_id_new', $data['merchant_id'], 300));
    $Tab1.=$PHPShopGUI->setField('Пароль', $PHPShopGUI->setInputText(false, 'merchant_pwd_new', $data['merchant_pwd'], 300));
    $Tab1.=$PHPShopGUI->setField('Подпись', $PHPShopGUI->setInputText(false, 'merchant_sig_new', $data['merchant_sig'], 300));

    // Доступые статусы заказов
    $PHPShopOrderStatusArray = new PHPShopOrderStatusArray();
    $OrderStatusArray = $PHPShopOrderStatusArray->getArray();
    $order_status_value[] = array(__('Новый заказ'), 0, $data['status']);
    if (is_array($OrderStatusArray))
        foreach ($OrderStatusArray as $order_status)
            $order_status_value[] = array($order_status['name'], $order_status['id'], $data['status']);

    // Статус заказа
    $Tab1.= $PHPShopGUI->setField('Оплата при статусе', $PHPShopGUI->setSelect('status_new', $order_status_value, 300));

    // Ссылка
    $Tab1.= $PHPShopGUI->setField('Текст ссылки на оплату', $PHPShopGUI->setInputText(null, 'link_new', $data['link'], 300));

    // Sandbox
    $sandbox_value[] = array('Включен', 1, $data['sandbox']);
    $sandbox_value[] = array('Выключен', 2, $data['sandbox']);
    $Tab1.= $PHPShopGUI->setField('Тестовый режим', $PHPShopGUI->setSelect('sandbox_new', $sandbox_value,300,true));

    // Логотип
    $logo_value[] = array('Слева', 1, $data['logo_enabled']);
    $logo_value[] = array('Справа', 2, $data['logo_enabled']);
    $logo_value[] = array('Выключен', 3, $data['logo_enabled']);
    $Tab1.= $PHPShopGUI->setField('Логотип PayPal', $PHPShopGUI->setSelect('logo_enabled_new', $logo_value,300,true));

    // Валюты
    $PHPShopValutaArray = new PHPShopValutaArray();
    $valuta_array = $PHPShopValutaArray->getArray();
    $valuta_area = null;
    if (is_array($valuta_array))
        foreach ($valuta_array as $val) {
            if ($data['currency_id'] == $val['id']) {
                $check = 'checked';
                $valuta_def_name = $val['code'];
            }
            else
                $check = false;
            $valuta_area.=$PHPShopGUI->setRadio('currency_id_new', $val['id'], $val['name'], $check,false, false, false, false);
        }
    $Tab1.= $PHPShopGUI->setLine().$PHPShopGUI->setField('Валюта расчета',$valuta_area);    

    $Tab4 = $PHPShopGUI->setField('Сообщение об отложенном платеже', $PHPShopGUI->setTextarea('title_end_new',$data['title_end']));
    $Tab4.=$PHPShopGUI->setField('Заголовок сообщения после оплаты', $PHPShopGUI->setInputText(null, 'message_header_new', $data['message_header']));
    $Tab4.=$PHPShopGUI->setField('Cообщения после оплаты', $PHPShopGUI->setTextarea('message_new', $data['message']));

    // Форма регистрации
    $Tab3 = $PHPShopGUI->setPay(false, false, $data['version'], true);

    $info = 'Для работы модуля требуется зарегистрироваться в PayPal по ссылке: <a href="https://www.paypal.com/ru/webapps/mpp/solutions" target="_blank">https://www.paypal.com/ru/webapps/mpp/solutions</a>. 
                <p>
В поля "Пользователь", "Пароль" и "Подпись" внести одноименные данные, полученные после регистрации Бизнес аккаунта в PayPal.</p> <p>
Для тестирования модуля используйте опцию "Тестовый режим" в закладке "Авторизация". Для отложенного платежа следует выбрать нужный статус заказа в закладке "Авторизация". </p><p>Опция "Логотип PayPal" показывает обязательный логотип платежной системы. Шаблон логотипа находится в файле <code>phpshop/modules/paypal/templates/paypal_logo.tpl</code>. Дополнительные обязательные логотипы доступны по ссылке: <a href="https://www.paypal.com/ru/webapps/mpp/logos" target="_blank">https://www.paypal.com/ru/webapps/mpp/logos</a>.</p> <p> Шаблон описания платежной системы: <code>phpshop/modules/paypal/templates/paypal_forma.tpl</code></p><p>IPN обработчик оплаты: <code>http://'.$_SERVER['SERVER_NAME'].'/phpshop/modules/paypal/payment/ipn.php</code></p>';

    $Tab2 = $PHPShopGUI->setInfo($info);

    // Вывод формы закладки
    $PHPShopGUI->setTab(array("Авторизация", $Tab1, true), array("Сообщения", $Tab4, true), array("Инструкция", $Tab2), array("О Модуле", $Tab3));

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