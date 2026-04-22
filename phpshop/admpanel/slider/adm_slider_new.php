<?php

$TitlePage = __('Создание слайдера');
$PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['slider']);

// Стартовый вид
function actionStart() {
    global $PHPShopGUI, $PHPShopModules, $TitlePage;

    $PHPShopGUI->setActionPanel($TitlePage, false, array('Сохранить и закрыть'));
    $PHPShopGUI->field_col = 3;

    $data['enabled'] = 1;
    $data = $PHPShopGUI->valid($data, 'image', 'link', 'mobile', 'num', 'alt', 'servers', 'link_text', 'name');

    // Содержание закладки 1
    $Tab1 = $PHPShopGUI->setField("Название", $PHPShopGUI->setInput("text", "name_new", $data['name'], null, 500));

    // Цвет
    $Tab1 .= $PHPShopGUI->setField("Инверсия цвета", $PHPShopGUI->setInputText(null, "color_new", (int) $data['color'], 100, '%'));

    $Tab1 .= $PHPShopGUI->setField("Изображение", $PHPShopGUI->setIcon($data['image'], "image_new", false, array('load' => true, 'server' => true, 'url' => false, 'multi' => false, 'view' => false))) .
            $PHPShopGUI->setField("Цель", $PHPShopGUI->setInput("text", "link_new", $data['link'], "none", 500) . $PHPShopGUI->setHelp("Пример: /pages/info.html или http://google.com")) .
            $PHPShopGUI->setField("Текст ссылки", $PHPShopGUI->setInput("text", "link_text_new", $data['link_text'], null, 500)) .
            $PHPShopGUI->setField("Статус", $PHPShopGUI->setRadio("enabled_new", 1, "Включить", $data['enabled']) . $PHPShopGUI->setRadio("enabled_new", 0, "Выключить", $data['enabled'])) .
            $PHPShopGUI->setField("Мобильный", $PHPShopGUI->setCheckbox("mobile_new", 1, "Отображать только на мобильных устройствах", $data['mobile'])) .
            $PHPShopGUI->setField("Приоритет", $PHPShopGUI->setInputText(false, 'num_new', $data['num'], 100)) .
            $PHPShopGUI->setField("Описание", $PHPShopGUI->setTextarea("alt_new", $data['alt'], true, 500)) .
            $PHPShopGUI->setField("Витрины", $PHPShopGUI->loadLib('tab_multibase', $data, 'catalog/'));

    $Tab1 = $PHPShopGUI->setCollapse('Информация', $Tab1);

    // Запрос модуля на закладку
    $PHPShopModules->setAdmHandler(__FILE__, __FUNCTION__, null);

    // Вывод формы закладки
    $PHPShopGUI->setTab(array("Основное", $Tab1, true));

    // Вывод кнопок сохранить и выход в футер
    $ContentFooter = $PHPShopGUI->setInput("submit", "saveID", "ОК", "right", 70, "", "but", "actionInsert.slider.create");

    // Футер
    $PHPShopGUI->setFooter($ContentFooter);
    return true;
}

// Функция записи
function actionInsert() {
    global $PHPShopOrm, $PHPShopModules;

    $_POST['image_new'] = iconAdd();

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
    header('Location: ?path=' . $_GET['path']);
    return $action;
}

// Добавление изображения 
function iconAdd() {
    global $PHPShopSystem;

    // Папка сохранения
    $path = $GLOBALS['SysValue']['dir']['dir'] . '/UserFiles/Image/' . $PHPShopSystem->getSerilizeParam('admoption.image_result_path');

    // Копируем от пользователя
    if (!empty($_FILES['file']['name'])) {
        $_FILES['file']['ext'] = PHPShopSecurity::getExt($_FILES['file']['name']);
        $_FILES['file']['name'] = PHPShopString::toLatin(str_replace('.' . $_FILES['file']['ext'], '', $_FILES['file']['name'])) . '.' . $_FILES['file']['ext'];
        if (in_array($_FILES['file']['ext'], array('gif', 'png', 'jpg', 'jpeg', 'svg'))) {
            if (move_uploaded_file($_FILES['file']['tmp_name'], $_SERVER['DOCUMENT_ROOT'] . $GLOBALS['dir']['dir'] . $path . $_FILES['file']['name'])) {
                $file = $GLOBALS['dir']['dir'] . $path . $_FILES['file']['name'];
            }
        }
    }

    // Читаем файл из URL
    elseif (!empty($_POST['furl'])) {
        $file = $_POST['image_new'];
    }

    // Читаем файл из файлового менеджера
    elseif (!empty($_POST['image_new'])) {
        $file = $_POST['image_new'];
    }

    if (empty($file))
        $file = '';

    // Нарезка
    if ($PHPShopSystem->ifSerilizeParam('admoption.image_slider') and ! empty($file)) {
        require_once $_SERVER['DOCUMENT_ROOT'] . $GLOBALS['SysValue']['dir']['dir'] . '/phpshop/lib/thumb/phpthumb.php';

        // Параметры ресайзинга
        $img_tw = $PHPShopSystem->getSerilizeParam('admoption.img_tw_s');
        $img_th = $PHPShopSystem->getSerilizeParam('admoption.img_th_s');
        $img_tw = empty($img_tw) ? 1440 : $img_tw;
        $img_th = empty($img_th) ? 300 : $img_th;
        $img_adaptive = $PHPShopSystem->getSerilizeParam('admoption.image_slider_adaptive');

        // Маленькое изображение (тумбнейл)
        $thumb = new PHPThumb($_SERVER['DOCUMENT_ROOT'] . $file);
        $thumb->setOptions(array('jpegQuality' => $PHPShopSystem->getSerilizeParam('admoption.width_kratko')));

        // Адаптивность
        if (!empty($img_adaptive))
            $thumb->adaptiveResize($img_tw, $img_th);
        else
            $thumb->resize($img_tw, $img_th);

        // Сохранение в webp
        if ($PHPShopSystem->ifSerilizeParam('admoption.image_webp_save')) {
            $thumb->setFormat('WEBP');
            $file = str_replace(['.jpg', '.JPG', '.png', '.PNG', '.gif', '.GIF'], '.webp', $file);
        }

        $thumb->save($_SERVER['DOCUMENT_ROOT'] . $file);
    }

    return $file;
}

// Обработка событий
$PHPShopGUI->getAction();

// Вывод формы при старте
$PHPShopGUI->setLoader($_POST['saveID'], 'actionStart');
?>