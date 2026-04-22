<?php

$TitlePage = __('Создание Юридического лица');
$PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['company']);

function actionStart() {
    global $PHPShopGUI, $PHPShopSystem, $TitlePage, $PHPShopModules;


    $PHPShopGUI->setActionPanel($TitlePage, false, array('Сохранить и закрыть'));
    // Размер названия поля
    $PHPShopGUI->field_col = 3;
    $data=$bank=[];
    $data = $PHPShopGUI->valid($data,'name');
    $bank =  $PHPShopGUI->valid($bank ,'org_ur_adre','org_adres','nds','org_inn','org_kpp','org_ogrn','org_schet','org_bank','org_bic','org_bank_schet','org_stamp','org_sig','org_sig_buh','org_logo','org_ur_adres');

    $forma_value[] = array("Индивидуальный предприниматель", 1, 0);
    $forma_value[] = array("Общество с ограниченной ответственностью", 2, 2);

    $Tab1 = $PHPShopGUI->setField("Наименование организации", $PHPShopGUI->setInputText(null, "name_new", $data['name']));
    $Tab1 .= $PHPShopGUI->setField("Форма собственности", $PHPShopGUI->setSelect('bank[org_forma]', $forma_value, 350,true));
    $Tab1 .= $PHPShopGUI->setField("Юридический адрес", $PHPShopGUI->setInputText(null, "bank[org_ur_adres]", $bank['org_ur_adres']));
    $Tab1 .= $PHPShopGUI->setField("Фактический адрес", $PHPShopGUI->setInputText(null, "bank[org_adres]", $bank['org_adres']));
    $Tab1 .= $PHPShopGUI->setField("Значение НДС", $PHPShopGUI->setInputText(false, 'bank[nds]', intval($bank['nds']), 100, '%'));
    $Tab1 .= $PHPShopGUI->setField("ИНН", $PHPShopGUI->setInputText(null, "bank[org_inn]", $bank['org_inn'], 350));
    $Tab1 .= $PHPShopGUI->setField("КПП", $PHPShopGUI->setInputText(null, "bank[org_kpp]", $bank['org_kpp'], 350));
    $Tab1 .= $PHPShopGUI->setField("ОГРН / ОГРНИП", $PHPShopGUI->setInputText(null, "bank[org_ogrn]", $bank['org_ogrn'], 350));
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
    $ContentFooter = $PHPShopGUI->setInput("submit", "saveID", "ОК", "right", 70, "", "but", "actionInsert.menu.create");

    // Футер
    $PHPShopGUI->setFooter($ContentFooter);
    return true;
}

// Функция записи
function actionInsert() {
    global $PHPShopOrm, $PHPShopModules;

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
    $PHPShopOrm->debug = true;
    $action = $PHPShopOrm->insert($_POST);

    header('Location: ?path=' . $_GET['path']);
}

// Обработка событий 
$PHPShopGUI->getAction();

// Вывод формы при старте
$PHPShopGUI->setLoader($_POST['editID'], 'actionStart');
?>