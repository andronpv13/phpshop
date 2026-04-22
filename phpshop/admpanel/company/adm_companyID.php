<?php

$TitlePage = __('Редактирование Юридического лица').' #' . $_GET['id'];
$PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['company']);

function actionStart() {
    global $PHPShopGUI, $PHPShopSystem, $PHPShopOrm, $PHPShopModules;

    // Выборка
    $data = $PHPShopOrm->select(array('*'), array('id' => '=' . intval($_GET['id'])));
    $bank = unserialize($data['bank']);
    
    $PHPShopGUI->setActionPanel(__("Редактирование") .": ". $data['name'], array('Удалить'), array('Сохранить', 'Сохранить и закрыть'));
    $PHPShopGUI->field_col = 3;
    
    $forma_value[] = array("Индивидуальный предприниматель", 1, $bank['org_forma']);
    $forma_value[] = array("Общество с ограниченной ответственностью", 2, $bank['org_forma']);

    $Tab1 = $PHPShopGUI->setField("Наименование организации", $PHPShopGUI->setInputText(null, "name_new", $data['name']));
    $Tab1 .= $PHPShopGUI->setField("Форма собственности", $PHPShopGUI->setSelect('bank[org_forma]', $forma_value,350,true));
    $Tab1 .= $PHPShopGUI->setField("Юридический адрес", $PHPShopGUI->setInputText(null, "bank[org_ur_adres]", $bank['org_ur_adres']));
    $Tab1 .= $PHPShopGUI->setField("Фактический адрес", $PHPShopGUI->setInputText(null, "bank[org_adres]", $bank['org_adres']));
    $Tab1 .= $PHPShopGUI->setField("Значение НДС", $PHPShopGUI->setInputText(false, 'bank[nds]', intval($bank['nds']), 100, '%'));
    $Tab1 .= $PHPShopGUI->setField("ИНН", $PHPShopGUI->setInputText(null, "bank[org_inn]", $bank['org_inn'], 350));
    
    if($bank['org_forma'] == 1)
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

    // Запрос модуля на закладку
    $PHPShopModules->setAdmHandler(__FILE__, __FUNCTION__, $data);

    // Вывод формы закладки
    $PHPShopGUI->setTab(array("Основное", $Tab1, true));


    // Вывод кнопок сохранить и выход в футер
    $ContentFooter =
            $PHPShopGUI->setInput("hidden", "rowID", $data['id'], "right", 70, "", "but") .
            $PHPShopGUI->setInput("button", "delID", "Удалить", "right", 70, "", "but", "actionDelete.menu.edit") .
            $PHPShopGUI->setInput("submit", "editID", "Сохранить", "right", 70, "", "but", "actionUpdate.menu.edit") .
            $PHPShopGUI->setInput("submit", "saveID", "Применить", "right", 80, "", "but", "actionSave.menu.edit");

    // Футер
    $PHPShopGUI->setFooter($ContentFooter);
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

    if (is_array($_POST['bank']))
        foreach ($_POST['bank'] as $key => $val)
            $bank[$key] = $val;

    $bank['org_stamp'] = $_POST['bank_org_stamp'];
    $bank['org_sig'] = $_POST['bank_org_sig'];
    $bank['org_sig_buh'] = $_POST['bank_org_sig_buh'];
    $bank['org_logo'] = $_POST['bank_org_logo'];
    $_POST['bank_new'] = serialize($bank);
    
    if (empty($_POST['enabled_new']))
        $_POST['emabled_new'] = 0;
    
    // Перехват модуля
    $PHPShopModules->setAdmHandler(__FILE__, __FUNCTION__, $_POST);

    $action = $PHPShopOrm->update($_POST, array('id' => '=' . $_POST['rowID']));
    return array('success' => $action);
}

// Функция удаления
function actionDelete() {
    global $PHPShopOrm, $PHPShopModules;

    // Перехват модуля
    $PHPShopModules->setAdmHandler(__FILE__, __FUNCTION__, $_POST);
    $action = $PHPShopOrm->delete(array('id' => '=' . $_POST['rowID']));
    return array('success' => $action);
}

// Обработка событий
$PHPShopGUI->getAction();

// Вывод формы при старте
$PHPShopGUI->setAction($_GET['id'], 'actionStart', 'none');
?>