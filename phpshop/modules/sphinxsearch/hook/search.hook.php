<?php

function words_sphinxsearch_hook($obj, $request, $route) {

    if ($route === 'START') {
        
        if (!empty($GLOBALS['RegTo']['SupportExpires']) and $GLOBALS['RegTo']['SupportExpires'] < time())
            return null;

        include_once dirname(__DIR__) . '/class/SphinxSearch.php';

        $SphinxSearch = new SphinxSearch();
        if (empty($SphinxSearch->link_db))
             return null;
        
        $pageSize = (int) SphinxSearch::getOption('search_page_size') > 0 ? (int) SphinxSearch::getOption('search_page_size') : 15;
        if ((int) $_REQUEST['pole'] > 0)
            $pole = (int) $_REQUEST['pole'];
        else
            $pole = (int) $obj->PHPShopSystem->getSerilizeParam('admoption.search_pole');
        (int) $_REQUEST['p'] > 0 ? $page = (int) $_REQUEST['p'] : $page = 1;
        (int) $_REQUEST['cat'] > 0 ? $category = (int) $_REQUEST['cat'] : $category = 0;
        if (empty($pole))
            $pole = 1;
        
        // Синонмы
        $_REQUEST['words'] = $SphinxSearch->synonyms($_REQUEST['words']);
        
        $query = PHPShopSecurity::true_search(trim($_REQUEST['words']));
        $obj->set('productValutaName', $obj->currency());

        if (isset($_REQUEST['ajax'])) {
            header('Content-type: text/html; charset=' . $GLOBALS['PHPShopLang']->charset);
           
            exit($SphinxSearch->searchAjax($query, $obj, (int) SphinxSearch::getOption('ajax_search_products_cnt')));
        }

        // Категория поиска
        $obj->category_select();

        if (empty($query)) {
            $obj->parseTemplate($obj->getValue('templates.search_page_list'));
            return true;
        }

        try {
            $result = $SphinxSearch->search($query, $pole, $category, $pageSize * ($page - 1), $pageSize);
        } catch (\Exception $exception) {
            return null; // выбрасываем из хука на стандартный поиск если пойман exception
        }

        $obj->set('searchString', $_REQUEST['words']);
        if ($pole == 1)
            $obj->set('searchSetC', 'checked');
        else
            $obj->set('searchSetD', 'checked');

        if ($result['total'] === 0) {
            $obj->add(PHPShopText::h3(__('Ничего не найдено')), true);

            $obj->parseTemplate($obj->getValue('templates.search_page_list'));

            return true;
        }

        $SphinxSearch->setPaginator($page, $result['total'], $pageSize, $obj, $query, $category, $pole);
        $categoryIds = $result['categories'];

        if ((int) SphinxSearch::getOption('use_additional_categories') === 1) {
            $additionalCategories = [];
            foreach ($result['products'] as $product) {
                if (!empty($product['dop_cat'])) {
                    $additionalCategories = array_merge(preg_split('/#/', $product['dop_cat'], -1, PREG_SPLIT_NO_EMPTY), $additionalCategories);
                }
            }
            $categoryIds = array_unique(array_merge($categoryIds, $additionalCategories));
        }

        $grid = '';

        // Блок "Найдено в категориях".
        if (is_array($categoryIds) && count($categoryIds) > 0) {
            $categories = SphinxSearch::getCategoriesByIds($categoryIds);

            if ((int) SphinxSearch::getOption('search_show_informer_string') === 1) {
                $obj->set('sphinxsearch_categories_count', is_array($categories) && count($categories) > 0 ? count($categories) : 0);
                $obj->set('sphinxsearch_products_count', $result['total']);
                $grid = PHPShopParser::file($GLOBALS['SysValue']['templates']['sphinxsearch']['search_informer_string'], true, false, true);
            }

            if ((int) SphinxSearch::getOption('find_in_categories') === 1 || (int) SphinxSearch::getOption('find_in_categories') === 2) {
                $categoriesHtml = '';

                if (count($result['categories']) > 0) {

                    $i = 0;

                    foreach ($categories as $cat) {

                        if ($i >= SphinxSearch::getOption('max_categories'))
                            continue;

                        $obj->set('sphinxsearch_category_title', $cat['name']);
                        $obj->set('sphinxsearch_category_count', $result['count'][$cat['id']]);
                        $obj->set('sphinxsearch_category_icon', $cat['icon']);
                        $obj->set('sphinxsearch_category_url', '/search/' . "?words=" . $query . "&pole=" . $pole . "&p=" . $page . "&cat=" . $cat['id']);

                        $categoriesHtml .= PHPShopParser::file(SphinxSearch::getCategoriesTemplate(), true, false, true);
                        $i++;
                    }

                    $obj->set('sphinxsearch_search_categories', $categoriesHtml);
                    $grid .= PHPShopParser::file(SphinxSearch::getCategoriesWrapperTemplate(), true, false, true);
                }
            }
        }

        // Добавляем в дизайн ячейки с товарами
        (int) SphinxSearch::getOption('search_page_row') > 0 ? $cell = (int) SphinxSearch::getOption('search_page_row') : $cell = 15;
        $grid .= $obj->product_grid($result['products'], $cell, false, $obj->line);

        $obj->add($grid, true);

        // Запись в журнал
        $obj->write($obj->get('searchString'), $page, $_REQUEST['cat'], $_REQUEST['set']);

        // Подключаем шаблон
        $obj->parseTemplate($obj->getValue('templates.search_page_list'));

        return true;
    }
}

$addHandler = [
    'words' => 'words_sphinxsearch_hook'
];
