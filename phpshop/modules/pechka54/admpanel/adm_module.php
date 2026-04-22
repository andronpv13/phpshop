<?php

// SQL
$PHPShopOrm = new PHPShopOrm($PHPShopModules->getParam("base.pechka54.pechka54_system"));

// Обновление версии модуля
function actionBaseUpdate() {
    global $PHPShopModules, $PHPShopOrm;
    $PHPShopOrm->clean();
    $option = $PHPShopOrm->select();
    $new_version = $PHPShopModules->getUpdate(number_format($option['version'], 1, '.', false));
    $PHPShopOrm->clean();
    $PHPShopOrm->update(array('version_new' => $new_version));
}

function actionStart() {
    global $PHPShopGUI, $PHPShopOrm,$_classPath;


    $PHPShopGUI->addJSFiles($_classPath.'modules/pechka54/admpanel/gui/pechka54.gui.js');
    
    // Выборка
    $data = $PHPShopOrm->select();
    $Tab1.= $PHPShopGUI->setField('Пароль', $PHPShopGUI->setInputText('', 'password_new', $data['password'], 300).$PHPShopGUI->setHelp('URL обмена данными: http://'.$_SERVER['SERVER_NAME'].'/phpshop/modules/pechka54/api.php?key=<span name="kkm_key">'.$data['password'].'</span>'),false,'Защита обработчика данных');
    $Tab1.= $PHPShopGUI->setField('№ ККМ', $PHPShopGUI->setInputText(false, 'kkm_new', $data['kkm'], 300,false, false, false, 1234567890));


    // НДС
    include_once($_classPath . 'modules/pechka54/class/pechka54.class.php');
    $Pechka54Rest = new Pechka54Rest();
    $nds_array = $Pechka54Rest->taxes;
    if (is_array($nds_array))
        foreach ($nds_array as $val) {
            $tax_product_value[] = array($val['tax_name'], $val['tax_id'], $data['tax_product']);
            $tax_delivery_value[] = array($val['tax_name'], $val['tax_id'], $data['tax_delivery']);
        }


    $Tab1.= $PHPShopGUI->setField('НДС для товаров', $PHPShopGUI->setSelect('tax_product_new', $tax_product_value,300));
    $Tab1.= $PHPShopGUI->setField('НДС для доставки', $PHPShopGUI->setSelect('tax_delivery_new', $tax_delivery_value,300));

    // Интструкция
    $info = '
        <p>Печать чека происходит заказов только со статусом "<b>Оплачено платежными системами</b>" с ID = 101. Такой статус заказ получает автоматически после успешной оплаты через любой платежный модуль (Альфабанк, Тинькофф и другие).</p>
<h4>Шаг №1 - Настройка модуля</h4>
        <ol>
        <li>Заполнить поле пароля обработчика обмена данными с кассой для защиты от несанкционированного доступа.</li>
        <li>Скопировать URL обмена данными <code>http://'.$_SERVER['SERVER_NAME'].'/phpshop/modules/pechka54/api.php?key=<span name="kkm_key">'.$data['password'].'</span></code>
        <li>Заполнить поле <kbd>№ ККМ</kbd> номером кассового накопителя. При использовании виртуальной кассы и отладки, вместо реального номера ККМ указывается виртуальный <code>1234567890</code>.
        </ol>
        
       <h4>Шаг №2 - Настройка приложения "Печка54"</h4>
        <ol>
        <li>Скачать программу <a href="http://54online.com/?p=56516611" target="_blank">Печка54</a> с сайта разработчика.
        <li>Произвести настройки приложения Печка54 по <a href="https://www.youtube.com/watch?v=PVQOX4r4ty8 " target="_blank">видео-инструкции</a>
        <li>В меню <kbd>Сайты</kbd> добавить свой сайт <b>'.$_SERVER['SERVER_NAME'].'</b> и заполнить поле "URL обмена данными"  <code>http://'.$_SERVER['SERVER_NAME'].'/phpshop/modules/pechka54/api.php?key=<span name="kkm_key">'.$data['password'].'</span></code>
        <li>Выполнить синхронизацию касс через меню <kbd>Кассы</kbd> - "Текущая касса" -  <kbd>Синхронизировать с сайтом</kbd>
        </ol>
        
        <h4>Шаг №3 - Настройка модуля</h4>
        <ol>
        <li>Заполнить поле регистрационного номера ККТ.
        <li>Сопоставить значение типа НДС, полученные при синхронизации, с НДС товаров и доставки в магазине.</li>
        </ol>

        <h4>Журнал работы кассы</h4>
        <ol>
        <li>Все операции с кассой заносятся в <a href="?path=modules.dir.pechka54">Журнал операций</a>.
        <li>Подробную информацию по состоянию операции можно получить при клике по номеру чека в журнале операций.
        </ol>
';

    $Tab3 = $PHPShopGUI->setPay(false, false, $data['version'], false);

    // Вывод формы закладки
    $PHPShopGUI->setTab(array("Основное", $Tab1, true), array("Инструкция", $PHPShopGUI->setInfo($info)), array("О Модуле", $Tab3), array("Журнал операций", null, '?path=modules.dir.pechka54'));

    // Вывод кнопок сохранить и выход в футер
    $ContentFooter =
            $PHPShopGUI->setInput("hidden", "rowID", $data['id']) .
            $PHPShopGUI->setInput("submit", "saveID", "Применить", "right", 80, "", "but", "actionUpdate.modules.edit");

    $PHPShopGUI->setFooter($ContentFooter);
    return true;
}

// Функция обновления
function actionUpdate() {
    global $PHPShopOrm;

    $PHPShopOrm->debug = false;
    $_POST['region_data_new'] = 1;
    $action = $PHPShopOrm->update($_POST);
    header('Location: ?path=modules&id=' . $_GET['id']);
    return $action;
}

// Обработка событий
$PHPShopGUI->getAction();

// Вывод формы при старте
$PHPShopGUI->setLoader($_POST['saveID'], 'actionStart');
?>