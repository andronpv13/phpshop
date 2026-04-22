<?php

$TitlePage = __("Локализация");
$PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['system']);

// Стартовый вид
function actionStart() {
    global $PHPShopGUI, $PHPShopModules, $TitlePage, $PHPShopOrm;

    $data = $PHPShopOrm->select();
    $option = unserialize($data['admoption']);

    $langFile = $GLOBALS['_classPath'] . 'locale/' . $option['lang'] . '/shop.ini';


    if (file_exists($langFile . '.bak')) {
        $PHPShopGUI->action_button['Сбросить изменения'] = array(
            'name' => 'Сбросить изменения',
            'action' => 'restoreID',
            'class' => 'btn btn-default btn-sm navbar-btn',
            'type' => 'submit',
            'icon' => 'glyphicon glyphicon-refresh'
        );

        // Подсказки
        $langArrayHelp = parse_ini_file_true($langFile. '.bak', 1);
    }

    $PHPShopGUI->addJSFiles('./js/jquery.waypoints.min.js', './system/gui/system.gui.js');
    $PHPShopGUI->setActionPanel($TitlePage, false, array('Сбросить изменения', 'Сохранить'));


    if (is_file($langFile)) {
        $langArray = parse_ini_file_true($langFile, 1);

        if (is_array($langArray['lang']))
            foreach ($langArray['lang'] as $k => $v) {

                if (!empty($langArrayHelp['lang'][$k]))
                    $help = $langArrayHelp['lang'][$k];
                else
                    $help = $v;

                $Tab1 .= $PHPShopGUI->setDiv(null, $PHPShopGUI->setInputText(null, 'lang[' . $k . ']', $v, false, false, false, false, $help), 'padding:7px');
            }

        if (is_array($langArray['locale']))
            foreach ($langArray['locale'] as $k => $v) {

                if (!empty($langArrayHelp['locale'][$k]))
                    $help = $langArrayHelp['locale'][$k];
                else
                    $help = $v;

                $Tab2 .= $PHPShopGUI->setDiv(null, $PHPShopGUI->setInputText(null, 'locale[' . $k . ']', $v, false, false, false, false, $help), 'padding:7px');
            }
    }

    $locale_array = array(
        'russian' => 'Русский',
        'ukrainian' => 'Українська',
        'belarusian' => 'Беларускі',
        'english' => 'English'
    );

    $PHPShopGUI->setTab(array($locale_array[$option['lang']], $PHPShopGUI->setCollapse('@lang@', $Tab1) . $PHPShopGUI->setCollapse('{locale}', $Tab2), true, false, true));

    // Запрос модуля на закладку
    $PHPShopModules->setAdmHandler(__FILE__, __FUNCTION__, $data);

    // Вывод кнопок сохранить и выход в футер
    $ContentFooter = $PHPShopGUI->setInput("hidden", "rowID", $data['id'], "right", 70, "", "but") .
            $PHPShopGUI->setInput("submit", "editID", "Сохранить", "right", 70, "", "but", "actionUpdate.system.edit") .
            $PHPShopGUI->setInput("submit", "restoreID", "Сохранить", "right", 70, "", "but", "actionRestore.system.edit") .
            $PHPShopGUI->setInput("submit", "saveID", "Применить", "right", 80, "", "but", "actionSave.system.edit");

    $PHPShopGUI->setFooter($ContentFooter);

    $sidebarleft[] = array('title' => 'Категории', 'content' => $PHPShopGUI->loadLib('tab_menu', false, './system/'));
    $PHPShopGUI->setSidebarLeft($sidebarleft, 2);

    // Футер
    $PHPShopGUI->Compile(2);
    return true;
}

/**
 * Экшен сброса правок
 */
function actionRestore() {

    // Выборка
    $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['system']);
    $data = $PHPShopOrm->select();
    $option = unserialize($data['admoption']);

    $langFile = $GLOBALS['_classPath'] . 'locale/' . $option['lang'] . '/shop.ini';

    // Бекап
    if (file_exists($langFile . '.bak')) {
        unlink($langFile);
        copy($langFile . '.bak', $langFile);
        unlink($langFile . '.bak');
    }

    header('Location: ?path=' . $_GET['path']);
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
    $option = unserialize($data['admoption']);
    $content = null;

    $langFile = $GLOBALS['_classPath'] . 'locale/' . $option['lang'] . '/shop.ini';
    if (is_file($langFile)) {
        $langArray = parse_ini_file_true($langFile, 1);

        if (is_array($langArray['lang']) and is_array($_POST['lang'])) {
            foreach ($_POST['lang'] as $k => $v) {
                if (!empty($v))
                    $langArray['lang'][$k] = $v;
            }
        }

        if (is_array($langArray['locale']) and is_array($_POST['locale'])) {
            foreach ($_POST['locale'] as $k => $v) {
                if (!empty($v))
                    $langArray['locale'][$k] = $v;
            }
        }

        if (is_array($langArray)) {
            foreach ($langArray as $k => $v) {
                $content .= '[' . $k . ']' . PHP_EOL;

                if (is_array($v))
                    foreach ($v as $i => $j)
                        $content .= $i . '="' . $j . '"' . PHP_EOL;
            }
        }

        // Бекап
        if (!file_exists($langFile . '.bak'))
            copy($langFile, $langFile . '.bak');

        if (file_put_contents($langFile, $content))
            $action = true;
    }

    // Перехват модуля
    $PHPShopModules->setAdmHandler(__FILE__, __FUNCTION__, $_POST);

    return array("success" => $action);
}

// Обработка событий
$PHPShopGUI->getAction();
?>