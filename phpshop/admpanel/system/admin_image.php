<?php

$TitlePage = __("Настройка изображений");
$PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['system']);

// Выбор шрифта ватермарка
function GetFonts($font) {
    global $PHPShopGUI;

    $dir = "../lib/font/";
    if (is_dir($dir)) {
        if (@$dh = opendir($dir)) {
            while (($file = readdir($dh)) !== false) {

                if (preg_match("/([a-zA-Z0-9_]{1,30}).ttf$/", $file, $match)) {

                    $file = str_replace(array('.ttf'), '', $file);

                    if ($font == $file)
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

    return $PHPShopGUI->setSelect('option[watermark_text_font]', $value);
}

// Стартовый вид
function actionStart() {
    global $PHPShopGUI, $PHPShopModules, $TitlePage, $PHPShopOrm, $hideCatalog, $hideSite;

// Выборка
    $data = $PHPShopOrm->select();
    $option = unserialize($data['admoption']);

// Размер названия поля
    $PHPShopGUI->field_col = 3;

    // bootstrap-colorpicker
    $PHPShopGUI->addCSSFiles('./css/bootstrap-colorpicker.min.css');
    $PHPShopGUI->addJSFiles('./js/bootstrap-colorpicker.min.js', './js/jquery.waypoints.min.js', './system/gui/system.gui.js');

    $PHPShopGUI->setActionPanel($TitlePage, false, array('Сохранить'));

    if (!function_exists('imagewebp')) {
        $webp_disabled = 1;
    } else
        $webp_disabled = 0;


    if (empty($hideSite))
        $PHPShopGUI->_CODE = $PHPShopGUI->setField('Макс. ширина оригинала', $PHPShopGUI->setInputText(false, 'option[img_w]', $option['img_w'], 100, 'px'), 1, 'Изображение товара в подробном описании товара') .
                $PHPShopGUI->setField('Макс. высота оригинала', $PHPShopGUI->setInputText(false, 'option[img_h]', $option['img_h'], 100, 'px'), 1, 'Изображение товара в подробном описании товара') .
                $PHPShopGUI->setField('Качество оригинала', $PHPShopGUI->setInputText(false, 'option[width_podrobno]', $option['width_podrobno'], 100, '%'), 1, 'Изображение товара в подробном описании товара') .
                $PHPShopGUI->setField('Исходное изображение', $PHPShopGUI->setCheckbox('option[image_save_source]', 1, 'Сохранять исходное изображение при ресайзинге', $option['image_save_source']), 1, 'Используется для увеличения фото в карточке товара') .
                // $PHPShopGUI->setField('Адаптивность', $PHPShopGUI->setCheckbox('option[image_adaptive_resize]', 1, 'Оптимизировать изображение точно под указанные размеры', $option['image_adaptive_resize'])) .
                $PHPShopGUI->setField('Исходное название', $PHPShopGUI->setCheckbox('option[image_save_name]', 1, 'Сохранять исходное название изображения', $option['image_save_name'])) .
                $PHPShopGUI->setField('Исходный путь', $PHPShopGUI->setCheckbox('option[image_save_path]', 1, 'Сохранять исходный путь изображения на сервере', $option['image_save_path'])) .
                $PHPShopGUI->setField('SEO название', $PHPShopGUI->setCheckbox('option[image_save_seo]', 1, 'Сохранять изображения по именам товара', $option['image_save_seo'])) .
                $PHPShopGUI->setField('Вывод в webp ', $PHPShopGUI->setCheckbox('option[image_webp]', 1, 'Конвертация изображений в формат webp для оптимизации в реальном времени', $option['image_webp'], $webp_disabled), 1, 'Сокращение в несколько раз веса картинок. Может приводить к замедлению сайта, требователен к ресурсам.') .
                $PHPShopGUI->setField('Сохранение в webp', $PHPShopGUI->setCheckbox('option[image_webp_save]', 1, 'Конвертация изображений в формат webp для оптимизации при загрузке изображений на сервер', $option['image_webp_save'], $webp_disabled), 1, 'Сокращение в несколько раз веса картинок.') .
                $PHPShopGUI->setField('Добавить путь каталога', $PHPShopGUI->setCheckbox('option[image_save_catalog]', 1, 'Сохранять изображения в папках по именам каталогов', $option['image_save_catalog']), 1, 'При выключенной опции, все изображения товаров сохраняются в одну папку на сервере. Если файлов становится много, это может вызывать торможение в работе файлового менеджера и влиять на загрузку сайта, на дешевых тарифах хостинга. Активируйте опцию, чтобы фото сохранялись на сервере в автоматически созданную папку для каждого созданного каталога.') .
                $PHPShopGUI->setField('Отключить фотогалерею', $PHPShopGUI->setCheckbox('option[image_off]', 1, 'Отключить возможность добавлять изображения в фотогалерею', $option['image_off']), 1, 'Режим для товара с одной подготовленной картинкой, размещенной на сервере.') .
                $PHPShopGUI->setField("Размещение", $PHPShopGUI->setInputText($GLOBALS['SysValue']['dir']['dir'] . '/UserFiles/Image/', "option[image_result_path]", $option['image_result_path'], 400), 1, 'Путь сохранения загружаемых изображений') .
                $PHPShopGUI->setField('Макс. ширина тумбнейла', $PHPShopGUI->setInputText(false, 'option[img_tw]', $option['img_tw'], 100, 'px'), 1, 'Изображение товара в кратком описании товара') .
                $PHPShopGUI->setField('Макс. высота тумбнейла', $PHPShopGUI->setInputText(false, 'option[img_th]', $option['img_th'], 100, 'px'), 1, 'Изображение товара в кратком описании товара') .
                $PHPShopGUI->setField('Качество тумбнейла', $PHPShopGUI->setInputText(false, 'option[width_kratko]', $option['width_kratko'], 100, '%'), 1, 'Изображение товара в кратком описании товара');

    if (empty($hideSite))
        $PHPShopGUI->_CODE = $PHPShopGUI->setCollapse('Основные', $PHPShopGUI->_CODE);

    if (empty($option['watermark_text_size']))
        $option['watermark_text_size'] = 20;

    if (empty($option['watermark_text_alpha']))
        $option['watermark_text_alpha'] = 80;

    if (empty($option['watermark_text_color']))
        $option['watermark_text_color'] = '#cccccc';

    if (empty($hideSite))
        $PHPShopGUI->_CODE .= $PHPShopGUI->setCollapse('Настройка ватермарка', $PHPShopGUI->setField('Защита оригинала', $PHPShopGUI->setCheckbox('option[watermark_big_enabled]', 1, 'Включить водяной знак', $option['watermark_big_enabled']), 1, 'Защиту от копирования изображений в подробном описании товара') .
                $PHPShopGUI->setField('Защита исходника', $PHPShopGUI->setCheckbox('option[watermark_source_enabled]', 1, 'Включить водяной знак', $option['watermark_source_enabled']), 1, 'Защиту от копирования исходного изображений в подробном описании товара') .
                $PHPShopGUI->setField('Защита тумбнейла', $PHPShopGUI->setCheckbox('option[watermark_small_enabled]', 1, 'Включить водяной знак', $option['watermark_small_enabled']), 1, 'Защиту от копирования изображений в кратком описании товара') .
                $PHPShopGUI->setField("Ватермарк изображение", $PHPShopGUI->setIcon($option['watermark_image'], "watermark_image", false, array('load' => false, 'server' => true)), 1, 'Изображение с прозрачным фоном') .
                $PHPShopGUI->setField('Ватермарк текст', $PHPShopGUI->setInputText(false, 'option[watermark_text]', $option['watermark_text'], 200), 1, 'Используется вместо ватермарка изображения') .
                $PHPShopGUI->setField('Цвет текста', $PHPShopGUI->setInputColor('option[watermark_text_color]', $option['watermark_text_color'])) .
                $PHPShopGUI->setField('Размер шрифта текста', $PHPShopGUI->setInputText(false, 'option[watermark_text_size]', $option['watermark_text_size'], 100, 'px')) .
                $PHPShopGUI->setField('Шрифт текста', GetFonts($option['watermark_text_font'])) .
                $PHPShopGUI->setField('Отступ ватермарка справа', $PHPShopGUI->setInputText(false, 'option[watermark_right]', intval($option['watermark_right']), 100, 'px')) .
                $PHPShopGUI->setField('Отступ ватермарка снизу', $PHPShopGUI->setInputText(false, 'option[watermark_bottom]', intval($option['watermark_bottom']), 100, 'px')) .
                $PHPShopGUI->setField('Центрировать', $PHPShopGUI->setCheckbox('option[watermark_center_enabled]', 1, 'Расположить ватермарк по центру', $option['watermark_center_enabled'])) .
                $PHPShopGUI->setField('Прозрачность текста', $PHPShopGUI->setInputText(false, 'option[watermark_text_alpha]', intval($option['watermark_text_alpha']), 100, '%'), 1, 'Альфа канал [0-127], рекомендуется 80%')
        );

    if (empty($option['img_tw_c']))
        $option['img_tw_c'] = 410;

    if (empty($option['img_th_c']))
        $option['img_th_c'] = 200;

    if (empty($hideSite))
        $PHPShopGUI->_CODE .= $PHPShopGUI->setCollapse('Иконки для каталогов', $PHPShopGUI->setField('Обработка изображений', $PHPShopGUI->setCheckbox('option[image_cat]', 1, null, $option['image_cat'])) .
                $PHPShopGUI->setField('Максимальная ширина', $PHPShopGUI->setInputText(false, 'option[img_tw_c]', $option['img_tw_c'], 100, 'px')) .
                $PHPShopGUI->setField('Максимальная высота', $PHPShopGUI->setInputText(false, 'option[img_th_c]', $option['img_th_c'], 100, 'px')) .
                $PHPShopGUI->setField('Адаптивность', $PHPShopGUI->setCheckbox('option[image_cat_adaptive]', 1, 'Оптимизировать изображение точно под указанные размеры', $option['image_cat_adaptive']))
        );

    if (empty($option['img_tw_s']))
        $option['img_tw_s'] = 1440;

    if (empty($option['img_th_s']))
        $option['img_th_s'] = 300;

    $PHPShopGUI->_CODE .= $PHPShopGUI->setCollapse('Слайдер', $PHPShopGUI->setField('Обработка изображений', $PHPShopGUI->setCheckbox('option[image_slider]', 1, null, $option['image_slider'])) .
            $PHPShopGUI->setField('Максимальная ширина', $PHPShopGUI->setInputText(false, 'option[img_tw_s]', $option['img_tw_s'], 100, 'px')) .
            $PHPShopGUI->setField('Максимальная высота', $PHPShopGUI->setInputText(false, 'option[img_th_s]', $option['img_th_s'], 100, 'px')) .
            $PHPShopGUI->setField('Адаптивность', $PHPShopGUI->setCheckbox('option[image_slider_adaptive]', 1, 'Оптимизировать изображение точно под указанные размеры', $option['image_slider_adaptive']))
    );
    
     if (empty($hideSite)){
         
        $ffmpeg_path_value[]=['/usr/local/bin/ffmpeg','/usr/local/bin/ffmpeg',$option['ffmpeg_path']];
        $ffmpeg_path_value[]=['ffmpeg','ffmpeg',$option['ffmpeg_path']];

        if(empty($option['ffmpeg_image']))
            $option['ffmpeg_image']=3;
        if(empty($option['ffmpeg_path']))
            $option['ffmpeg_path']='/usr/local/bin/ffmpeg';
        $PHPShopGUI->_CODE .= $PHPShopGUI->setCollapse('Генерация видео для товаров из изображений', 
                $PHPShopGUI->setField('Генерация видео', $PHPShopGUI->setCheckbox('option[ffmpeg_enabled]', 1, null, $option['ffmpeg_enabled'])) .
                $PHPShopGUI->setField('Путь размещения утилиты FFmpeg', $PHPShopGUI->setSelect('option[ffmpeg_path]', $ffmpeg_path_value)).
                $PHPShopGUI->setField('Количество изображений', $PHPShopGUI->setInputText(false, 'option[ffmpeg_image]', $option['ffmpeg_image'], 50))
        );
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

    // Счетчик сообщений о поддержке
    unset($option['support_notice']);

    // Ватермарк PNG
    $_POST['option']['watermark_image'] = $_POST['watermark_image'];

    // Корректировка пустых значений
    $PHPShopOrm->updateZeroVars('option.image_save_source', 'option.image_adaptive_resize', 'option.image_save_name', 'option.watermark_big_enabled', 'option.watermark_source_enabled', 'option.watermark_center_enabled', 'option.image_save_path', 'option.image_save_catalog', 'option.watermark_small_enabled', 'option.image_off', 'option.image_cat', 'option.image_slider', 'option.image_slider_adaptive', 'option.image_cat_adaptive', 'option.image_save_seo', 'option.image_webp', 'option.image_webp_save','option.ffmpeg_enabled');

    if (is_array($_POST['option']))
        foreach ($_POST['option'] as $key => $val)
            $option[$key] = $val;

    // Создаем папку
    if (!is_dir($_SERVER['DOCUMENT_ROOT'] . $GLOBALS['SysValue']['dir']['dir'] . '/UserFiles/Image/' . $option['image_result_path']))
        @mkdir($_SERVER['DOCUMENT_ROOT'] . $GLOBALS['SysValue']['dir']['dir'] . '/UserFiles/Image/' . $option['image_result_path'], 0777, true);

    // Проверка пути сохранения изображений
    if (stristr($option['image_result_path'], '..') or ! is_dir($_SERVER['DOCUMENT_ROOT'] . $GLOBALS['SysValue']['dir']['dir'] . '/UserFiles/Image/' . $option['image_result_path']))
        $option['image_result_path'] = null;

    if (substr($option['image_result_path'], -1) != '/' and ! empty($option['image_result_path']))
        $option['image_result_path'] .= '/';

    if (!function_exists('imagewebp')) {
        $option['image_webp'] = 0;
        $option['image_webp_save'] = 0;
    }

    $_POST['admoption_new'] = serialize($option);

    // Перехват модуля
    $PHPShopModules->setAdmHandler(__FILE__, __FUNCTION__, $_POST);

    $action = $PHPShopOrm->update($_POST, array('id' => '=' . $_POST['rowID']));


    return array("success" => $action);
}

// Обработка событий
$PHPShopGUI->getAction();
?>