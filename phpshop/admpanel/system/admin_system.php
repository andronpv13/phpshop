<?php

$TitlePage = __("Основные Настройки");
PHPShopObj::loadClass('order');
$PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['system']);

// Выбор языка
function GetLocaleList($skin, $option = 'lang') {
    global $PHPShopGUI;
    $dir = "../locale/";

    if (empty($skin))
        $skin = 'russian';

    $locale_array = array(
        'russian' => 'Русский',
        'ukrainian' => 'Українська',
        'belarusian' => 'Беларускі',
        'english' => 'English'
    );

    if (is_dir($dir)) {
        if (@$dh = opendir($dir)) {
            while (($file = readdir($dh)) !== false) {

                //$name = $locale_array[$file];
                $name = $file;
                if (empty($name))
                    $name = $file;

                if ($skin == $file)
                    $sel = "selected";
                else
                    $sel = "";

                if ($file != "." and $file != ".." and ! strpos($file, '.'))
                    $value[] = array($name, $file, $sel, 'data-content="<img src=\'' . $dir . $file . '/icon.png\'/> ' . $name . '"');
            }
            closedir($dh);
        }
    }

    return $PHPShopGUI->setSelect('option[' . $option . ']', $value);
}

// Выбор html редактора
function GetEditors($editor) {
    global $PHPShopGUI;

    if ($editor == 'tiny_mce')
        $editor = 'default';

    $dir = "./editors/";
    if (is_dir($dir)) {
        if (@$dh = opendir($dir)) {
            while (($file = readdir($dh)) !== false) {

                if ($editor == $file)
                    $sel = "selected";
                else
                    $sel = "";

                if ($file != "." and $file != ".." and $file != "index.html")
                    $value[] = array($file, $file, $sel);
            }
            closedir($dh);
        }
    }

    return $PHPShopGUI->setSelect('option[editor]', $value);
}

// Выбор шаблона дизайна
function GetSkinList($skin) {
    global $PHPShopGUI;
    $dir = "../templates/";

    if (is_dir($dir)) {
        if (@$dh = opendir($dir)) {
            while (($file = readdir($dh)) !== false) {
                if (file_exists($dir . '/' . $file . "/main/index.tpl")) {

                    if ($skin == $file)
                        $sel = "selected";
                    else
                        $sel = "";

                    if ($file != "." and $file != ".." and ! strpos($file, '.'))
                        $value[] = array($file, $file, $sel);
                }
            }
            closedir($dh);
        }
    }

    return $PHPShopGUI->setSelect('skin_new', $value);
}

// Выбор цвета редактора шабонов
function GetAceSkinList($skin) {
    global $PHPShopGUI;
    $dir = "./tpleditor/gui/ace/";

    if (empty($skin))
        $skin = 'dawn';

    if (is_dir($dir)) {
        if (@$dh = opendir($dir)) {
            while (($file = readdir($dh)) !== false) {

                if (preg_match("/^theme-([a-zA-Z0-9_]{1,30}).js$/", $file, $match)) {

                    $file = str_replace(array('.js', 'theme-'), '', $file);

                    if ($skin == $file)
                        $sel = "selected";
                    else
                        $sel = "";

                    if ($file == 'dawn')
                        $value[] = array('default', 'dawn', $sel);

                    elseif ($file != "." and $file != ".." and ! strpos($file, '.'))
                        $value[] = array($file, $file, $sel);
                }
            }
            closedir($dh);
        }
    }

    return $PHPShopGUI->setSelect('option[ace_theme]', $value);
}

// Выбор шаблона панели управления
function GetAdminSkinList($skin) {
    global $PHPShopGUI;
    $dir = "./css/";

    $color = array(
        'default' => '#178ACC',
        'cyborg' => '#000',
        'flatly' => '#D9230F',
        'spacelab' => '#46709D',
        'slate' => '#4E5D6C',
        'yeti' => '#008CBA',
        'simplex' => '#DF691A',
        'sardbirds' => '#45B3AF',
        'wordless' => '#468966',
        'wildspot' => '#564267',
        'loving' => '#FFCAEA',
        'retro' => '#BBBBBB',
        'cake' => '#E3D2BA',
        'dark' => '#3E444C',
        'matrix' => '#00B800'
    );

    if (is_dir($dir)) {
        if (@$dh = opendir($dir)) {
            while (($file = readdir($dh)) !== false) {

                if (preg_match("/^bootstrap-theme-([a-zA-Z0-9_]{1,30}).css$/", $file, $match)) {
                    $icon = $color[$match[1]];

                    $file = str_replace(array('.css', 'bootstrap-theme-'), '', $file);

                    if ($skin == $file)
                        $sel = "selected";
                    else
                        $sel = "";

                    if ($file != "." and $file != ".." and ! strpos($file, '.'))
                        $value[] = array($file, $file, $sel, 'data-content="<span class=\'glyphicon glyphicon-picture\' style=\'color:' . $icon . '\'></span> ' . $file . '"');
                }
            }
            closedir($dh);
        }
    }

    return $PHPShopGUI->setSelect('option[theme]', $value, null, null, false, false, false, 1, false, 'theme_new');
}

// Стартовый вид
function actionStart() {
    global $PHPShopGUI, $PHPShopModules, $TitlePage, $PHPShopOrm, $PHPShopBase, $hideCatalog, $hideSite;

    PHPShopObj::loadClass('valuta');
    PHPShopObj::loadClass('user');

    // Выборка
    $data = $PHPShopOrm->select();
    $option = unserialize($data['admoption']);
    $bank = unserialize($data['bank']);

    // Валюты
    $PHPShopValutaArray = new PHPShopValutaArray();
    $valuta_array = $PHPShopValutaArray->getArray();
    if (is_array($valuta_array))
        foreach ($valuta_array as $val) {
            $dengi_value[] = array($val['name'], $val['id'], $data['dengi']);
            $kurs_value[] = array($val['name'], $val['id'], $data['kurs']);
        }


    // Статусы
    $PHPShopUserStatusArray = new PHPShopUserStatusArray();
    $userstatus_array = $PHPShopUserStatusArray->getArray();

    $userstatus_value[] = array(__('Авторизованный пользователь'), 0, $option['user_status']);
    if (is_array($userstatus_array))
        foreach ($userstatus_array as $val) {
            $userstatus_value[] = array($val['name'], $val['id'], $option['user_status']);
        }

    // Размер названия поля
    $PHPShopGUI->field_col = 3;
    $PHPShopGUI->addJSFiles('./js/jquery.waypoints.min.js', './system/gui/system.gui.js');
    $PHPShopGUI->setActionPanel($TitlePage, false, array('Сохранить'));

    $num_vitrina_value[] = array(1, 1, $data['num_vitrina']);
    $num_vitrina_value[] = array(2, 2, $data['num_vitrina']);
    $num_vitrina_value[] = array(3, 3, $data['num_vitrina']);
    $num_vitrina_value[] = array(4, 4, $data['num_vitrina']);
    $num_vitrina_value[] = array(5, 5, $data['num_vitrina']);
    $num_vitrina_value[] = array(6, 6, $data['num_vitrina']);

    $sklad_status_value[] = array('Не используется', 1, $option['sklad_status']);
    $sklad_status_value[] = array('Товар убирается с продаж', 2, $option['sklad_status']);
    $sklad_status_value[] = array('Товара нет в наличии', 3, $option['sklad_status']);

    $search_enabled_value[] = array('Искать в заказах', 2, $option['search_enabled']);
    $search_enabled_value[] = array('Искать в товарах', 3, $option['search_enabled']);
    $search_enabled_value[] = array('Не используется', 1, $option['search_enabled']);

    $new_enabled_value[] = array('Только со статусом новинки', 0, $option['new_enabled']);
    $new_enabled_value[] = array('Спецпредложения если нет новинок', 1, $option['new_enabled']);
    $new_enabled_value[] = array('Последние добавленные товары если нет новинок', 2, $option['new_enabled']);

    $search_pole_value[] = array('Наименование', 1, $option['search_pole']);
    $search_pole_value[] = array('Учитывать все', 2, $option['search_pole']);

    $filter_logic_value[] = array('Перекрестная', 0, $option['filter_logic']);
    $filter_logic_value[] = array('Перекрестная с  множественным выбором', 1, $option['filter_logic']);
    $filter_logic_value[] = array('Объединенная', 2, $option['filter_logic']);

    $timezone_value[] = array('Europe/Moscow', 'Europe/Moscow', $option['timezone']);
    $timezone_value[] = array('Europe/Kiev', 'Europe/Kiev', $option['timezone']);
    $timezone_value[] = array('Europe/Minsk', 'Europe/Minsk', $option['timezone']);
    $timezone_value[] = array('Asia/Novosibirsk ', 'Asia/Novosibirsk ', $option['timezone']);
    $timezone_value[] = array(__('Определяется сервером'), '', $option['timezone']);

    if (empty($data['num_row_adm']))
        $data['num_row_adm'] = 3;
    $num_row_adm_value[] = array('1', 1, $data['num_row_adm']);
    $num_row_adm_value[] = array('2', 2, $data['num_row_adm']);
    $num_row_adm_value[] = array('3', 3, $data['num_row_adm']);
    $num_row_adm_value[] = array('4', 4, $data['num_row_adm']);
    $num_row_adm_value[] = array('5', 5, $data['num_row_adm']);
    $num_row_adm_value[] = array('6', 6, $data['num_row_adm']);

    if (empty($option['catlist_depth'])) {
        $option['catlist_depth'] = 2;
    }

    $shop_type_value[] = array('Интернет-магазин', 0, $data['shop_type']);
    $shop_type_value[] = array('Каталог продукции', 1, $data['shop_type']);
    $shop_type_value[] = array('Сайт компании', 2, $data['shop_type']);

    // Содержание закладки 1
    if (empty($hideSite))
        $disp = $PHPShopGUI->setField('Конфигурация', $PHPShopGUI->setSelect('shop_type_new', $shop_type_value, false, true)) .
                $PHPShopGUI->setField("Общая пагинация", $PHPShopGUI->setInputText(false, 'num_row_new', $data['num_row'], 50), 1, 'Количество позиций на одной странице') .
                $PHPShopGUI->setField("Количество в Спецпредложениях", $PHPShopGUI->setInputText(false, 'spec_num_new', $data['spec_num'], 50)) .
                $PHPShopGUI->setField("Количество в Новинках", $PHPShopGUI->setInputText(false, 'new_num_new', $data['new_num'], 50)) .
                $PHPShopGUI->setField("Товарная сетка витрины", $PHPShopGUI->setSelect('num_vitrina_new', $num_vitrina_value, 50), 1, 'Товаров в длину 
	  для витрины главной страницы. Сетки 5 и 6 поддерживаются не всеми шаблонами') .
                $PHPShopGUI->setField("Товарная сетка в каталоге", $PHPShopGUI->setSelect('num_row_adm_new', $num_row_adm_value, 50) . '&nbsp;' . $PHPShopGUI->setCheckbox('num_row_set', 1, 'Применить сейчас ко всем каталогам', 0), 1, 'Товаров в длину 
	  для каталогов по умолчанию. Сетки 5 и 6 поддерживаются не всеми шаблонами') .
                $PHPShopGUI->setField("Вывод новинок", $PHPShopGUI->setSelect('option[new_enabled]', $new_enabled_value, null, true)) .
                $PHPShopGUI->setField("Сейчас покупают", $PHPShopGUI->setCheckbox('option[nowbuy_enabled]', 1, null, $option['nowbuy_enabled']), 1, false, $hideCatalog) .
                $PHPShopGUI->setField("Цифровые товары", $PHPShopGUI->setCheckbox('option[digital_product_enabled]', 1, 'Продажа цифровых товаров', $option['digital_product_enabled']), 1, 'Прикрепленные к товару файлы доступны после оплаты заказа в личном кабинете', $hideCatalog) .
                $PHPShopGUI->setField("Вывод товаров в каталоге", $PHPShopGUI->setCheckbox('option[catlist_enabled]', 1, 'Выводить товары в корневом каталоге', $option['catlist_enabled']), 1) .
                $PHPShopGUI->setField("Уровень вложенности вывода товаров", $PHPShopGUI->setInputText(false, 'option[catlist_depth]', $option['catlist_depth'], 50), 1) .
                $PHPShopGUI->setField("Динамическая прокрутка", $PHPShopGUI->setCheckbox('option[ajax_scroll]', 1, 'Отключить прокрутку у товаров', $option['ajax_scroll']), 1, 'Работает не во всех шаблонах') .
                $PHPShopGUI->setField('Временная зона', $PHPShopGUI->setSelect('option[timezone]', $timezone_value)
        );
    else
        $disp = $PHPShopGUI->setField('Конфигурация', $PHPShopGUI->setSelect('shop_type_new', $shop_type_value)) .
                $PHPShopGUI->setField("Общая пагинация", $PHPShopGUI->setInputText(false, 'num_row_new', $data['num_row'], 50), 1, 'Количество позиций на одной странице') .
                $PHPShopGUI->setField('Временная зона', $PHPShopGUI->setSelect('option[timezone]', $timezone_value))
        ;

    // Статусы заказов
    $PHPShopOrderStatusArray = new PHPShopOrderStatusArray();

    $status_array = $PHPShopOrderStatusArray->getArray();
    $status_array[0] = ['name' => __('Новый заказ'), 'sklad_action' => false, 'id' => 0, 'description' => __('Системный')];

    if (is_array($status_array))
        foreach ($status_array as $status_val) {
            if ($status_val['id'] != 1)
                $order_status_value[] = array($status_val['name'], $status_val['id'], $option['status_new_order'], 'data-subtext="' . $status_val['description'] . '"');
        }

    if (empty($hideSite) and empty($hideCatalog))
        $disp .= $PHPShopGUI->setField('Статус нового заказа', $PHPShopGUI->setSelect('option[status_new_order]', $order_status_value));

    $PHPShopGUI->_CODE = $PHPShopGUI->setCollapse('Вывод', $disp);

    $warehouse_enabled = $PHPShopBase->getNumRows('warehouses', "where enabled='1'");

    $price = $PHPShopGUI->setField("Валюта по умолчанию", $PHPShopGUI->setSelect('dengi_new', $dengi_value)) .
            $PHPShopGUI->setField("Валюта в заказе", $PHPShopGUI->setSelect('kurs_new', $kurs_value)) .
            $PHPShopGUI->setField("Накрутка цены", $PHPShopGUI->setInputText(false, 'percent_new', (int) $data['percent'], 100, '%')) .
            $PHPShopGUI->setField("НДС", $PHPShopGUI->setCheckbox('nds_enabled_new', 1, 'Учитывать НДС в счете', (int) $data['nds_enabled'])) .
            $PHPShopGUI->setField("Значение НДС", $PHPShopGUI->setInputText(false, 'nds_new', (int) $data['nds'], 100, '%')) .
            $PHPShopGUI->setField("Контроль склада", $PHPShopGUI->setSelect('option[sklad_status]', $sklad_status_value, null, true), 1, 'Активируется при сохранении товара') .
            $PHPShopGUI->setField("Склад", $PHPShopGUI->setCheckbox('option[sklad_enabled]', 1, 'Показывать значение склада у товара', $option['sklad_enabled']));

    if ($warehouse_enabled) {
        $price .= $PHPShopGUI->setField("Общий склад", $PHPShopGUI->setCheckbox('option[sklad_sum_enabled]', 1, 'Суммировать остатки по складам', $option['sklad_sum_enabled']), 1, 'Суммирует количество товара в дочерних складах в Общий склад. Срабатывает по нажатию кнопки Сохранить в карточке товара');
        $price .= $PHPShopGUI->setField("Сортировка по складам", $PHPShopGUI->setCheckbox('option[sklad_sort_enabled]', 1, 'Показывать фильтр отбора по складам', $option['sklad_sort_enabled']));
    }

    $price .= $PHPShopGUI->setField("Округление цен", $PHPShopGUI->setInputText(false, 'option[price_znak]', intval($option['price_znak']), 50), 1, 'Количество знаков после запятой в цене') .
            $PHPShopGUI->setField("Минимальная сумма заказа", $PHPShopGUI->setInputText(false, 'option[cart_minimum]', intval($option['cart_minimum']), 100)) .
            $PHPShopGUI->setField("Мультивалютные цены", $PHPShopGUI->setCheckbox('option[multi_currency_search]', 1, 'Сортировка по цене среди мультивалютных товаров', $option['multi_currency_search']), false, __('Автоматизируется через модуль Задачи')) .
            $PHPShopGUI->setField("Подтипы", $PHPShopGUI->setCheckbox('option[parent_price_enabled]', 1, 'Отключить автоматический расчет минимальной цены главного товара', $option['parent_price_enabled'])) .
            $PHPShopGUI->setField("Скидки", $PHPShopGUI->setCheckbox('option[auto_discount_disabled]', 1, 'Отключить автоматический расчет максимальной скидки при редактировании заказа', $option['auto_discount_disabled']));

    if (empty($option['search_row']))
        $option['search_row'] = $data['num_row_adm'];

    $search_row_value[] = array(1, 1, $option['search_row']);
    $search_row_value[] = array(2, 2, $option['search_row']);
    $search_row_value[] = array(3, 3, $option['search_row']);
    $search_row_value[] = array(4, 4, $option['search_row']);
    $search_row_value[] = array(5, 5, $option['search_row']);
    $search_row_value[] = array(6, 6, $option['search_row']);

    if (empty($option['search_num']))
        $option['search_num'] = $data['num_row'];

    $PHPShopGUI->_CODE .= $PHPShopGUI->setCollapse('Настройка поиска', $PHPShopGUI->setField('Область поиска', $PHPShopGUI->setSelect('option[search_pole]', $search_pole_value, null, true), 1, false, $hideSite) .
            $PHPShopGUI->setField("Товарная сетка поиска", $PHPShopGUI->setSelect('option[search_row]', $search_row_value, 50), 1, 'Товаров в длину 
	  для поиска. Сетки 5 и 6 поддерживаются не всеми шаблонами') .
            $PHPShopGUI->setField("Количество в Поиске", $PHPShopGUI->setInputText(false, 'option[search_num]', $option['search_num'], 50)) .
            $PHPShopGUI->setField("Яндекс.Спеллер", $PHPShopGUI->setCheckbox('option[yandex_speller_enabled]', 1, 'Использовать Яндекс.Спеллер для  проверки орфографии в поиске на сайте', $option['yandex_speller_enabled']))
    );

    if (empty($option['filter_cache_period']))
        $option['filter_cache_period'] = 10;

    // Тип загрузки характеристик
    $sort_value[] = array(__('Раздельные'), 0, $option['update_sort_type']);
    $sort_value[] = array(__('Общие'), 1, $option['update_sort_type']);

    $filter = $PHPShopGUI->setField("Кешировать значения фильтра", $PHPShopGUI->setCheckbox('option[filter_cache_enabled]', 1, 'Запоминать пустые сортировки фильтра, чтобы не показывать их в последующем', $option['filter_cache_enabled']), 1) .
            $PHPShopGUI->setField("Период кеширования", $PHPShopGUI->setInputText(false, 'option[filter_cache_period]', $option['filter_cache_period'], 100, __('дней'), false, false, false, false), 1, 'Сколько дней хранить кешированные данные') .
            $PHPShopGUI->setField("Отображать количество товара", $PHPShopGUI->setCheckbox('option[filter_products_count]', 1, 'Выводить количество товара рядом со значением фильтра', $option['filter_products_count']), 1) .
            $PHPShopGUI->setField('Логика фильтра', $PHPShopGUI->setSelect('option[filter_logic]', $filter_logic_value, null, true)) .
            $PHPShopGUI->setField("Импортировать характеристики", $PHPShopGUI->setSelect('option[update_sort_type]', $sort_value)
    );

    if (empty($hideSite))
        $PHPShopGUI->_CODE .= $PHPShopGUI->setCollapse('Настройка фильтра', $filter);

    if (empty($hideCatalog))
        $PHPShopGUI->_CODE .= $PHPShopGUI->setCollapse('Настройка цен', $price);

    if (empty($option['user_phone_mask']))
        $option['user_phone_mask'] = '(999) 999-9999';

    $PHPShopGUI->_CODE .= $PHPShopGUI->setCollapse('Настройка дизайна', $PHPShopGUI->setField('Язык', GetLocaleList($option['lang'])) . $PHPShopGUI->setField('Дизайн', GetSkinList($data['skin']) . '<br>' . $PHPShopGUI->setCheckbox('option[user_skin]', 1, 'Панель редактора дизайна Live Edit', $option["user_skin"]), 1, 'Дизайн шаблон сайта (front-end)') .
            $PHPShopGUI->setField("Логотип", $PHPShopGUI->setIcon($data['logo'], "logo_new", false), 1, 'Используется в шапке дизайна и печатных документах') .
            $PHPShopGUI->setField("Favicon", $PHPShopGUI->setIcon($data['icon'], "icon_new", false, array('load' => false, 'server' => true, 'url' => true, 'multi' => false, 'view' => false)), 1, 'Иконка сайта в браузере и поиске') .
            $PHPShopGUI->setField("ВКонтакте", $PHPShopGUI->setInputText(null, "bank[vk]", $bank['vk'], 350, false, false, false, 'адрес страницы')) .
            $PHPShopGUI->setField("Telegram", $PHPShopGUI->setInputText(null, "bank[telegram]", $bank['telegram'], 350, false, false, false, 'адрес страницы')) .
            $PHPShopGUI->setField("Одноклассники", $PHPShopGUI->setInputText(null, "bank[odnoklassniki]", $bank['odnoklassniki'], 350, false, false, false, 'адрес страницы')) .
            $PHPShopGUI->setField("WhatsApp", $PHPShopGUI->setInputText(null, "bank[whatsapp]", $bank['whatsapp'], 350, false, false, false, 'адрес страницы')) .
            $PHPShopGUI->setField("Rutube", $PHPShopGUI->setInputText(null, "bank[youtube]", $bank['youtube'], 350, false, false, false, 'адрес страницы')) .
            $PHPShopGUI->setField("Формат ввода телефона", $PHPShopGUI->setInputText(null, "option[user_phone_mask]", $option['user_phone_mask'], 300) .
                    $PHPShopGUI->setCheckbox('option[user_phone_mask_enabled]', 1, 'Отключить маску ввода телефона', $option['user_phone_mask_enabled']), 1, 'Формат: (999) 999-9999') .
            $PHPShopGUI->setField("Защита от ботов", $PHPShopGUI->setCheckbox('option[user_captcha_enabled]', 1, 'Отключить проверочную картинку для защиты от ботов', $option['user_captcha_enabled'])) .
            $PHPShopGUI->setField("Конфиденциальность", $PHPShopGUI->setCheckbox('option[user_cookie_enabled]', 1, 'Отключить сообщение об использование cookie-файлов на PC', $option['user_cookie_enabled']) . '<br>' . $PHPShopGUI->setCheckbox('option[user_cookie_mobile_enabled]', 1, 'Отключить сообщение об использование cookie-файлов на мобильных устройствах', $option['user_cookie_mobile_enabled']))
    );

    $PHPShopGUI->_CODE .= $PHPShopGUI->setCollapse('Настройка e-mail уведомлений', $PHPShopGUI->setField("E-mail оповещение", $PHPShopGUI->setInputText(null, "adminmail2_new", $data['adminmail2'], 300), 1, 'Для использования сторонних SMTP сервисов адрес должен совпадать с пользователем SMTP') .
            $PHPShopGUI->setField("SMTP", $PHPShopGUI->setCheckbox('option[mail_smtp_enabled]', 1, 'Отправка почты через SMTP протокол', $option['mail_smtp_enabled']) . '<br>' .
                    $PHPShopGUI->setCheckbox('option[mail_smtp_debug]', 1, 'Включить отладочные сообщения (Debug)', $option['mail_smtp_debug']) . '<br>' .
                    $PHPShopGUI->setCheckbox('option[mail_smtp_auth]', 1, 'Автоопределение TLS для SMTP', $option['mail_smtp_auth'])
            ) .
            $PHPShopGUI->setField("Почтовый сервер SMTP", $PHPShopGUI->setInputText(null, "option[mail_smtp_host]", $option['mail_smtp_host'], 300, false, false, false, 'smtp.yandex.ru'), 1, 'Сервер исходяшей почты SMTP') .
            $PHPShopGUI->setField("Порт сервера", $PHPShopGUI->setInputText(null, "option[mail_smtp_port]", $option['mail_smtp_port'], 100, false, false, false, '25'), 1, 'Порт почтового SMTP сервера') .
            $PHPShopGUI->setField("Пользователь", $PHPShopGUI->setInputText(null, "option[mail_smtp_user]", $option['mail_smtp_user'], 300, false, false, false, 'user@yandex.ru')) .
            $PHPShopGUI->setField("Пароль", $PHPShopGUI->setInput('password', "option[mail_smtp_pass]", $option['mail_smtp_pass'], null, 300)) .
            $PHPShopGUI->setField("Обратный адрес", $PHPShopGUI->setInputText(null, "option[mail_smtp_replyto]", $option['mail_smtp_replyto'], 300), 1, 'Ответы на почтовые сообщения будут приходить на этот адрес')
    );


    if (empty($hideCatalog))
        $PHPShopGUI->_CODE .= $PHPShopGUI->setCollapse('Настройка пользователей', $PHPShopGUI->setField("Регистрация пользователей", $PHPShopGUI->setCheckbox('option[user_mail_activate]', 1, 'Активация через E-mail', $option['user_mail_activate']) . '<br>' . $PHPShopGUI->setCheckbox('option[user_mail_activate_pre]', 1, 'Ручная активация администратором', $option['user_mail_activate_pre']) . '<br>' . $PHPShopGUI->setCheckbox('option[user_price_activate]', 1, 'Регистрация для просмотра цен', $option['user_price_activate']) . '<br>' . $PHPShopGUI->setCheckbox('option[user_items_activate]', 1, 'Регистрация для просмотра остатков', $option['user_items_activate']) . '<br>' . $PHPShopGUI->setCheckbox('option[user_servers_control]', 1, 'Разделение пользователей для витрин', $option['user_servers_control']))
                . $PHPShopGUI->setField("Статус после регистрации", $PHPShopGUI->setSelect('option[user_status]', $userstatus_value, 300))
                . $PHPShopGUI->setField("Бонусы за покупку:", $PHPShopGUI->setInputText(null, "option[bonus]", intval($option['bonus']), 100, '%'))
                . $PHPShopGUI->setField("Максимальная оплата бонусами:", $PHPShopGUI->setInputText(null, "option[order_bonus]", intval($option['order_bonus']), 100, '%'))
        );


    $PHPShopGUI->_CODE .= $PHPShopGUI->setCollapse('Настройка управления', $PHPShopGUI->setField('Цветовая тема', GetAdminSkinList($option['theme']), 1, 'Цветовая тема оформления панели управления (back-end)') .
            $PHPShopGUI->setField('Язык', GetLocaleList($option['lang_adm'], 'lang_adm')) .
            $PHPShopGUI->setField("HTML-редактор по умолчанию", GetEditors($option['editor']), 1, 'Визуальный редактор контента') .
            $PHPShopGUI->setField("Цвет редактора исходного кода", GetAceSkinList($option['ace_theme']), 1, 'Стилизованная подсветка синтаксиса исходного HTML кода') .
            $PHPShopGUI->setField("Заголовок", $PHPShopGUI->setInputText(null, "option[adm_title]", substr($option['adm_title'], 0, 70), 300), 1, 'Брендовый заголовок в левом верхнем углу панели управления') .
            $PHPShopGUI->setField("Multi Manager", $PHPShopGUI->setCheckbox('option[rule_enabled]', 1, 'Учет прав управления товарами для менеджеров', $option['rule_enabled']), null, null, $hideSite) .
            $PHPShopGUI->setField("Логика совместных продаж", $PHPShopGUI->setCheckbox('option[odnotip]', 1, 'Включить перекрестное добавление товаров для совместной продажи', $option['odnotip']), null, null, $hideCatalog) .
            $PHPShopGUI->setField("Yandex Cloud", $PHPShopGUI->setCheckbox('option[yandexcloud_enabled]', 1, 'Отключить интеграцию с Yandex Cloud', $option['yandexcloud_enabled'])) .
            $PHPShopGUI->setField("Проверка IP адреса", $PHPShopGUI->setCheckbox('option[ip_enabled]', 1, 'Отключить проверку IP адреса для авторизации через CloudFlare', $option['ip_enabled'])) .
            $PHPShopGUI->setField("Быстрый просмотр", $PHPShopGUI->setCheckbox('option[fast_view]', 1, 'Отключить быстрый просмотр товаров в окне', $option['fast_view']), null, null, $hideSite) .
            $PHPShopGUI->setField("Звуковое оповещение", $PHPShopGUI->setCheckbox('option[sound_off]', 1, 'Отключить звуковое оповещение', $option['sound_off'])) .
            $PHPShopGUI->setField("Быстрый поиск", $PHPShopGUI->setSelect('option[search_enabled]', $search_enabled_value, null, true), 1, 'Поиск в верхнем правом углу панели управления', $hideCatalog)
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

    // Счетчик сообщений о поддержке
    unset($option['support_notice']);

    // Корректировка пустых значений
    $PHPShopOrm->updateZeroVars('option.user_calendar', 'option.cloud_enabled', 'option.digital_product_enabled', 'option.parent_price_enabled', 'option.user_skin', 'option.user_mail_activate', 'option.user_mail_activate_pre', 'option.user_price_activate', 'option.mail_smtp_enabled', 'option.mail_smtp_debug', 'option.multi_currency_search', 'option.mail_smtp_auth', 'option.sklad_enabled', 'option.rule_enabled', 'option.catlist_enabled', 'option.filter_cache_enabled', 'option.filter_products_count', 'option.chat_enabled', 'option.new_enabled', 'option.sklad_sum_enabled', 'option.user_servers_control', 'option.user_phone_mask_enabled', 'option.user_items_activate', 'option.ajax_scroll', 'option.ajax_scroll_paginator', 'option.fast_view', 'option.auto_discount_disabled', 'option.chat_support', 'option.user_captcha_enabled', 'option.user_cookie_enabled', 'option.user_cookie_mobile_enabled', 'option.odnotip', 'option.sklad_sort_enabled', 'option.nowbuy_enabled', 'option.yandex_speller_enabled', 'option.yandexcloud_enabled', 'option.ip_enabled', 'option.sound_off');

    if (is_array($_POST['option']))
        foreach ($_POST['option'] as $key => $val)
            $option[$key] = $val;

    if ($option['product_off'] == 1)
        $option['cart_off'] = $option['product_off'];

    // Смена шаблона на front-end
    if ($data['skin'] != $_POST['skin_new'] and PHPShopSecurity::true_skin($_POST['skin_new']))
        $_SESSION['skin'] = $_POST['skin_new'];

    $_POST['admoption_new'] = serialize($option);
    $_POST['nds_enabled_new'] = $_POST['nds_enabled_new'] ? 1 : 0;
    $_POST['nds_enabled_new'] = $_POST['nds_enabled_new'] ? 1 : 0;

    // Логотип
    $logo_new = iconAdd('logo_new');
    if (!empty($logo_new))
        $_POST['logo_new'] = $logo_new;

    // Favicon
    $icon_new = iconAdd('icon_new');
    if ($icon_new)
        $_POST['icon_new'] = $icon_new;

    // Смена сетки принудительно
    if (!empty($_POST['num_row_set'])) {
        $PHPShopOrmCat = new PHPShopOrm($GLOBALS['SysValue']['base']['categories']);
        $PHPShopOrmCat->update(array('num_row_new' => $_POST['num_row_adm_new']));
    }

    $bank = unserialize($data['bank']);
    if (is_array($_POST['bank']))
        foreach ($_POST['bank'] as $key => $val)
            $bank[$key] = $val;

    $_POST['bank_new'] = serialize($bank);


    // Перехват модуля
    $PHPShopModules->setAdmHandler(__FILE__, __FUNCTION__, $_POST);

    $action = $PHPShopOrm->update($_POST, array('id' => '=' . $_POST['rowID']));

    return array("success" => $action);
}

// Добавление изображения 
function iconAdd($name = 'icon_new') {
    global $PHPShopSystem;

    // Папка сохранения
    $path = '/UserFiles/Image/' . $PHPShopSystem->getSerilizeParam('admoption.image_result_path');

    // Копируем от пользователя
    if (!empty($_FILES['file']['name'])) {
        $_FILES['file']['ext'] = PHPShopSecurity::getExt($_FILES['file']['name']);
        $_FILES['file']['name'] = PHPShopString::toLatin(str_replace('.' . $_FILES['file']['ext'], '', PHPShopString::utf8_win1251($_FILES['file']['name']))) . '.' . $_FILES['file']['ext'];
        if (in_array($_FILES['file']['ext'], array('gif', 'png', 'jpg', 'svg'))) {
            if (move_uploaded_file($_FILES['file']['tmp_name'], $_SERVER['DOCUMENT_ROOT'] . $GLOBALS['dir']['dir'] . $path . $_FILES['file']['name'])) {
                $file = $GLOBALS['dir']['dir'] . $path . $_FILES['file']['name'];
            }
        }
    }

    // Читаем файл из URL
    elseif (!empty($_POST['furl'])) {
        $file = $_POST[$name];
    }

    // Читаем файл из файлового менеджера
    elseif (!empty($_POST[$name])) {
        $file = $_POST[$name];
    }

    if (empty($file))
        $file = '';

    return $file;
}

// Обработка событий
$PHPShopGUI->getAction();
?>