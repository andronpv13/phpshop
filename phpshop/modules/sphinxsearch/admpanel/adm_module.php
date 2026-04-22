<?php

// SQL
$PHPShopOrm = new PHPShopOrm($PHPShopModules->getParam("base.sphinxsearch.sphinxsearch_system"));

// Обновление версии модуля
function actionBaseUpdate() {
    global $PHPShopModules, $PHPShopOrm;
    $PHPShopOrm->clean();
    $option = $PHPShopOrm->select();
    $new_version = $PHPShopModules->getUpdate($option['version']);
    $PHPShopOrm->clean();
    $action = $PHPShopOrm->update(array('version_new' => $new_version));
}

// Функция обновления
function actionUpdate() {
    global $PHPShopOrm, $PHPShopModules, $PHPShopBase;

    // Настройки витрины
    $PHPShopModules->updateOption($_GET['id'], $_POST['servers']);

    if (empty($_POST["filter_show_counts_new"]))
        $_POST["filter_show_counts_new"] = 0;
    if (empty($_POST["filter_update_new"]))
        $_POST["filter_update_new"] = 0;
    if (empty($_POST["search_show_informer_string_new"]))
        $_POST["search_show_informer_string_new"] = 0;
    if (empty($_POST["ajax_search_categories_new"]))
        $_POST["ajax_search_categories_new"] = 0;
    if (empty($_POST["available_sort_new"]))
        $_POST["available_sort_new"] = 0;
    if (empty($_POST["use_additional_categories_new"]))
        $_POST["use_additional_categories_new"] = 0;
    if (empty($_POST["use_proxy_new"]))
        $_POST["use_proxy_new"] = 0;
    if (empty($_POST["search_uid_first_new"]))
        $_POST["search_uid_first_new"] = 0;
    if (empty($_POST["yandexsearch_new"]))
        $_POST["yandexsearch_new"] = 0;

    $PHPShopOrm->debug = false;
    $action = $PHPShopOrm->update($_POST);

    header('Location: ?path=modules&id=' . $_GET['id']);
    return $action;
}

function actionStart() {
    global $PHPShopGUI, $PHPShopOrm, $TitlePage, $select_name, $PHPShopBase;

    $PHPShopGUI->setActionPanel($TitlePage, $select_name, ['Сохранить и закрыть']);
    $PHPShopGUI->field_col = 4;

    include_once dirname(__DIR__) . '/class/SphinxSearch.php';
    $SphinxSearch = new SphinxSearch();

    if (!empty($SphinxSearch->link_db))
        $check = '<span class="glyphicon glyphicon-ok text-success"></span>';
    else
        $check = '<span class="glyphicon glyphicon-remove text-danger"></span>';

    // Выборка
    $data = $PHPShopOrm->select();


    $Tab1 .= $PHPShopGUI->setCollapse('Настройки поиска', $PHPShopGUI->setField('Блок "Найдено в категориях"', $PHPShopGUI->setSelect('find_in_categories_new', [
                        ['Не использовать', 0, $data['find_in_categories']],
                        ['Отображение плитками', 1, $data['find_in_categories']],
                        ['Отображение списком', 2, $data['find_in_categories']]
                            ], 250)) .
            $PHPShopGUI->setField('Адрес поискового сервера', $PHPShopGUI->setInputText($check, 'host_new', $data['host'], 250, false, false, false, '127.0.0.1')) .
            $PHPShopGUI->setField('Порт поискового сервера для MySQL', $PHPShopGUI->setInputText($check, 'port_new', $data['port'], 100, false, false, false, '9306')) .
            $PHPShopGUI->setField('Товаров в ряд', $PHPShopGUI->setSelect('search_page_row_new', [
                        [1, 1, $data['search_page_row']],
                        [2, 2, $data['search_page_row']],
                        [3, 3, $data['search_page_row']],
                        [4, 4, $data['search_page_row']],
                        [5, 5, $data['search_page_row']]
                            ], 50)) .
            $PHPShopGUI->setField('Максимальное кол-во категорий в блоке "Найдено в категориях"', $PHPShopGUI->setInputText(false, 'max_categories_new', $data['max_categories'], 50)) .
            $PHPShopGUI->setField('Товаров на странице', $PHPShopGUI->setInputText(false, 'search_page_size_new', $data['search_page_size'], 50)) .
            $PHPShopGUI->setField('Минимальное количество символов в слове для индексации', $PHPShopGUI->setInputText(false, 'misprints_ajax_new', $data['misprints_ajax'], 50)) .
            $PHPShopGUI->setField('Учитывать опечатку при длине поискового запроса от', $PHPShopGUI->setInputText(false, 'misprints_from_cnt_new', $data['misprints_from_cnt'], 50)) .
            $PHPShopGUI->setField('Информационная строка', $PHPShopGUI->setCheckbox('search_show_informer_string_new', 1, 'Отображать строку "Найдено XX результатов в XX категориях."', $data['search_show_informer_string'])) .
            $PHPShopGUI->setField('Дополнительные категории', $PHPShopGUI->setCheckbox('use_additional_categories_new', 1, 'Отображать дополнительные категории товаров', $data['use_additional_categories'])) .
            $PHPShopGUI->setField('Товаров в быстром поиске', $PHPShopGUI->setInputText(false, 'ajax_search_products_cnt_new', $data['ajax_search_products_cnt'], 50)) .
            $PHPShopGUI->setField('Категорий в быстром поиске', $PHPShopGUI->setInputText(false, 'ajax_search_categories_cnt_new', $data['ajax_search_categories_cnt'], 50)) .
            $PHPShopGUI->setField('Сначала в наличии', $PHPShopGUI->setCheckbox('available_sort_new', 1, 'Выводить сначала товары в наличии', $data['available_sort'])) .
            $PHPShopGUI->setField('Искать сначала по артикулу', $PHPShopGUI->setCheckbox('search_uid_first_new', 1, 'Сначала искать по совпадению артикула', $data['search_uid_first'])) .
            $PHPShopGUI->setField('Искать в Яндексе', $PHPShopGUI->setCheckbox('yandexsearch_new', 1, 'Искать в Яндексе если ничего не найдено', $data['yandexsearch'], $PHPShopGUI->disabled_yandexcloud))
    );


    $config = "source mainConfSourse
{
	type = mysql
	sql_host = " . $PHPShopBase->getParam("connect.host") . "
	sql_user = " . $PHPShopBase->getParam("connect.user_db") . "
	sql_pass = " . $PHPShopBase->getParam("connect.pass_db") . "
	sql_db = " . $PHPShopBase->getParam("connect.dbase") . "
	sql_port = " . $PHPShopBase->getParam("connect.port") . "	
	sql_query_pre	= SET NAMES utf8
}


source productsSrc : mainConfSourse
{

	sql_query = SELECT id,name,uid,content,category,items \
					FROM phpshop_products where enabled='1' and parent_enabled='0';

	#type of group fields
	sql_field_string = uid
	sql_field_string = name
	sql_field_string = content
	sql_field_string = category
	sql_field_string = items
}

index productsIndex
{
	source	= productsSrc
	path = /var/lib/sphinx/data/productsIndex
	morphology = stem_enru, Soundex, Metaphone
	min_word_len = " . $data['misprints_ajax'] . "
	expand_keywords  = 1
	index_exact_words = 1
	min_infix_len = " . $data['misprints_from_cnt'] . "
	html_strip = 1
}

source categoriesSrc : mainConfSourse
{

	sql_query		= SELECT id,name \
					FROM phpshop_categories where skin_enabled='0';

	#type of group fields
	sql_field_string = name
}

index categoriesIndex
{
	source					= categoriesSrc
	path					= /var/lib/sphinx/data/categoriesIndex
	morphology				= stem_enru, Soundex, Metaphone
	min_word_len		= 1
	expand_keywords		= 1
	index_exact_words	= 1
	min_infix_len		= 3
}

indexer
{
	mem_limit = 240M
}

searchd
{      
        
    log = /var/log/sphinx/searchd.log
	query_log = /var/log/sphinx/query.log
	pid_file = /var/run/sphinx/searchd.pid
	listen = " . $data['port'] . "
}";


    $PHPShopGUI->setEditor('ace', true);
    $oFCKeditor = new Editor('config', true);
    $oFCKeditor->Height = '520';
    $oFCKeditor->ToolbarSet = 'Normal';
    $oFCKeditor->Value = $config;

    $Tab2 = $PHPShopGUI->setCollapse('sphinx.conf', $oFCKeditor->AddGUI());

    $Tab3 = '<div class="form-group form-group-sm"><div class="col-sm-12" style="padding-left: 20px;padding-right: 20px;">
                ' . $PHPShopGUI->setTextarea('synonyms_new', $data['synonyms'], true, '100%', 300, 'Через запятую слово и синоним. Каждая новая пара с новой строки. Например:<br> томат, помидор <br> 
                            ryzen, райзен') .
            '</div></div>';

    $info = '
    <h4>Подключение Sphinx на хостинге Beget</h4>
    <ol>
        <li>В личном кабинете аккаунта хостинга Beget в разделе <a href="https://cp.beget.com/cloudservices" target="_blank">Сервисы</a> активируйте сервис <kbd>Sphinx</kbd>.</li>
        <li><a href="https://beget.com/ru/kb/how-to/services/ispolzovanie-sphinx#ispolzovanie-sphinx-na-hostinge" target="_blank">Инструкция по использованию</a> сервиса Sphinx на хостинге Beget.</li>
        <li>В качестве конфигурационного файла <kbd>sphinx.conf</kbd> для Sphinx используйте данных из закладки <kbd>Конфигурация</kbd> в настройках модуля.</li>
    </ol>   
    
    <h4>Подключение Sphinx на других хостингах</h4>
    <ol>
        <li>Установите или активируйте поисковой сервис <a href="https://sphinxsearch.com/" target="_blank">Sphinx</a>.</li>
        <li>В качестве примера конфигурационного файла <kbd>sphinx.conf</kbd> для Sphinx используйте данных из закладки <kbd>Конфигурация</kbd> в настройках модуля.<br>Параметры <code>log</code>, <code>query_log</code>, <code>pid_file</code>, <code>path</code> зависят от хостинга и операционной системы на нем. Правильность указания этих параметров следует уточнить в поддержке хостинга.</li>
    </ol> 
    
    <h4>Подключение Sphinx на хостинге Beget и использование его на других хостингах</h4>
    <ol>
        <li><a href="https://beget.com/p566" target="_blank">Зарегистрироваться</a> на хостинге Beget.</li>
        <li>В личном кабинете аккаунта хостинга Beget в разделе <a href="https://cp.beget.com/cloudservices" target="_blank">Сервисы</a> активируйте сервис <kbd>Sphinx</kbd>.</li>
        <li><a href="https://cp.beget.com/cloudservices" target="_blank">Инструкция по использованию</a> сервиса Sphinx на хостинге Beget.</li>
        <li>Включите на своем хостинге доступ к базе данных MySQL по внешнему IP-адресу</li>
        <li>В качестве конфигурационного файла <kbd>sphinx.conf</kbd> для Sphinx используйте данных из закладки <kbd>Конфигурация</kbd> в настройках модуля. Вместо параметра <code>sql_host=' . $PHPShopBase->getParam("connect.host") . '</code> указать свой выделенный IP-адрес базы данных MySQL <code>sql_host={IP_MYSQL}</code>.</li>
        <li>В личном кабинете аккаунта хостинга Beget в разделе <a href="https://cp.beget.com/cloudservices/sphinx/searchd" target="_blank">Сервисы - Sphinx - Поисковой сервер</a> активировать внешний доступ и указать IP-адрес своего сайта.</li>    
    </ol>   

    <h4>Настройка модуля</h4>
    <ol>
        <li>Указать адрес поискового сервера Sphinx, по умолчанию <code>127.0.0.1</code>. При активации режима доступа к Sphinx по внешнему IP-адресу через Beget, адрес будет вида <code>sphinx.{LOGIN}.beget.hosting</code>.</li>
        <li>Указать порт поискового сервера Sphinx, по умолчанию <code>9306</code>. При активации режима доступа к Sphinx по внешнему IP-адресу через Beget , порт будет <code>55408</code>.</li>
        <li>При изменении параметров "Минимальное количество символов в слове для индексации" и "Учитывать опечатку при длине поискового запроса от" следует обновить конфигурационный файл <kbd>sphinx.conf</kbd> для Sphinx на поисковом сервере и произвести индексацию средствами хостинга.</li>
        <li>При наличии активной подписки <a href="https://docs.phpshop.ru/nastroiky/yandex-cloud" target="_blank">YandexCloud</a> можно включить режим поиска товаров в Яндексе если ничего не найдено во внутреннем поиске.</li>
    </ol>
    
    <h4>Режим работы модуля</h4>
    <ol>  
      <li>Модуль активируется автоматически при наличии активной технической поддержки.</li>
      <li>Модуль деактивируется автоматически при отсутствии активной технической поддержки.</li>
      <li>Дата окончания работы модуля доступна в закладке <kbd>О модуле</kbd> - <kbd>Окончание работы</kbd>.</li>
      <li>Техническая поддержка по работе поискового сервера Sphinx и его настройка осуществляется технической службой хостинга.</li>
    </ol> 
';

    $Tab4 = $PHPShopGUI->setInfo($info);

    $Tab5 = $PHPShopGUI->setPay($serial = false, false, $data['version'], true);

    // Вывод формы закладки
    $PHPShopGUI->setTab(["Основное", $Tab1, true],["Конфигурация", $Tab2], ["Синонимы", $Tab3],  ["Инструкция", $Tab4],  ["О Модуле", $Tab5]);

    // Вывод кнопок сохранить и выход в футер
    $ContentFooter = $PHPShopGUI->setInput("hidden", "rowID", $data['id']) .
            $PHPShopGUI->setInput("submit", "saveID", "Применить", "right", 80, "", "but", "actionUpdate.modules.edit");
    $PHPShopGUI->setFooter($ContentFooter);
    return true;
}

// Обработка событий
$PHPShopGUI->getAction();

// Вывод формы при старте
$PHPShopGUI->setLoader($_POST['editID'], 'actionStart');
?>