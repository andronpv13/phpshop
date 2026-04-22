<?php

PHPShopObj::loadClass('order');

// SQL
$PHPShopOrm = new PHPShopOrm($PHPShopModules->getParam("base.acquiropay.acquiropay_system"));

// Обновление версии модуля
function actionBaseUpdate()
{
    global $PHPShopModules, $PHPShopOrm;
    $PHPShopOrm->clean();
    $option = $PHPShopOrm->select();
    $new_version = $PHPShopModules->getUpdate($option['version']);
    $PHPShopOrm->clean();
    return $PHPShopOrm->update(array('version_new' => $new_version));
}

// Функция обновления
function actionUpdate()
{
    global $PHPShopOrm;

    $PHPShopOrm->debug = false;
    $action = $PHPShopOrm->update($_POST);
    header('Location: ?path=modules&id=acquiropay');
    return $action;
}

function actionStart()
{
    global $PHPShopGUI, $PHPShopOrm;

    // Выборка
    $data = $PHPShopOrm->select();

    $Tab1 = $PHPShopGUI->setField(
        'ID Продукта',
        $PHPShopGUI->setInputText(false, 'product_id_new', $data['product_id'], 250)
    );
    $Tab1 .= $PHPShopGUI->setField(
        'ID Торговца',
        $PHPShopGUI->setInputText(false, 'merchant_id_new', $data['merchant_id'], 250)
    );
    $Tab1 .= $PHPShopGUI->setField(
        'Секретный ключ',
        $PHPShopGUI->setInputText(false, 'merchant_skey_new', $data['merchant_skey'], 250)
    );
    $Tab1 .= $PHPShopGUI->setField(
        'URL платежной формы',
        $PHPShopGUI->setInputText(false, 'endpoint_url_new', $data['endpoint_url'], 250)
    );
    $Tab1 .= '<input type="hidden" name="use_cashbox_new" value="0" />';
    $Tab1 .= $PHPShopGUI->setField(
        'Онлайн касса через AcquiroPay',
        $PHPShopGUI->setCheckbox(
            'use_cashbox_new',
            1,
            'Использовать',
            (int)$data['use_cashbox'] === 1
        )
    );

    // Доступые статусы заказов
    $PHPShopOrderStatusArray = new PHPShopOrderStatusArray();
    $OrderStatusArray = $PHPShopOrderStatusArray->getArray();
    $order_status_value[] = array(__('Новый заказ'), 0, $data['status']);
    if (is_array($OrderStatusArray)) {
        foreach ($OrderStatusArray as $order_status) {
            $order_status_value[] = array($order_status['name'], $order_status['id'], $data['status']);
        }
    }

    // Статус заказа
    $Tab1 .= $PHPShopGUI->setField('Оплата при статусе',
        $PHPShopGUI->setSelect('status_new', $order_status_value, 250));

    $Tab1 .= $PHPShopGUI->setField('Сообщение перед оплатой', $PHPShopGUI->setTextarea('title_new', $data['title']));
    $Tab1 .= $PHPShopGUI->setField('Сообщение предварительной проверки',
        $PHPShopGUI->setTextarea('title_sub_new', $data['title_sub']));

    $info = '
<h4>Настройка модуля</h4>
<ol>
    <li>Зарегистрироваться в <a href="http://acquiropay.com/" target="_blank">AcquiroPay</a></li>
    <li>Полученные в результате регистрации id продукта, id торговца и секретный ключ внести в настройки данного модуля</li>
    <li>Указать URL платежной формы - https://secure.acqp.co (тестовый шлюз) или https://secure.acquiropay.com</li>
    <li>В случае успешной оплаты, пользователь будет перенаправлен на <code>http://'.$_SERVER['SERVER_NAME'].'/success/</code></li>
    <li>Если оплата по каким-то причинам не прошла, пользователь будет перенаправлен на <code>http://'.$_SERVER['SERVER_NAME'].'/fail/</code></li>
    <li>Оповещения о платежах от AcquiroPay будут приходить на <code>http://'.$_SERVER['SERVER_NAME'].'/phpshop/modules/acquiropay/payment/result.php</code></li>
</ol>
<p>Дополнительные параметры по Федеральному закону 54 передаются по умолчанию с установкой нашего модуля.</p>
';

    $Tab2 = $PHPShopGUI->setInfo($info);

    // Форма регистрации
    $Tab3 = $PHPShopGUI->setPay();

    // Вывод формы закладки
    $PHPShopGUI->setTab(array("Основное", $Tab1, true), array("Инструкция", $Tab2), array("О Модуле", $Tab3));

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