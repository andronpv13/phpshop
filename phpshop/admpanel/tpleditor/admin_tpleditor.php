<?php

// Заголовок
$TitlePage = __("Редактор шаблонов");
PHPShopObj::loadClass('page');

$skin_base_path = 'http://template.phpshop.ru';

function _tpl($file) {

    // Описание файлов
    $TemplateHelper = array(
        'banner' => 'Баннер',
        'baner_list_forma.tpl' => 'Форма баннера в колонке',
        'banner_horizontal_forma.tpl' => 'Форма горизонтального баннера',
        'banner_window_forma.tpl' => 'Форма всплывающего баннера',
        'catalog' => 'Каталог',
        'catalog_forma.tpl' => 'Форма каталога товаров',
        'catalog_forma_2.tpl' => 'Форма каталога товаров Б',
        'catalog_forma_3.tpl' => 'Форма каталога товаров C',
        'catalog_info_forma.tpl' => 'Форма описания каталога товаров',
        'catalog_page_forma.tpl' => 'Форма каталога статей',
        'catalog_page_forma_2.tpl' => 'Форма Б каталога статей',
        'catalog_page_info_forma.tpl' => 'Форма описания каталога статей',
        'catalog_table_forma.tpl' => 'Форма каталога продукции',
        'cid_category.tpl' => 'Форма списка каталогов',
        'catalog_list_forma.tpl' => 'Форма списка всех каталогов',
        'podcatalog_forma.tpl' => 'Форма подкаталога товаров',
        'podcatalog_page_forma.tpl' => 'Форма подкаталога статей',
        'main' => 'Основное',
        'index.tpl' => 'Главная страница',
        'left_menu.tpl' => 'Левый текстовый блок',
        'right_menu.tpl' => 'Правый текстовый блок',
        'shop.tpl' => 'Другие страницы',
        'top_menu.tpl' => 'Горизонтальное меню',
        'valuta_forma.tpl' => 'Форма выбора валюты',
        'news' => 'Новости',
        'main_news_forma.tpl' => 'Форма краткого описания',
        'main_news_forma_full.tpl' => 'Форма подробного описания',
        'news_main_mini.tpl' => 'Форма мини-новостей',
        'news_page_full.tpl' => 'Страница подробного описания',
        'news_page_list.tpl' => 'Страница списка новостей',
        'page' => 'Страницы',
        'page_catalog_list.tpl' => 'Форма страницы каталога статей',
        'page_page_list.tpl' => 'Форма страницы',
        'product' => 'Товары',
        'brand_uid_description.tpl' => 'Описание бренда',
        'main_product_forma_1.tpl' => 'Форма вывода товара в 1 ячейку',
        'main_product_forma_2.tpl' => 'Форма вывода товара в 2 ячейки',
        'main_product_forma_3.tpl' => 'Форма вывода товара в 3 ячейки',
        'main_product_forma_4.tpl' => 'Форма вывода товара в 4 ячейки',
        'main_product_forma_5.tpl' => 'Форма вывода товара в 5 ячеек',
        'main_product_forma_6.tpl' => 'Форма вывода товара в 6 ячеек',
        'main_product_forma_full.tpl' => 'Форма подробного описания товара',
        'main_product_forma_full_productArt.tpl' => 'Артикул',
        'main_product_odnotip_list.tpl' => 'Список однотипных продуктов',
        'main_spec_forma_icon.tpl' => 'Форма спецпредложений-иконок',
        'newtipIcon.tpl' => 'Стикер новинки',
        'product_odnotip_product_parent.tpl' => 'Блок починенных товаров',
        'product_odnotip_product_parent_one.tpl' => 'Форма подчиненного товара параметр 1',
        'product_option_product.tpl' => 'Форма выбора опций товара',
        'product_page_full.tpl' => 'Страница подробного описания товара',
        'product_page_list.tpl' => 'Страница списка товаров',
        'product_page_spec_list.tpl' => 'Страница спецпредложений',
        'product_pagetema_forma.tpl' => 'Форма статей к товару',
        'product_pagetema_list.tpl' => 'Список статей к товару',
        'specIcon.tpl' => 'Стикер спецпредложения',
        'style.css' => 'Cтили оформления',
        'container' => 'Вид каталога',
        'container_1.tpl' => 'Каталог слева',
        'container_2.tpl' => 'Каталог справа',
        'container_3.tpl' => 'Каталог сверху',
        'filter' => 'Вид фильтров',
        'warehouse.tpl' => 'Фильтр по складам',
        'filter_1.tpl' => 'Фильтр в виде кнопок',
        'filter_2.tpl' => 'Фильтр в виде чекбоксов',
        'footer' => 'Вид подвала',
        'footer_1.tpl' => 'Подвал вариант 1',
        'footer_2.tpl' => 'Подвал вариант 2',
        'footer_3.tpl' => 'Подвал вариант 3',
        'header' => 'Вид шапки',
        'header_1.tpl' => 'Шапка вариант 1',
        'header_2.tpl' => 'Шапка вариант 2',
        'header_3.tpl' => 'Шапка вариант 3',
        'page_forma.tpl' => 'Форма страницы в списке',
        'page_mini.tpl' => 'Форма страницы в превью',
        'page_top_menu.tpl' => 'Форма страницы в главном меню',
        'main_product_forma_full_1.tpl' => 'Форма подробного описания товара 1',
        'main_product_forma_full_2.tpl' => 'Форма подробного описания товара 2',
        'main_product_forma_full_3.tpl' => 'Форма подробного описания товара 3',
        'main_product_forma_full_ajax.tpl' => 'Форма ajax описания товара',
        'product_catalog_content.tpl' => 'Описание каталога в списке товаров',
        'promoIcon.tpl' => 'Стикер промоакции',
        'product_odnotip_product_parent_one_color.tpl' => 'Форма подчиненного товара параметр 2',
        'product_odnotip_product_parent_one_value.tpl' => 'Форма значения подчиненного товара',
        'preview_sort_one.tpl' => 'Вид характеристики в превью',
        'preview_sorts.tpl' => 'Форма блока характеристик в превью',
        'bottom_menu.tpl' => 'Меню в подвале',
        'top_catalog_forma.tpl' => 'Форма верхнего каталога товаров A',
        'top_catalog_forma_3.tpl' => 'Форма верхнего каталога товаров B',
        'top_podcatalog_forma.tpl' => 'Форма верхнего подкаталога товаров',
        'catalog_top_menu.tpl' => 'Форма каталога товаров в главном меню',
        'banner_menu_forma.tpl' => 'Баннер в меню каталгов'
    );

    if ($_GET['option'] != 'pro' && !empty($TemplateHelper[$file]))
        $result = __($TemplateHelper[$file]);
    else
        $result = $file;

    return substr($result, 0, 40);
}

/**
 * Вывод товаров
 */
function actionStart() {
    global $PHPShopGUI, $TitlePage, $PHPShopSystem, $selectModalBody, $hideSite, $hideCatalog;

    $PHPShopGUI->addJSFiles('./js/jquery.waypoints.min.js', './js/jquery.treegrid.js', './tpleditor/gui/tpleditor.gui.js', './tpleditor/gui/ace/ace.js', './js/bootstrap-tour.min.js');

    if ($GLOBALS['PHPShopBase']->codBase == 'utf-8')
        $PHPShopGUI->addJSFiles('./tpleditor/gui/tour_utf.gui.js');
    else
        $PHPShopGUI->addJSFiles('./tpleditor/gui/tour.gui.js');

    $ace = false;

    if (empty($_GET['option']) or $_GET['option'] == 'lite') {
        $lite_class = 'disabled';
        $pro_class = $option_str = null;
    } else {
        $lite_class = null;
        $pro_class = 'disabled';
        $option_str = '&option=' . $_GET['option'];
    }

    if (!empty($_GET['name']))
        $PHPShopGUI->action_select['Режим 1'] = array(
            'name' => 'Упрощенный режим',
            'url' => '?path=tpleditor&name=' . $_GET['name'] . '&option=lite',
            'class' => $lite_class
        );


    $PHPShopGUI->action_select['Режим 2'] = array(
        'name' => 'Расширенный режим',
        'url' => '?' . $_SERVER['QUERY_STRING'] . '&option=pro',
        'class' => $pro_class
    );

    $PHPShopGUI->action_select['Учебник'] = array(
        'name' => 'Инструкция',
        'url' => 'https://wiki.phpshop.ru/dizain/nastroika-shablona',
        'target' => '_blank'
    );

    $PHPShopGUI->action_select['Урок'] = array(
        'name' => 'Обучение',
        'action' => 'presentation',
        'icon' => ''
    );

    $PHPShopGUI->action_select['Магазин'] = array(
        'name' => 'Архивные шаблоны',
        'url' => 'http://template.phpshop.ru/?old',
        'icon' => '',
        'target' => '_blank'
    );

    $PHPShopGUI->action_select['Кастомизация'] = array(
        'name' => 'Кастомизация шаблона',
        'url' => 'https://www.phpshop.ru/calculation/brifdesign/',
        'icon' => 'glyphicon glyphicon-ruble',
        'target' => '_blank'
    );

    if (!empty($_GET['file'])) {

        $file = PHPShopSecurity::TotalClean('../templates/' . $_GET['name'] . '/' . $_GET['file']);
        $info = PHPShopSecurity::getExt($file);
        if (file_exists($file) and in_array($info, array('tpl', 'css'))) {
            $content = str_replace('textarea', 'txtarea', @file_get_contents($file));

            // Кодировка
            if ($GLOBALS['PHPShopBase']->codBase == 'utf-8')
                $content = PHPShopString::win_utf8($content, true);

            $ace = true;

            $PHPShopGUI->action_button['Размер'] = array(
                'name' => __('Размер'),
                'class' => 'ace-full btn btn-default btn-sm navbar-btn',
                'type' => 'button',
                'icon' => 'glyphicon glyphicon-resize-small glyphicon-fullscreen'
            );

            $PHPShopGUI->action_button['Выполнить'] = array(
                'name' => __('Сохранить'),
                'action' => 'editID',
                'class' => 'ace-save btn btn-default btn-sm navbar-btn',
                'type' => 'button',
                'icon' => 'glyphicon glyphicon-floppy-saved'
            );
        } else {

            $content = null;
        }
    }

    if (empty($_GET['mod']))
        $_GET['mod'] = true;

    switch ($_GET['mod']) {
        case 'html':
            $mod = 'rhtml';
            break;
        case 'css':
            $mod = 'css';
            break;
        default: $mod = 'rhtml';
    }

    if ($ace) {
        // Тема
        $theme = $PHPShopSystem->getSerilizeParam('admoption.ace_theme');
        if (empty($theme))
            $theme = 'dawn';

        $wysiwyg = xml2array('./tpleditor/gui/wysiwyg.xml', "template", true);
        $var_list = $selectModalBody = $selectModal = null;
        if (is_array($wysiwyg))
            foreach ($wysiwyg as $template) {
                if ('/' . $template['path'] == $_GET['file']) {

                    // Заголовок
                    if (!empty($_GET['option']) and $_GET['option'] == 'pro')
                        $TitlePage .= ': ' . $_GET['name'] . $_GET['file'];
                    else
                        $TitlePage .= ': ' . __($template['description']);

                    if (is_array($template['var']))
                        if (empty($template['var'][1])) {
                            $var_list .= '<button class="btn btn-xs btn-info editor_var" data-insert="@' . $template['var']['name'] . '@" type="button" data-toggle="tooltip" data-placement="top" title="' . __($template['var']['description']) . '"><span class="glyphicon glyphicon-tag"></span> ' . $template['var']['name'] . '</button>';
                            $selectModal .= '<tr><td>@' . $template['var']['name'] . '@</td><td>' . $template['var']['description'] . '</td></tr>';
                        } else {
                            foreach ($template['var'] as $var) {

                                // Поиск переменной в файле
                                if (preg_match("/@" . $var['name'] . "@/", $content)) {
                                    $class_btn = 'btn-default';
                                    $class_icon = 'glyphicon-tag';
                                } else {
                                    $class_btn = 'btn-info';
                                    $class_icon = 'glyphicon-plus';
                                }

                                $var_list .= '<button class="btn btn-xs ' . $class_btn . ' editor_var" data-insert="@' . $var['name'] . '@" type="button" data-toggle="tooltip" data-placement="top" title="' . __($var['description']) . '"><span class="glyphicon ' . $class_icon . '"></span> ' . $var['name'] . '</button>';

                                $selectModal .= '<tr><td><kbd>@' . $var['name'] . '@</kbd></td><td>' . __($var['description']) . '</td></tr>';
                            }
                        }
                }
            }

        if (!empty($var_list)) {
            $PHPShopGUI->_CODE = '<div class="panel panel-default" id="varlist">
            <div class="panel-body">' . $var_list . '<div class="text-right data-row"><a href="#" id="vartable" data-toggle="modal" data-target="#selectModal" data-title="' . $_GET['file'] . '"><span class="glyphicon glyphicon-question-sign"></span>' . __('Описание переменных') . '</a></div></div></div>';

            // Модальное окно таблицы описаний переменных
            $selectModalBody = '<table class="table table-striped"><tr><th>' . __('Переменная') . '</th><th>' . __('Описание') . '</th></tr>' . $selectModal . '</table>';
        }

        $PHPShopGUI->_CODE .= '<textarea class="hide hidden-edit" id="editor_src" name="editor_src" data-mod="' . $mod . '" data-theme="' . $theme . '">' . $content . '</textarea><pre id="editor">' . __('Загрузка...') . '</pre>';
    } else {
        $PHPShopGUI->_CODE = '<p class="text-muted hidden-xs data-row">' . __('Выберите установленный шаблон и файл для редактирования в левом меню.  
            Установка шаблона для отображения на сайте производится в основных системных настройках, закладка') . ' <a href="?path=system#1"><span class="glyphicon glyphicon-share-alt"></span>' . __('Настройка дизайна') . '</a>. ' . __('Цветовая тема подсветки синтаксиса меняется в основных системных настройках, закладка') . ' <a href="?path=system#4"><span class="glyphicon glyphicon-share-alt"></span>' . __('Настройка управления') . '</a>.</p>';

        // Карта шаблона
        if (!empty($_GET['name']))
            $PHPShopGUI->_CODE .= $PHPShopGUI->loadLib('tab_map', false);
    }

    $PHPShopGUI->setActionPanel(PHPShopSecurity::TotalClean($TitlePage), array('Режим 1', 'Режим 2', 'Учебник', 'Урок', '|', 'Кастомизация'), array('Размер', 'Учебник', 'Выполнить'));

    $dir = "../templates/*";
    $k = 1;

    // Стоп лист
    if (empty($_GET['option']) or $_GET['option'] == 'lite')
        $stop_array = array('css', 'icon', 'php', 'js', 'fonts', 'images', 'icon', 'modules', 'index.html', 'style.css', 'font', 'brands', 'breadcrumbs', 'calendar', 'clients', 'comment', 'error', 'forma', 'gbook', 'links', 'map', 'opros', 'order', 'paginator', 'price', 'print', 'search', 'slider', 'selection', 'users', 'pricemail', 'editor', 'assets', 'svg');
    else
        $stop_array = array('css', 'icon', 'php', 'js', 'fonts', 'images', 'icon', 'modules', 'index.html', 'style.css', 'font', 'assets', 'svg');

    if ($hideSite) {
        $stop_array[] = 'product';
        $stop_array[] = 'catalog';
    }

    if (empty($_GET['name'])) {

        // Левый сайдбар дерева шаблонов
        $tree = '<div><table class="table table-hover" id="template-tree">';

        $root = glob("../templates/*", GLOB_ONLYDIR);
        if (is_array($root)) {
            foreach ($root as $dir) {
                $path_parts = pathinfo($dir);
                $tree .= '<tr class="treegrid-all"><td><a href="?path=' . $_GET['path'] . '&name=' . $path_parts['basename'] . $option_str . '">' . ucfirst($path_parts['basename']) . '</a></td></tr>';
            }
        }
        $title_icon = null;

        // Дополнительные шаблоны
        $PHPShopGUI->_CODE .= $PHPShopGUI->loadLib('tab_base', $root);
    } else {

        // Левый сайдбар дерева шаблонов
        $tree = '<div id="template-tree-block"><table class="table tree table-hover" id="template-tree">';

        // Левый сайдбар дерева шаблонов
        $tree .= '<tr class="treegrid-all">
           <td><a href="?path=' . $_GET['path'] . $option_str . '" class="btn btn-default btn-sm">' . __('Все шаблоны') . '</a> <span class="glyphicon glyphicon-triangle-right"></span> <span class="btn btn-info btn-sm" id="templatename">' . @ucfirst(PHPShopSecurity::TotalClean($_GET['name'], 4)) . '</span></td>
	</tr>';

        $dir = '../templates/' . $_GET['name'];
        $path_parts = pathinfo($dir);
        $root1 = glob($dir . "/*");
        if (is_array($root1)) {
            $parent1 = $k;
            foreach ($root1 as $dir1) {
                $path_parts1 = pathinfo($dir1);

                if (!in_array($path_parts1['basename'], $stop_array)) {

                    $k++;
                    $tree .= '<tr class="treegrid-' . $k . '"><td><a href="#" class="treegrid-parent" data-parent="treegrid-' . $k . '">' . _tpl($path_parts1['basename']) . '</a></td></tr>';

                    $root2 = glob($dir1 . "/*.tpl");
                    if (is_array($root2)) {
                        $parent2 = $k;
                        foreach ($root2 as $dir2) {

                            $path_parts2 = pathinfo($dir2);
                            if (!in_array($path_parts2['basename'], $stop_array)) {
                                $k++;

                                $link = str_replace($dir, '', $dir2);
                                if (!empty($_GET['file']) and $link == $_GET['file']) {
                                    $active = ' treegrid-active';
                                    $active_icon = '<span class="glyphicon glyphicon-edit text-warning"></span>';
                                } else
                                    $active = $active_icon = null;

                                $tree .= '<tr class="treegrid-parent-' . $parent2 . $active . ' "><td class="data-row"><a href="?path=' . $_GET['path'] . '&name=' . $_GET['name'] . '&file=' . $link . '&mod=html' . $option_str . '" title="' . $path_parts2['basename'] . '">' . $active_icon . _tpl($path_parts2['basename']) . '</a></td></tr>';
                            }
                        }
                    }
                }
            }
        }

        if (!empty($parent2)) {
            $dir2 = str_replace($dir, '', $dir . '/style.css');
            $tree .= '<tr class="treegrid-parent-' . $parent1 . ' data-row"><td><span class="glyphicon glyphicon-text-width"></span> <a href="?path=' . $_GET['path'] . '&name=' . $_GET['name'] . '&file=' . $dir2 . '&mod=css' . $option_str . '" title="style.css">' . _tpl('style.css') . '</a></td></tr>';
        }

        $title_icon = '<span class="glyphicon glyphicon-chevron-down" data-toggle="tooltip" data-placement="top" title="' . __('Развернуть все') . '"></span>&nbsp;<span class="glyphicon glyphicon-chevron-up" data-toggle="tooltip" data-placement="top" title="' . __('Свернуть') . '"></span>';
    }

    $tree .= '</table></div>';

    // Вывод кнопок сохранить и выход в футер
    $ContentFooter = $PHPShopGUI->setInput("submit", "editID", "Применить", "right", 80, "", "but", "actionSave.system.edit");

    $PHPShopGUI->setFooter($ContentFooter);

    $sidebarleft[] = array('title' => 'Шаблоны в системе', 'content' => $tree, 'title-icon' => $title_icon);

    $PHPShopGUI->sidebarLeftCell = 3;
    $PHPShopGUI->setSidebarLeft($sidebarleft, 3);

    $PHPShopGUI->Compile(3);
}

function actionSerial() {

    if (strlen($_POST['path']) < 20) {

        $PHPShopTemplates = new PHPShopTemplates();
        if ($PHPShopTemplates->checkKey($_POST['path'], $_POST['key_new'])) {

            $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['templates_key']);
            if ($PHPShopOrm->update($_POST, array('path' => '="' . $_POST['path'] . '"'))) {
                $result = __('Ключ принят для шаблона') . ' <b>' . $_POST['path'] . '</b>';
                $success = true;
            } else {
                $result = __('Ключ шаблона не принят');
                $success = false;
            }
        }

        return array("success" => $success, "result" => PHPShopSTring::win_utf8($result));
    }
}

// Функция обновления
function actionSave() {
    $file = PHPShopSecurity::TotalClean('../templates/' . $_GET['name'] . '/' . $_GET['file']);
    $info = PHPShopSecurity::getExt($file);
    if (file_exists($file) and in_array($info, array('tpl', 'css'))) {
        PHPShopFile::chmod($file);

        // Кодировка
        if ($GLOBALS['PHPShopBase']->codBase == 'utf-8')
            $_POST['editor_src'] = PHPShopString::utf8_win1251($_POST['editor_src'], true);

        if (PHPShopFile::write($file, $content = str_replace(array('txtarea', '&#43;'), array('textarea', '+'), $_POST['editor_src'])))
            $action = true;
        else
            $action = false;
    } else
        $action = false;

    return array("success" => $action);
}

// Загрузка дополнительных шаблонов
function actionLoad() {
    global $skin_base_path, $_classPath, $PHPShopBase;

    $success = $is_commerce = false;
    if (PHPShopSecurity::true_skin($_POST['template_load'])) {

        // Проверка 
        if (strlen($_POST['template_load']) < 20) {
            if ($_POST['template_type'] == 'commerce') {
                $is_commerce = true;
                $load = $skin_base_path . '/commerce/' . $_POST['template_load'] . '/' . $_POST['template_load'] . '.zip';
            } elseif ($_POST['template_type'] == 'archive')
                $load = $skin_base_path . '/templates-archive/' . $_POST['template_load'] . '/' . $_POST['template_load'] . '.zip';
            elseif ($_POST['template_type'] == "default")
                $load = $skin_base_path . '/templates5/' . $_POST['template_load'] . '/' . $_POST['template_load'] . '.zip';
        } else
            $load = null;

        // Включаем таймер
        $time = explode(' ', microtime());
        $start_time = $time[1] + $time[0];

        $Content = file_get_contents($load);
        if (!empty($Content)) {
            $zip = $_SERVER['DOCUMENT_ROOT'] . $GLOBALS['SysValue']['dir']['dir'] . "/UserFiles/Files/" . $_POST['template_load'] . '.zip';
            $zip_load = $_SERVER['SERVER_NAME'] . $GLOBALS['SysValue']['dir']['dir'] . "/UserFiles/Files/" . $_POST['template_load'] . '.zip';
            $handle = fopen($zip, "w+");
            fwrite($handle, $Content);
            fclose($handle);
            if (is_file($zip)) {

                // Попытка изменить атрибуты
                @chmod($_classPath . "templates", 0775);

                $archive = new ZipArchive;
                $archive->open($zip);

                if ($archive->extractTo($_classPath . "templates/")) {

                    @unlink($zip);
                    $archive->close();

                    // Выключаем таймер
                    $time = explode(' ', microtime());
                    $seconds = ($time[1] + $time[0] - $start_time);
                    $seconds = substr($seconds, 0, 6);

                    $result = 'Шаблон <b>' . $_POST['template_load'] . '</b> загружен за ' . $seconds . ' сек.';
                    $success = true;

                    if ($is_commerce) {
                        $date_end = time() + 2592000;
                        $key = null;
                        $sql = "INSERT INTO " . $GLOBALS['SysValue']['base']['templates_key'] . "  VALUES ('" . $_POST['template_load'] . "'," . $date_end . ",'" . $key . "','" . md5($_POST['template_load'] . $date_end . $_SERVER['SERVER_NAME'] . $key) . "')";
                        mysqli_query($PHPShopBase->link_db, $sql);
                    }
                } else
                    $result = __('Ошибка распаковки файла') . ' ' . $_POST['template_load'] . '.zip, ' . __('нет прав записи в папку') . ' phpshop/templates/';
            } else
                $result = __('Ошибка записи файла') . ' ' . $_POST['template_load'] . '.zip, ' . __('нет прав записи в папку') . ' /UserFiles/Files/';
        }
        else {
            $result = __('Ошибка чтения файла') . ' ' . $_POST['template_load'] . '.zip';
        }
    }

    return array('success' => $success, 'result' => PHPShopSTring::win_utf8($result), 'zip' => $zip_load);
}

// Обработка событий
$PHPShopGUI->getAction();
?>