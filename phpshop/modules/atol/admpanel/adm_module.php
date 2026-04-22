<?php

// SQL
$PHPShopOrm = new PHPShopOrm($PHPShopModules->getParam("base.atol.atol_system"));

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
    global $PHPShopGUI, $PHPShopOrm;


    // Выборка
    $data = $PHPShopOrm->select();
    $Tab1 = $PHPShopGUI->setField('Логин в Атол', $PHPShopGUI->setInputText(false, 'login_new', $data['login'], 300));
    $Tab1.= $PHPShopGUI->setField('Пароль в Атол', $PHPShopGUI->setInputText(false, 'password_new', $data['password'],300));
    $Tab1.= $PHPShopGUI->setField('Код группы в Атол', $PHPShopGUI->setInputText(false, 'group_code_new', $data['group_code'], 300));
    $Tab1.= $PHPShopGUI->setField('URL магазина в Атол', $PHPShopGUI->setInputText(false, 'payment_address_new', $data['payment_address'], 300));
    $Tab1.= $PHPShopGUI->setField('ИНН в Атол', $PHPShopGUI->setInputText(false, 'inn_new', $data['inn'], 300));
    $Tab1.= $PHPShopGUI->setField('Ручное управление', $PHPShopGUI->setCheckbox("manual_control_new", 1, "Отключить автоматическое создание чеков", $data["manual_control"]));

    // Интструкция
    $info='<h4>Регистрация в АТОЛ Онлайн</h4>
        <ol>
        <li>Зарегистрироваться на сервисе <a href="https://online.atol.ru/lk/Account/Register?partnerUid=deb4b494-75b2-423e-9af0-6b32df3c67a3" target="_blank">АТОЛ Онлайн</a>.
        <li>Пройти процедуру регистрации кассы по официальной инструкции <a href="http://www.phpshop.ru/UserFiles/File/atol.pdf" target="_blank">Atol.pdf</a>
        <li>Добавить новый магазин <b>'.$_SERVER['SERVER_NAME'].'</b> в список подключенных магазинов к кассе и получить параметры доступа к кассе.
        </ol>
        
        <h4>Настройка модуля</h4>
        <ol>
        <li>В полях "Логин в Атол","Пароль в Атол" и "Код группы в Атол" указываются данные, полученные после регистрации в АТОЛ Онлайн.</a>
        <li>Поле "URL магазина в Атол" должно <b>полностью совпадать</b> с адресом магазина, указанным в АТОЛ Онлайн.
        <li>Поле "ИНН в Атол" должно полностью совпадать с ИНН компании, указанным в АТОЛ Онлайн.
        </ol>
        
        <h4>Создание чеков прихода и возврата</h4>
        <ol>
        <li>Чеки прихода (продажа) создаются автоматически при срабатывании логики оповещения магазина об успешном платеже через любой шлюз оплаты или платежный модуль по приему электронных денег и карт. Настройка дополнительных параметров в способах оплаты не требуется.
        <li>Выписанный чек доступен в закладке <kbd>Касса</kbd> в разделе редактирования заказа.
        <li>Чек прихода можно выписать в ручном режиме для любого заказа в разделе редактирования заказа.
        <li>Чек возврата прихода можно выписать в ручном режиме для любого заказа, имеющий чек прихода.
        </ol>
        
        <h4>Журнал работы кассы</h4>
        <ol>
        <li>Все операции с кассой заносятся в <a href="?path=modules.dir.atol">Журнал операций</a>.
        <li>Подробную информацию по состоянию операции можно получить при клике по номеру чека в журнале операций.
        </ol>
        
        <h4>Настройка доставки</h4>
        <ol>
        <li>Параметр ставки НДС для доставки можно настроить в карточке редактирования доставки.
        </ol>
';
    
    $Tab3 = $PHPShopGUI->setPay(false, false, $data['version'], false);

    // Вывод формы закладки
    $PHPShopGUI->setTab(array("Основное", $Tab1, true), array("Инструкция", $PHPShopGUI->setInfo($info),true), array("О Модуле", $Tab3), array("Журнал операций", null, '?path=modules.dir.atol'));

    // Вывод кнопок сохранить и выход в футер
    $ContentFooter =
            $PHPShopGUI->setInput("hidden", "rowID", $data['id']) .
            $PHPShopGUI->setInput("submit", "saveID", "Применить", "right", 80, "", "but", "actionUpdate.modules.edit");

    $PHPShopGUI->setFooter($ContentFooter);
    return true;
}

// Функция обновления
function actionUpdate() {
    global $PHPShopOrm,$PHPShopModules;
    
    // Настройки витрины
    $PHPShopModules->updateOption($_GET['id'], $_POST['servers']);

    $PHPShopOrm->debug = false;
    $_POST['region_data_new']=1;

    if (empty($_POST["manual_control_new"]))
        $_POST["manual_control_new"] = 0;

    $action = $PHPShopOrm->update($_POST);
    header('Location: ?path=modules&id=' . $_GET['id']);
    return $action;
}

// Обработка событий
$PHPShopGUI->getAction();

// Вывод формы при старте
$PHPShopGUI->setLoader($_POST['saveID'], 'actionStart');
?>