<?php

PHPShopObj::loadClass("category");
PHPShopObj::loadClass("string");
include_once dirname(dirname(dirname(__DIR__))) . '/lib/thumb/phpthumb.php';

class ThumbnailImages {

    public $options = [];
    private $originalWidth;
    private $originalHeight;
    private $thumbnailWidth;
    private $thumbnailHeight;
    private $thumbnailQuality;
    private $adaptive;

    /** @var PHPShopSystem */
    private $system;

    public function __construct() {
        $orm = new PHPShopOrm('phpshop_modules_thumbnailimages_system');

        $this->options = $orm->select();
        $this->system = new PHPShopSystem();
        $this->originalWidth = !empty($this->system->getSerilizeParam('admoption.img_w')) ? (int) $this->system->getSerilizeParam('admoption.img_w') : 1000;
        $this->originalHeight = !empty($this->system->getSerilizeParam('admoption.img_h')) ? (int) $this->system->getSerilizeParam('admoption.img_h') : 1000;
        $this->originalQuality = !empty($this->system->getSerilizeParam('admoption.width_podrobno')) ? (int) $this->system->getSerilizeParam('admoption.width_podrobno') : 100;
        $this->thumbnailWidth = !empty($this->system->getSerilizeParam('admoption.img_tw')) ? (int) $this->system->getSerilizeParam('admoption.img_tw') : 300;
        $this->thumbnailHeight = !empty($this->system->getSerilizeParam('admoption.img_th')) ? (int) $this->system->getSerilizeParam('admoption.img_th') : 300;
        $this->thumbnailQuality = !empty($this->system->getSerilizeParam('admoption.width_kratko')) ? (int) $this->system->getSerilizeParam('admoption.width_kratko') : 100;
        $this->adaptive = (int) $this->system->getSerilizeParam('admoption.image_adaptive_resize') === 1;
    }

    protected function getProduct($parent) {
        $product = (new PHPShopOrm($GLOBALS['SysValue']['base']['products']))->getOne(['category', 'name', 'id'], ['id' => '=' . $parent . '']);
        return ['category' => $product['category'], 'name' => $product['name'], 'id' => $product['id']];
    }

    public function generateThumbnail() {
        $count = 0;
        $skipped = [];

        foreach ($this->getImages('thumb') as $row) {

            $image = $row['name'];
            $parent = $row['parent'];
            $source = $this->getSourceImage($image);
            $image_new=null;

            if (!empty($source)) {
                $thumb = new PHPThumb($source);
                $thumb->setOptions(['jpegQuality' => $this->thumbnailQuality]);

                // Адаптивность
                if (!empty($this->adaptive))
                    $thumb->adaptiveResize($this->thumbnailWidth, $this->thumbnailHeight);
                else
                    $thumb->resize($this->thumbnailWidth, $this->thumbnailHeight);

                // Ватермарк тубнейла
                if ($this->system->ifSerilizeParam('admoption.watermark_small_enabled')) {
                    $this->createWatermark($thumb);
                }

                $path = pathinfo(str_replace('_big.', '.', $source));

                // Имя товара и каталог
                if ($this->system->ifSerilizeParam('admoption.image_save_catalog') or $this->system->ifSerilizeParam('admoption.image_save_seo'))
                    $getProduct = $this->getProduct($parent);

                // Сохранять в папки каталогов
                if ($this->system->ifSerilizeParam('admoption.image_save_catalog')) {

                    $PHPShopCategory = new PHPShopCategory($getProduct['category']);
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

                    $path['dirname'] = $_SERVER['DOCUMENT_ROOT'].$GLOBALS['SysValue']['dir']['dir'] .'/UserFiles/Image/' .$this->system->getSerilizeParam('admoption.image_result_path'). $pathName;
                    

                    if (!is_dir($path['dirname'] . '/')) {
                        mkdir($path['dirname'] . '/', 0777, true);
                        //echo "Попытка создать " . $path['dirname'];
                    }

                    $image_new = str_replace([$_SERVER['DOCUMENT_ROOT'],'//'], ['','/'], $path['dirname'] . '/' . $path['filename'] . '.' . $path['extension']);
                }

                // SEO название
                if ($this->system->ifSerilizeParam('admoption.image_save_seo')) {

                    // Соль
                    $RName = $count+1;
                    $path['filename'] = str_replace(array("_", "+", '&#43;'), array("-", "", ""), PHPShopString::toLatin($getProduct['name'])) . '-' . $getProduct['id'] . '-' . $RName;

                    $image_new = str_replace($_SERVER['DOCUMENT_ROOT'], '', $path['dirname'] . '/' . $path['filename'] . '.' . $path['extension']);
                }

                // Коррекиция имени с учетом каталога и seo
                if (empty($image_new))
                    $image_current = $image;
                else
                    $image_current = $image_new;

                // Сохранение в webp
                if ($this->options['type'] == 3 and $path['extension'] != 'webp') {

                    $thumb->setFormat('WEBP');

                    $update = new PHPShopOrm($GLOBALS['SysValue']['base']['products']);
                    $update->debug = false;
                    $image_old = str_replace([".png", ".jpg", ".jpeg", ".gif", ".PNG", ".JPG", ".JPEG", ".GIF", ".WEBP", ".webp"], ["s.png", "s.jpg", "s.jpeg", "s.gif", "s.PNG", "s.JPG", "s.JPEG", "s.GIF", "s.WEBP", "s.webp"], $image_current);
                    $image_new = str_replace([".png", ".jpg", ".jpeg", ".gif", ".PNG", ".JPG", ".JPEG", ".GIF"], 's.webp', $image_current);
                    $update->update(['pic_small_new' => $image_new,'datas_new'=>time()], ['id' => '=' . $parent,'pic_big' => '="' . $image . '"']);

                    // Удаление старого файла
                    if ($this->options['delete'] == 2)
                        @unlink($_SERVER['DOCUMENT_ROOT'] . $image_old);

                    $path['extension'] = 'webp';
                }

                // Сохранение в jpg
                else if ($this->options['type'] == 2 and $path['extension'] != 'jpg' and $path['extension'] != 'jpeg') {

                    $thumb->setFormat('JPG');

                    $update = new PHPShopOrm($GLOBALS['SysValue']['base']['products']);
                    $update->debug = false;

                    $image_old = str_replace([".png", ".jpg", ".jpeg", ".gif", ".PNG", ".JPG", ".JPEG", ".GIF", ".WEBP", ".webp"], ["s.png", "s.jpg", "s.jpeg", "s.gif", "s.PNG", "s.JPG", "s.JPEG", "s.GIF", "s.WEBP", "s.webp"], $image_current);
                    $image_new = str_replace([".png", ".jpg", ".jpeg", ".gif", ".PNG", ".JPG", ".JPEG", ".GIF", '.WEBP', '.webp'], 's.jpg', $image_current);
                    $update->update(['pic_small_new' => $image_new,'datas_new'=>time()],['id' => '=' . $parent,'pic_big' => '="' . $image . '"']);

                    // Удаление старого файла
                    if ($this->options['delete'] == 2)
                        @unlink($_SERVER['DOCUMENT_ROOT'] . $image_old);

                    $path['extension'] = 'jpg';
                }
                // Оригинальный
                elseif (!empty($image_new)) {
                    $image_new = str_replace($_SERVER['DOCUMENT_ROOT'], '', $path['dirname'] . '/' . $path['filename'] . 's.' . $path['extension']);
                    $update = new PHPShopOrm($GLOBALS['SysValue']['base']['products']);
                    $update->debug = false;
                    $update->update(['pic_small_new' => $image_new,'datas_new'=>time()], ['id' => '=' . $parent, 'pic_big' => '="' . $image . '"']);
                }

                $thumb->save($path['dirname'] . '/' . $path['filename'] . 's.' . $path['extension']);

                $count++;
            } else {
                $skipped[] = $image;
            }
        }

        return ['count' => $count, 'skipped' => $skipped];
    }

    public function generateOriginal() {
        $count = 0;
        $skipped = [];
        foreach ($this->getImages('original') as $row) {

            $image = $row['name'];
            $parent = $row['parent'];
            $id = $row['id'];
            $image_new=null;
            
            $source = $this->getSourceImage($image);

            if (!empty($source)) {
                $thumb = new PHPThumb($source);
                $thumb->setOptions(['jpegQuality' => $this->originalQuality]);

                // Адаптивность
                if (!empty($this->adaptive))
                    $thumb->adaptiveResize($this->originalWidth, $this->originalHeight);
                else
                    $thumb->resize($this->originalWidth, $this->originalHeight);

                // Ватермарк оригинала
                if ($this->system->ifSerilizeParam('admoption.watermark_big_enabled')) {
                    $this->createWatermark($thumb);
                }

                $path = pathinfo(str_replace('_big.', '.', $source));

                // Имя товара и каталог
                if ($this->system->ifSerilizeParam('admoption.image_save_catalog') or $this->system->ifSerilizeParam('admoption.image_save_seo'))
                    $getProduct = $this->getProduct($parent);

                // Сохранять в папки каталогов
                if ($this->system->ifSerilizeParam('admoption.image_save_catalog')) {

                    $PHPShopCategory = new PHPShopCategory($getProduct['category']);
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

                    $path['dirname'] = $_SERVER['DOCUMENT_ROOT'].$GLOBALS['SysValue']['dir']['dir'] .'/UserFiles/Image/' .$this->system->getSerilizeParam('admoption.image_result_path'). $pathName;

                    if (!is_dir($path['dirname'] . '/')) {
                        mkdir($path['dirname'] . '/', 0777, true);
                        //echo "Попытка создать " . $path['dirname'];
                    }

                    $image_new = str_replace([$_SERVER['DOCUMENT_ROOT'],'//'], ['','/'], $path['dirname'] . '/' . $path['filename'] . '.' . $path['extension']);
                }

                // SEO название
                if ($this->system->ifSerilizeParam('admoption.image_save_seo')) {

                    // Соль
                    $RName = $count+1;
                    $path['filename'] = str_replace(array("_", "+", '&#43;'), array("-", "", ""), PHPShopString::toLatin($getProduct['name'])) . '-' . $getProduct['id'] . '-' . $RName;

                    $image_new = str_replace($_SERVER['DOCUMENT_ROOT'], '', $path['dirname'] . '/' . $path['filename'] . '.' . $path['extension']);
                }

                // Коррекиция имени с учетом каталога и seo
                if (empty($image_new))
                    $image_current = $image;
                else
                    $image_current = $image_new;

                // Сохранение в webp
                if ($this->options['type'] == 3 and $path['extension'] != 'webp') {

                    $thumb->setFormat('WEBP');

                    $update = new PHPShopOrm($GLOBALS['SysValue']['base']['products']);
                    $update->debug = false;
                    $image_new = str_replace([".png", ".jpg", ".jpeg", ".gif", ".PNG", ".JPG", ".JPEG", ".GIF"], '.webp', $image_current);
                    $update->update(['pic_big_new' => $image_new,'datas_new'=>time()], ['id' => '=' . $parent,'pic_big' => '="' . $image . '"']);

                    $update = new PHPShopOrm($GLOBALS['SysValue']['base']['foto']);
                    $update->update(['name_new' => $image_new], ['id' => '=' . $id ]);

                    // Удаление старого файла
                    if ($this->options['delete'] == 2)
                        @unlink($_SERVER['DOCUMENT_ROOT'] . $image);

                    $path['extension'] = 'webp';
                }
                // Сохранение в jpg
                elseif ($this->options['type'] == 2 and $path['extension'] != 'jpg' and $path['extension'] != 'jpeg') {

                    $thumb->setFormat('JPG');

                    $update = new PHPShopOrm($GLOBALS['SysValue']['base']['products']);
                    $update->debug = false;
                    $image_new = str_replace([".png", ".jpg", ".jpeg", ".gif", ".PNG", ".JPG", ".JPEG", ".GIF", '.WEBP', '.webp'], '.jpg', $image_current);
                    $update->update(['pic_big_new' => $image_new,'datas_new'=>time()], ['id' => '=' . $parent,'pic_big' => '="' . $image . '"']);

                    $update = new PHPShopOrm($GLOBALS['SysValue']['base']['foto']);
                    $update->update(['name_new' => $image_new], ['id' => '=' . $id ]);

                    // Удаление старого файла
                    if ($this->options['delete'] == 2)
                        @unlink($_SERVER['DOCUMENT_ROOT'] . $image);

                    $path['extension'] = 'jpg';
                }
                // Оригинальный
                elseif (!empty($image_new)) {
                    $update = new PHPShopOrm($GLOBALS['SysValue']['base']['products']);
                    $update->debug = false;
                    $update->update(['pic_big_new' => $image_new,'datas_new'=>time()], ['id' => '=' . $parent,'pic_big' => '="' . $image . '"']);

                    $update = new PHPShopOrm($GLOBALS['SysValue']['base']['foto']);
                    $update->debug = false;
                    $update->update(['name_new' => $image_new], ['id' => '=' . $id ]);
                }

                $thumb->save($path['dirname'] . '/' . $path['filename'] . '.' . $path['extension']);

                $count++;
            } else {
                $skipped[] = $image;
            }
        }

        return ['count' => $count, 'skipped' => $skipped];
    }

    private function getImages($operation) {
        $settings = new PHPShopOrm('phpshop_modules_thumbnailimages_system');

        $orm = new PHPShopOrm($GLOBALS['SysValue']['base']['foto']);

        $from = (int) $this->options['processed'];
        $to = (int) $this->options['limit'];

        // Нажали кнопку генерации другого типа картинок, сбрасываем прогресс
        if ($operation !== $this->options['last_operation']) {
            $from = 0;
        }

        $images = $orm->getList(['name', 'parent','id'], false, false, ['limit' => $from . ',' . $to]);

        // Выбрано меньше чем лимит, значит картинки закончились. Обнуляем настройки, что бы процесс начался заново.
        if (count($images) < (int) $this->options['limit']) {
            $settings->update(['processed_new' => '0','stop_new'=>'1','run_new'=>'0', 'last_operation_new' => $operation], ['id' => '="1"']);
        } else {
            $settings->update([
                'processed_new' => (int) $this->options['processed'] + (int) $this->options['limit'],
                'last_operation_new' => $operation,
                'run_new'=>1,
                    ], ['id' => '="1"']);
        }

        return $images;
    }

    private function getSourceImage($image) {
        $system = new PHPShopSystem();
        $path = pathinfo($image);

        $root = '';
        if (strpos($image, 'http:') === false && strpos($image, 'https:') === false) {
            $root = $_SERVER['DOCUMENT_ROOT'];
        }

        if ((int) $system->getSerilizeParam('admoption.image_save_source') === 1) {
            $bigImg = $path['dirname'] . '/' . $path['filename'] . '_big.' . $path['extension'];
            if (file_exists($root . $bigImg)) {
                return $root . $bigImg;
            }
        }

        if (file_exists($root . $image)) {
            return $root . $image;
        }

        return null;
    }

    private function createWatermark($image) {
        $watermarkImage = $this->system->getSerilizeParam('admoption.watermark_image');
        $watermarkText = $this->system->getSerilizeParam('admoption.watermark_text');

        // Image
        if (!empty($watermarkImage) and file_exists($_SERVER['DOCUMENT_ROOT'] . $watermarkImage))
            $image->createWatermark(
                    $_SERVER['DOCUMENT_ROOT'] . $watermarkImage, $this->system->getSerilizeParam('admoption.watermark_right'), $this->system->getSerilizeParam('admoption.watermark_bottom'), $this->system->getSerilizeParam('admoption.watermark_center_enabled')
            );
        // Text
        elseif (!empty($watermarkText))
            $image->createWatermarkText(
                    $watermarkText, $this->system->getSerilizeParam('admoption.watermark_text_size'), $_SERVER['DOCUMENT_ROOT'] . $GLOBALS['SysValue']['dir']['dir'] . '/phpshop/lib/font/' . $this->system->getSerilizeParam('admoption.watermark_text_font') . '.ttf', $this->system->getSerilizeParam('admoption.watermark_right'), $this->system->getSerilizeParam('admoption.watermark_bottom'), $this->system->getSerilizeParam('admoption.watermark_text_color'), $this->system->getSerilizeParam('admoption.watermark_text_alpha'), 0, $this->system->getSerilizeParam('admoption.watermark_center_enabled')
            );
    }

}
