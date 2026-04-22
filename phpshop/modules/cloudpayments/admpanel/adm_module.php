<?php

// SQL
$PHPShopOrm = new PHPShopOrm($PHPShopModules->getParam("base.cloudpayments.cloudpayment_system"));

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


    $Tab1 = $PHPShopGUI->setField('Ссылка на оплату', $PHPShopGUI->setInputText(false, 'title_new', $data['title']));
    $Tab1.=$PHPShopGUI->setField('Идентификатор сайта publicId', $PHPShopGUI->setInputText(false, 'publicId_new', $data['publicId'], 300));
    $Tab1.=$PHPShopGUI->setField('Пароль для API', $PHPShopGUI->setInputText(false, 'api_new', $data['api'], 300));
    $Tab1.=$PHPShopGUI->setField('Описание назначения оплаты в произвольном формате', $PHPShopGUI->setInputText(false, 'description_new', $data['description'], 300));

    // Система налогообложения
    $tax_system = array (
        array("Общая система налогообложения", 0, $data["taxationSystem"]),
        array("Упрощенная система налогообложения (Доход)", 1, $data["taxationSystem"]),
        array("Упрощенная система налогообложения (Доход минус Расход)", 2, $data["taxationSystem"]),
        array("Единый налог на вмененный доход", 3, $data["taxationSystem"]),
        array("Единый сельскохозяйственный налог", 4, $data["taxationSystem"]),
        array("Патентная система налогообложения", 5, $data["taxationSystem"])
    );
    $Tab1.= $PHPShopGUI->setField('Cистема налогообложения', $PHPShopGUI->setSelect('taxationSystem_new', $tax_system, 300,true));

    // Доступые статусы заказов
    $PHPShopOrderStatusArray = new PHPShopOrderStatusArray();
    $OrderStatusArray = $PHPShopOrderStatusArray->getArray();
    $order_status_value[] = array(__('Новый заказ'), 0, $data['status']);
    if (is_array($OrderStatusArray))
        foreach ($OrderStatusArray as $order_status)
            $order_status_value[] = array($order_status['name'], $order_status['id'], $data['status']);

    // Статус заказа
    $Tab1.= $PHPShopGUI->setField('Оплата при статусе', $PHPShopGUI->setSelect('status_new', $order_status_value, 300));

    $Tab1.=$PHPShopGUI->setField('Описание оплаты', $PHPShopGUI->setTextarea('title_end_new', $data['title_end']));

    // Форма регистрации
    $Tab3 = $PHPShopGUI->setPay(false, false, $data['version'], false);

    $info = '
        <h4>Как подключиться к CloudPayments?</h4>
        <ol>
<li>Договориться о коммерческих условиях.</li>
<li>Ознакомиться с <a href="https://cloudpayments.ru/Docs/Oferta" target="_blank">договором-офертой</a></li>
<li>Заполнить <a href="https://cloudpayments.ru/Docs/%D0%9F%D1%80%D0%B8%D0%BB%D0%BE%D0%B6%D0%B5%D0%BD%D0%B8%D0%B5%201.docx" target="_blank">Приложение №1</a>, подписать, поставить печать и прислать на адрес sales@cloudpayments.ru</li>
<li>Проверить сайт на соответствие <a href="https://cloudpayments.ru/Docs/Requirements" target="_blank">требованиям</a></li>
<li>В личном кабинете CloudPayments создать сайт, указать адрес <code>http://' . $_SERVER['SERVER_NAME'] . '</code></li>
<li>Из настроек сайта в личном кабинете CloudPayments скопировать Public ID и Пароль для API, на закладке "Основное" модуля внести их в соответствующие поля</li>
<li>В личном кабинете CloudPayments указать адрес для Pay уведомлений <code>http://' . $_SERVER['SERVER_NAME'] . '/phpshop/modules/cloudpayments/payment/pay.php</code> HTTP метод POST, кодировка Windows-1251</li>
<li>В личном кабинете CloudPayments указать адрес для Check уведомлений <code>http://' . $_SERVER['SERVER_NAME'] . '/phpshop/modules/cloudpayments/payment/check.php</code> HTTP метод POST, кодировка Windows-1251</li>
</ol>
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