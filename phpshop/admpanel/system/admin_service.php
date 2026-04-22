<?php

$TitlePage = __("Обслуживание");
$PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['system']);

// Стартовый вид
function actionStart() {
    global $PHPShopGUI, $PHPShopModules, $TitlePage, $PHPShopOrm, $PHPShopBase, $hideCatalog, $hideSite, $PHPShopSystem;

    // Выборка
    $data = $PHPShopOrm->select();
    $option = unserialize($data['admoption']);

    // Размер названия поля
    $PHPShopGUI->field_col = 3;

    $PHPShopGUI->setActionPanel($TitlePage, false, array('Сохранить'));
    $PHPShopGUI->addJSFiles('./js/jquery.waypoints.min.js', './system/gui/system.gui.js');

    // Редактор 1
    $PHPShopGUI->setEditor($PHPShopSystem->getSerilizeParam("admoption.editor"));
    $oFCKeditor = new Editor('service_content');
    $oFCKeditor->Height = '350';
    $oFCKeditor->Value = $option['service_content'];

    // Режим обслуживания
    $PHPShopGUI->_CODE .= $PHPShopGUI->setCollapse('Обслуживание', $PHPShopGUI->setField("Режим обслуживания", $PHPShopGUI->setCheckbox('option[service_enabled]', 1, 'Включить вывод сообщения о проведении технических работ на сайте', $option['service_enabled'])) .
            $PHPShopGUI->setField('Служебные IP адреса', $PHPShopGUI->setTextarea('option[service_ip]', $option['service_ip'], false, $width = false, 50), 1, 'Укажите IP адреса через запятую') .
            $PHPShopGUI->setField('Заголовок', $PHPShopGUI->setInputText(null, 'option[service_title]', $option['service_title'])) .
            $PHPShopGUI->setField('Сообщение', $oFCKeditor->AddGUI())
    );


    $PHPShopGUI->_CODE .= $PHPShopGUI->setCollapse('Защита', $PHPShopGUI->setField('Заблокированные IP адреса', $PHPShopGUI->setTextarea('option[block_ip]', $option['block_ip'], false, $width = false, 100), 1, 'Укажите IP адреса через запятую') .
            $PHPShopGUI->setField("Режим блокировки ботов", $PHPShopGUI->setRadio("option[block_bot]", 0, "Не используется", $option['block_bot']) . '<br>' .
                    $PHPShopGUI->setRadio("option[block_bot]", 2, "Разрешать и добавлять новых ботов в журнал", $option['block_bot']) . '<br>' .
                    $PHPShopGUI->setRadio("option[block_bot]", 1, "Блокировать всех новых ботов", $option['block_bot'])
            ) .
            $PHPShopGUI->setField('Список поисковых ботов', $PHPShopGUI->loadLib('tab_bot', $data, 'system/'), 1, 'Боты добавляются автоматически по анализу трафика')
    );

    if (!class_exists('Memcached')) {
        $disabled_memcached = 'disabled="disabled"';
    }

    if (empty($_SESSION['mod_pro'])) {
        $disabled_cache = 'disabled="disabled"';
        $disabled_cache_info = ' (только для Pro)';
    }

    $cache_value[] = array(__('Файловая система'), 2, $option['cache']);
    $cache_value[] = array(__('База данных MySQL'), 3, $option['cache']);
    $cache_value[] = array(__('Сервер кеширования Memcached'), 1, $option['cache'], $disabled_memcached);

    $cache_time_value = $PHPShopGUI->setSelectValue($option['cache_time'], 10);

    if (empty($option['cache_gzip']))
        $option['cache_gzip'] = 1;
    $cache_gzip_value = $PHPShopGUI->setSelectValue($option['cache_gzip'], 9);

    $cache_mod_value[] = array(__('Не используется'), 0, $option['cache_seo']);
    $cache_mod_value[] = array(__('HTML страницы целиком' . $disabled_cache_info), 1, $option['cache_mod'], $disabled_cache);
    $cache_mod_value[] = array(__('Только статические элементы' . $disabled_cache_info), 2, $option['cache_mod'], $disabled_cache);

    $cache_seo_value[] = array(__('Не используется'), 0, $option['cache_seo']);
    $cache_seo_value[] = array(__('HTML страницы целиком' . $disabled_cache_info), 1, $option['cache_seo'], $disabled_cache);
    $cache_seo_value[] = array(__('Только статические элементы' . $disabled_cache_info), 2, $option['cache_seo'], $disabled_cache);

     if ($GLOBALS['PHPShopBase']->codBase == 'utf-8' and empty($disabled_cache))
         $disabled_cache_utf='disabled="disabled"';
     else $disabled_cache_utf=$disabled_cache;

    $PHPShopGUI->_CODE .= $PHPShopGUI->setCollapse('Быстродействие', $PHPShopGUI->setField("Хранение кеша", $PHPShopGUI->setSelect('option[cache]', $cache_value)) .
            $PHPShopGUI->setField("Тип кеша для пользователей", $PHPShopGUI->setSelect('option[cache_mod]', $cache_mod_value)) .
            $PHPShopGUI->setField("Тип кеша для поисковых ботов", $PHPShopGUI->setSelect('option[cache_seo]', $cache_seo_value) . '<br>' .
                    $PHPShopGUI->setCheckbox('option[cache_seo_utf]', 1, 'Кодировка UTF-8 для ботов', $option['cache_seo_utf'], $disabled_cache_utf)) .
            $PHPShopGUI->setField("Кол-во дней хранение кеша", $PHPShopGUI->setSelect('option[cache_time]', $cache_time_value, 50, true) . '<br>' .
                    $PHPShopGUI->setCheckbox('cache_clean', 1, 'Очистить кеш', 0, $disabled_cache)) .
            $PHPShopGUI->setField("Оптимизация кода", $PHPShopGUI->setCheckbox('option[cache_compres]', 1, 'Удаление комментариев и форматирования из HTML кода страниц', $option['cache_compres'])) .
            $PHPShopGUI->setField("GZIP сжатие", $PHPShopGUI->setSelect('option[cache_gzip]', $cache_gzip_value, 50, true)) .
            $PHPShopGUI->setField("Счетчик", $PHPShopGUI->setCheckbox('option[cache_debug]', 1, 'Показать время генерации страниц', $option['cache_debug'])) .
            $PHPShopGUI->setField("Оптимизация статических файлов", $PHPShopGUI->setCheckbox('option[min]', 1, 'Удаление комментариев и форматирования из JS и CSS файлов', $option['min']))
    );

    // Robots.txt
    if (file_exists($_SERVER['DOCUMENT_ROOT'] . '/robots.txt'))
        $robots = file_get_contents($_SERVER['DOCUMENT_ROOT'] . '/robots.txt');
    else
        $robots = null;



    $PHPShopGUI->_CODE .= $PHPShopGUI->setCollapse('Индексирование', 
            $PHPShopGUI->setField('Метатег для Яндекс Вебмастер', $PHPShopGUI->setInputText(null, 'option[service_yandex_metatag]', $option['service_yandex_metatag'])) .
            $PHPShopGUI->setField('Метатег для Google Search Console', $PHPShopGUI->setInputText(null, 'option[service_google_metatag]', $option['service_google_metatag'])) .
            $PHPShopGUI->setField('Robots.txt', $PHPShopGUI->setTextarea('service_robots', $robots, false, $width = false, 500))
    );


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
    $PHPShopOrm->updateZeroVars('option.service_enabled', 'option.min', 'option.cache_debug', 'option.cache_compres','option.cache_seo_utf');

    if (is_array($_POST['option']))
        foreach ($_POST['option'] as $key => $val)
            $option[$key] = $val;

    $option['service_content'] = $_POST['service_content'];

    file_put_contents($_SERVER['DOCUMENT_ROOT'] . '/robots.txt', $_POST['service_robots']);

    $_POST['admoption_new'] = serialize($option);


    // Перехват модуля
    $PHPShopModules->setAdmHandler(__FILE__, __FUNCTION__, $_POST);

    $action = $PHPShopOrm->update($_POST, array('id' => '=' . $_POST['rowID']));

    // Чистка html кэша
    if (!empty($_POST['cache_clean']) and $_POST['option']['cache'] > 0) {
        PHPShopObj::loadClass('cache');
        $PHPShopCache = new PHPShopCache(false);
        $PHPShopCache->flush();
    }

    // Чистка css js кэша
    if (!empty($_POST['cache_clean']) and $_POST['option']['min'] > 0) {
        PHPShopObj::loadClass('cache');

        $PHPShopFileCache = new PHPShopFileCache(false);
        $PHPShopFileCache->dir = "/UserFiles/Cache/static/";
        $PHPShopFileCache->flush();
    }

    return array("success" => $action);
}

// Обработка событий
$PHPShopGUI->getAction();
?>