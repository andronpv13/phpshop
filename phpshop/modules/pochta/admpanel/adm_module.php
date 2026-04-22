<?php

include_once dirname(__DIR__) . '/class/include.php';

// SQL
$PHPShopOrm = new PHPShopOrm($PHPShopModules->getParam('base.pochta.pochta_system'));

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
    global $PHPShopModules;

    // Настройки витрины
    $PHPShopModules->updateOption($_GET['id'], $_POST['servers']);

    $PHPShopOrm = new PHPShopOrm($PHPShopModules->getParam('base.pochta.pochta_system'));
    $PHPShopOrm->debug = false;

    if(!isset($_POST['easy_return_new'])) {
        $_POST['easy_return_new'] = '0';
    }
    if(!isset($_POST['no_return_new'])) {
        $_POST['no_return_new'] = '0';
    }
    if(!isset($_POST['fragile_new'])) {
        $_POST['fragile_new'] = '0';
    }
    if(!isset($_POST['wo_mail_rank_new'])) {
        $_POST['wo_mail_rank_new'] = '0';
    }
    if(!isset($_POST['completeness_checking_new'])) {
        $_POST['completeness_checking_new'] = '0';
    }
    if(!isset($_POST['sms_notice_new'])) {
        $_POST['sms_notice_new'] = '0';
    }
    if(!isset($_POST['electronic_notice_new'])) {
        $_POST['electronic_notice_new'] = '0';
    }
    if(!isset($_POST['order_of_notice_new'])) {
        $_POST['order_of_notice_new'] = '0';
    }
    if(!isset($_POST['simple_notice_new'])) {
        $_POST['simple_notice_new'] = '0';
    }
    if(!isset($_POST['vsd_new'])) {
        $_POST['vsd_new'] = '0';
    }
    if(!isset($_POST['paid_new'])) {
        $_POST['paid_new'] = '0';
    }
    
    $action = $PHPShopOrm->update($_POST);

    header('Location: ?path=modules&id=' . $_GET['id']);

    return $action;
}

function actionStart() {
    global $PHPShopGUI, $PHPShopOrm,$PHPShopBase;

    $PHPShopGUI->addJSFiles('../modules/pochta/admpanel/gui/script.gui.js?v=1.0');

    // Выборка
    $data = $PHPShopOrm->select();
    
    // Демо-режим
    if ($PHPShopBase->getParam('template_theme.demo') == 'true') {
        $data['token'] = $data['login'] = $data['password']= '';
    }

    $Tab1 = $PHPShopGUI->setField('Токен авторизации приложения', $PHPShopGUI->setInputText(false, 'token_new', $data['token'], 300));
    $Tab1.= $PHPShopGUI->setField('Логин пользователя', $PHPShopGUI->setInputText(false, 'login_new', $data['login'], 300));
    $Tab1.= $PHPShopGUI->setField('Пароль пользователя', $PHPShopGUI->setInputText(false, 'password_new', $data['password'], 300));
    $Tab1.= $PHPShopGUI->setField('ID виджета', $PHPShopGUI->setInputText(false, 'widget_id_new', $data['widget_id'], 300));
    $Tab1.= $PHPShopGUI->setField('ID виджета курьерской доставки', $PHPShopGUI->setInputText(false, 'courier_widget_id_new', $data['courier_widget_id'], 300));
    $Tab1.= $PHPShopGUI->setField('Статус для отправки', $PHPShopGUI->setSelect('status_new', Settings::getStatusesVariants($data['status']), 300));
    $Tab1.= $PHPShopGUI->setField('Доставка', $PHPShopGUI->setSelect('delivery_id_new', Settings::getDeliveryVariants($data['delivery_id']), 300));
    $Tab1.= $PHPShopGUI->setField('Доставка курьером', $PHPShopGUI->setSelect('delivery_courier_id_new', Settings::getDeliveryVariants($data['delivery_courier_id']), 300));
    $Tab1.= $PHPShopGUI->setField('Категория РПО', $PHPShopGUI->setSelect('mail_category_new', Settings::getMailCategoryVariants($data['mail_category']), 300));
    $Tab1.= $PHPShopGUI->setField('Вид РПО', $PHPShopGUI->setSelect('mail_type_new', Settings::getMailTypeVariants($data['mail_type']), 300));
    $Tab1.= $PHPShopGUI->setField('Типоразмер', $PHPShopGUI->setSelect('dimension_type_new', Settings::getDimensionVariants($data['dimension_type']), 300));
    $Tab1.= $PHPShopGUI->setField('Почтовый индекс города отправителя', '<input class="form-control input-sm " onkeypress="pochtavalidate(event)" type="text" value="' . $data['index_from'] . '" name="index_from_new" style="width:300px; ">');
    $Tab1.= $PHPShopGUI->setField('Объявленная ценность', $PHPShopGUI->setInputText('От суммы корзины', 'declared_percent_new', $data['declared_percent'], 300,'%'));
    $Tab1= $PHPShopGUI->setCollapse('Настройки',$Tab1);
    $Tab1.= $PHPShopGUI->setCollapse('Настройки отправляемого заказа',
        $PHPShopGUI->setField('Лёгкий возврат', $PHPShopGUI->setCheckbox('easy_return_new', 1, 'Отметка "Лёгкий возврат"', $data["easy_return"])) .
        $PHPShopGUI->setField('Возврату не подлежит', $PHPShopGUI->setCheckbox('no_return_new', 1, 'Отметка "Возврату не подлежит"', $data["no_return"])) .
        $PHPShopGUI->setField('Осторожно/Хрупкое', $PHPShopGUI->setCheckbox('fragile_new', 1, 'Отметка "Осторожно/Хрупкое"', $data["fragile"])) .
        $PHPShopGUI->setField('Без разряда', $PHPShopGUI->setCheckbox('wo_mail_rank_new', 1, 'Отметка "Без разряда"', $data["wo_mail_rank"])) .
        $PHPShopGUI->setField('Комплектность', $PHPShopGUI->setCheckbox('completeness_checking_new', 1, "Услуга проверки комплектности", $data["completeness_checking"])) .
        $PHPShopGUI->setField('SMS уведомление', $PHPShopGUI->setCheckbox('sms_notice_new', 1, 'Услуга SMS уведомление', $data["sms_notice"])) .
        $PHPShopGUI->setField('Электронное уведомление', $PHPShopGUI->setCheckbox('electronic_notice_new', 1, 'Услуга электронное уведомление', $data["electronic_notice"])) .
        $PHPShopGUI->setField('Заказное уведомление', $PHPShopGUI->setCheckbox('order_of_notice_new', 1, 'Услуга заказное уведомление', $data["order_of_notice"])) .
        $PHPShopGUI->setField('Простое уведомление', $PHPShopGUI->setCheckbox('simple_notice_new', 1, 'Услуга простое уведомление', $data["simple_notice"])) .
        $PHPShopGUI->setField('Сопроводительные документы', $PHPShopGUI->setCheckbox('vsd_new', 1, 'Возврат сопроводительных документов', $data["vsd"])).
        $PHPShopGUI->setField('Статус оплаты', $PHPShopGUI->setCheckbox('paid_new', 1, 'Заказ оплачен', $data["paid"]))
    );

    $info = '<h4>Получение токена авторизации</h4>
       <ol>
        <li>Зарегистрироваться на онлайн-сервисе <a href="https://otpravka.pochta.ru/" target="_blank">«Отправка»</a></li>
        <li>Токен авторизации пользователя можно узнать в <a href="https://otpravka.pochta.ru/settings#/api-settings" target="_blank">настройках личного кабинета</a>.</li>
        <li>Создать виджет в <a href="https://widget.pochta.ru/widgets" target="_blank">Виджет Почты России</a>.</li>
        <li>В настройках виджетов включить <code>Callback function name</code> и вписать значение <code>pochtaCallback</code> 
            для виджета <kbd>В пункт выдачи</kbd> и <code>pochtaCallbackCourier</code> для виджета <kbd>Курьером</kbd>.
        </li>
        <li>Из кода виджета <kbd>В пункт выдачи</kbd> <code>ecomStartWidget({
        id: 1234,
        callbackFunction: pochtacallback,
        containerId: \'ecom-widget\'
      });</code> скопировать числовой id, в примере 1234, вставить его в поле <kbd>ID виджета</kbd> в настройках модуля.</li>
              <li>Из кода виджета <kbd>Курьером</kbd> <code>courierStartWidget({
        id: 1234,
        callbackFunction: pochtaCallbackCourier,
        containerId: \'ecom-widget-courier\'
      });</code> скопировать числовой id, в примере 1234, вставить его в поле <kbd>ID виджета курьерской доставки</kbd> в настройках модуля.</li>
        </ol>
        
       <h4>Настройка модуля</h4>
        <ol>
        <li>Ввести токен авторизации пользователя.</li>
        <li>Ввести логин и пароль от личного кабинета Почты России.</li>
        <li>Ввести индекс города отправки отправлений.</li>
        <li>Выбрать способ доставки и способ курьерской доставки.</li>
        <li>Ввести вес по умолчанию, он будет использован если у товара не задан вес.</li>
        <li>Настроить дополнительные услуги.</li>
        </ol>
        
       <h4>Настройка доставки</h4>
        <ol>
        <li>В карточке редактирования доставки в закладке <kbd>Изменение стоимости доставки</kbd> настроить дополнительный параметр сохранения стоимости доставки для модуля. Опция "Не изменять стоимость" должна быть активна.</li>
        <li>В карточке редактирования доставки в закладке <kbd>Адреса пользователя</kbd> отметить <kbd>Индекс</kbd> "Вкл." и "Обязательное"</li>
        </ol>

';

    $Tab2 = $PHPShopGUI->setInfo($info);

    // Форма регистрации
    $Tab4 = $PHPShopGUI->setPay($serial = false, false, $data['version'], true);

    // Вывод формы закладки
    $PHPShopGUI->setTab(array("Основное", $Tab1, true), array("Инструкция", $Tab2), array("О Модуле", $Tab4));

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