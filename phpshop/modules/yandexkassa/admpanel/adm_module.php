<?php

include_once dirname(__FILE__) . '/../class/YandexKassa.php';

// SQL
$PHPShopOrm = new PHPShopOrm($PHPShopModules->getParam("base.yandexkassa.yandexkassa_system"));

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
    
    
    
    $Tab1 = $PHPShopGUI->setField('Ссылка на оплату', $PHPShopGUI->setInputText(false, 'title_new', $data['title'], 300));
    $Tab1 .= $PHPShopGUI->setField('ShopID', $PHPShopGUI->setInputText(false, 'shop_id_new', $data['shop_id'], 300));
    $Tab1 .= $PHPShopGUI->setField('Секретный ключ', $PHPShopGUI->setInputText(false, 'api_key_new', $data['api_key'], 300));
    $Tab1 .= $PHPShopGUI->setField('Оплата при статусе', $PHPShopGUI->setSelect('status_new', YandexKassa::getOrderStatuses($data['status']) , 300));
    $Tab1 .= $PHPShopGUI->setField('Второй чек при статусе', $PHPShopGUI->setSelect('receipt_status_new', YandexKassa::getOrderStatuses($data['receipt_status']) , 300));
       
    $payment_mode_value[] = array('Полная предоплата', 1, $data['payment_mode']);
    $payment_mode_value[] = array('Полный расчет', 2, $data['payment_mode']);
    
    $Tab1 .= $PHPShopGUI->setField('Способ расчета', $PHPShopGUI->setSelect('payment_mode_new', $payment_mode_value , 300, true));
    $Tab1 .= $PHPShopGUI->setField('Описание оплаты', $PHPShopGUI->setTextarea('title_end_new', $data['title_end'], true, 300,200));

    // Форма регистрации
    $Tab3 = $PHPShopGUI->setPay(false, false, $data['version'], true);

    $protocol = YandexKassa::isHttps() ? 'https://' : 'http://';

    $info = '
        <h4>Как подключиться к ЮKassa?</h4>
        <ol>
<li>Подайте заявку на подключение по ссылке <a href="https://yookassa.ru/joinups/?source=phpshop" target="_blank">https://yookassa.ru/joinups/?source=phpshop</a> и получите доступ в личный кабинет.</li>
<li>Заполните анкету.</li>
<li>Выберите способ подключения API.</li>
<li>Подпишите договор.</li>
</ol>

<h4>Технические данные необходимые для регистрации и подключения к ЮKassa</h4>
            <p>В личном кабинете ЮKassa перейти в меню <bkd>Интеграция</bkd> и в поле "URL для уведомлений" указать<code>' . $protocol . $_SERVER['SERVER_NAME'] . '/phpshop/modules/yandexkassa/payment/check.php</code> <br>
                <p>Поле "<b>Секретный ключ</b>" необходимо скопировать с личного кабинета ЮKassa (Настройки/Ключи API, Секретный ключ).</p>
                <p>Поле "<b>ShopID</b>" необходимо скопировать с личного кабинета ЮKassa (Настройки/Магазин, shopId).</p>
                <p>В настройка "Оплата при статусе" выберите статус заказа, при котором пользователю станет доступной возможность оплатить заказ данным способом. Если выбран статус "Новый заказ", пользователь сможет оплатить заказ сразу после оформления. Сообщение заданное в поле "Описание оплаты" выводится после оформления заказа в случае, когда статус заказа не совпадает со статусом указанным в настройке "Оплата при статусе".</p>
                
        <h4>Настройка доставки</h4>
        <p>Параметр ставки НДС для доставки можно настроить в карточке редактирования доставки.
        </p>
                <h4>Шаблоны дизайна</h4>
                <p>Шаблон вывода информации о платёжной системе после офрмления: <code>phpshop/modules/yandexkassa/templates/payment_forma.tpl</code><br>
                Шаблон сообщения об успешной оплате: <code>phpshop/modules/yandexkassa/templates/success_forma.tpl</code><br>
                Шаблон сообщения об успешной оплате: <code>phpshop/modules/yandexkassa/templates/fail_forma.tpl</code></p>
                <h4>Настройка хостинга</h4>
                Для приема уведомлений о платежах от ЮKassa на хостинге должна быть включена функция <kbd>allow_url_fopen</kbd> в настройках PHP.
    ';

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