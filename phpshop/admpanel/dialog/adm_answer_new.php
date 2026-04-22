<?php

$TitlePage = __('Создание Ответа Диалога');
$PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['dialog_answer']);

// Заполняем выбор
function setSelectChek($n) {
    $i = 1;
    while ($i <= 10) {
        if ($n == $i)
            $s = "selected";
        else
            $s = "";
        $select[] = array($i, $i, $s);
        $i++;
    }
    return $select;
}

function actionStart() {
    global $PHPShopGUI, $PHPShopSystem, $TitlePage, $PHPShopModules;

    // Выборка
    $data['enabled'] = $data['view'] = 1;
    $data['name'] = __('Подсказка');

    $PHPShopGUI->setActionPanel($TitlePage, false, array('Сохранить и закрыть'));
    $PHPShopGUI->field_col = 2;

    // Редактор 1
    $PHPShopGUI->setEditor('none');
    $oFCKeditor = new Editor('message_new');
    $oFCKeditor->Height = '150';
    $oFCKeditor->Value = $data['message'];

    $Select1 = setSelectChek($data['num']);

    // Содержание закладки 1
    $Tab1 = $PHPShopGUI->setField("Название", $PHPShopGUI->setInput("text", "name_new", $data['name'])) .
            $PHPShopGUI->setField("Статус", $PHPShopGUI->setRadio("enabled_new", 1, "Включить", $data['enabled']) . $PHPShopGUI->setRadio("enabled_new", 0, "Выключить", $data['enabled'])) .
            $PHPShopGUI->setField("Подсказка в чате", $PHPShopGUI->setRadio("view_new", 1, "Включить", $data['view']) . $PHPShopGUI->setRadio("view_new", 2, "Выключить", $data['view'])) .
            $PHPShopGUI->setField("Позиция", $PHPShopGUI->setSelect("num_new", $Select1, 50));

    $Tab1 .= $PHPShopGUI->setField("Витрины", $PHPShopGUI->loadLib('tab_multibase', $data, 'catalog/'));

    $Tab1 .= $PHPShopGUI->setField("Содержание", $oFCKeditor->AddGUI());

    // Запрос модуля на закладку
    $PHPShopModules->setAdmHandler(__FILE__, __FUNCTION__, $data);

    // Вывод формы закладки
    $PHPShopGUI->setTab(array("Основное", $Tab1, true, false, true));

    // Вывод кнопок сохранить и выход в футер
    $ContentFooter = $PHPShopGUI->setInput("submit", "saveID", "ОК", "right", 70, "", "but", "actionInsert.shopusers.create");

    // Футер
    $PHPShopGUI->setFooter($ContentFooter);
    return true;
}

// Функция записи
function actionInsert() {
    global $PHPShopOrm, $PHPShopModules;

    // Мультибаза
    if (is_array($_POST['servers'])) {
        $_POST['servers_new'] = "";
        foreach ($_POST['servers'] as $v)
            if ($v != 'null' and ! strstr($v, ',') and $v != null and ! strstr($_POST['servers_new'], "i" . $v . "i"))
                $_POST['servers_new'] .= "i" . $v . "i";
    }

    // Перехват модуля
    $PHPShopModules->setAdmHandler(__FILE__, __FUNCTION__, $_POST);
    $action = $PHPShopOrm->insert($_POST);

    header('Location: ?path=dialog');
}

// Обработка событий 
$PHPShopGUI->getAction();

// Вывод формы при старте
$PHPShopGUI->setLoader($_POST['editID'], 'actionStart');
?>