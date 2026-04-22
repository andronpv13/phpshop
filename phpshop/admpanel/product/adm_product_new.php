<?php

PHPShopObj::loadClass("base");
PHPShopObj::loadClass("system");
PHPShopObj::loadClass("valuta");
PHPShopObj::loadClass("array");
PHPShopObj::loadClass("page");
PHPShopObj::loadClass("security");
PHPShopObj::loadClass("category");
PHPShopObj::loadClass("product");

$TitlePage = __('Новый товар');
$PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['products']);

$colorArray = array(
    'белый' => '#ffffff',
    'черный' => '#000000',
    'красный' => '#FF0000',
    'зеленый' => '#008000',
    'синий' => '#0000FF',
    'голубой' => '#00FFFF',
    'желтый' => '#FFFF00',
    'розовый' => '#FFC0CB',
    'оранжевый' => '#FFA500',
    'фиолетовый' => '#EE82EE',
    'коричневый' => '#A0522D',
    'серый' => '#808080',
    'серебряный' => '#C0C0C0'
);

// Построение дерева категорий
function treegenerator($array, $i, $curent, $dop_cat_array) {
    global $tree_array;
    $del = '&brvbar;&nbsp;&nbsp;&nbsp;&nbsp;';
    $tree_select = $tree_select_dop = $check = false;

    $del = str_repeat($del, $i);
    if (!empty($array) and is_array($array['sub'])) {
        foreach ($array['sub'] as $k => $v) {

            $check = treegenerator(@$tree_array[$k], $i + 1, $curent, $dop_cat_array);

            if ($k == $curent)
                $selected = 'selected';
            else
                $selected = null;

            // Допкаталоги
            $selected_dop = null;
            if (is_array($dop_cat_array))
                foreach ($dop_cat_array as $vs) {
                    if ($k == $vs)
                        $selected_dop = "selected";
                }

            if (empty($check['select'])) {
                $tree_select .= '<option value="' . $k . '" ' . $selected . '>' . $del . $v . '</option>';
                $tree_select_dop .= '<option value="' . $k . '" ' . $selected_dop . '>' . $del . $v . '</option>';

                $i = 1;
            } else {
                $tree_select .= '<option value="' . $k . '" ' . $selected . ' disabled>' . $del . $v . '</option>';
                $tree_select_dop .= '<option value="' . $k . '" ' . $selected_dop . ' disabled >' . $del . $v . '</option>';
            }

            $tree_select .= $check['select'];
            $tree_select_dop .= $check['select_dop'];
        }
    }
    return array('select' => $tree_select, 'select_dop' => $tree_select_dop);
}

function actionStart() {
    global $PHPShopGUI, $PHPShopModules, $PHPShopOrm, $PHPShopSystem, $PHPShopBase, $hideCatalog;

    // ID будущего создаваемого товара
    $newId = getLastID();
    (new PHPShopOrm($GLOBALS['SysValue']['base']['foto']))->delete(array('parent' => '=' . (int) $newId));
    unset($_SESSION['imgCopyID']);

    // Начальные данные
    $data = array();
    if (!empty($_GET['cat']))
        $data['category'] = intval($_GET['cat']);

    if (empty($_GET['id'])) {
        $data['ed_izm'] = $ed_izm = __('шт.');
        $data['baseinputvaluta'] = $PHPShopSystem->getDefaultOrderValutaId();
        $data['name'] = __('Новый товар');
        $data['enabled'] = 1;
        $data['newtip'] = 1;
        $data['p_enabled'] = 1;
        $data['yml'] = 1;
        $data['id'] = $newId;
    } else {
        // Создание копии товара
        $data = $PHPShopOrm->select(array('*'), array('id' => '=' . intval($_GET['id'])));
        $data['prod_seo_name'] = null;

        // Копирование подтипов
        if (!empty($data['parent']) and empty($data['parent_enabled'])) {
            $parent = explode(',', $data['parent']);
            if (is_array($parent)) {

                foreach ($parent as $parent_id) {
                    $parent_data = $PHPShopOrm->select(array('*'), array('id' => '=' . intval($parent_id)));

                    if (is_array($parent_data)) {
                        unset($parent_data['id']);
                        $PHPShopOrmParent = new PHPShopOrm($GLOBALS['SysValue']['base']['products']);
                        $parent_list[] = $PHPShopOrmParent->insert($parent_data, null);
                    }
                }

                $data['parent'] = implode(',', $parent_list);
            }

            // получаем ИД будущего создаваемого товара после создания подтипов
            $newId = getLastID();
        }

        $data['id'] = $newId;

        // Копирование галереи
        if (!empty($data['pic_small'])) {
            $images = imgCopy($_GET['id'], $newId, $data['pic_big']);
            $data['pic_small'] = $images['pic_small'];
            $data['pic_big'] = $images['pic_big'];
        }
    }

    $data = $PHPShopGUI->valid($data, 'dop_cat', 'category', 'uid', 'items', 'weight', 'length', 'width', 'height', 'odnotip', 'spec', 'num', 'price', 'price2', 'price3', 'price4', 'price5', 'price_n', 'price_purch', 'sklad', 'yml_bid_array', 'description', 'content', 'page', 'files', 'pic_small', 'pic_big', 'vendor_array', 'title', 'title_enabled', 'title_shablon', 'descrip', 'descrip_enabled', 'descrip_shablon', 'keywords', 'keywords_enabled', 'keywords_shablon');

    $PHPShopGUI->action_select['Урок'] = array(
        'name' => 'Обучение',
        'action' => 'presentation',
        'icon' => 'glyphicon glyphicon-education'
    );

    // Фрейм
    if (!empty($GLOBALS['isFrame'])) {
        $PHPShopGUI->action_button['Сохранить и закрыть'] = array(
            'name' => 'Сохранить',
            'locale' => true,
            'action' => 'actionInsert',
            'class' => 'btn btn-default btn-sm navbar-btn',
            'type' => 'submit',
            'icon' => 'glyphicon glyphicon-floppy-saved'
        );

        $PHPShopGUI->action_button['Создать и редактировать'] = array(
            'name' => 'Сохранить',
            'locale' => true,
            'action' => 'editID',
            'class' => 'btn btn-default btn-sm navbar-btn hidden',
            'type' => 'submit',
            'icon' => 'glyphicon glyphicon-floppy-saved'
        );
    }

    $PHPShopGUI->setActionPanel(__("Новый товар"), false, array('Создать и редактировать', 'Сохранить и закрыть'));

    // Размер названия поля
    $PHPShopGUI->field_col = 4;
    $PHPShopGUI->addJSFiles('./js/jquery.tagsinput.min.js', './catalog/gui/catalog.gui.js', './js/jquery.waypoints.min.js', './product/gui/product.gui.js', './js/bootstrap-tour.min.js');
    if ($GLOBALS['PHPShopBase']->codBase == 'utf-8')
        $PHPShopGUI->addJSFiles('./product/gui/tour_utf.gui.js');
    else
        $PHPShopGUI->addJSFiles('./product/gui/tour.gui.js');

    $PHPShopGUI->addCSSFiles('./css/jquery.tagsinput.css');

    // Права менеджеров
    if ($PHPShopSystem->ifSerilizeParam('admoption.rule_enabled', 1) and ! $PHPShopBase->Rule->CheckedRules('catalog', 'remove')) {
        $where = array('secure_groups' => " REGEXP 'i" . $_SESSION['idPHPSHOP'] . "i' or secure_groups = ''");
        $secure_groups = true;
    } else
        $where = $secure_groups = false;

    $PHPShopCategoryArray = new PHPShopCategoryArray($where);
    $CategoryArray = $PHPShopCategoryArray->getArray();

    $CategoryArray[0]['name'] = '- ' . __('Выбрать каталог') . ' -';
    $tree_array = array();
    $tree_select = $tree_select_dop = null;

    $getKey = $PHPShopCategoryArray->getKey('parent_to.id', true);

    if (is_array($getKey))
        foreach ($getKey as $k => $v) {
            foreach ($v as $cat) {
                $tree_array[$k]['sub'][$cat] = $CategoryArray[$cat]['name'];
            }
            $tree_array[$k]['name'] = $CategoryArray[$k]['name'];
            $tree_array[$k]['id'] = $k;
        }

    $tree_array[0]['sub'][1000000] = __('Неопределенные товары');
    $tree_array[1000000]['name'] = __('Неопределенные товары');
    $tree_array[1000000]['id'] = 1000000;
    $tree_array[1000000]['sub'][1000001] = __('Загруженные CRM');
    $tree_array[1000000]['sub'][1000002] = __('Загруженные CSV');
    $tree_array[1000002]['id'] = 0;

    $GLOBALS['tree_array'] = &$tree_array;

    $dop_cat_array = preg_split('/#/', $data['dop_cat'], -1, PREG_SPLIT_NO_EMPTY);

    if (!empty($tree_array) and is_array($tree_array[0]['sub']))
        foreach ($tree_array[0]['sub'] as $k => $v) {
            $check = treegenerator(@$tree_array[$k], 1, $data['category'], $dop_cat_array);

            if ($k == $data['category'])
                $selected = 'selected';
            else
                $selected = null;

            // Допкаталоги
            $selected_dop = null;
            if (is_array($dop_cat_array))
                foreach ($dop_cat_array as $vs) {
                    if ($k == $vs)
                        $selected_dop = "selected";
                }

            if (empty($tree_array[$k]))
                $disabled = null;
            else
                $disabled = 'disabled';

            $tree_select .= '<option value="' . $k . '" ' . $selected . $disabled . '>' . $v . '</option>';
            $tree_select_dop .= '<option value="' . $k . '" ' . $selected_dop . $disabled . '>' . $v . '</option>';

            $tree_select .= $check['select'];
            $tree_select_dop .= $check['select_dop'];
        }

    $tree_select_dop = '<select class="selectpicker show-menu-arrow hidden-edit" data-live-search="true" data-container="body"  data-style="btn btn-default btn-sm" name="dop_cat[]" data-width="100%" multiple><option value="0">' . $CategoryArray[0]['name'] . '</option>' . $tree_select_dop . '</select>';

    $tree_select = '<select class="selectpicker show-menu-arrow hidden-edit" data-live-search="true" data-container="body"  data-style="btn btn-default btn-sm" name="category_new"  data-width="100%"><option value="0">' . $CategoryArray[0]['name'] . '</option>' . $tree_select . '</select>';

    // Наименование
    $Tab_info .= $PHPShopGUI->setField("Название", $PHPShopGUI->setTextarea('name_new', $data['name']));

    // Артикул
    $Tab_info .= $PHPShopGUI->setField('Артикул', $PHPShopGUI->setInputText(null, 'uid_new', $data['uid']));

    // Иконка
    if ($PHPShopSystem->ifSerilizeParam("admoption.image_off", 1)) {
        $Tab_info .= $PHPShopGUI->setField("Изображение", $PHPShopGUI->setIcon($data['pic_big'], "pic_big_new", true, array('load' => false, 'server' => true, 'url' => true)), 1, 'Активирован режим для товара с одной картинкой, заранее подготовленной на сервере. Для загрузки дополнительных фото товара, снимите запрет на фотогалерею, снимите галку в Настройка - Изображения - Отключить фотогалерею.');

        $Tab_info .= $PHPShopGUI->setField("Превью", $PHPShopGUI->setFile($data['pic_small'], "pic_small_new", array('load' => false, 'server' => 'image', 'url' => true)), 1, 'Активирован режим для товара с одной картинкой, заранее подготовленной на сервере. Для загрузки дополнительных фото товара, снимите запрет на фотогалерею, снимите галку в Настройка - Изображения - Отключить фотогалерею.');
    }
    // Копирование
    else if (!empty($_GET['id'])) {
        $Tab_info .= $PHPShopGUI->setField("Изображение", $PHPShopGUI->setIcon($data['pic_big'], "pic_big_new", true, array('load' => false, 'server' => true, 'url' => true, 'view' => true)));
        $Tab_info .= $PHPShopGUI->setFile($data['pic_small'], "pic_small_new", array('load' => false, 'server' => 'image', 'url' => false, 'view' => true));
    }

    if (empty($hideCatalog)) {

        // Дополнительный склад
        $PHPShopOrmWarehouse = new PHPShopOrm($GLOBALS['SysValue']['base']['warehouses']);
        $dataWarehouse = $PHPShopOrmWarehouse->select(array('*'), array('enabled' => "='1'"), array('order' => 'num'), array('limit' => 100));
        if (is_array($dataWarehouse)) {

            $Tab_info .= $PHPShopGUI->setField('Общий склад', $PHPShopGUI->setInputText(false, 'items_new', $data['items'], 100, $ed_izm), 'left');

            foreach ($dataWarehouse as $row) {
                $Tab_info .= $PHPShopGUI->setField($row['name'], $PHPShopGUI->setInputText(false, 'items' . $row['id'] . '_new', $data['items' . $row['id']], 100, $ed_izm), 2, $row['description'], null, 'control-label', false);
            }
        } else
            $Tab_info .= $PHPShopGUI->setField('Склад', $PHPShopGUI->setInputText(false, 'items_new', $data['items'], 100, $ed_izm), 'left');
    }

    // Габариты по умолчанию
    if (!empty($_GET['cat'])) {
        $PHPShopCategory = new PHPShopCategory((int) $_GET['cat']);
        if ($PHPShopCategory) {
            $data['weight'] = $PHPShopCategory->getParam('weight');
            $data['length'] = $PHPShopCategory->getParam('length');
            $data['width'] = $PHPShopCategory->getParam('width');
            $data['height'] = $PHPShopCategory->getParam('height');
            $data['ed_izm'] = $PHPShopCategory->getParam('ed_izm');
        }
    }

    if (empty($data['ed_izm']))
        $ed_izm = __('шт.');

    // Вес
    $Tab_info_size = $PHPShopGUI->setField('Вес', $PHPShopGUI->setInputText(false, 'weight_new', $data['weight'], 100, __('г&nbsp;&nbsp;&nbsp;&nbsp;')), 'left');
    $Tab_info_size .= $PHPShopGUI->setField('Длина', $PHPShopGUI->setInputText(false, 'length_new', $data['length'], 100, __('см&nbsp;')), 'left');
    $Tab_info_size .= $PHPShopGUI->setField('Ширина', $PHPShopGUI->setInputText(false, 'width_new', $data['width'], 100, __('см&nbsp;')), 'left');
    $Tab_info_size .= $PHPShopGUI->setField('Высота', $PHPShopGUI->setInputText(false, 'height_new', $data['height'], 100, __('см&nbsp;')), 'left');

    $Tab_info_size .= $PHPShopGUI->setField('Единица измерения', $PHPShopGUI->setInputText(false, 'ed_izm_new', $ed_izm, 100));

    // Выбор каталога
    $Tab_info_dop = $PHPShopGUI->setField("Каталог", $tree_select);

    // Рекомендуемые товары
    $Tab_info_dop .= $PHPShopGUI->setField('Рекомендуемые товары для совместной продажи', $PHPShopGUI->setTextarea('odnotip_new', $data['odnotip'], false, false, false, __('Укажите ID товаров или воспользуйтесь') . ' <a href="#" data-target="#odnotip_new"  class="btn btn-sm btn-default tag-search"><span class="glyphicon glyphicon-search"></span> ' . __('поиском товаров') . '</a>'));

    // Дополнительные каталоги
    $Tab_info_dop .= $PHPShopGUI->setField('Дополнительные каталоги', $tree_select_dop, 1, 'Товары одновременно выводятся в нескольких каталогах.');

    // Опции вывода
    $Tab_info .= $PHPShopGUI->setField('Опции вывода', $PHPShopGUI->setCheckbox('enabled_new', 1, 'Вывод в каталоге', $data['enabled']) . '<br>' .
            $PHPShopGUI->setCheckbox('sklad_new', 1, 'Нет в наличии', $data['sklad']) . '<br>' .
            $PHPShopGUI->setCheckbox('spec_new', 1, 'Спецпредложение', $data['spec']) . '<br>' .
            $PHPShopGUI->setCheckbox('newtip_new', 1, 'Новинка', $data['newtip']));
    $Tab_info .= $PHPShopGUI->setField('Сортировка', $PHPShopGUI->setInputText('&#8470;', 'num_new', $data['num'], 100));

    $type_value[] = array('Товар', 1, $data['type']);
    $type_value[] = array('Услуга', 2, $data['type']);
    $Tab_info .= $PHPShopGUI->setField('Тип', $PHPShopGUI->setSelect('type_new', $type_value, 100, true));

    $Tab_rating = $PHPShopGUI->setField('Значение', $PHPShopGUI->setInputText(null, 'rate_new', $data['rate'], 50), 1, 'Значение от 0 до 5');
    $Tab_rating .= $PHPShopGUI->setField('Голоса', $PHPShopGUI->setInputText(null, 'rate_count_new', $data['rate_count'], 50));

    $Tab1 = $PHPShopGUI->setCollapse('Информация', $Tab_info);
    $Tab1 .= $PHPShopGUI->setCollapse('Размещение', $Tab_info_dop);

    // Валюты
    $PHPShopValutaArray = new PHPShopValutaArray();
    $valuta_array = $PHPShopValutaArray->getArray();
    $defvaluta = $PHPShopSystem->getValue('dengi');
    $valuta_area = null;
    if (is_array($valuta_array))
        foreach ($valuta_array as $val) {
            if ($data['baseinputvaluta'] == $val['id']) {
                $check = 'checked';
                $valuta_def_name = $val['code'];
            } else
                $check = false;
            $valuta_area .= $PHPShopGUI->setRadio('baseinputvaluta_new', $val['id'], $val['name'], $defvaluta, false);
        }

    // Цены
    $Tab_price = $PHPShopGUI->setField('Цена 1', $PHPShopGUI->setInputText(null, 'price_new', $data['price'], 150, $valuta_def_name));
    $Tab_price .= $PHPShopGUI->setField('Цена 2', $PHPShopGUI->setInputText(null, 'price2_new', $data['price2'], 150, $valuta_def_name));
    $Tab_price .= $PHPShopGUI->setField('Цена 3', $PHPShopGUI->setInputText(null, 'price3_new', $data['price3'], 150, $valuta_def_name));
    $Tab_price .= $PHPShopGUI->setField('Цена 4', $PHPShopGUI->setInputText(null, 'price4_new', $data['price4'], 150, $valuta_def_name));
    $Tab_price .= $PHPShopGUI->setField('Цена 5', $PHPShopGUI->setInputText(null, 'price5_new', $data['price5'], 150, $valuta_def_name));
    $Tab_price .= $PHPShopGUI->setField('Старая цена', $PHPShopGUI->setInputText(null, 'price_n_new', $data['price_n'], 150, $valuta_def_name));
    $Tab_price .= $PHPShopGUI->setField('Закупочная цена', $PHPShopGUI->setInputText(null, 'price_purch_new', $data['price_purch'], 150, $valuta_def_name));

    // Валюта
    $Tab_price .= $PHPShopGUI->setField('Валюта', $valuta_area);

    $Tab1 .= $PHPShopGUI->setCollapse('Цены', $Tab_price, 'in', true, true, array('type' => 'price'));
    $Tab1 .= $PHPShopGUI->setCollapse('Габариты', $Tab_info_size);


    $Tab_rating = $PHPShopGUI->setCollapse('Рейтинг', $Tab_rating, false);

    // Подтипы
    $option_info = '<p class="text-muted">' . __('Для добавления новых подтипов необходимо сначала создать основной товар и затем перейти в его редактирование') . ' <span href="?path=sort" class="btn btn-default btn-xs"><span class="glyphicon glyphicon-floppy-saved"></span> ' . __('Создать и редактировать') . '</span>.</p>';
    // Подтипы
    if (!empty($data['parent']))
        $Tab1 .= $PHPShopGUI->setInputArg(array('name' => 'parent_new', 'type' => 'hidden', 'value' => $data['parent']));

    $Tab_option = $PHPShopGUI->setCollapse('Подтипы', $option_info, $collapse = 'none', false, false);

    // Редактор краткого описания
    $Tab2 = $PHPShopGUI->setCollapse('Краткое описание', $PHPShopGUI->loadLib('tab_description', $data));

    // Редактор подробного описания
    $Tab3 = $PHPShopGUI->setCollapse('Подробное описание', $PHPShopGUI->loadLib('tab_content', $data));

    // Статьи
    $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['page']);
    $data_page = $PHPShopOrm->select(['*'], ['category' => '!=2000', 'enabled' => "='1'"], array('order' => 'name'), array('limit' => 500));

    if (strstr($data['page'], ',')) {
        $data['page'] = explode(",", $data['page']);
    } else
        $data['page'] = array($data['page']);

    $value = array();
    if (is_array($data_page))
        foreach ($data_page as $val) {
            if (is_numeric(array_search($val['link'], $data['page']))) {
                $check = 'selected';
            } else
                $check = false;

            $value[] = array($val['name'], $val['link'], $check);
        }

    // Статьи
    $Tab_docs = $PHPShopGUI->setCollapse('Статьи', $PHPShopGUI->setSelect('page_new[]', $value, '100%', false, false, true, false, false, true));

    // Файлы
    $Tab_docs .= $PHPShopGUI->setCollapse('Файлы', $PHPShopGUI->loadLib('tab_files', $data));

    // Фотогалерея
    $Tab6 = $PHPShopGUI->loadLib('tab_img', $data);

    // Характеристики
    $Tab_sorts = $PHPShopGUI->loadLib('tab_sorts', $data);

    // Заголовки
    $Tab_header = $PHPShopGUI->loadLib('tab_headers', $data);

    // Запрос модуля на закладку
    $PHPShopModules->setAdmHandler(__FILE__, __FUNCTION__, $data);

    // Вывод формы закладки
    $PHPShopGUI->setTab(array("Основное", $Tab1, true, false, true), array("Изображение и видео", $Tab6, true, $PHPShopSystem->ifSerilizeParam("admoption.image_off", 1)), array("Описание", $Tab2 . $Tab3, true, false, true), array("Дополнительно", $Tab_docs . $Tab_rating . $Tab_header, true, false, true), array("Характеристики", $Tab_sorts, true), array("Подтипы", $Tab_option, true));

    // Вывод кнопок сохранить и выход в футер
    $ContentFooter = $PHPShopGUI->setInput("submit", "saveID", "ОК", "right", 70, "", "but", "actionInsert.catalog.create") .
            $PHPShopGUI->setInput("hidden", "rowID", $data['id']) . $PHPShopGUI->setInput("hidden", "tabName", null);

    // Футер
    $PHPShopGUI->setFooter($ContentFooter);
    return true;
}

/**
 * Копирование галереи товара
 */
function imgCopy($j, $n, $main) {

    $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['foto']);
    $data = $PHPShopOrm->select(array('*'), array('parent' => '=' . intval($j)), false, array('limit' => 100));
    if (is_array($data))
        foreach ($data as $row) {
            $pic_b = $row['name'];
            $name = $row['name'];
            $pic_s = str_replace(".", "s.", $name);
            $pic_big = str_replace(".", "_big.", $name);
            $num = $row['num'];
            $info = $row['info'];
            $myRName = substr(abs(crc32(uniqid($n))), 0, 5);

            if (file_exists($_SERVER['DOCUMENT_ROOT'] . $pic_s) and file_exists($_SERVER['DOCUMENT_ROOT'] . $pic_b)) {

                // Большая картинка
                $pathinfo = pathinfo($pic_b);
                $pic_b_ext = $pathinfo['extension'];
                $pic_b_name_new = "img" . $n . "_" . $myRName . "." . $pic_b_ext;
                $pic_b_name_old = $pathinfo['basename'];
                $pic_b_new = str_replace($pic_b_name_old, $pic_b_name_new, $pic_b);

                $oldWD = getcwd();
                $dirWhereRenameeIs = $_SERVER['DOCUMENT_ROOT'] . $pathinfo['dirname'];
                $oldFilename = $pathinfo['basename'];
                $newFilename = $pic_b_name_new;
                @chdir($dirWhereRenameeIs);
                @copy($oldFilename, $newFilename);
                @chdir($oldWD);

                // Маленькая иконка
                $pathinfo = pathinfo($pic_s);
                $pic_s_ext = $pathinfo['extension'];
                $pic_s_name_new = "img" . $n . "_" . $myRName . "s." . $pic_s_ext;
                $pic_s_name_old = $pathinfo['basename'];
                $pic_s_new = str_replace($pic_s_name_old, $pic_s_name_new, $pic_s);

                $oldFilename = $pathinfo['basename'];
                $newFilename = $pic_s_name_new;
                @chdir($dirWhereRenameeIs);
                @copy($oldFilename, $newFilename);
                @chdir($oldWD);

                // Исходник
                if (file_exists($_SERVER['DOCUMENT_ROOT'] . $pic_big)) {
                    $pathinfo = pathinfo($pic_big);
                    $pic_big_ext = $pathinfo['extension'];
                    $pic_big_name_new = "img" . $n . "_" . $myRName . "_big." . $pic_big_ext;
                    $pic_big_name_old = $pathinfo['basename'];
                    $pic_big_new = str_replace($pic_big_name_old, $pic_big_name_new, $pic_big);

                    $oldFilename = $pathinfo['basename'];
                    $newFilename = $pic_big_name_new;
                    @chdir($dirWhereRenameeIs);
                    @copy($oldFilename, $newFilename);
                    @chdir($oldWD);
                }

                $insert['parent_new'] = $n;
                $insert['name_new'] = $pic_b_new;
                $insert['num_new'] = $num;
                $insert['info_new'] = $info;

                // Проверка главного изображения
                if ($name == $main) {
                    $result['pic_big'] = $insert['name_new'];
                    $result['pic_small'] = str_replace(array('.png', '.jpg', '.gif', '.jpeg'), array('s.png', 's.jpg', 's.gif', 's.jpeg'), $insert['name_new']);
                }


                $PHPShopOrm->clean();
                $_SESSION['imgCopyID'][] = $PHPShopOrm->insert($insert);
            }
        }

    return $result;
}

/**
 * ID новой записи в таблице
 * @return integer
 */
function getLastID() {
    global $PHPShopBase;
    $PHPShopOrm = new PHPShopOrm();

    if (floatval(mysqli_get_server_info($PHPShopBase->link_db)) > 6) {

        $row = $PHPShopOrm->getOne(array('id'), false, array('order' => 'id desc'));
        $last = (int) $row['id'];
        $last++;
        return $last;
    } else {
        $PHPShopOrm->sql = 'SHOW TABLE STATUS LIKE "' . $GLOBALS['SysValue']['base']['products'] . '"';
        $data = $PHPShopOrm->select();
        if (is_array($data)) {
            return $data[0]['Auto_increment'];
        }
    }
}

/**
 * Экшен записи
 */
function actionInsert() {
    global $PHPShopModules, $PHPShopOrm, $PHPShopSystem;

    // Сумма по складам
    if ($PHPShopSystem->ifSerilizeParam('admoption.sklad_sum_enabled')) {
        $PHPShopOrmW = new PHPShopOrm($GLOBALS['SysValue']['base']['warehouses']);
        $data = $PHPShopOrmW->select(array('*'), false, array('order' => 'num DESC'), array('limit' => 100));
        if (is_array($data)) {
            $items = 0;
            foreach ($data as $row) {
                if (isset($_POST['items' . $row['id'] . '_new'])) {
                    $items += $_POST['items' . $row['id'] . '_new'];
                }
            }
        }

        if (!empty($items)) {
            $_POST['items_new'] = $items;
        }
    }

    $_POST['datas_new'] = time();
    $_POST['yml_bid_array_new'] = serialize($_POST['yml_bid_array']);

    // Добавление характеристик
    if (is_array($_POST['vendor_array_add'])) {
        foreach ($_POST['vendor_array_add'] as $k => $val) {

            $sort_array = $result = null;

            if (!empty($val)) {

                if (strstr($val, '#')) {
                    $sort_array = explode('#', $val);
                } else
                    $sort_array[] = $val;

                if (is_array($sort_array))
                    foreach ($sort_array as $val_sort) {

                        $PHPShopOrmSort = new PHPShopOrm($GLOBALS['SysValue']['base']['sort']);

                        // Проверка уникальности
                        $checkName = $PHPShopOrmSort->select(array('id'), array('name' => '="' . trim($val_sort) . '"', 'category' => '=' . intval($k)), false, array('limit' => 1));

                        // Нет характеристики, создаем новую
                        if (empty($checkName['id'])) {
                            $PHPShopOrmSort->clean();

                            $result = $PHPShopOrmSort->insert(array('name_new' => trim($val_sort), 'category_new' => intval($k)));
                            if (!empty($result))
                                $_POST['vendor_array_new'][$k][] = $result;
                        }
                        // Есть, назначем Из базы
                        else {
                            $_POST['vendor_array_new'][$k][] = $checkName['id'];
                        }
                    }
            } else
                unset($_POST['vendor_array_add'][$k]);
        }
    }

    // Характеристики
    $_POST['vendor_new'] = null;

    if (is_array($_POST['vendor_array_new'])) {

        foreach ($_POST['vendor_array_new'] as $k => $v) {
            if (is_array($v)) {
                $v = array_unique($v);
                $_POST['vendor_array_new'][$k] = $v;

                foreach ($v as $key => $p) {
                    $_POST['vendor_new'] .= "i" . $k . "-" . $p . "i";
                    if (empty($p))
                        unset($_POST['vendor_array_new'][$k][$key]);
                }
            } else
                $_POST['vendor_new'] .= "i" . $k . "-" . $v . "i";
        }
    }

    $_POST['vendor_array_new'] = serialize($_POST['vendor_array_new']);

    // Статьи
    if (is_array($_POST['page_new']))
        $_POST['page_new'] = array_pop($_POST['page_new']);

    // Файлы
    $_POST['files_new'] = serialize($_POST['files_new']);

    // Добавление изображения в фотогалерею
    $img = fotoAdd();
    if (is_array($img)) {
        $_POST['pic_big_new'] = $img['name_new'];
        $_POST['pic_small_new'] = str_replace('.', 's.', $img['name_new']);
    }

    // Доп каталоги
    $_POST['dop_cat_new'] = "";
    if (is_array($_POST['dop_cat']) and $_POST['dop_cat'][0] != 'null') {
        $_POST['dop_cat_new'] = "#";
        foreach ($_POST['dop_cat'] as $v)
            if ($v != 'null' and ! strstr($v, ','))
                $_POST['dop_cat_new'] .= $v . "#";
    }

    // Права пользователя
    $_POST['user_new'] = $_SESSION['idPHPSHOP'];

    // Конвертер цвета
    if (isset($_POST['parent2_new']) and empty($_POST['color_new']))
        $_POST['color_new'] = PHPShopString::getColor($_POST['parent2_new']);

    $postOdnotip = explode(',', $_POST['odnotip_new']);
    $odnotip = [];
    if (is_array($postOdnotip)) {
        foreach ($postOdnotip as $value) {
            if ((int) $value > 0) {
                $odnotip[] = (int) $value;

                // Связи однотипов
                if ($PHPShopSystem->ifSerilizeParam('admoption.odnotip')) {
                    $odnotip_data = $PHPShopOrm->getOne(['odnotip'], ['id' => '=' . (int) $value]);
                    $odnotip_array = explode(',', $odnotip_data['odnotip']);
                    if (is_array($odnotip_array)) {
                        if (!in_array($_POST['rowID'], $odnotip_array))
                            $odnotip_array[] = $_POST['rowID'];
                    } else
                        $odnotip_array[] = $_POST['rowID'];

                    $PHPShopOrm->update(['odnotip_new' => implode(',', $odnotip_array)], ['id' => '=' . (int) $value]);
                }
            }
        }
        $_POST['odnotip_new'] = implode(',', $odnotip);
    }

    if (empty($_POST['pic_small_new']) || empty($_POST['pic_big_new'])) {
        $orm = new PHPShopOrm($GLOBALS['SysValue']['base']['foto']);
        $photo = $orm->getOne(['name'], ['parent' => sprintf('="%s"', $_POST['rowID'])], ['order' => 'id asc']);
        if (empty($_POST['pic_big_new'])) {
            $_POST['pic_big_new'] = $photo['name'];
        }
        if (empty($_POST['pic_small_new'])) {
            $_POST['pic_small_new'] = str_replace(".", "s.", $photo['name']);
            if (!file_exists($_SERVER['DOCUMENT_ROOT'] . $_POST['pic_small_new'])) {
                $_POST['pic_small_new'] = $photo['name'];
            }
        }
    }

    if (isset($_POST['price_new'])) {
        $_POST['price_new'] = str_replace(',', '.', $_POST['price_new']);
    }
    if (isset($_POST['price_n_new'])) {
        $_POST['price_n_new'] = str_replace(',', '.', $_POST['price_n_new']);
    }
    if (isset($_POST['price2_new'])) {
        $_POST['price2_new'] = str_replace(',', '.', $_POST['price2_new']);
    }
    if (isset($_POST['price3_new'])) {
        $_POST['price3_new'] = str_replace(',', '.', $_POST['price3_new']);
    }
    if (isset($_POST['price4_new'])) {
        $_POST['price4_new'] = str_replace(',', '.', $_POST['price4_new']);
    }
    if (isset($_POST['price5_new'])) {
        $_POST['price5_new'] = str_replace(',', '.', $_POST['price5_new']);
    }

    // Перехват модуля
    $PHPShopModules->setAdmHandler(__FILE__, __FUNCTION__, $_POST);

    // Корректировка пустых значений
    $PHPShopOrm->updateZeroVars('newtip_new', 'enabled_new', 'spec_new', 'yml_new');
    $PHPShopOrm->debug = false;
    $action = $PHPShopOrm->insert($_POST);

    // Обновлние ID в фотогалереи нового товара
    if (!empty($_SESSION['imgCopyID']) and is_array($_SESSION['imgCopyID'])) {
        $PHPShopOrmImg = new PHPShopOrm($GLOBALS['SysValue']['base']['foto']);
        foreach ($_SESSION['imgCopyID'] as $imgID) {
            $PHPShopOrmImg->update(array('parent_new' => $action), array('id' => '=' . (int) $imgID));
        }
    }

    // Ajax для подтипов
    if (isset($_POST['ajax'])) {

        $PHPShopProduct = new PHPShopProduct($_POST['parent']);
        $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['products']);
        $parent_array = @explode(",", $PHPShopProduct->getParam('parent'));

        // Режим артикулов
        if ($PHPShopSystem->getSerilizeParam("1c_option.update_option") == 1) {
            $PHPShopOrm->update(array('uid_new' => 'parent-' . $action), array('id' => '=' . intval($action)));
            $parent_array_true[] = 'parent-' . $action;
        }
        // Режим ID
        else
            $parent_array_true[] = $action;

        if (is_array($parent_array))
            foreach ($parent_array as $v)
                if (!empty($v))
                    $parent_array_true[] = $v;

        $PHPShopOrm->update(array('parent_new' => @implode(",", $parent_array_true)), array('id' => '=' . intval($_POST['parent'])));

        return array("success" => $action);
    } else if ($_POST['saveID'] == 'Создать и редактировать') {

        // Изображение
        if ($_POST['tabName'] == 'Изображение')
            $tab = '&tab=1';
        else
            $tab = null;

        header('Location: ?path=product&return=catalog&id=' . $action . $tab);
    } else
        header('Location: ?path=catalog&cat=' . $_POST['category_new']);

    return $action;
}

// Добавление изображенияи видео в фотогалерею
function fotoAdd() {
    global $PHPShopSystem;
    require_once $_SERVER['DOCUMENT_ROOT'] . $GLOBALS['SysValue']['dir']['dir'] . '/phpshop/lib/thumb/phpthumb.php';

    // Параметры ресайзинга
    $img_tw = $PHPShopSystem->getSerilizeParam('admoption.img_tw');
    $img_th = $PHPShopSystem->getSerilizeParam('admoption.img_th');
    $img_w = $PHPShopSystem->getSerilizeParam('admoption.img_w');
    $img_h = $PHPShopSystem->getSerilizeParam('admoption.img_h');
    $img_tw = empty($img_tw) ? 150 : $img_tw;
    $img_th = empty($img_th) ? 150 : $img_th;
    $img_w = empty($img_w) ? 300 : $img_w;
    $img_h = empty($img_h) ? 300 : $img_h;

    $img_adaptive = $PHPShopSystem->getSerilizeParam('admoption.image_adaptive_resize');
    $image_save_source = $PHPShopSystem->getSerilizeParam('admoption.image_save_source');
    $width_kratko = $PHPShopSystem->getSerilizeParam('admoption.width_kratko');
    $width_podrobno = $PHPShopSystem->getSerilizeParam('admoption.width_podrobno');

    // Папка сохранения
    $path = $GLOBALS['SysValue']['dir']['dir'] . '/UserFiles/Image/' . $PHPShopSystem->getSerilizeParam('admoption.image_result_path');

    // Сохранять в папки каталогов
    if ($PHPShopSystem->ifSerilizeParam('admoption.image_save_catalog')) {

        $PHPShopCategory = new PHPShopCategory($_POST['category_new']);
        $parent_to = $PHPShopCategory->getParam('parent_to');
        $pathName = ucfirst(PHPShopString::toLatin($PHPShopCategory->getName()));

        if (!empty($parent_to)) {
            $PHPShopCategory = new PHPShopCategory($parent_to);
            $pathName = ucfirst(PHPShopString::toLatin($PHPShopCategory->getName())) . '/' . $pathName;
            $parent_to = $PHPShopCategory->getParam('parent_to');
        }

        if (!empty($parent_to)) {
            $PHPShopCategory = new PHPShopCategory($parent_to);
            $pathName = '/' . ucfirst(PHPShopString::toLatin($PHPShopCategory->getName())) . '/' . $pathName;
        }

        $path .= $pathName . '/';

        if (!is_dir($_SERVER['DOCUMENT_ROOT'] . $path))
            @mkdir($_SERVER['DOCUMENT_ROOT'] . $path, 0777, true);
    }

    // Корекция
    $path = str_replace('//', '/', $path);

    // Соль
    $RName = substr(abs(crc32(time())), 0, 5);

    // Копируем от пользователя
    if (!empty($_FILES['file']['name'])) {
        $_FILES['file']['ext'] = PHPShopSecurity::getExt($_FILES['file']['name']);
        $_FILES['file']['name'] = PHPShopString::toLatin(str_replace('.' . $_FILES['file']['ext'], '', PHPShopString::utf8_win1251($_FILES['file']['name']))) . '.' . $_FILES['file']['ext'];
        if (in_array($_FILES['file']['ext'], array('gif', 'png', 'jpg', 'jpeg', 'webp','mp4','mov'))) {
            if (move_uploaded_file($_FILES['file']['tmp_name'], $_SERVER['DOCUMENT_ROOT'] . $path . $_FILES['file']['name'])) {
                $file = $_SERVER['DOCUMENT_ROOT'] . $path . $_FILES['file']['name'];
                $file_name = $_FILES['file']['name'];
                $path_parts = pathinfo($file);
                $tmp_file = $_SERVER['DOCUMENT_ROOT'] . $path . $_FILES['file']['name'];
            }
        }
    }

    // Читаем файл из URL
    elseif (!empty($_POST['furl'])) {
        $file = $_POST['img_new'];
        $path_parts = pathinfo($file);
        $file_name = $path_parts['basename'];
    }

    // Читаем файл из файлового менеджера
    elseif (!empty($_POST['img_new'])) {
        $file = $_SERVER['DOCUMENT_ROOT'] . $GLOBALS['dir']['dir'] . $_POST['img_new'];
        $path_parts = pathinfo($file);
        $file_name = $path_parts['basename'];

        // Сохранение пути файла
        if ($PHPShopSystem->ifSerilizeParam('admoption.image_save_path'))
            $path = $GLOBALS['SysValue']['dir']['dir'] . str_replace($_SERVER['DOCUMENT_ROOT'], '', $path_parts['dirname']) . '/';
    }

    if (!empty($file)) {

        // Видео
        if (in_array($path_parts['extension'], array('mp4','mov'))) {
            $name = 'vid' . $_POST['rowID'] . '_' . $RName . '.' . strtolower($path_parts['extension']);
            $name_s = '';
            @copy($file, $_SERVER['DOCUMENT_ROOT'] . $path . $name);
        } 
        // Изображение
        else {

            // Маленькое изображение (тумбнейл)
            $thumb = new PHPThumb($file);
            $thumb->setOptions(array('jpegQuality' => $width_kratko));

            // Адаптивность
            if (!empty($img_adaptive))
                $thumb->adaptiveResize($img_tw, $img_th);
            else
                $thumb->resize($img_tw, $img_th);

            $watermark = $PHPShopSystem->getSerilizeParam('admoption.watermark_image');
            $watermark_text = $PHPShopSystem->getSerilizeParam('admoption.watermark_text');

            // Исходное название
            if ($PHPShopSystem->ifSerilizeParam('admoption.image_save_name')) {
                $name_s = $path_parts['filename'] . 's.' . strtolower($thumb->getFormat());
                $name = $path_parts['filename'] . '.' . strtolower($thumb->getFormat());
                $name_big = $path_parts['filename'] . '_big.' . strtolower($thumb->getFormat());

                if (!empty($image_save_source)) {
                    $file_big = $_SERVER['DOCUMENT_ROOT'] . $path . $name_big;
                    @copy($file, $file_big);
                }
            }
            // SEO название
            elseif ($PHPShopSystem->ifSerilizeParam('admoption.image_save_seo')) {

                if (!empty($_POST['prod_seo_name'])) {
                    $seo_name = $_POST['prod_seo_name'];
                } else {
                    PHPShopObj::loadClass("string");
                    $seo_name = str_replace(array("_", "+", '&#43;'), array("-", "", ""), PHPShopString::toLatin($_POST['name_new']));
                }
                $name_s = $seo_name . '-' . $_POST['rowID'] . '-' . $RName . 's.' . strtolower($thumb->getFormat());
                $name = $seo_name . '-' . $_POST['rowID'] . '-' . $RName . '.' . strtolower($thumb->getFormat());
                $name_big = $seo_name . '-' . $_POST['rowID'] . '-' . $RName . '_big.' . strtolower($thumb->getFormat());
            } else {
                $name_s = 'img' . $_POST['rowID'] . '_' . $RName . 's.' . strtolower($thumb->getFormat());
                $name = 'img' . $_POST['rowID'] . '_' . $RName . '.' . strtolower($thumb->getFormat());
                $name_big = 'img' . $_POST['rowID'] . '_' . $RName . '_big.' . strtolower($thumb->getFormat());
            }


            // Ватермарк тубнейла
            if ($PHPShopSystem->ifSerilizeParam('admoption.watermark_small_enabled')) {

                // Image
                if (!empty($watermark) and file_exists($_SERVER['DOCUMENT_ROOT'] . $watermark))
                    $thumb->createWatermark($_SERVER['DOCUMENT_ROOT'] . $watermark, $PHPShopSystem->getSerilizeParam('admoption.watermark_right'), $PHPShopSystem->getSerilizeParam('admoption.watermark_bottom'), $PHPShopSystem->getSerilizeParam('admoption.watermark_center_enabled'));
                // Text
                elseif (!empty($watermark_text))
                    $thumb->createWatermarkText($watermark_text, $PHPShopSystem->getSerilizeParam('admoption.watermark_text_size'), $_SERVER['DOCUMENT_ROOT'] . $GLOBALS['SysValue']['dir']['dir'] . '/phpshop/lib/font/' . $PHPShopSystem->getSerilizeParam('admoption.watermark_text_font') . '.ttf', $PHPShopSystem->getSerilizeParam('admoption.watermark_right'), $PHPShopSystem->getSerilizeParam('admoption.watermark_bottom'), $PHPShopSystem->getSerilizeParam('admoption.watermark_text_color'), $PHPShopSystem->getSerilizeParam('admoption.watermark_text_alpha'), 0, $PHPShopSystem->getSerilizeParam('admoption.watermark_center_enabled'));
            }

            // Сохранение в webp
            if ($PHPShopSystem->ifSerilizeParam('admoption.image_webp_save')) {
                $thumb->setFormat('WEBP');
                $name_s = str_replace([".png", ".jpg", ".jpeg", ".gif", ".PNG", ".JPG", ".JPEG", ".GIF"], '.webp', $name_s);
            }

            $thumb->save($_SERVER['DOCUMENT_ROOT'] . $path . $name_s);

            // Большое изображение
            $thumb = new PHPThumb($file);
            $thumb->setOptions(array('jpegQuality' => $width_podrobno));

            // Адаптивность
            if (!empty($img_adaptive))
                $thumb->adaptiveResize($img_w, $img_h);
            else
                $thumb->resize($img_w, $img_h);

            // Ватермарк большого изображения
            if ($PHPShopSystem->ifSerilizeParam('admoption.watermark_big_enabled')) {

                // Image
                if (!empty($watermark) and file_exists($_SERVER['DOCUMENT_ROOT'] . $watermark))
                    $thumb->createWatermark($_SERVER['DOCUMENT_ROOT'] . $watermark, $PHPShopSystem->getSerilizeParam('admoption.watermark_right'), $PHPShopSystem->getSerilizeParam('admoption.watermark_bottom'), $PHPShopSystem->getSerilizeParam('admoption.watermark_center_enabled'));
                // Text
                elseif (!empty($watermark_text))
                    $thumb->createWatermarkText($watermark_text, $PHPShopSystem->getSerilizeParam('admoption.watermark_text_size'), $_SERVER['DOCUMENT_ROOT'] . $GLOBALS['SysValue']['dir']['dir'] . '/phpshop/lib/font/' . $PHPShopSystem->getSerilizeParam('admoption.watermark_text_font') . '.ttf', $PHPShopSystem->getSerilizeParam('admoption.watermark_right'), $PHPShopSystem->getSerilizeParam('admoption.watermark_bottom'), $PHPShopSystem->getSerilizeParam('admoption.watermark_text_color'), $PHPShopSystem->getSerilizeParam('admoption.watermark_text_alpha'), 0, $PHPShopSystem->getSerilizeParam('admoption.watermark_center_enabled'));
            }

            // Сохранение в webp
            if ($PHPShopSystem->ifSerilizeParam('admoption.image_webp_save')) {
                $thumb->setFormat('WEBP');
                $name = str_replace(['.jpg', '.JPG', '.png', '.PNG', '.gif', '.GIF'], '.webp', $name);
            }

            $thumb->save($_SERVER['DOCUMENT_ROOT'] . $path . $name);

            // Исходное изображение
            if (!empty($image_save_source)) {

                // Сохранение в webp
                if ($PHPShopSystem->ifSerilizeParam('admoption.image_webp_save')) {
                    $thumb->setFormat('WEBP');
                    $name_big = str_replace(['.jpg', '.JPG', '.png', '.PNG', '.gif', '.GIF'], '.webp', $name_big);
                }

                if (!$PHPShopSystem->ifSerilizeParam('admoption.image_save_name')) {
                    $file_big = $_SERVER['DOCUMENT_ROOT'] . $path . $name_big;
                    @copy($file, $file_big);
                }

                // Ватермарк
                if ($PHPShopSystem->ifSerilizeParam('admoption.watermark_source_enabled')) {

                    $thumb = new PHPThumb($file_big);
                    $thumb->setOptions(array('jpegQuality' => $width_podrobno));
                    $thumb->setWorkingImage($thumb->getOldImage());

                    // Image
                    if (!empty($watermark) and file_exists($_SERVER['DOCUMENT_ROOT'] . $watermark))
                        $thumb->createWatermark($_SERVER['DOCUMENT_ROOT'] . $watermark, $PHPShopSystem->getSerilizeParam('admoption.watermark_right'), $PHPShopSystem->getSerilizeParam('admoption.watermark_bottom'), $PHPShopSystem->getSerilizeParam('admoption.watermark_center_enabled'));
                    // Text
                    elseif (!empty($watermark_text))
                        $thumb->createWatermarkText($watermark_text, $PHPShopSystem->getSerilizeParam('admoption.watermark_text_size'), $_SERVER['DOCUMENT_ROOT'] . $GLOBALS['SysValue']['dir']['dir'] . '/phpshop/lib/font/' . $PHPShopSystem->getSerilizeParam('admoption.watermark_text_font') . '.ttf', $PHPShopSystem->getSerilizeParam('admoption.watermark_right'), $PHPShopSystem->getSerilizeParam('admoption.watermark_bottom'), $PHPShopSystem->getSerilizeParam('admoption.watermark_text_color'), $PHPShopSystem->getSerilizeParam('admoption.watermark_text_alpha'), 0, $PHPShopSystem->getSerilizeParam('admoption.watermark_center_enabled'));

                    $thumb->save($file_big);
                }
            }
        }

        if (!$PHPShopSystem->ifSerilizeParam('admoption.image_save_name') and ! empty($tmp_file))
            unlink($tmp_file);

        // Добавление в таблицу фотогалереи
        $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['foto']);
        $insert['parent_new'] = $_POST['rowID'];
        $insert['name_new'] = $path . $name;
        $insert['pic_small_new'] = $path . $name_s;
        $PHPShopOrm->insert($insert);
        return $insert;
    }
}

// Обработка событий
$PHPShopGUI->getAction();

// Вывод формы при старте
$PHPShopGUI->setLoader($_POST['editID'], 'actionStart');
?>