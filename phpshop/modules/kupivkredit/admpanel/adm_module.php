<?php

$PHPShopOrm = new PHPShopOrm($PHPShopModules->getParam("base.kupivkredit.kupivkredit_system"));

/**
 * Обновление версии модуля
 * @return mixed
 */
function actionBaseUpdate(){
    global $PHPShopModules, $PHPShopOrm;

    $PHPShopOrm->clean();
    $option = $PHPShopOrm->select();
    $new_version = $PHPShopModules->getUpdate($option['version']);
    $PHPShopOrm->clean();
    $PHPShopOrm->update(array('version_new' => $new_version));
}

/**
 * Обновление настроек
 * @return mixed
 */
function actionUpdate(){
    global $PHPShopModules, $PHPShopOrm;
    
    // Настройки витрины
    $PHPShopModules->updateOption($_GET['id'], $_POST['servers']);
    
    if (!isset($_POST['dev_mode_new'])) $_POST['dev_mode_new'] = '0';
    $PHPShopOrm->debug = false;
    $action = $PHPShopOrm->update($_POST);
    header('Location: ?path=modules&id=' . $_GET['id']);

    return $action;
}

/**
 * Отображение настроек модуля
 * @return bool
 */
function actionStart()
{
    global $PHPShopGUI, $PHPShopOrm;

    $data = $PHPShopOrm->select();

    $Tab1 = $PHPShopGUI->setField('ShopID', $PHPShopGUI->setInputText(false, 'shop_id_new', $data['shop_id'], 300), 1, "Уникальный идентификатор магазина, выдается банком при подключении.");
    $Tab1 .= $PHPShopGUI->setField('ShowcaseID', $PHPShopGUI->setInputText(false, 'showcase_id_new', $data['showcase_id'], 300), 1, "Идентификатор витрины магазина. В случае единственной витрины можно не указывать.");
    $Tab1 .= $PHPShopGUI->setField('Промокод в заказе', $PHPShopGUI->setInputText(false, 'promo_new', $data['promo'], 300));
    
    // Форма регистрации
    $Tab3 = $PHPShopGUI->setPay(null, false, $data['version'], true);

    // Инструкция
    $info = '
        <h4>Информация о модуле</h4>
        <p>Модуль позволяет интегрировать кнопку "Купи в кредит":</p>
        <ol>
        <li><strong>В карточку товара</strong>. Для этого разместите переменную <kbd>@kvk_product@</kbd> в шаблоне <code>phpshop/templates/имя шаблона/product/main_product_forma_full.tpl</code> в нужном вам месте.</li>
        <li><strong>В способы оплаты</strong>. Для передачи всех данных о заказе из корзины</li>
        </ol>

        <h4>Настройка модуля</h4>
        <ol>
 <li>Предоставить необходимые документы и <a href="https://www.tbank.ru/business/loans/?utm_source=partner_rko_sme&utm_medium=ptr.act&utm_campaign=sme.partners&partnerId=5-IV4AJGWE#form-application" target="blank">заключить договор с Т-банк</a>.</li>
<li>Сообщите банку, что готовы к прохождению модерации.</li>
<li>Значения <kbd>ShopId</kbd>, <kbd>ShowcaseId</kbd> и <kbd>PromoCode</kbd> для работы в боевом режиме магазину отправляет менеджер банка при успешной интеграции с тестовыми параметрами. Заполнить эти значения в настройках модуля.</li>
<li>Включить доступным для продажи в кредит товарам признак "Кредит доступен" в закдадке <kbd>Кредит</kbd>, указать Промокод (опционально, если получен от менеджера банка).</li>
<li>Для персонализации формы вывода отредактируйте шаблоны <code>phpshop/modules/kupivkredit/templates/</code></li>
</ol>
';

    
    // Вывод формы закладки
    $PHPShopGUI->setTab(array("Основное", $Tab1,true), array("Инструкция", $PHPShopGUI->setInfo($info)), array("О Модуле", $Tab3));

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
$PHPShopGUI->setAction($_GET['id'], 'actionStart');
