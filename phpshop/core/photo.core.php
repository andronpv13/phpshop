<?php

/**
 * Обработчик фото галереи
 * @author PHPShop Software
 * @version 1.5
 * @package PHPShopCore
 */
class PHPShopPhoto extends PHPShopCore {

    var $empty_index_action = true;

    /**
     * Конструктор
     */
    function __construct() {

        // Кол-во фото на странице
        $num_row = 30;

        // Имя Бд
        $this->objBase = $GLOBALS['SysValue']['base']['photo'];

        // Отладка
        $this->debug = false;

        // Список экшенов
        $this->action = array("nav" => "CID");

        // Массив для обработки хлебных крошек
        $this->navigationArray = 'CatalogPhoto';

        // БД для хлебных крошек
        $this->navigationBase = 'base.photo_categories';
        parent::__construct();

        $this->page = $GLOBALS['SysValue']['nav']['page'];
        if (strlen($this->page) == 0)
            $this->page = 1;

        $this->num_row = $num_row;
    }

    /**
     * Экшен по умолчанию, заглушка
     */
    function index() {

        // Мета
        $this->category_name = __('Фотогалереи');
        $this->title = $this->category_name . " - " . $this->PHPShopSystem->getValue("title");
        $this->description = $this->category_name . ", " . $this->PHPShopSystem->getValue("descrip");
        $this->ListCategory();
    }

    /**
     * Экшен выборки информации при наличии переменной навигации CID
     */
    function CID() {

        // ID категории
        $this->category = PHPShopSecurity::TotalClean($this->PHPShopNav->getId(), 1);
        $this->PHPShopPhotoCategory = new PHPShopPhotoCategory($this->category);
        $this->category_name = $this->PHPShopPhotoCategory->getName();
        if (empty($this->category_name))
            $this->category_name = __('Фотогалереи');

        if (empty($this->category)) {
            return $this->setError404();
        }

        $PHPShopOrm = new PHPShopOrm($this->getValue('base.photo_categories'));
        $PHPShopOrm->debug = $this->debug;
        $row = $PHPShopOrm->select(array('id,name'), array('parent_to' => "=" . (int) $this->category, 'enabled' => "='1'"), false, array('limit' => 1));

        // Если фото
        if (empty($row['id'])) {

            $this->ListPhoto();
        }
        // Если каталоги
        else {

            $this->ListCategory();
        }
    }

    /**
     * Вывод списка фото
     */
    function ListPhoto() {
        $dis = '
                <link href="phpshop/lib/templates/photo/highslide/highslide.css" rel="stylesheet">
                <script src="phpshop/lib/templates/photo/highslide/highslide-p.js"></script>
                <script>
                    hs.registerOverlay({html: \'<div class="closebutton" onclick="return hs.close(this)" title="Закрыть"></div>\', position: \'top right\', fade: 2});
                    hs.graphicsDir = \'phpshop/lib/templates/photo/highslide/graphics/\';
                    hs.wrapperClassName = \'borderless\';
                </script>
                ';

        // Перехват модуля
        $this->setHook(__CLASS__, __FUNCTION__, $this->dataArray, 'START');

        // Путь для навигации
        $this->objPath = '/photo/CID_' . $this->category . '_';

        // Выборка данных
        $this->dataArray = parent::getListInfoItem(array('*'), array('category' => '=' . $this->category, 'enabled' => "='1'"), array('order' => 'num'));
        if (is_array($this->dataArray))
            foreach ($this->dataArray as $row) {


                // Видео
                if (in_array(pathinfo($row['name'], PATHINFO_EXTENSION), array('mp4', 'mov'))) {
                    $this->set('photoVideo', $row['name']);
                    $this->set('photoInfo', $row['info']);

                    // Перехват модуля
                    $this->setHook(__CLASS__, __FUNCTION__, $row, 'MIDDLE');

                    if (PHPShopParser::checkFile('photo/photo_element_forma_video.tpl'))
                        $dis .= ParseTemplateReturn('photo/photo_element_forma_video.tpl');
                    else
                        $dis .= ParseTemplateReturn('phpshop/lib/templates/photo/photo_element_forma_video.tpl', true);
                }
                // Изображение
                else {
                    $name_s = str_replace(".", "s.", $row['name']);
                    $this->set('photoIcon', $name_s);
                    $this->set('photoInfo', $row['info']);
                    $this->set('photoImg', $row['name']);

                    // Перехват модуля
                    $this->setHook(__CLASS__, __FUNCTION__, $row, 'MIDDLE');

                    if (PHPShopParser::checkFile('photo/photo_element_forma.tpl'))
                        $dis .= ParseTemplateReturn('photo/photo_element_forma.tpl');
                    else
                        $dis .= ParseTemplateReturn('phpshop/lib/templates/photo/photo_element_forma.tpl', true);
                }
            }
           
        // Если есть описание каталога
        if (empty($this->LoadItems['CatalogPhoto'][$this->category]))
            $content = $this->PHPShopPhotoCategory->getContent();
        elseif (!empty($this->LoadItems['CatalogPhoto'][$this->category]['content_enabled']))
            $content = $this->PHPShopPhotoCategory->getContent();


        $this->set('pageContent', $content . PHPShopText::div($dis, false, false, false, 'row'));
        $this->set('pageTitle', $this->category_name);

        // Пагинатор
        $this->setPaginator();

        // Мета
        $this->title = $this->category_name . " - " . $this->PHPShopSystem->getValue("title");
        $this->description = $this->category_name . ", " . $this->PHPShopSystem->getValue("descrip");

        // Навигация хлебные крошки
        $this->navigation($row['parent_to'], $this->category_name);

        // Перехват модуля
        $this->setHook(__CLASS__, __FUNCTION__, $this->dataArray, 'END');

        // Подключаем шаблон
        $this->parseTemplate($this->getValue('templates.page_page_list'));
    }

    /**
     * Вывод списка категорий фото
     */
    function ListCategory() {

        // Выборка данных
        $PHPShopOrm = new PHPShopOrm($this->getValue('base.photo_categories'));
        $PHPShopOrm->debug = $this->debug;

        $dataArray = $PHPShopOrm->select(array('name', 'id'), array('parent_to' => '=' . (int) $this->category), array('order' => 'num'), array('limit' => 100));
        if (is_array($dataArray))
            foreach ($dataArray as $row) {
                $dis .= PHPShopText::li($row['name'], "/photo/CID_" . $row['id'] . ".html");
            }

        // Если есть описание каталога
        if (!empty($this->LoadItems['CatalogPhoto'][$this->category]['content_enabled']))
            $disp .= $this->PHPShopPhotoCategory->getContent();

        $disp .= PHPShopText::ul($dis);

        $this->set('isPage', true);
        $this->set('pageContent', Parser($disp));
        $this->set('pageTitle', $this->category_name);

        // Мета
        $this->title = $this->category_name . " - " . $this->PHPShopSystem->getValue("name");
        $this->description = $this->category_name . ", " . $this->PHPShopSystem->getValue("descrip");

        // Навигация хлебные крошки
        $this->navigation($this->category, $this->category_name);

        // Перехват модуля
        $this->setHook(__CLASS__, __FUNCTION__, $dataArray);

        // Подключаем шаблон
        $this->parseTemplate($this->getValue('templates.page_page_list'));
    }
}

?>