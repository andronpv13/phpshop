<?php

PHPShopObj::loadClass("page");

$TitlePage = __('Редактирование страницы') . ' #' . $_GET['id'];
$PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['page']);

// Построение дерева категорий
function treegenerator($array, $i, $curent) {
    global $tree_array;
    $del = '¦&nbsp;&nbsp;&nbsp;&nbsp;';
    $tree_select = $check = false;

    $del = str_repeat($del, $i);
    if (!empty($array) and is_array($array['sub'])) {
        foreach ($array['sub'] as $k => $v) {

            $check = treegenerator(@$tree_array[$k], $i + 1, $curent);

            if ($k == $curent)
                $selected = 'selected';
            else
                $selected = null;

            if (empty($check['select'])) {
                $tree_select .= '<option value="' . $k . '" ' . $selected . '>' . $del . $v . '</option>';
                $i = 1;
            } else {
                $tree_select .= '<option value="' . $k . '" ' . $selected . '>' . $del . $v . '</option>';
            }

            $tree_select .= $check['select'];
        }
    }
    return array('select' => $tree_select);
}

function actionStart() {
    global $PHPShopGUI, $PHPShopSystem, $PHPShopModules, $PHPShopOrm, $hideSite;

    // Выборка
    $data = $PHPShopOrm->select(array('*'), array('id' => '=' . intval($_GET['id'])));

    // Нет данных
    if (!is_array($data)) {
        header('Location: ?path=' . $_GET['return']);
    }

    $PHPShopGUI->action_select['Предпросмотр'] = array(
        'name' => 'Предпросмотр',
        'url' => '../../page/' . $data['link'] . '.html',
        'action' => 'front',
        'target' => '_blank',
        'class' => $GLOBALS['isFrame']
    );

    // Имя
    if (strlen($data['name']) > 77)
        $title_name = substr($data['name'], 0, 77) . '...';
    else
        $title_name = $data['name'];

    $PHPShopGUI->field_col = 3;
    $PHPShopGUI->setActionPanel(__("Страница") . ': ' . $title_name, array('Создать', 'Предпросмотр', '|', 'Удалить'), array('Сохранить', 'Сохранить и закрыть'), false);
    $PHPShopGUI->addJSFiles('./js/jquery.tagsinput.min.js', './js/bootstrap-datetimepicker.min.js', './js/jquery.waypoints.min.js', './page/gui/page.gui.js');
    $PHPShopGUI->addCSSFiles('./css/jquery.tagsinput.css', './css/bootstrap-datetimepicker.min.css');

    $PHPShopCategoryArray = new PHPShopPageCategoryArray();
    $CategoryArray = $PHPShopCategoryArray->getArray();

    $tree_array = array();

    $PHPShopCategoryArrayKey = $PHPShopCategoryArray->getKey('parent_to.id', true);
    if (is_array($PHPShopCategoryArrayKey))
        foreach ($PHPShopCategoryArrayKey as $k => $v) {
            foreach ($v as $cat) {
                $tree_array[$k]['sub'][$cat] = $CategoryArray[$cat]['name'];
            }
            $tree_array[$k]['name'] = @$CategoryArray[$k]['name'];
            $tree_array[$k]['id'] = $k;
        }

    $GLOBALS['tree_array'] = &$tree_array;

    $tree_select = '<select class="selectpicker show-menu-arrow hidden-edit" data-container="body"  data-style="btn btn-default btn-sm" name="category_new" data-width="100%">';

    $tree_array[0]['sub'][1000] = __('Главное меню сайта');
    $tree_array[0]['sub'][2000] = __('Начальная страница');

    $tree_select .= '<option value="0" ' . $data['category'] . ' data-subtext="<span class=\'glyphicon glyphicon-cog\'></span> ' . __('Настройка') . '">' . __('Внутренняя страница') . '</option>';
    if (!empty($tree_array) and is_array($tree_array[0]['sub']))
        foreach ($tree_array[0]['sub'] as $k => $v) {
            $check = treegenerator(@$tree_array[$k], 1, $data['category']);

            if ($k == $data['category'])
                $selected = 'selected';
            else
                $selected = null;

            if (in_array($k, array(1000, 2000)))
                $subtext = 'data-subtext="<span class=\'glyphicon glyphicon-cog\'></span> ' . __('Настройка') . '"';
            else
                $subtext = null;

            $tree_select .= '<option value="' . $k . '" ' . $selected . ' ' . $subtext . '>' . $v . '</option>';

            $tree_select .= $check['select'];
        }
    $tree_select .= '</select>';

    // Редактор 1
    $PHPShopGUI->setEditor($PHPShopSystem->getSerilizeParam("admoption.editor"));
    $oFCKeditor = new Editor('content_new');
    $oFCKeditor->Height = '700';
    $oFCKeditor->Value = $data['content'];

    $SelectValue[] = array('Вывод в каталоге', 1, $data['enabled']);
    $SelectValue[] = array('Заблокировать', 0, $data['enabled']);

    $Tab_description = $PHPShopGUI->setCollapse('Заголовок', $PHPShopGUI->setInput("text", "name_new", $data['name']));

    // Содержание закладки 1
    $Tab_info = $PHPShopGUI->setField("Каталог", $tree_select) .
            $PHPShopGUI->setField("Сортировка", $PHPShopGUI->setInputText("№", "num_new", $data['num'], 150)) .
            $PHPShopGUI->setField("URL Ссылка", $PHPShopGUI->setInputText('/page/', "link_new", $data['link'], '100%', '.html', false, false, 'my-link', false, true)) .
            $PHPShopGUI->setField("Опции вывода:", $PHPShopGUI->setSelect("enabled_new", $SelectValue, 300, true)
    );

    if ($data['category'] != 2000)
        $Tab_info .= $PHPShopGUI->setField("Подвал", $PHPShopGUI->setCheckbox('footer_new', 1, 'Главное меню в подвале', $data['footer']));

    $Tab_info = $PHPShopGUI->setCollapse('Информация', $Tab_info);

    // Содержание закладки 3
    if ($data['category'] != 2000) {
        $Tab3 = $PHPShopGUI->setField("Title", $PHPShopGUI->setTextarea("title_new", $data['title']) . $PHPShopGUI->setAIHelpButton('title_new', 70, 'page_title'));
        $Tab3 .= $PHPShopGUI->setField("Description", $PHPShopGUI->setTextarea("description_new", $data['description']) . $PHPShopGUI->setAIHelpButton('description_new', 80, 'page_descrip'));
        $Tab3 .= $PHPShopGUI->setField("Keywords", $PHPShopGUI->setTextarea("keywords_new", $data['keywords']));
        $Tab_seo = $PHPShopGUI->setCollapse('SEO / Мета-данные', $Tab3);

        // Безопасность
        $SecurityValue[] = array('Всем пользователям', 0, $data['secure']);
        $SecurityValue[] = array('Только зарегистрированным пользователям', 1, $data['secure']);
        $TabSec = $PHPShopGUI->setField("Показывать", $PHPShopGUI->setSelect("secure_new", $SecurityValue, 300, true));
    } else
        $TabSec = null;

    // Иконка
    if (!empty($data['category']) and $data['category'] != 2000 and $data['category'] != 1000)
        $Tab_dop = $PHPShopGUI->setField("Изображение", $PHPShopGUI->setIcon($data['icon'], "icon_new", false, ['load' => true, 'server' => true, 'url' => true, 'multi' => false, 'search' => true]));


    // Рекомендуемые товары
    if ($data['category'] != 2000) {

        // Дата
        $Tab_dop .= $PHPShopGUI->setField("Дата", $PHPShopGUI->setInputDate("datas_new", PHPShopDate::get($data['datas'])));

        if (empty($hideSite))
            $Tab_dop .= $PHPShopGUI->setField('Рекомендуемые товары для совместной продажи', $PHPShopGUI->setTextarea('odnotip_new', $data['odnotip'], false, false, false, __('Укажите ID товаров или воспользуйтесь') . ' <a href="#" data-target="#odnotip_new"  class="btn btn-sm btn-default tag-search"><span class="glyphicon glyphicon-search"></span> ' . __('поиском товаров') . '</a>'));

        // Анонс
        $oFCKeditor2 = new Editor('preview_new');
        $oFCKeditor2->Height = '340';
        $oFCKeditor2->Value = $data['preview'];
    }

    if ($data['category'] != 2000)
        $Tab_dop = $PHPShopGUI->setCollapse('Дополнительно', $Tab_dop);

    $Tab_sec = $PHPShopGUI->setCollapse('Доступность', $TabSec . $PHPShopGUI->setField("Витрины", $PHPShopGUI->loadLib('tab_multibase', $data, 'catalog/')));

    // Запрос модуля на закладку
    $PHPShopModules->setAdmHandler(__FILE__, __FUNCTION__, $data);

    // Вывод формы закладки
    if (!empty($data['category']) and $data['category'] != 2000 and $data['category'] != 1000) {
        $Tab_content = $PHPShopGUI->setCollapse("Анонс", $oFCKeditor2->AddGUI() . $PHPShopGUI->setAIHelpButton('preview_new', 100, 'page_description'));
        $Tab_description .= $PHPShopGUI->setCollapse("Содержание", $oFCKeditor->AddGUI() . $PHPShopGUI->setAIHelpButton('content_new', 300, 'page_content'));
        $PHPShopGUI->setTab(array("Основное", $Tab_description . $Tab_info . $Tab_seo . $Tab_content . $Tab_dop . $Tab_sec, true, false, true));
    } else {

        $Tab_description .= $PHPShopGUI->setCollapse("Содержание", $oFCKeditor->AddGUI() . $PHPShopGUI->setAIHelpButton('content_new', 300, 'page_content'));

        if ($data['category'] == 2000)
            $grid = false;
        else
            $grid = true;

        $PHPShopGUI->setTab(array("Основное", $Tab_description . $Tab_info . $Tab_seo . $Tab_dop . $Tab_sec, true, false, true));
    }


    // Вывод кнопок сохранить и выход в футер
    $ContentFooter = $PHPShopGUI->setInput("hidden", "rowID", $data['id'], "right", 70, "", "but") .
            $PHPShopGUI->setInput("button", "delID", "Удалить", "right", 70, "", "but", "actionDelete.page.edit") .
            $PHPShopGUI->setInput("submit", "editID", "Сохранить", "right", 70, "", "but", "actionUpdate.page.edit") .
            $PHPShopGUI->setInput("submit", "saveID", "Применить", "right", 80, "", "but", "actionSave.page.edit");

    // Футер
    $PHPShopGUI->setFooter($ContentFooter);
    return true;
}

// Функция сохранения
function actionUpdate() {
    global $PHPShopModules, $PHPShopOrm;

    if (!empty($_POST['datas_new']))
        $_POST['datas_new'] = PHPShopDate::GetUnixTime($_POST['datas_new']);
    else
        $_POST['datas_new'] = PHPShopDate::GetUnixTime(time());

    $PHPShopOrm->debug = false;

    // Корректировка пустых значений
    $PHPShopOrm->updateZeroVars('enabled_new', 'secure_new', 'footer_new');

    // Мультибаза
    if (is_array($_POST['servers'])) {
        $_POST['servers_new'] = "";
        foreach ($_POST['servers'] as $v)
            if ($v != 'null' and ! strstr($v, ',') and $v != null and ! strstr($_POST['servers_new'], "i" . $v . "i"))
                $_POST['servers_new'] .= "i" . $v . "i";
    }

    $_POST['icon_new'] = iconAdd();

    if (!empty($_POST['odnotip_new']))
        $postOdnotip = explode(',', $_POST['odnotip_new']);
    $odnotip = [];
    if (!empty($postOdnotip) and is_array($postOdnotip)) {
        foreach ($postOdnotip as $value) {
            if ((int) $value > 0) {
                $odnotip[] = (int) $value;
            }
        }
        $_POST['odnotip_new'] = implode(',', $odnotip);
    }

    if (empty($_POST['link_new']))
        $_POST['link_new'] = 'page' . $_POST['rowID'];

    // Перехват модуля
    $PHPShopModules->setAdmHandler(__FILE__, __FUNCTION__, $_POST);

    $action = $PHPShopOrm->update($_POST, array('id' => '=' . $_POST['rowID']));

    return array("success" => $action);
}

/**
 * Экшен сохранения
 */
function actionSave() {

    // Сохранение данных
    actionUpdate();

    header('Location: ?path=page.catalog&cat=' . $_POST['category_new']);
}

// Функция удаления
function actionDelete() {
    global $PHPShopOrm, $PHPShopModules;

    // Перехват модуля
    $PHPShopModules->setAdmHandler(__FILE__, __FUNCTION__, $_POST);
    $action = $PHPShopOrm->delete(array('id' => '=' . $_POST['rowID']));
    return array("success" => $action);
}

// Добавление изображения 
function iconAdd() {
    global $PHPShopSystem;

    // Папка сохранения
    $path = $GLOBALS['SysValue']['dir']['dir'] . '/UserFiles/Image/' . $PHPShopSystem->getSerilizeParam('admoption.image_result_path');

    // Копируем от пользователя
    if (!empty($_FILES['file']['name'])) {
        $_FILES['file']['ext'] = PHPShopSecurity::getExt($_FILES['file']['name']);
        if (in_array($_FILES['file']['ext'], array('gif', 'png', 'jpg', 'jpeg', 'svg'))) {
            if (move_uploaded_file($_FILES['file']['tmp_name'], $_SERVER['DOCUMENT_ROOT'] . $GLOBALS['dir']['dir'] . $path . $_FILES['file']['name'])) {
                $file = $GLOBALS['dir']['dir'] . $path . $_FILES['file']['name'];
            }
        }
    }

    // Копируем файл из URL
    elseif (!empty($_POST['furl'])) {
        $file = $_POST['icon_new'];
        $path_parts = pathinfo($file);
        $file_name = $path_parts['basename'];
        $file_ext = PHPShopSecurity::getExt($file_name);
        $file_name = PHPShopString::toLatin(str_replace('.' . $file_ext, '', PHPShopString::utf8_win1251($file_name))) . '.' . $file_ext;

        if (in_array($file_ext, array('gif', 'png', 'jpg', 'jpeg', 'svg', 'webp'))) {
            if (copy($file, $_SERVER['DOCUMENT_ROOT'] . $GLOBALS['dir']['dir'] . $path . $file_name)) {
                $file = $GLOBALS['dir']['dir'] . $path . $file_name;
            }
        }
    }

    // Читаем файл из файлового менеджера
    elseif (!empty($_POST['icon_new'])) {
        $file = $_POST['icon_new'];
    }

    if (empty($file))
        $file = '';

    return $file;
}

// Обработка событий
$PHPShopGUI->getAction();

// Вывод формы при старте
$PHPShopGUI->setAction($_GET['id'], 'actionStart', 'none');
?>
