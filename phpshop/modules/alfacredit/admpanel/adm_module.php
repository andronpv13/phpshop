<?php

$PHPShopOrm = new PHPShopOrm($PHPShopModules->getParam("base.alfacredit.alfacredit_system"));

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
    
    if (!isset($_POST['prod_mode_new'])) $_POST['prod_mode_new'] = '0';
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
    
        // Размер названия поля
    $PHPShopGUI->field_col = 3;

    $data = $PHPShopOrm->select();

    $Tab1 = $PHPShopGUI->setField('* ИНН', $PHPShopGUI->setInputText(false, 'inn_new', $data['inn'], 300), 1, "ИНН интернет-магазина.");
    $Tab1 .= $PHPShopGUI->setField('* Код категории товара', $PHPShopGUI->setInputText(false, 'category_name_new', $data['category_name'], 300), 1, "Требуется сообщить данный код менеджеру Альфа Банка или отправить на is_support@alfabank.ru Банк, зная Вашу категорию, автоматически сопоставит ее с категорией Банка.");
    $Tab1 .= $PHPShopGUI->setField('Код акции (если есть)', $PHPShopGUI->setInputText(false, 'action_name_new', $data['action_name'], 300), 1, "Код/название акции (кредитного продукта банка с акцией).");
    $Tab1 .= $PHPShopGUI->setField('* Минимальная сумма товара/заказа для кредита', $PHPShopGUI->setInputText(false, 'min_sum_cre_new', $data['min_sum_cre'], 300), 1, "Минимальная сумма, при которой выводить вариант оплаты товара/заказа в кредит.");
    $Tab1 .= $PHPShopGUI->setField('Надпись кнопки', $PHPShopGUI->setInputText(false, 'cre_name_new', $data['cre_name'], 300), 1, "Надпись на кнопке/варианте оплаты при варианте оплаты в кредит.");
    $Tab1 .= $PHPShopGUI->setField('Минимальная сумма товара/заказа для рассрочки', $PHPShopGUI->setInputText(false, 'min_sum_ras_new', $data['min_sum_ras'], 300), 1, "Минимальная сумма, при которой выводить вариант оплаты товара/заказа в рассрочку (оставить пустым или 0 если нет).");
    $Tab1 .= $PHPShopGUI->setField('Надпись кнопки', $PHPShopGUI->setInputText(false, 'ras_name_new', $data['ras_name'], 300), 1, "Надпись на кнопке/варианте оплаты при варианте оплаты в рассрочку.");
    $Tab1 .= $PHPShopGUI->setField('Режим товара', $PHPShopGUI->setCheckbox("prod_mode_new", 1, "Выводить кнопку продажи в кредит/рассрочку на странице товара", $data["prod_mode"]));
    
    // Форма регистрации
    $Tab3 = $PHPShopGUI->setPay(null, false, $data['version'], true);

    // Инструкция
    $info = '
        <h4>Информация о модуле</h4>
        <p>Модуль позволяет интегрировать кнопку "Купи легко" от Альфа-Банк:</p>
        <ol>
        <li><strong>В карточку товара</strong>. Для этого разместите переменную <kbd>@acredit_product@</kbd> в шаблоне <code>phpshop/templates/имя шаблона/product/main_product_forma_full.tpl</code> в нужном вам месте.</li>
        <li><strong>В способы оплаты</strong>. Для передачи всех данных о заказе из корзины</li>
        </ol>

        <h4>Настройка модуля</h4>
        <ol>
<li>Предоставить необходимые документы и заключить договор с <a href="https://anketa.alfabank.ru/kupilegko/" target="blank">Альфа-Банк</a>.</li>
<li>Прислать URL, на который необходимо направлять статусы на адрес <a href="mailto:is_support@alfabank.ru" target="blank">is_support@alfabank.ru</a> в формате: ИНН Интернет-магазина (<code>Ваш ИНН</code>), Статус (<code>ANY</code>), URL (<code>'.$_SERVER['SERVER_NAME'].'/phpshop/modules/alfacredit/status/accept.php</code>)</li>
<li>Сообщите банку, что готовы к прохождению модерации.</li>
<li>Для персонализации формы вывода отредактируйте шаблоны <code>phpshop/modules/alfacredit/templates/</code></li>
</ol>
';

    
    // Вывод формы закладки
    $PHPShopGUI->setTab(array("Основное", $Tab1,true), array("Инструкция", $PHPShopGUI->setInfo($info)), array("О Модуле", $Tab3), array("Заявки", null, '?path=modules.dir.alfacredit'));

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
