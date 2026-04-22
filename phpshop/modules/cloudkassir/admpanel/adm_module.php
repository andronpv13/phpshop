<?php

// SQL
$PHPShopOrm = new PHPShopOrm($PHPShopModules->getParam("base.cloudkassir.cloudkassir_system"));

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
    $Tab1 = $PHPShopGUI->setField('ИНН организации или ИП', $PHPShopGUI->setInputText(false, 'inn_new', $data['inn'], 300));
    $Tab1.= $PHPShopGUI->setField('Public ID', $PHPShopGUI->setInputText(false, 'publicid_new', $data['publicid'],300));
    $Tab1.= $PHPShopGUI->setField('API Secret', $PHPShopGUI->setInputText(false, 'apisecret_new', $data['apisecret'], 300));

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

    // Инструкция
    $info='<h4>Регистрация в онлайн-кассе CloudKassir</h4>
        <ol>
        <li><a href="https://cloudpayments.ru/Docs/Connect" target="_blank">Подключиться</a> к платежному сервису CloudKassir, если вы еще не являетесь партнером.</li>
        <li>Получить квалифицированную электронную подпись для работы с сайтом ФНС.</li>
        <li>Зарегистрироваться в личном кабинете налоговой службы:<br>
— <a href="http://lkul.nalog.ru/" target="_blank">для юридических лиц</a><br>
— <a href="https://lkip.nalog.ru/" target="_blank">для индивидуальных предпринимателей</a></li>
        <li>Заключить договор на онлайн-фискализацию.</li>
        <li>После подписания договора и оплаты счета вам будут предоставлены номер ККТ и ФН для регистрации в ФНС.</li>
        </ol>
        
        <h4>Настройка модуля</h4>
        <ol>
        <li>В поле "ИНН организации или ИП" введите ИНН вашей организации или ИП, на который зарегистрирована касса.</a></li>
        <li>В поля "Public ID" и "API Secret" введите Public ID и API Secret из личного кабинета CloudKassir</li>
        <li>Выбрать систему налогообложения</li>
        <li>В личном кабинете CloudKassir указать адрес для уведомлений о кассовых чеках <code>http://' . $_SERVER['SERVER_NAME'] . '/phpshop/modules/cloudkassir/notifications/receipt.php</code> HTTP метод POST, кодировка Windows-1251</li>
        </ol>
        
        <h4>Создание чеков прихода и возврата</h4>
        <ol>
        <li>Чеки прихода (продажа) создаются автоматически при срабатывании логики оповещения магазина об успешном платеже через любой шлюз оплаты или платежный модуль по приему электронных денег и карт. Настройка дополнительных параметров в способах оплаты не требуется.</li>
        <li>Выписанный чек доступен в закладке <kbd>Касса</kbd> в разделе редактирования заказа.</li>
        <li>Чек прихода можно выписать в ручном режиме для любого заказа в разделе редактирования заказа.</li>
        <li>Чек возврата прихода можно выписать в ручном режиме для любого заказа, имеющего чек прихода.</li>
        </ol>
        
        <h4>Журнал работы кассы</h4>
        <ol>
        <li>Все операции с кассой заносятся в <a href="?path=modules.dir.cloudkassir">Журнал операций</a>.</li>
        <li>Подробную информацию по состоянию операции можно получить при клике по номеру чека в журнале операций.</li>
        </ol>
        
        <h4>Настройка доставки</h4>
        <ol>
        <li>Параметр ставки НДС для доставки можно настроить в карточке редактирования доставки.</li>
        </ol>
';
    
    $Tab3 = $PHPShopGUI->setPay(false, false, $data['version'], false);

    // Вывод формы закладки
    $PHPShopGUI->setTab(array("Основное", $Tab1, true), array("Инструкция", $PHPShopGUI->setInfo($info),true), array("О Модуле", $Tab3), array("Журнал операций", null, '?path=modules.dir.cloudkassir'));

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
    $action = $PHPShopOrm->update($_POST);
    header('Location: ?path=modules&id=' . $_GET['id']);
    return $action;
}

// Обработка событий
$PHPShopGUI->getAction();

// Вывод формы при старте
$PHPShopGUI->setLoader($_POST['saveID'], 'actionStart');
?>