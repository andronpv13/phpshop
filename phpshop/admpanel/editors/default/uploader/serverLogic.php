<?php

session_start();
$_classPath = "../../../../";
include($_classPath . "class/obj.class.php");
PHPShopObj::loadClass(array("base", "system", "admgui", "orm", "date", "xml", "security", "string", "parser", "mail", "lang", 'product', 'category'));

$PHPShopBase = new PHPShopBase($_classPath . "inc/config.ini", true, true);
$PHPShopBase->chekAdmin();

// Системные настройки
$PHPShopSystem = new PHPShopSystem();

// Добавление изображения в фотогалерею
function fotoAdd() {
    global $PHPShopSystem;
    require_once $_SERVER['DOCUMENT_ROOT'] . $GLOBALS['SysValue']['dir']['dir'] . '/phpshop/lib/thumb/phpthumb.php';

    // Параметры ресайзинга
    $img_tw = $PHPShopSystem->getSerilizeParam('admoption.img_tw');
    $img_th = $PHPShopSystem->getSerilizeParam('admoption.img_th');
    $img_tw = empty($img_tw) ? 150 : $img_tw;
    $img_th = empty($img_th) ? 150 : $img_th;

    $img_w = $PHPShopSystem->getSerilizeParam('admoption.img_w');
    $img_h = $PHPShopSystem->getSerilizeParam('admoption.img_h');
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

        $PHPShopCategory = new PHPShopCategory($_POST['category']);
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
    $RName = rand(10000, 99999);

    // Копируем от пользователя
    if (!empty($_FILES['file']['name'])) {
        $_FILES['file']['ext'] = PHPShopSecurity::getExt($_FILES['file']['name']);
        if (in_array($_FILES['file']['ext'], array('gif', 'png', 'jpg', 'jpeg','webp'))) {
            if (move_uploaded_file($_FILES['file']['tmp_name'], $_SERVER['DOCUMENT_ROOT'] . $path . $_FILES['file']['name'])) {
                $file = $_SERVER['DOCUMENT_ROOT'] . $path . $_FILES['file']['name'];
                $file_name = $_FILES['file']['name'];
                $path_parts = pathinfo($file);
                $tmp_file = $_SERVER['DOCUMENT_ROOT'] . $path . $_FILES['file']['name'];
            }
        }
    }

    if (!empty($file)) {

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
            PHPShopObj::loadClass("product");
            $PHPShopProduct = new PHPShopProduct($_POST['rowID']);
            $prod_seo_name = $PHPShopProduct->getParam('prod_seo_name');
            if (!empty($prod_seo_name)) {
                $seo_name = $prod_seo_name;
            } else {
                PHPShopObj::loadClass("string");
                $seo_name = str_replace(array("_", "+", '&#43;'), array("-", "", ""), PHPShopString::toLatin($PHPShopProduct->getParam('name')));
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

        if (!$PHPShopSystem->ifSerilizeParam('admoption.image_save_name') and ! empty($tmp_file))
            unlink($tmp_file);

        switch ($_REQUEST['path']) {

            // Добавление в таблицу фотогалереи сайта
            case "photo":
                $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['photo']);
                $insert['category_new'] = $_POST['rowID'];
                $insert['name_new'] = $path . $name;
                $insert['info_new'] = $name;
                $insert['enabled_new'] = 1;
                break;


            // Добавление в таблицу фотогалереи товаров
            default:
                $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['foto']);
                $insert['parent_new'] = $_POST['rowID'];
                $insert['name_new'] = $path . $name;
        }

        $PHPShopOrm->insert($insert);
    }
}

/*
 * Handler for ajax requests (modern browsers with HTML5 file API) will post here
 */

header('Content-Type: application/json; charset=utf-8');


if (!empty($_FILES) && !empty($_FILES['file'])) {

    $fileMeta = $_FILES['file']; // Key was defined in 'fieldName' option
    if (in_array($fileMeta['type'], array('image/png', 'image/gif', 'image/jpg', 'image/jpeg', 'image/webp'))) {
        // Добавление изображения в фотогалерею
        fotoAdd();
    }

    // Sending JSON-encoded response
    echo json_encode(array(
        'file' => $fileMeta,
        'post' => $_POST
    ));
} else {
    echo json_encode(array('error' => 'Wrong request!'));
}
?>