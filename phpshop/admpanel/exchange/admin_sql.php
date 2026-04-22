<?php

PHPShopObj::loadClass("file");

$TitlePage = __("SQL запрос к базе");

// Описание таблиц
$sqlHelper = array(
    'phpshop_categories' => __('Категории товаров'),
    'phpshop_orders' => __('Заказы пользователей'),
    'phpshop_products' => __('Товарные позиции') . '. <a href="https://help.phpshop.ru/knowledgebase/article/171" target="_blank"><span class="glyphicon glyphicon-share-alt"></span> ' . __('Описание полей') . '</a>',
    'phpshop_system' => __('Настройки сайта'),
    "phpshop_gbook" => __("Отзывы о сайте из гостевой книги"),
    "phpshop_news" => __('Новости'),
    "phpshop_jurnal" => __('Журнал авторизации администраторов'),
    "phpshop_page" => __('Страницы сайта (главное меню, контакты и т.д.)'),
    "phpshop_menu" => __('Текстовые информационные блоки'),
    "phpshop_baners" => __('Рекламные баннеры'),
    "phpshop_links" => __('Полезные ссылки'),
    "phpshop_search_jurnal" => __('Журнал поиска по сайту'),
    "phpshop_users" => __('Администраторы сайта'),
    "phpshop_sort_categories" => __('Наборы характеристик для привязки к каталогам товаров'),
    "phpshop_sort" => __('Характеристики их значения'),
    "phpshop_shopusers" => __('Пользователи сайта, покупатели'),
    "phpshop_page_categories" => __('Категории страниц'),
    "phpshop_foto" => __('Изображения товаров'),
    "phpshop_comment" => __('Комментарии к товарам, оставленные пользователями'),
    "phpshop_messages" => __('Сообщения для администрации, оставленные пользователями'),
    "phpshop_modules" => __('Подключенные дополнительные модули'),
    "phpshop_newsletter" => __('Тексты рассылок'),
    "phpshop_slider" => __('Слайдер на главной странице'),
    "phpshop_slider" => __('Слайдер на главной странице'),
);

// Описания полей
$key_name = array(
    'id' => 'Id',
    'name' => 'Наименование',
    'uid' => 'Артикул',
    'price' => 'Цена 1',
    'price2' => 'Цена 2',
    'price3' => 'Цена 3',
    'price4' => 'Цена 4',
    'price5' => 'Цена 5',
    'price_n' => 'Старая цена',
    'sklad' => 'Под заказ',
    'newtip' => 'Новинка',
    'spec' => 'Спецпредложение',
    'items' => 'Склад',
    'weight' => 'Вес',
    'num' => 'Приоритет',
    'enabled' => 'Вывод',
    'content' => 'Подробное описание',
    'description' => 'Краткое описание',
    'pic_small' => 'Маленькое изображение',
    'pic_big' => 'Большое изображение',
    'yml' => 'Яндекс.Маркет',
    'icon' => 'Иконка',
    'parent_to' => 'Родитель',
    'category' => 'Каталог',
    'title' => 'Заголовок',
    'login' => 'Логин',
    'tel' => 'Телефон',
    'cumulative_discount' => 'Накопительная скидка',
    'seller' => 'Статус загрузки в 1С',
    'fio' => 'Ф.И.О',
    'city' => 'Город',
    'street' => 'Улица',
    'odnotip' => 'Сопутствующие товары',
    'page' => 'Страницы',
    'parent' => 'Подчиненные товары',
    'dop_cat' => 'Дополнительные каталоги',
    'ed_izm' => 'Единица измерения',
    'baseinputvaluta' => 'Валюта',
    'vendor_array' => 'Характеристики',
    'p_enabled' => 'Наличие в Яндекс.Маркет',
    'parent_enabled' => 'Подтип',
    'descrip' => 'Meta description',
    'keywords' => 'Meta keywords',
    "prod_seo_name" => 'SEO ссылка',
    'num_row' => 'Товаров в длину',
    'num_cow' => 'Товаров на странице',
    'count' => 'Содержит товаров',
    'cat_seo_name' => 'SEO ссылка каталога',
    'sum' => 'Сумма',
    'servers' => 'Витрины',
    'items1' => 'Склад 2',
    'items2' => 'Склад 3',
    'items3' => 'Склад 4',
    'items4' => 'Склад 5',
    'data_adres' => 'Адрес',
    'color' => 'Код цвета',
    'parent2' => 'Цвет',
    'rate' => 'Рейтинг',
    'productday' => 'Товар дня',
    'hit' => 'Хит',
    'sendmail' => 'Подписка на рассылку',
    'statusi' => 'Статус заказа',
    'country' => 'Страна',
    'state' => 'Область',
    'index' => 'Индекс',
    'house' => 'Дом',
    'porch' => 'Подъезд',
    'door_phone' => 'Домофон',
    'flat' => 'Квартира',
    'delivtime' => 'Время доставки',
    'org_name' => 'Организация',
    'org_inn' => 'ИНН',
    'org_kpp' => 'КПП',
    'org_yur_adres' => 'Юридический адрес',
    'dop_info' => 'Комментарий пользоватея',
    'tracking' => 'Код отслеживания',
    'path' => 'Путь каталога',
    'length' => 'Длина',
    'width' => 'Ширина',
    'height' => 'Высота',
    'moysklad_product_id' => 'МойСклад Id',
    'bonus' => 'Бонус',
    'price_purch' => 'Закупочная цена',
    'files' => 'Файлы',
    'external_code' => 'Внешний код',
    'barcode' => 'Штрихкод',
    'rate_count' => 'Голоса'
);

// Функция обновления
function actionSave() {
    global $PHPShopGUI, $result_message, $result_error_tracert, $link_db;

    // Выполнение команд из формы
    if (!empty($_POST['sql_text'])) {
        $sql_query = explode(";\r", trim($_POST['sql_text']));

        foreach ($sql_query as $v) {
            $result = mysqli_query($link_db, trim($v));
        }

        // Выполнено успешно
        if ($result)
            $result_message = $PHPShopGUI->setAlert('SQL запрос успешно выполнен');
        else {
            $result_message = $PHPShopGUI->setAlert('SQL ошибка: ' . mysqli_error($link_db), 'danger');
            $result_error_tracert = $_POST['sql_text'];
        }
    }

    // Копируем csv от пользователя
    if (!empty($_FILES['file']['name'])) {
        $_FILES['file']['ext'] = PHPShopSecurity::getExt($_FILES['file']['name']);
        if ($_FILES['file']['ext'] == "sql") {
            if (move_uploaded_file($_FILES['file']['tmp_name'], "csv/" . $_FILES['file']['name'])) {
                $csv_file = "csv/" . $_FILES['file']['name'];
                $csv_file_name = $_FILES['file']['name'];
            } else
                $result_message = $PHPShopGUI->setAlert('Ошибка сохранения файла' . ' <strong>' . $csv_file_name . '</strong> в phpshop/admpanel/csv', 'danger');
        }
    }

    // Читаем файл из URL
    elseif (!empty($_POST['furl'])) {
        $csv_file = $_POST['furl'];
        $path_parts = pathinfo($csv_file);
        $csv_file_name = $path_parts['basename'];
    }

    // Читаем файл из файлового менеджера
    elseif (!empty($_POST['lfile'])) {
        $csv_file = $_SERVER['DOCUMENT_ROOT'] . $GLOBALS['dir']['dir'] . $_POST['lfile'];
        $path_parts = pathinfo($csv_file);
        $csv_file_name = $path_parts['basename'];
    }


    // Обработка sql
    if (!empty($csv_file)) {
        $result_error_tracer = $error_line = null;

        // GZIP
        if ($path_parts['extension'] == 'gz') {
            ob_start();
            readgzfile($csv_file);
            $sql_file_content = ob_get_clean();
        } else
            $sql_file_content = file_get_contents($csv_file);

        // Кодировка UTF
        if ($GLOBALS['PHPShopBase']->codBase == 'utf-8') {
            $sql_file_content = str_replace("CHARSET=cp1251", "CHARSET=utf8", $sql_file_content);
            $sql_file_content = PHPShopString::win_utf8($sql_file_content, true);
        }

        $sql_query = PHPShopFile::sqlStringToArray($sql_file_content);

        foreach ($sql_query as $k => $v) {

            if (strlen($v) > 10) {
                $result = mysqli_query($link_db, $v);
            }

            if (!$result) {
                $error_line .= '[Line ' . $k . '] ';
                $result_error_tracert .= 'Запрос: ' . $v . '
Ошибка: ' . mysqli_error($link_db);
            }
        }

        // Удаление файла после выполнения
        if (isset($_POST['clean']))
            @unlink($csv_file);

        // Выполнено успешно
        if (empty($result_error_tracert)) {
            if (!empty($_POST['ajax']))
                return array("success" => true);
            else
                $result_message = $PHPShopGUI->setAlert('SQL запрос успешно выполнен ' . $csv_file_name);
        }
        else {
            if (!empty($_POST['ajax']))
                return array("success" => false, "error" => mysqli_error($link_db) . ' -> ' . $error_line);
            else
                $result_message = $PHPShopGUI->setAlert('SQL ошибка ' . mysqli_error($link_db), 'danger');
        }
    }
}

// Стартовый вид
function actionStart() {
    global $PHPShopGUI, $TitlePage, $PHPShopModules, $result_message, $result_error_tracert, $PHPShopSystem, $selectModalBody, $sqlHelper, $key_name;

    $PHPShopGUI->action_button['Выполнить'] = array(
        'name' => __('Выполнить'),
        'class' => 'btn btn-primary btn-sm navbar-btn ace-save',
        'type' => 'button',
        'icon' => 'glyphicon glyphicon-ok'
    );

    $bases = $DROP = $TRUNCATE = $selectModal = null;
    $baseArray = array();

    foreach ($GLOBALS['SysValue']['base'] as $val) {
        if (is_array($val)) {
            foreach ($val as $mod_base)
                $baseArray[$mod_base] = $mod_base;
        } else
            $baseArray[$val] = $val;
    }

    foreach ($baseArray as $val) {
        if (!empty($val)) {
            $bases .= "`" . $val . "`, ";
            $DROP .= 'DROP TABLE ' . $val . ';
';
            if (!empty($sqlHelper[$val]))
                $selectModal .= '<tr><td><kbd>' . $val . '</kbd></td><td>' . $sqlHelper[$val] . '</td></tr>';
        }
    }

    unset($baseArray['phpshop_system']);
    unset($baseArray['phpshop_users']);
    unset($baseArray['phpshop_valuta']);
    unset($baseArray['phpshop_modules_key']);
    unset($baseArray['phpshop_payment_systems']);
    unset($baseArray['phpshop_exchanges']);
    unset($baseArray['phpshop_baners']);
    unset($baseArray['phpshop_parent_name']);
    unset($baseArray['phpshop_delivery']);
    unset($baseArray['phpshop_order_status']);
    unset($baseArray['phpshop_payment_systems']);
    unset($baseArray['phpshop_page']);
    unset($baseArray['phpshop_jurnal']);

    $TRUNCATE = null;

    foreach ($baseArray as $val) {
        if (!strstr($val, '_modules'))
            $TRUNCATE .= 'TRUNCATE `' . $val . '`;
';
    }

    $bases = substr($bases, 0, strlen($bases) - 2) . ';';

    // Размер названия поля
    $PHPShopGUI->field_col = 2;
    $PHPShopGUI->addJSFiles('./exchange/gui/exchange.gui.js', './tpleditor/gui/ace/ace.js');

    $PHPShopGUI->_CODE = $result_message;
    $help = '<p class="text-muted">' . __('Для очистки демо-базы и демо-товаров следует выбрать SQL команду <kbd>Очистить базу</kbd></p> <p class="text-muted">Для увеличения производительности сайта вызвать SQL команду <kbd>Оптимизировать базу</kbd></p> <p class="text-muted">Справочник полезных SQL команд для пакетной обработки товаров доступен в <a href="https://help.phpshop.ru/knowledgebase/article/398" target="_blank" class="btn btn-default btn-xs"><span class="glyphicon glyphicon-book"></span> Базе знаний</a>') . '</p>';


    $PHPShopGUI->setActionPanel($TitlePage, false, array('Выполнить'));

    if (!empty($_GET['query']) and $_GET['query'] == 'optimize')
        $optimize_sel = 'selected';
    else
        $optimize_sel = null;

    $query_value[] = array('Выбрать SQL команду', 0, '');
    $query_value[] = array('Оптимизировать базу', 'OPTIMIZE TABLE ' . $bases, $optimize_sel);
    $query_value[] = array('Починить базу', 'REPAIR TABLE ' . $bases, '');
    $query_value[] = array('Выключить отсутствующие товары', 'UPDATE ' . $GLOBALS['SysValue']['base']['products'] . ' SET enabled=\'0\' WHERE items<1', '');
    $query_value[] = array('Удалить все фото товаров', 'TRUNCATE ' . $GLOBALS['SysValue']['base']['foto'] . ';
UPDATE ' . $GLOBALS['SysValue']['base']['products'] . ' set pic_small=\'\', pic_big=\'\';', '');
    $query_value[] = array('Удалить характеристики', 'TRUNCATE ' . $GLOBALS['SysValue']['base']['sort'] . ';
TRUNCATE ' . $GLOBALS['SysValue']['base']['sort_categories'] . ';
UPDATE ' . $GLOBALS['SysValue']['base']['products'] . ' set vendor=\'\', vendor_array=\'\';
UPDATE ' . $GLOBALS['SysValue']['base']['categories'] . ' set sort=\'\';', '');
    $query_value[] = array('Удалить каталог товаров', 'DELETE FROM ' . $GLOBALS['SysValue']['base']['categories'] . ' WHERE ID=', '');
    $query_value[] = array('Удалить все каталоги', 'TRUNCATE ' . $GLOBALS['SysValue']['base']['categories'], '');
    $query_value[] = array('Удалить все товары', 'TRUNCATE ' . $GLOBALS['SysValue']['base']['products'] . ';
TRUNCATE ' . $GLOBALS['SysValue']['base']['foto'] . ';', '');
    $query_value[] = array('Удалить товары в каталоге', 'DELETE FROM ' . $GLOBALS['SysValue']['base']['products'] . ' WHERE category=', '');
    $query_value[] = array('Удалить страницу', 'DELETE FROM ' . $GLOBALS['SysValue']['base']['page'] . ' WHERE ID=', '');
    $query_value[] = array('Починить зацикливающиеся каталоги', 'UPDATE ' . $GLOBALS['SysValue']['base']['categories'] . ' SET parent_to=0 WHERE parent_to=id', '');
    $query_value[] = array('Уменьшить время генерации меню каталогов', "UPDATE phpshop_categories SET phpshop_categories.vid = '0' WHERE phpshop_categories.parent_to IN (select * from ( SELECT phpshop_categories.id
 FROM phpshop_categories WHERE phpshop_categories.parent_to='0')t );
 UPDATE phpshop_categories SET vid='1' where parent_to !='0';");
    $query_value[] = array('Очистить базу городов', 'TRUNCATE ' . $GLOBALS['SysValue']['base']['citylist_country'] . ';
TRUNCATE ' . $GLOBALS['SysValue']['base']['citylist_region'] . ';
TRUNCATE ' . $GLOBALS['SysValue']['base']['citylist_city'] . ';', '');
     $query_value[] = array('Удалить все лиды', 'TRUNCATE ' . $GLOBALS['SysValue']['base']['notes'] . ';', '');



    $query_value[] = array('Очистить базу', $TRUNCATE, '');
    //$query_value[] = array('Уничтожить базу (!)', $DROP, '');
    // Оптимизация по ссылке
    if (!empty($_GET['query']) and $_GET['query'] == 'optimize')
        $result_error_tracert = 'OPTIMIZE TABLE ' . $bases;

    // Тема
    $theme = $PHPShopSystem->getSerilizeParam('admoption.ace_theme');
    if (empty($theme))
        $theme = 'dawn';

    $PHPShopGUI->_CODE .= '<textarea class="hide" id="editor_src" name="sql_text" data-mod="sql" data-theme="' . $theme . '">' . $result_error_tracert . '</textarea><pre id="editor">' . __('Загрузка') . '...</pre>';

    $PHPShopGUI->_CODE .= '<p class="text-right data-row"><a href="#" id="vartable" data-toggle="modal" data-target="#selectModal" data-title="' . __('Основные таблицы') . '"><span class="glyphicon glyphicon-question-sign"></span>' . __('Описание таблиц') . '</a></p>';

    // Модальное окно таблицы описаний перменных
    $selectModalBody = '<table class="table table-striped"><tr><th>' . __('Таблица') . '</th><th>' . __('Описание') . '</th></tr>' . $selectModal . '</table>';

    $Tab1 = $PHPShopGUI->setField('Команда', $PHPShopGUI->setSelect('sql_query', $query_value, 400, true, false, false, false, 1, false, false, 'selectpicker')) .
            $PHPShopGUI->setField("Файл", $PHPShopGUI->setFile());

    // Конструктор
    $query_table_value[] = ['Не выбрано', '', $_POST['query_table']];
    $query_table_value[] = ['Товары', $GLOBALS['SysValue']['base']['products'], $_POST['query_table']];
    $query_table_value[] = ['Каталоги', $GLOBALS['SysValue']['base']['categories'], $_POST['query_table']];
    $query_table_value[] = ['Пользователи', $GLOBALS['SysValue']['base']['shopusers'], $_POST['query_table']];
    $query_table_value[] = ['Заказы', $GLOBALS['SysValue']['base']['orders'], $_POST['query_table']];

    $query_action_value[] = ['Не выбрано', '',$_POST['query_action']];
    $query_action_value[] = ['Изменить', 'update', $_POST['query_action']];
    $query_action_value[] = ['Удалить', 'delete', $_POST['query_action']];
    $query_action_value[] = ['Выбрать', 'select', $_POST['query_action']];

    $query_var_value[] = ['Не выбрано', '', ''];
    foreach ($key_name as $k => $v)
        $query_var_value[] = [$v, $k, $_POST['query_var']];

    $query_condition_value[] = ['Не выбрано', '', $_POST['query_condition']];
    $query_condition_value[] = ['Равно', '=', $_POST['query_condition']];
    $query_condition_value[] = ['Не равно', '!=', $_POST['query_condition']];
    $query_condition_value[] = ['Больше', '>', $_POST['query_condition']];
    $query_condition_value[] = ['Меньше', '<', $_POST['query_condition']];

    $query_val_value[] = ['Не выбрано', '', $_POST['query_val']];
    $query_val_value[] = ['0', "'0'", $_POST['query_val']];
    $query_val_value[] = ['1', "'1'", $_POST['query_val']];
    $query_val_value[] = ['Пусто', "''", $_POST['query_val']];
    $query_val_value[] = ['Ввести', "prompt", $_POST['query_val']];

    $Tab2 = $PHPShopGUI->setField('Тип данных', $PHPShopGUI->setSelect('query_table', $query_table_value, 200, true, false, false, false, 1, false, false, 'selectpicker'). $PHPShopGUI->setButton('Сгенерировать','play','query_generation'));
    $Tab2 .= $PHPShopGUI->setField('Действие', $PHPShopGUI->setSelect('query_action', $query_action_value, 200, true, false, false, false, 1, false, false, 'selectpicker'));
    $Tab2 .= $PHPShopGUI->setField('Поле', $PHPShopGUI->setSelect('query_var', $query_var_value, 200, true, false, true, false, 1, false, false, 'selectpicker'));
     $Tab2 .= $PHPShopGUI->setField('Условие', $PHPShopGUI->setSelect('query_condition', $query_condition_value, 200, true, false, false, false, 1, false, false, 'selectpicker'));
    $Tab2 .= $PHPShopGUI->setField('Значение', $PHPShopGUI->setSelect('query_val', $query_val_value, 200, true, false, false, false, 1, false, false, 'selectpicker'));
   
    $PHPShopGUI->tab_return = true;
    $PHPShopGUI->setTab(array('Настройки', $Tab1, true), array('Конструктор запросов', $Tab2, true));

    // Запрос модуля на закладку
    $PHPShopModules->setAdmHandler(__FILE__, __FUNCTION__, false);


    // Вывод кнопок сохранить и выход в футер
    $ContentFooter = $PHPShopGUI->setInput("hidden", "saveID", "Применить", "right", 80, "", "but", "actionSave.system.edit");
    $ContentFooter .= $PHPShopGUI->setInput("hidden", "restoreID", "Применить", "right", 80, "", "but", "actionRestore.system.edit");

    $PHPShopGUI->setFooter($ContentFooter);

    // Футер
    $sidebarleft[] = array('title' => 'Категории', 'content' => $PHPShopGUI->loadLib('tab_menu_service', false, './exchange/'));
    $sidebarleft[] = array('title' => 'Подсказка', 'content' => $help);
    $PHPShopGUI->setSidebarLeft($sidebarleft, 2);
    $PHPShopGUI->Compile(2);
    return true;
}

// Обработка событий
$PHPShopGUI->getAction();
?>