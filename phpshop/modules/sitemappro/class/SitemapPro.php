<?php

class SitemapPro {

    const CONTENT_STEP = 'content';
    const PRODUCTS_STEP = 'products';
    const FILTER_COMBINATIONS_STEP = 'filter';
    const FILTER_COMBINATIONS_STEP_LIMIT = 100;

    private $options = [];
    private $xml = '';
    // seourl modules
    private $isSeoUrlEnabled = false;
    private $isSeoUrlProEnabled = false;
    private $isSeoNewsEnabled = false;
    private $isSeoPagesEnabled = false;
    private $isSeoBrandsEnabled = false;

    public function __construct() {
        $orm = new PHPShopOrm('phpshop_modules_sitemappro_system');

        $this->options = $orm->select();

        // Учет модуля SEOURL
        if (!empty($GLOBALS['SysValue']['base']['seourl']['seourl_system'])) {
            $this->isSeoUrlEnabled = true;
        }

        // Учет модуля SEOURLPRO
        if (!empty($GLOBALS['SysValue']['base']['seourlpro']['seourlpro_system'])) {
            $this->isSeoUrlProEnabled = true;

            $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['seourlpro']['seourlpro_system']);
            $settings = $PHPShopOrm->select(['seo_news_enabled, seo_page_enabled', 'seo_brands_enabled', 'html_enabled'], ['id' => "='1'"]);
            if ($settings['seo_news_enabled'] == 2)
                $this->isSeoNewsEnabled = true;
            if ($settings['seo_page_enabled'] == 2)
                $this->isSeoPagesEnabled = true;
            if ($settings['seo_brands_enabled'] == 2)
                $this->isSeoBrandsEnabled = true;

            if ($settings['html_enabled'] == 2)
                $this->html = null;
            else
                $this->html = '.html';

            include_once dirname(dirname(__DIR__)) . '/seourlpro/inc/option.inc.php';
        }
    }

    public function generateSitemap($ssl = false) {
        switch ($this->options['step']) {
            case self::PRODUCTS_STEP: {
                    $this->xml .= '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
                    $this->xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
                    $this->addProducts($ssl);
                    $this->xml .= '</urlset>';
                }
                break;
            case self::FILTER_COMBINATIONS_STEP: {
                    $this->addFilterCombinations($ssl);
                }
                break;
            default: {
                    $this->addMainPage($ssl);
                    $this->addPages($ssl);
                    $this->addNews($ssl);
                    $this->addCategories($ssl);

                    if ($this->isSeoUrlProEnabled && $this->isSeoBrandsEnabled) {
                        $this->addBrands($ssl);
                    }

                    $this->xml .= '</urlset>';

                    $orm = new PHPShopOrm('phpshop_modules_sitemappro_system');
                    $orm->update(['step_new' => self::PRODUCTS_STEP], ['id' => '="1"']);
                }
        }

        $this->compile($ssl);
    }

    private function addFilterCombinations($ssl) {
        $from = (int) $this->options['processed'];
        $to = self::FILTER_COMBINATIONS_STEP_LIMIT;

        if ($from === 0) {
            $host = '';
            if (defined("HostID"))
                $host = '_' . HostID;

            $filterIndex = 1;
            while (file_exists(dirname(dirname(dirname(dirname(__DIR__)))) . sprintf('/UserFiles/Files/sitemap_filter%s_%s.xml', $host, $filterIndex))) {
                unlink(dirname(dirname(dirname(dirname(__DIR__)))) . sprintf('/UserFiles/Files/sitemap_filter%s_%s.xml', $host, $filterIndex));
                $filterIndex++;
            }
        }

        $system = new PHPShopSystem();

        $titleTemplate = $system->getParam('sort_title_shablon');
        $descrTemplate = $system->getParam('sort_description_shablon');

        $orm = new PHPShopOrm();
        $result = $orm->query("select * from " . $GLOBALS['SysValue']['base']['sort'] . " limit $from, $to");

        // Выбрано меньше чем лимит, значит характеристики закончились. Изменяем операцию.
        $systemOrm = new PHPShopOrm('phpshop_modules_sitemappro_system');
        if (mysqli_num_rows($result) < self::FILTER_COMBINATIONS_STEP_LIMIT) {
            $systemOrm->update(['step_new' => self::CONTENT_STEP, 'processed_new' => '0'], ['id' => '="1"']);
        } else {
            $systemOrm->update(['processed_new' => (int) $this->options['processed'] + self::FILTER_COMBINATIONS_STEP_LIMIT], ['id' => '="1"']);
        }

        $ormCategories = new PHPShopOrm($GLOBALS['SysValue']['base']['categories']);

        $where['skin_enabled'] = "!='1'";

        // Мультибаза
        if (defined("HostID"))
            $where['servers'] = " REGEXP 'i" . HostID . "i'";
        elseif (defined("HostMain"))
            $where['skin_enabled'] .= ' and (servers ="" or servers REGEXP "i1000i")';

        $categories = array_map(function ($category) {
            $category['sort'] = unserialize($category['sort']);
            return $category;
        }, $ormCategories->getList(['id', 'name', 'sort', 'cat_seo_name'], $where, false, ['limit' => 100000]));

        while ($row = mysqli_fetch_assoc($result)) {

            // Нет meta title, meta description и шаблонов для их генерации. Пропускаем такую характеристику.
            if (empty($row['sort_seo_name']) && (empty($row['title']) || empty($row['meta_description'])) && (empty($titleTemplate) || empty($descrTemplate))) {
                continue;
            }

            foreach ($categories as $category) {
                // У каталога нет такой характеристики, пропускаем.
                if (!is_array($category['sort'])) {
                    continue;
                }

                $count = mysqli_fetch_assoc($orm->query(
                                "select COUNT(`id`) as count from " . $GLOBALS['SysValue']['base']['products'] . " WHERE (`category`='" . $category['id'] . "' or `dop_cat` LIKE '%#" . $category['id'] . "#%') and `vendor` REGEXP 'i" . $row['category'] . "-" . $row['id'] . "i'"
                ));

                // Есть мета (или можно сгенерировать), есть характеристика у каталога - генерим ссылку.
                if (in_array($row['category'], $category['sort']) && (int) $count['count'] > 0) {

                    // Стандартный урл
                    $url = 'shop/CID_' . $category['id'];

                    //  SEOURLPRO
                    if ($this->isSeoUrlProEnabled) {
                        if (empty($category['cat_seo_name']))
                            $url = str_replace("_", "-", PHPShopString::toLatin($category['name']));
                        else
                            $url = $category['cat_seo_name'];
                    }

                    if (empty($row['sort_seo_name']))
                        continue;

                    // Виртуальные каталоги
                    $sortLink = '/filters/' . $row['sort_seo_name'];

                    if (empty($this->xml)) {
                        $this->xml .= '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
                        $this->xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
                    }

                    $this->xml .= '<url>' . "\n";
                    $this->xml .= '<loc>' . $this->getSiteUrl($ssl) . $url . $this->html . $sortLink . '</loc>' . "\n";
                    $this->xml .= '<changefreq>weekly</changefreq>' . "\n";
                    $this->xml .= '<priority>0.5</priority>' . "\n";
                    $this->xml .= '</url>' . "\n";
                }
            }
        }

        if (!empty($this->xml)) {
            $this->xml .= '</urlset>';
        }
    }

    private function addProducts($ssl) {
        $from = (int) $this->options['processed'];
        $to = (int) $this->options['limit_products'];

        $system = new PHPShopSystem();
        $enabled = "and enabled='1'";
        if ((int) $system->getSerilizeParam('admoption.safe_links') === 1) {
            $enabled = '';
        }

        // Мультибаза
        $queryMultibase = $this->productsMultibase();

        $orm = new PHPShopOrm();
        $result = $orm->query("select * from " . $GLOBALS['SysValue']['base']['products'] . " where $queryMultibase parent_enabled='0' $enabled and price>0 limit $from, $to");

        $orm = new PHPShopOrm('phpshop_modules_sitemappro_system');
        // Выбрано меньше чем лимит, значит товары закончились. Изменяем операцию.
        if (mysqli_num_rows($result) < (int) $this->options['limit_products']) {
            $step = self::CONTENT_STEP;
            if ((int) $this->options['use_filter_combinations'] === 1) {
                $step = self::FILTER_COMBINATIONS_STEP;
            }

            $orm->update(['step_new' => $step, 'processed_new' => '0'], ['id' => '="1"']);
        } else {
            $orm->update(['processed_new' => (int) $this->options['processed'] + (int) $this->options['limit_products']], ['id' => '="1"']);
        }

        while ($row = mysqli_fetch_assoc($result)) {

            $this->xml .= '<url>' . "\n";

            // Стандартный урл
            $url = 'shop/UID_' . $row['id'];

            // SEOURL
            if (!empty($this->isSeoUrlEnabled))
                $url .= '_' . PHPShopString::toLatin($row['name']);

            //  SEOURLPRO
            if ($this->isSeoUrlProEnabled) {
                if (empty($row['prod_seo_name']))
                    $url = 'id/' . $GLOBALS['PHPShopSeoPro']->setLatin($row['name']) . '-' . $row['id'];
                else
                    $url = 'id/' . $row['prod_seo_name'] . '-' . $row['id'];
            }

            $this->xml .= '<loc>' . $this->getSiteUrl($ssl) . $url . $this->html . '</loc>' . "\n";
            $this->xml .= '<lastmod>' . PHPShopDate::dataV($row['datas'], false, true) . '</lastmod>' . "\n";
            $this->xml .= '<changefreq>daily</changefreq>' . "\n";
            $this->xml .= '<priority>1.0</priority>' . "\n";
            $this->xml .= '</url>' . "\n";
        }
    }

    private function addMainPage($ssl) {
        $this->xml .= '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $this->xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
        $this->xml .= '<url>' . "\n";
        $this->xml .= '<loc>' . $this->getSiteUrl($ssl) . '</loc>' . "\n";
        $this->xml .= '<changefreq>weekly</changefreq>' . "\n";
        $this->xml .= '<priority>1.0</priority>' . "\n";
        $this->xml .= '</url>' . "\n";
    }

    private function addPages($ssl) {
        $where = [
            'enabled' => "!='0'",
            'category' => "!=2000"
        ];

        // Мультибаза
        if (defined("HostID"))
            $where['servers'] = " REGEXP 'i" . HostID . "i'";
        elseif (defined("HostMain"))
            $where['enabled'] .= ' and (servers ="" or servers REGEXP "i1000i")';

        $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['page']);
        $data = $PHPShopOrm->getList(['*'], $where, ['order' => 'datas DESC']);

        foreach ($data as $row) {
            $this->xml .= '<url>' . "\n";
            $this->xml .= '<loc>' . $this->getSiteUrl($ssl) . 'page/' . $row['link'] .$this->html. '</loc>' . "\n";
            $this->xml .= '<lastmod>' . PHPShopDate::dataV($row['datas'], false, true) . '</lastmod>' . "\n";
            $this->xml .= '<changefreq>weekly</changefreq>' . "\n";
            $this->xml .= '<priority>1.0</priority>' . "\n";
            $this->xml .= '</url>' . "\n";
        }

        // Страницы каталоги
        unset($where);
        $where = ['parent_to' => '=0'];

        // Мультибаза
        if (defined("HostID"))
            $where['servers'] = " REGEXP 'i" . HostID . "i'";
        elseif (defined("HostMain"))
            $where['parent_to'] .= ' and (servers ="" or servers REGEXP "i1000i")';
        else
            $where = null;

        $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['page_categories']);
        $data = $PHPShopOrm->getList(['*'], $where);

        foreach ($data as $row) {
            // Стандартный url
            $url = 'page/CID_' . $row['id'];

            if ($this->isSeoUrlEnabled)
                $url = 'page/CID_' . $row['id'] . '_' . PHPShopString::toLatin($row['name']);

            //  SEOURLPRO
            if ($this->isSeoUrlProEnabled && $this->isSeoPagesEnabled) {
                if (empty($row['page_cat_seo_name']))
                    $url = 'page/' . PHPShopString::toLatin($row['name']);
                else
                    $url = 'page/' . $row['page_cat_seo_name'];
            }

            $this->xml .= '<url>' . "\n";
            $this->xml .= '<loc>' . $this->getSiteUrl($ssl) . $url . $this->html.'</loc>' . "\n";
            $this->xml .= '<changefreq>weekly</changefreq>' . "\n";
            $this->xml .= '<priority>0.5</priority>' . "\n";
            $this->xml .= '</url>' . "\n";
        }
    }

    private function addNews($ssl) {
        $where['datau'] = '<' . time();

        // Мультибаза
        if (defined("HostID"))
            $where['servers'] = " REGEXP 'i" . HostID . "i'";
        elseif (defined("HostMain"))
            $where['datau'] .= ' and (servers ="" or servers REGEXP "i1000i")';

        $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['table_name8']);
        $data = $PHPShopOrm->getList(['*'], $where, ['order' => 'datas DESC']);

        foreach ($data as $row) {

            // Стандартный url
            $url = 'news/ID_' . $row['id'];

            if ($this->isSeoUrlEnabled)
                $url = 'news/ID_' . $row['id'] . '_' . PHPShopString::toLatin($row['zag']);

            //  SEOURLPRO
            if ($this->isSeoUrlProEnabled && $this->isSeoNewsEnabled) {
                if (empty($row['news_seo_name']))
                    $url = 'news/' . PHPShopString::toLatin($row['zag']);
                else
                    $url = 'news/' . $row['news_seo_name'];
            }

            $this->xml .= '<url>' . "\n";
            $this->xml .= '<loc>' . $this->getSiteUrl($ssl) . $url . $this->html.'</loc>' . "\n";
            $this->xml .= '<lastmod>' . PHPShopDate::dataV(PHPShopDate::GetUnixTime($row['datas']), false, true) . '</lastmod>' . "\n";
            $this->xml .= '<changefreq>daily</changefreq>' . "\n";
            $this->xml .= '<priority>0.5</priority>' . "\n";
            $this->xml .= '</url>' . "\n";
        }
    }

    private function addCategories($ssl) {
        $where['skin_enabled'] = "!='1'";

        // Мультибаза
        if (defined("HostID"))
            $where['servers'] = " REGEXP 'i" . HostID . "i'";
        elseif (defined("HostMain"))
            $where['skin_enabled'] .= ' and (servers ="" or servers REGEXP "i1000i")';

        $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['categories']);
        $data = $PHPShopOrm->getList(['*'], $where);

        foreach ($data as $row) {

            // Стандартный урл
            $url = 'shop/CID_' . $row['id'];

            // SEOURL
            if ($this->isSeoUrlEnabled)
                $url .= '_' . PHPShopString::toLatin($row['name']);

            //  SEOURLPRO
            if ($this->isSeoUrlProEnabled) {
                if (empty($row['cat_seo_name']))
                    $url = str_replace("_", "-", PHPShopString::toLatin($row['name']));
                else
                    $url = $row['cat_seo_name'];
            }

            $this->xml .= '<url>' . "\n";
            $this->xml .= '<loc>' . $this->getSiteUrl($ssl) . $url . $this->html.'</loc>' . "\n";
            $this->xml .= '<changefreq>weekly</changefreq>' . "\n";
            $this->xml .= '<priority>0.5</priority>' . "\n";
            $this->xml .= '</url>' . "\n";
        }
    }

    private function addBrands($ssl) {
        $brandsOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['sort_categories']);
        $brandsIds = [];
        $result = $brandsOrm->getList(['id'], ['brand' => '="1"']);
        foreach ($result as $value) {
            $brandsIds[] = $value['id'];
        }

        if (count($brandsIds) > 0) {
            $brandValuesOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['sort']);

            $brandValues = $brandValuesOrm->getList(['sort_seo_name'], [
                'category' => sprintf(' IN(%s)', implode(',', $brandsIds)),
                'sort_seo_name' => '<> ""'
            ]);

            foreach ($brandValues as $brandValue) {
                $this->xml .= '<url>' . "\n";
                $this->xml .= '<loc>' . $this->getSiteUrl($ssl) . 'brand/' . $brandValue['sort_seo_name'] .$this->html. '</loc>' . "\n";
                $this->xml .= '<changefreq>weekly</changefreq>' . "\n";
                $this->xml .= '<priority>0.5</priority>' . "\n";
                $this->xml .= '</url>' . "\n";
            }
        }
    }

    private function getSiteUrl($ssl = false) {
        $protocol = 'http://';
        if ($ssl) {
            $protocol = 'https://';
        }

        return $protocol . $_SERVER['SERVER_NAME'] . '/';
    }

    private function productsMultibase() {
        // Мультибаза
        if (defined("HostID") or defined("HostMain")) {

            $multi_cat = [];

            // Не выводить скрытые каталоги
            $where['skin_enabled '] = "!='1'";

            if (defined("HostID"))
                $where['servers'] = " REGEXP 'i" . HostID . "i'";
            elseif (defined("HostMain"))
                $where['skin_enabled'] .= ' and (servers ="" or servers REGEXP "i1000i")';

            $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['categories']);
            $data = $PHPShopOrm->getList(['id'], $where);

            foreach ($data as $row) {
                $multi_cat[] = $row['id'];
            }

            return ' category IN (' . @implode(',', $multi_cat) . ') and ';
        }
    }

    private function compile($ssl) {
        if (empty($this->xml)) {
            return;
        }

        $files = [];

        $host = '';
        if (defined("HostID"))
            $host = '_' . HostID;

        switch ($this->options['step']) {
            case self::PRODUCTS_STEP: {
                    $index = (((int) $this->options['processed'] + (int) $this->options['limit_products']) / (int) $this->options['limit_products']) + 1;
                    $file = sprintf('sitemap%s_%s', $host, $index);
                }
                break;
            case self::FILTER_COMBINATIONS_STEP: {
                    $file = sprintf('sitemap_filter%s_1', $host);
                    $filterIndex = 1;
                    while (file_exists(dirname(dirname(dirname(dirname(__DIR__)))) . sprintf('/UserFiles/Files/sitemap_filter%s_%s.xml', $host, $filterIndex))) {
                        $filterIndex++;
                        $file = sprintf('sitemap_filter%s_%s', $host, $filterIndex);
                    }
                }
                break;
            default:
                $file = sprintf('sitemap%s_1', $host);
        }

        // Запись в файл
        fwrite(fopen(dirname(dirname(dirname(dirname(__DIR__)))) . sprintf('/UserFiles/Files/%s.xml', $file), "w+"), $this->xml);

        for ($fileIndex = 1; file_exists(dirname(dirname(dirname(dirname(__DIR__)))) . sprintf('/UserFiles/Files/sitemap%s_%s.xml', $host, $fileIndex)); $fileIndex++) {
            $files[] = $this->getSiteUrl($ssl) . sprintf('UserFiles/Files/sitemap%s_%s.xml', $host, $fileIndex);
        }
        for ($filterIndex = 1; file_exists(dirname(dirname(dirname(dirname(__DIR__)))) . sprintf('/UserFiles/Files/sitemap_filter%s_%s.xml', $host, $filterIndex)); $filterIndex++) {
            $files[] = $this->getSiteUrl($ssl) . sprintf('UserFiles/Files/sitemap_filter%s_%s.xml', $host, $filterIndex);
        }

        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
        foreach ($files as $file) {
            $xml .= '<sitemap><loc>' . $file . '</loc></sitemap>' . "\n";
        }
        $xml .= '</sitemapindex>';

        // Обновляем файл ссылок на карты сайта
        fwrite(fopen(dirname(dirname(dirname(dirname(__DIR__)))) . sprintf('/sitemap%s.xml', $host), "w+"), $xml);
    }

}
