<?php

$TitlePage = __("SEO заголовки");
$PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['system']);

// Стартовый вид
function actionStart() {
    global $PHPShopGUI, $PHPShopModules, $TitlePage, $PHPShopOrm, $hideSite;

    // Выборка
    $data = $PHPShopOrm->select();
    $option = unserialize($data['admoption']);

    // Размер названия поля
    $PHPShopGUI->field_col = 3;
    $PHPShopGUI->addJSFiles('./js/jquery.waypoints.min.js', './system/gui/system.gui.js', './system/gui/headers.gui.js');
    $PHPShopGUI->setActionPanel($TitlePage, false, array('Сохранить'));

    $PHPShopGUI->_CODE .= $PHPShopGUI->setField('Основной заголовок (Title)', $PHPShopGUI->setTextarea('title_new', $data['title'], false, false, 100) . $PHPShopGUI->setAIHelpButton('title_new', 200, 'site_title'));

    $PHPShopGUI->_CODE .= $PHPShopGUI->setField('Основное описание (Description)', $PHPShopGUI->setTextarea('descrip_new', $data['descrip'], false, false, 100). $PHPShopGUI->setAIHelpButton('descrip_new', 200, 'site_descrip'));
    $PHPShopGUI->_CODE .= $PHPShopGUI->setField('Основные ключевые слова (Keywords)', $PHPShopGUI->setTextarea('keywords_new', $data['keywords'], false, false, 100));
    $PHPShopGUI->_CODE .= $PHPShopGUI->setField("Заголовок HSTS", $PHPShopGUI->setCheckbox('option[hsts]', 1, 'Открытие сайта только по протоколу HTTPS', $option['hsts']));
    $PHPShopGUI->_CODE .= $PHPShopGUI->setField("Ссылочная масса", $PHPShopGUI->setCheckbox('option[safe_links]', 1, 'Показывать отключенные товары по прямым ссылкам для поисковиков вместо 404 ошибки', $option['safe_links']));
    $PHPShopGUI->_CODE = $PHPShopGUI->setCollapse('Основные', $PHPShopGUI->_CODE);

    if (empty($hideSite)) {
        $PHPShopGUI->_CODE .= $PHPShopGUI->setCollapse('Шаблон каталога', $PHPShopGUI->loadLib('tab_headers', $data, './system/', 'catalog'));
        $PHPShopGUI->_CODE .= $PHPShopGUI->setCollapse('Шаблон подкаталога', $PHPShopGUI->loadLib('tab_headers', $data, './system/', 'podcatalog'));
        $PHPShopGUI->_CODE .= $PHPShopGUI->setCollapse('Шаблон товара', $PHPShopGUI->loadLib('tab_headers', $data, './system/', 'product'));
        $PHPShopGUI->_CODE .= $PHPShopGUI->setCollapse('Шаблон фильтра в каталоге', $PHPShopGUI->loadLib('tab_headers', $data, './system/', 'sort'));
    }

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
    $option = unserialize($data['admoption']);

    // Корректировка пустых значений
    $PHPShopOrm->updateZeroVars('option.safe_links','option.hsts');

    if (is_array($_POST['option']))
        foreach ($_POST['option'] as $key => $val)
            $option[$key] = $val;


    $_POST['admoption_new'] = serialize($option);


    // Перехват модуля
    $PHPShopModules->setAdmHandler(__FILE__, __FUNCTION__, $_POST);

    $action = $PHPShopOrm->update($_POST, array('id' => '=' . $_POST['rowID']));


    return array("success" => $action);
}

// Обработка событий
$PHPShopGUI->getAction();
?>