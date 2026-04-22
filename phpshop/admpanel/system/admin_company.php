<?php

$TitlePage = __("Реквизиты");
$PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['system']);

// Стартовый вид
function actionStart() {
    global $PHPShopGUI, $PHPShopModules, $TitlePage, $PHPShopOrm, $hideCatalog;

    // Выборка
    $data = $PHPShopOrm->select();
    $bank = unserialize($data['bank']);

    // Размер названия поля
    $PHPShopGUI->field_col = 3;
    $PHPShopGUI->addJSFiles('./system/gui/system.gui.js');
    $PHPShopGUI->setActionPanel($TitlePage, false, array('Сохранить'));

    $forma_value[] = array("Индивидуальный предприниматель", 1, $bank['org_forma']);
    $forma_value[] = array("Общество с ограниченной ответственностью", 2, $bank['org_forma']);

    $Tab1 = $PHPShopGUI->setField("Название магазина", $PHPShopGUI->setInputText(null, "name_new", $data['name']));
    $Tab1 .= $PHPShopGUI->setField("Владелец", $PHPShopGUI->setInputText(null, "company_new", $data['company']));
    $Tab1 .= $PHPShopGUI->setField("Телефон основной", $PHPShopGUI->setInputText(null, "tel_new", $data['tel']));
    $Tab1 .= $PHPShopGUI->setField("Телефон дополнительный", $PHPShopGUI->setInputText(null, "bank[org_tel]", $bank['org_tel']));
    $Tab1 .= $PHPShopGUI->setField("Режим работы", $PHPShopGUI->setInputText(null, "bank[org_time]", $bank['org_time']));
    $Tab1 .= $PHPShopGUI->setField("Наименование организации", $PHPShopGUI->setInputText(null, "bank[org_name]", $bank['org_name']));
    $Tab1 .= $PHPShopGUI->setField("Фактический адрес", $PHPShopGUI->setInputText(null, "bank[org_adres]", $bank['org_adres']));

    if (empty($hideCatalog)) {
        $Tab1 .= $PHPShopGUI->setField("Юридический адрес", $PHPShopGUI->setInputText(null, "bank[org_ur_adres]", $bank['org_ur_adres']));
        $Tab1 .= $PHPShopGUI->setField("Форма собственности", $PHPShopGUI->setSelect('bank[org_forma]', $forma_value, 350, true));
        $Tab1 .= $PHPShopGUI->setField("ИНН", $PHPShopGUI->setInputText(null, "bank[org_inn]", $bank['org_inn'], 350));

        if ($bank['org_forma'] == 1)
            $Tab1 .= $PHPShopGUI->setField("ОГРНИП", $PHPShopGUI->setInputText(null, "bank[org_ogrn]", $bank['org_ogrn'], 350));
        else {
            $Tab1 .= $PHPShopGUI->setField("КПП", $PHPShopGUI->setInputText(null, "bank[org_kpp]", $bank['org_kpp'], 350));
            $Tab1 .= $PHPShopGUI->setField("ОГРН", $PHPShopGUI->setInputText(null, "bank[org_ogrn]", $bank['org_ogrn'], 350));
        }

        $Tab1 .= $PHPShopGUI->setField("№ Счета организации", $PHPShopGUI->setInputText(null, "bank[org_schet]", $bank['org_schet'], 350));
        $Tab1 .= $PHPShopGUI->setLine() . $PHPShopGUI->setField("Наименование банк", $PHPShopGUI->setInputText(null, "bank[org_bank]", $bank['org_bank'], 350));
        $Tab1 .= $PHPShopGUI->setField("БИК", $PHPShopGUI->setInputText(null, "bank[org_bic]", $bank['org_bic'], 350));
        $Tab1 .= $PHPShopGUI->setField("№ Счета банка", $PHPShopGUI->setInputText(null, "bank[org_bank_schet]", $bank['org_bank_schet'], 350));
        $Tab1 .= $PHPShopGUI->setField("Печать", $PHPShopGUI->setIcon($bank['org_stamp'], "bank_org_stamp", false, array('load' => false, 'server' => true, 'url' => false, 'multi' => false, 'view' => false)));
        $Tab1 .= $PHPShopGUI->setField("Подпись руководителя", $PHPShopGUI->setIcon($bank['org_sig'], "bank_org_sig", false, array('load' => false, 'server' => true, 'url' => false, 'multi' => false, 'view' => false)));
        $Tab1 .= $PHPShopGUI->setField("Подпись бухгалтера", $PHPShopGUI->setIcon($bank['org_sig_buh'], "bank_org_sig_buh", false, array('load' => false, 'server' => true, 'url' => false, 'multi' => false, 'view' => false)));
        $Tab1 .= $PHPShopGUI->setField("Логотип для бланков", $PHPShopGUI->setIcon($bank['org_logo'], "bank_org_logo", false, array('load' => false, 'server' => true, 'url' => false, 'multi' => false, 'view' => false)));
    }

    $PHPShopGUI->_CODE = $PHPShopGUI->setCollapse('Реквизиты', $Tab1);

    // Запрос модуля на закладку
    $PHPShopModules->setAdmHandler(__FILE__, __FUNCTION__, $data);

    // Вывод кнопок сохранить и выход в футер
    $ContentFooter = $PHPShopGUI->setInput("hidden", "rowID", $data['id'], "right", 70, "", "but") .
            $PHPShopGUI->setInput("submit", "editID", "Сохранить", "right", 70, "", "but", "actionUpdate.system.edit") .
            $PHPShopGUI->setInput("submit", "saveID", "Применить", "right", 80, "", "but", "actionSave.system.edit");

    $PHPShopGUI->setFooter($ContentFooter);

    $sidebarleft[] = array('title' => 'Категории', 'content' => $PHPShopGUI->loadLib('tab_menu', false, './system/'));
    $PHPShopGUI->setSidebarLeft($sidebarleft, 2);

    // Футер
    $PHPShopGUI->Compile(2);
    return true;
}

/**
 * Экшен сохранения
 */
function actionSave() {

    // Сохранение данных
    actionUpdate();

    header('Location: ?path=' . $_GET['path']);
}

// Функция обновления
function actionUpdate() {
    global $PHPShopOrm, $PHPShopModules;

    // Выборка
    $data = $PHPShopOrm->select();
    $bank = unserialize($data['bank']);

    if (is_array($_POST['bank']))
        foreach ($_POST['bank'] as $key => $val)
            $bank[$key] = $val;

    $bank['org_stamp'] = $_POST['bank_org_stamp'];
    $bank['org_sig'] = $_POST['bank_org_sig'];
    $bank['org_sig_buh'] = $_POST['bank_org_sig_buh'];
    $bank['org_logo'] = $_POST['bank_org_logo'];
    $_POST['bank_new'] = serialize($bank);

    // Перехват модуля
    $PHPShopModules->setAdmHandler(__FILE__, __FUNCTION__, $_POST);

    $action = $PHPShopOrm->update($_POST, array('id' => '=' . $_POST['rowID']));


    return array("success" => $action);
}

// Обработка событий
$PHPShopGUI->getAction();
?>