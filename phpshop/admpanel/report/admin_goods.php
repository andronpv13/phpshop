<?php

use Shuchkin\SimpleXLSXGen;

$TitlePage = __("Аналитика товаров");

PHPShopObj::loadClass("array");
PHPShopObj::loadClass("category");

// === ИНИЦИАЛИЗАЦИЯ ORM ===
$PHPShopOrmOrders = new PHPShopOrm($GLOBALS['SysValue']['base']['orders']);
$PHPShopOrmProducts = new PHPShopOrm($GLOBALS['SysValue']['base']['products']);
$PHPShopOrmCategories = new PHPShopOrm($GLOBALS['SysValue']['base']['categories']);

function getProductInfo($product_id) {
    global $PHPShopOrmProducts, $PHPShopOrmCategories;

    // Получаем информацию о товаре через ORM
    $product = $PHPShopOrmProducts->getOne(
            array('id', 'name', 'price', 'price_purch', 'category', 'pic_small'), array('id' => '=' . $product_id)
    );

    if (!$product) {
        return null;
    }

    // Получаем название категории через ORM
    if ($product['category']) {
        $category = $PHPShopOrmCategories->getOne(
                array('name'), array('id' => '=' . $product['category'])
        );
        $product['category_name'] = $category ? $category['name'] : 'Без категории';
    } else {
        $product['category_name'] = 'Без категории';
    }

    return $product;
}

// Построение дерева категорий
function treegenerator($array, $i, $curent, $dop_cat_array) {
    global $tree_array;
    $del = '&brvbar;&nbsp;&nbsp;&nbsp;&nbsp;';
    $tree_select = $tree_select_dop = $check = false;

    $del = str_repeat($del, $i);
    if (!empty($array['sub']) and is_array($array['sub'])) {
        foreach ($array['sub'] as $k => $v) {

            $check = treegenerator(@$tree_array[$k], $i + 1, $_GET['category'], $dop_cat_array);

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
                $tree_select .= '<option value="' . $k . '" disabled>' . $del . $v . '</option>';
                $tree_select_dop .= '<option value="' . $k . '" disabled >' . $del . $v . '</option>';
            }

            $tree_select .= $check['select'];
            $tree_select_dop .= $check['select_dop'];
        }
    }
    return array('select' => $tree_select, 'select_dop' => $tree_select_dop);
}

function actionStart() {
    global $PHPShopInterface, $PHPShopSystem, $TitlePage, $PHPShopOrmOrders;

    $PHPShopInterface->addJSFiles('./report/gui/analytics.gui.js', './js/bootstrap-datetimepicker.min.js', './report/gui/chart.min.js');
    $PHPShopInterface->addCSSFiles('./css/bootstrap-datetimepicker.min.css');


    $PHPShopInterface->action_button['Экспортировать данные'] = [
        'name' => __('Экспортировать данные'),
        'class' => 'btn  btn-default btn-sm navbar-btn btn-action-panel-blank',
        'action' => './csv/report_goods.xlsx',
        'type' => 'button',
        'icon' => 'glyphicon glyphicon-export'
    ];

    $PHPShopInterface->setActionPanel($TitlePage, false, ['Экспортировать данные']);

    // Восстанавление настроек фильтрации из кеша
    if (empty($_GET['status'])) {

        $file = 'analytics-goods';
        $cache_key = md5(str_replace("www.", "", getenv('SERVER_NAME')) . $file);
        $PHPShopCache = new PHPShopCache($cache_key);
        $PHPShopFileCache = new PHPShopFileCache(0);
        $PHPShopFileCache->check_time = false;
        $PHPShopFileCache->dir = "/UserFiles/Cache/static/";

        // Сброс
        if (!empty($_GET['reset']))
            $PHPShopFileCache->delete($cache_key);
        else
            $analytics_cache = $PHPShopFileCache->get($cache_key);

        if (!empty($analytics_cache))
            $analytics_cache = unserialize(gzuncompress($analytics_cache));

        if (empty($_GET['status']) and is_array($analytics_cache['analytics_filter'])) {
            foreach ($analytics_cache['analytics_filter'] as $k => $v)
                $_GET[$k] = $v;
        }
    }

    // Сброс
    if (!empty($_GET['date_from']) or ! empty($_GET['date_to']) or ! empty($_GET['min_sales']) or ! empty($_GET['max_sales']) or ! empty($_GET['min_gmv'])) {
        $clean = true;
    }

    // === ПАРАМЕТРЫ ФИЛЬТРАЦИИ ===
    $filter_category = (int) ($_GET['category'] ?? 0);
    $filter_product = trim($_GET['product'] ?? '');
    $min_sales = (int) ($_GET['min_sales'] ?? 0);
    $max_sales = (int) ($_GET['max_sales'] ?? 0);
    $min_gmv = (float) ($_GET['min_gmv'] ?? 0);

// Период по умолчанию
    $default_date_from = date('d-m-Y', strtotime('-90 days'));
    $default_date_to = date('d-m-Y');
    $date_from = $_GET['date_from'] ?? $default_date_from;
    $date_to = $_GET['date_to'] ?? $default_date_to;

    $sort = $_GET['sort'] ?? 'gmv';
    $order = (($_GET['order'] ?? 'desc') === 'asc') ? 'ASC' : 'DESC';

    if (!in_array($sort, ['gmv', 'sales_count', 'avg_price', 'margin', 'name'])) {
        $sort = 'gmv';
    }

    // === СБОР ДАННЫХ ИЗ ЗАКАЗОВ ЧЕРЕЗ ORM ===
    $timestamp_from = strtotime($date_from);
    $timestamp_to = strtotime($date_to . ' 23:59:59');

    // Статусы заказов
    PHPShopObj::loadClass('order');
    $PHPShopOrderStatusArray = new PHPShopOrderStatusArray();

    $status_array = $PHPShopOrderStatusArray->getArray();
    $status_array[0] = ['name' => __('Новый заказ'), 'sklad_action' => false, 'id' => 'null', 'description' => __('Системный')];

    if (is_array($status_array))
        foreach ($status_array as $status_val) {

            if (!is_array($_GET['status']) and $status_val['sklad_action'] == 1) {
                $sel = true;
                $allowed_statuses[] = $status_val['id'];
            } elseif (is_array($_GET['status']) and in_array($status_val['id'], $_GET['status']))
                $sel = true;
            else
                $sel = false;

            $order_status_value[] = array(substr($status_val['name'], 0, 22), $status_val['id'], $sel, 'data-subtext="' . $status_val['description'] . '"');
        }


    if (is_array($_GET['status'])) {

        // Коррекция для нового заказа
        foreach ($_GET['status'] as $k => $v) {
            if ($v == 'null')
                $_GET['status'][$k] = 0;
        }

        $allowed_statuses = array_values($_GET['status']);
    }


    // Формируем условия для ORM
    $where_conditions = array(
        'statusi' => ' IN (' . implode(',', $allowed_statuses) . ')',
        'date' => '>=' . $timestamp_from . ' and date <=' . $timestamp_to,
        'sum' => ' IS NOT NULL and sum>0'
    );

    // Получаем заказы через ORM
    $orders = $PHPShopOrmOrders->getList(
            array('id', 'date', 'orders'), $where_conditions, array('order' => 'date DESC'), array('limit' => 1000)
    );

    // === АГРЕГАЦИЯ ДАННЫХ ПО ТОВАРАМ ===
    $products = [];
    $total_gmv = 0;
    $chart_data = [];

    if (is_array($orders)) {
        foreach ($orders as $row) {
            $blob = $row['orders'];
            $date = date('d-m-Y', $row['date']);
            if ($blob) {
                $data = @unserialize($blob);
                if (is_array($data) && isset($data['Cart']['cart']) && is_array($data['Cart']['cart'])) {
                    foreach ($data['Cart']['cart'] as $item) {
                        if (!is_array($item))
                            continue;

                        $product_id = intval($item['id'] ?? 0);
                        $product_name = trim($item['name'] ?? '');
                        $price = floatval($item['price'] ?? 0);
                        $quantity = intval($item['count'] ?? 1);

                        if (!$product_id || $price <= 0)
                            continue;

                        // Получаем информацию о товаре через ORM
                        $product_info = getProductInfo($product_id);
                        if (!$product_info) {
                            $category_name = 'Неизвестно';
                            $purchase_price = null;
                        } else {
                            $category_name = $product_info['category_name'] ?? 'Без категории';
                            $purchase_price = !empty($product_info['price_purch']) ? floatval($product_info['price_purch']) : null;
                            $product_name = $product_info['name'] ?? $product_name;
                        }

                        // Фильтр по категории
                        if ($filter_category > 0 && $product_info && $product_info['category'] != $filter_category) {
                            continue;
                        }

                        // Фильтр по названию товара
                        if ($filter_product !== '' && stripos($product_name, $filter_product) === false) {
                            continue;
                        }

                        if (!isset($products[$product_id])) {
                            $products[$product_id] = [
                                'id' => $product_id,
                                'uid' => $item['uid'],
                                'name' => $product_name,
                                'category' => $category_name,
                                'category_id' => $product_info['category'],
                                'pic_small' => $product_info['pic_small'],
                                'sales_count' => 0,
                                'gmv' => 0,
                                'prices' => [],
                                'purchase_price' => $purchase_price,
                                'margin' => null,
                                'profit' => null,
                                'avg_price' => 0
                            ];
                        }

                        $products[$product_id]['sales_count'] += $quantity;
                        $products[$product_id]['gmv'] += $price * $quantity;
                        $products[$product_id]['prices'] = $price;
                        $total_gmv += $price * $quantity;

                        // Данные для графика
                        $chart_data[$date]['products'][$product_id] = true;
                        $chart_data[$date]['sales'] += $quantity;
                        $chart_data[$date]['gmv'] += $price * $quantity;

                        $chart_data[$date]['prices'][] = $price;
                    }
                }
            }
        }
    }

    // === РАСЧЕТ ДОПОЛНИТЕЛЬНЫХ МЕТРИК ===
    foreach ($products as $product_id => &$product) {
        $product['avg_price'] = $product['sales_count'] > 0 ? $product['gmv'] / $product['sales_count'] : 0;

        $product['gmv_share'] = $total_gmv > 0 ? ($product['gmv'] / $total_gmv) * 100 : 0;

        if (!empty($product['purchase_price']) && $product['purchase_price'] > 0 && $product['avg_price'] > 0) {
            $product['margin'] = (($product['avg_price'] - $product['purchase_price']) / $product['avg_price']) * 100;
            $product['profit'] = ($product['avg_price'] - $product['purchase_price']) * $product['sales_count'];
        }

        // Применяем фильтры
        if ($product['sales_count'] < $min_sales || $product['gmv'] < $min_gmv) {
            unset($products[$product_id]);
        }
    }
    unset($product);

    // === СОРТИРОВКА ===
    // Заменяем стрелочную функцию на обычную анонимную функцию
    usort($products, function($a, $b) use ($sort, $order) {
        $cmp = 0;
        if ($a[$sort] < $b[$sort]) {
            $cmp = -1;
        } elseif ($a[$sort] > $b[$sort]) {
            $cmp = 1;
        }

        if ($order === 'DESC') {
            return -$cmp;
        } else {
            return $cmp;
        }
    });

    $PHPShopCategoryArray = new PHPShopCategoryArray();
    $CategoryArray = $PHPShopCategoryArray->getArray();
    $GLOBALS['count'] = count($CategoryArray);

    $tree_array = array();

    foreach ($PHPShopCategoryArray->getKey('parent_to.id', true) as $k => $v) {
        foreach ($v as $cat) {
            $tree_array[$k]['sub'][$cat] = $CategoryArray[$cat]['name'];
        }
        $tree_array[$k]['name'] = $CategoryArray[$k]['name'];
        $tree_array[$k]['id'] = $k;
        if ($k == $_GET['category'])
            $tree_array[$k]['selected'] = true;
    }

    $GLOBALS['tree_array'] = &$tree_array;

    $tree_select = '<option value="0">' . __('Все категории') . '</option>';

    if (is_array($tree_array[0]['sub']))
        foreach ($tree_array[0]['sub'] as $k => $v) {
            $check = treegenerator($tree_array[$k], 1, $k, false);

            if ($k == $data['category'])
                $selected = 'selected';
            else
                $selected = null;


            if (empty($tree_array[$k]))
                $disabled = null;
            else
                $disabled = ' disabled';

            $tree_select .= '<option value="' . $k . '"  ' . $selected . $disabled . '>' . $v . '</option>';

            $tree_select .= $check['select'];
        }


    $tree_select = '<select class="selectpicker show-menu-arrow hidden-edit" data-live-search="true" data-container="body" data-style="btn btn-default btn-sm" name="category"  data-width="100%">' . $tree_select . '</select>';


    // === БЫСТРЫЕ ФИЛЬТРЫ ДЛЯ ТОВАРОВ ===
    $quick_filters = [
        'popular' => [
            'title' => 'Самые востребованные',
            'filters' => [
                'sales_hits' => [
                    'name' => 'Хиты продаж',
                    'tooltip' => 'Топ 10 товаров по GMV',
                    'params' => [
                        'min_gmv' => 50000,
                        'date_from' => $date_from,
                        'date_to' => $date_to
                    ]
                ],
                'new_products' => [
                    'name' => 'Новинки',
                    'tooltip' => 'Товары с первыми продажами за последние 30 дней',
                    'params' => [
                        'date_from' => date('Y-m-d', strtotime('-30 days'))
                    ]
                ],
                'slow_moving' => [
                    'name' => 'Неликвид',
                    'tooltip' => 'Товары с продажами менее 3 за период',
                    'params' => [
                        'min_sales' => 0,
                        'max_sales' => 3,
                        'date_from' => $date_from,
                        'date_to' => $date_to
                    ]
                ],
                'premium' => [
                    'name' => 'Премиум сегмент',
                    'tooltip' => 'Средняя цена более 10 000 рублей',
                    'params' => [
                        'min_gmv' => 10000,
                        'date_from' => $date_from,
                        'date_to' => $date_to
                    ]
                ]
            ]
        ],
        'financial' => [
            'title' => 'Финансовые метрики',
            'filters' => [
                'high_gmv' => [
                    'name' => 'Высокий GMV',
                    'tooltip' => 'Оборот более 100 000 рублей',
                    'params' => [
                        'min_gmv' => 100000,
                        'date_from' => $date_from,
                        'date_to' => $date_to
                    ]
                ],
                'high_margin' => [
                    'name' => 'Высокая маржа',
                    'tooltip' => 'Маржа более 40%',
                    'params' => [
                        'min_gmv' => 4000,
                        'date_from' => $date_from,
                        'date_to' => $date_to
                    ]
                ],
                'low_margin' => [
                    'name' => 'Низкая маржа',
                    'tooltip' => 'Маржа менее 10%',
                    'params' => [
                        'min_gmv' => 1000,
                        'date_from' => $date_from,
                        'date_to' => $date_to
                    ]
                ]
            ]
        ],
        'sales' => [
            'title' => 'По продажам',
            'filters' => [
                'bestsellers' => [
                    'name' => 'Бестселлеры',
                    'tooltip' => 'Продажи более 50 штук',
                    'params' => [
                        'min_sales' => 50,
                        'date_from' => $date_from,
                        'date_to' => $date_to
                    ]
                ],
                'medium_sales' => [
                    'name' => 'Средние продажи',
                    'tooltip' => 'Продажи от 10 до 50 штук',
                    'params' => [
                        'min_sales' => 10,
                        'date_from' => $date_from,
                        'date_to' => $date_to
                    ]
                ],
                'slow_sales' => [
                    'name' => 'Медленные',
                    'tooltip' => 'Продажи менее 5 штук',
                    'params' => [
                        'min_sales' => 0,
                        'max_sales' => 5,
                        'date_from' => $date_from,
                        'date_to' => $date_to
                    ]
                ]
            ]
        ],
        'problems' => [
            'title' => 'Проблемные сегменты',
            'filters' => [
                'unknown_cost' => [
                    'name' => 'Неизвестные',
                    'tooltip' => 'Товары без закупочной цены',
                    'params' => [
                        'min_sales' => 1,
                        'date_from' => $date_from,
                        'date_to' => $date_to
                    ]
                ],
                'low_turnover' => [
                    'name' => 'Низкий оборот',
                    'tooltip' => 'GMV менее 5 000 рублей',
                    'params' => [
                        'min_gmv' => 0,
                        'max_gmv' => 5000,
                        'date_from' => $date_from,
                        'date_to' => $date_to
                    ]
                ]
            ]
        ]
    ];


// Быстрые фильтры:
    $quick_filters_html = '';
    foreach ($quick_filters as $category) {
        $quick_filters_html .= '<div class="quick-filter-category">';
        $quick_filters_html .= '<strong>' . __($category['title']) . '</strong>';
        $quick_filters_html .= '<div class="quick-filter-tags">';

        foreach ($category['filters'] as $filter) {
            // Определяем, активен ли фильтр в данный момент
            $is_active = true;
            foreach ($filter['params'] as $k => $v) {
                if ($_GET[$k] != $v) {
                    $is_active = false;
                    break;
                }
            }

            $filter_class = $is_active ? 'quick-filter-tag-active' : '';

            // СОЗДАЕМ URL С СОХРАНЕНИЕМ ТЕКУЩИХ ДАТ
            if ($is_active) {
                // Если фильтр активен - создаем URL для его сброса
                $reset_params = $_GET;
                foreach ($filter['params'] as $k => $v) {
                    unset($reset_params[$k]);
                }

                // Восстанавливаем формат status[] если это массив (для сброса тоже)
                if (isset($reset_params['status']) && is_array($reset_params['status'])) {
                    $status_params = [];
                    foreach ($reset_params['status'] as $status_val) {
                        $status_params[] = "status[]=" . urlencode($status_val);
                    }
                    unset($reset_params['status']);
                    $url = '?' . http_build_query($reset_params) . '&' . implode('&', $status_params);
                } else {
                    $url = '?' . http_build_query($reset_params);
                }
            } else {
                // Если фильтр не активен - применяем его, СОХРАНЯЯ текущие даты
                $current_params = $_GET;

                // Убираем date_from/date_to из params фильтра если они уже есть в URL
                $filter_params = $filter['params'];
                if (isset($_GET['date_from']) && isset($filter_params['date_from'])) {
                    unset($filter_params['date_from']);
                }
                if (isset($_GET['date_to']) && isset($filter_params['date_to'])) {
                    unset($filter_params['date_to']);
                }

                // Правильное объединение параметров с сохранением структуры массивов
                $merged_params = $current_params;
                foreach ($filter_params as $k => $v) {
                    $merged_params[$k] = $v;
                }

                // Восстанавливаем формат status[] если это массив
                if (isset($merged_params['status']) && is_array($merged_params['status'])) {
                    $status_params = [];
                    foreach ($merged_params['status'] as $status_val) {
                        if ($status_val == 0)
                            $status_val = 'null';
                        $status_params[] = "status[]=" . urlencode($status_val);
                    }
                    unset($merged_params['status']);
                    $url = '?' . http_build_query($merged_params) . '&' . implode('&', $status_params);
                } else {
                    $url = '?' . http_build_query($merged_params);
                }
            }

            $quick_filters_html .= '
            <a href="' . $url . '" class="quick-filter-tag ' . $filter_class . '" data-toggle="tooltip" data-placement="top" title="' . __(htmlspecialchars($filter['tooltip'], ENT_QUOTES)) . '">' . __($filter['name']) . '</a>
        ';
        }

        $quick_filters_html .= '</div></div>';
    }

    $total_products = count($products);
    $total_sales = array_sum(array_column($products, 'sales_count'));
    $avg_price = $total_sales > 0 ? $total_gmv / $total_sales : 0;

    // Знак рубля
    if ($PHPShopSystem->getDefaultValutaIso() == 'RUB' or $PHPShopSystem->getDefaultValutaIso() == 'RUR')
        $currency = ' <span class="rubznak hidden-xs">p</span>';
    else
        $currency = $PHPShopSystem->getDefaultValutaCode();

    $metrics_html = '
    <div class="row" style="padding-bottom:10px">
    
        <div class="col-xs-12 col-sm-6 col-md-3">
            <div class="card card-hover-shadow">
                <div class="card-body" style="padding: 1rem;">
                    <h6 class="card-subtitle">
                        ' . __('Общий GMV') . '
                    </h6>
                    <h2 class="card-title text-inherit" style="color: #377dff; ">' . number_format($total_gmv, 0, '', ' ') . ' <small>' . $currency . '</small></h2>
                    
                    <div class="chartjs-custom" style="height: 4.5rem;">
                            <canvas id="js-chart-gmv"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xs-12 col-sm-6 col-md-3">
            <div class="card card-hover-shadow">
                <div class="card-body" style="padding: 1rem;">
                    <h6 class="card-subtitle" style="color: #677788;">
                        ' . __('Товаров в отчете') . '
                    </h6>
                    <h2 class="card-title text-inherit" style="color: #28a745;">' . number_format($total_products, 0, '', ' ') . ' <small>' . __('шт.') . '</small></h2>
                        
                    <div class="chartjs-custom" style="height: 4.5rem;">
                            <canvas id="js-chart-products"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xs-12 col-sm-6 col-md-3">
            <div class="card card-hover-shadow">
                <div class="card-body" style="padding: 1rem;">
                    <h6 class="card-subtitle" style="color: #677788;">
                        ' . __('Всего продаж') . '
                    </h6>
                    <h2 class="card-title text-inherit" style="color: #b37cfc;">' . number_format($total_sales, 0, '', ' ') . ' <small>' . __('шт.') . '</small></h2>
                    <div class="chartjs-custom" style="height: 4.5rem;">
                            <canvas id="js-chart-sales"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xs-12 col-sm-6 col-md-3">
            <div class="card card-hover-shadow">
                <div class="card-body" style="padding: 1rem;">
                    <h6 class="card-subtitle" style="color: #677788;">
                        ' . __('Средняя цена') . '
                    </h6>
                    <h2 class="card-title text-inherit" style="color: #f0616e;">' . number_format($avg_price, 0, '', ' ') . ' <small>' . $currency . '</small></h2>
                        
                    <div class="chartjs-custom" style="height: 4.5rem;">
                            <canvas id="js-chart-avgprice"></canvas>
                        </div>
                </div>
            </div>
        </div>
    </div>';

    // === ДАННЫЕ ДЛЯ ГРАФИКОВ ===
// Определяем период для графиков 
    $end_date = $date_to;
    $start_date = $date_from;

// Формируем массивы для графиков
    $chart_dates = [];
    $chart_gmv = [];
    $chart_products = [];
    $chart_sales = [];
    $chart_avgprice = [];

    $current_date = $start_date;
    while (strtotime($current_date) <= strtotime($end_date)) {

        $chart_dates[] = date('d.m', strtotime($current_date));
        $chart_gmv[] = (int) $chart_data[$current_date]['gmv'];

        if (is_array($chart_data[$current_date]['products']))
            $chart_products[] = count($chart_data[$current_date]['products']);

        $chart_sales[] = (int) $chart_data[$current_date]['sales'];

        // Средняя цена за день
        if (is_array($chart_data[$current_date]['prices']) and count($chart_data[$current_date]['prices']) > 0) {
            $chart_avgprice[] = array_sum($chart_data[$current_date]['prices']) / count($chart_data[$current_date]['prices']);
        } else {
            $chart_avgprice[] = 0;
        }

        $current_date = date('d-m-Y', strtotime($current_date . ' +1 day'));
    }

    $PHPShopInterface->_CODE .= $metrics_html;
    $PHPShopInterface->checkbox_action = false;

    // === ТАБЛИЦА С ТОВАРАМИ ===
    if (is_array($products)) {

        $CSV[] = ["Артикул", "Товар", "Категория", "Продано", "GMV", "Средняя цена продажи", "Доля", "Закупочная цена", "Маржа в процентах"];

        $PHPShopInterface->setCaption(
                array("Иконка", "6%", array('tooltip' => 'Артикул товара','sort'=>'none')), array("Товар", "30%", array('tooltip' => 'Название товара')), array("Категория", "10%", array('tooltip' => 'Категория товара')), array("Прод.", "8%", array('tooltip' => 'Количество проданных единиц')), array("GMV", "12%", array('tooltip' => 'Общий оборот (Цена ? Количество)')), array("Ср. цена", "10%", array('tooltip' => 'Средняя цена продажи')), array("Доля", "8%", array('tooltip' => 'Доля в общем обороте')), array("Закуп.", "8%", array('tooltip' => 'Закупочная цена')), array("Маржа", "9%", array('tooltip' => 'Маржа в процентах'))
        );

        foreach ($products as $product) {

            $CSV[] = [$product['uid'], $product['name'], $product['category'], $product['sales_count'], number_format($product['gmv'], 0, ',', ' '), number_format($product['avg_price'], 0, ',', ' '), number_format($product['gmv_share'], 0, ',', ' '), number_format($product['purchase_price'], 0, ',', ' '), number_format($product['margin'], 0, ',', ' ')];

            if (!empty($product['pic_small']))
                $icon = '<img src="' . $product['pic_small'] . '" onerror="this.onerror = null;this.src = \'./images/no_photo.gif\'" class="media-object">';
            else
                $icon = '<img class="media-object" src="./images/no_photo.gif">';

            // Артикул
            if (!empty($product['uid']))
                $uid = '<div class="text-muted">' . __('Арт') . ' ' . $product['uid'] . '</div>';
            else
                $uid = null;

            $PHPShopInterface->setRow(
                    array('name' => $icon, 'order' => $product['id'], 'link' => '?path=product&id=' . $product['id'], 'target' => '_blank'), array('name' => $product['name'], 'order' => $product['name'], 'link' => '?path=product&id=' . $product['id'], 'target' => '_blank', 'addon' => $uid,), array('name' => $product['category'], 'order' => $product['category'], 'link' => '?path=catalog&cat=' . $product['category_id'], 'target' => '_blank'), array('name' => $product['sales_count'], 'order' => $product['sales_count']), array('name' => number_format($product['gmv'], 0, ',', ' '), 'order' => $product['gmv']), array('name' => number_format($product['avg_price'], 0, ',', ' '), 'order' => $product['avg_price']), array('name' => number_format($product['gmv_share'], 0, ',', ' ') . '%', 'order' => $product['gmv_share']), array('name' => $product['purchase_price'] ? number_format($product['purchase_price'], 0, ',', ' ') : __('—'), 'order' => $product['purchase_price'] ?? 0), array('name' => $product['margin'] ? number_format($product['margin'], 0, ',', ' ') . '%' : __('—'), 'order' => $product['margin'] ?? 0)
            );
        }
    }



    $sidebarright[] = array('title' => 'Категория и статус', 'content' => '<form method="get" target="" enctype="multipart/form-data" action="" name="report_search" id="report_search" class="form-sidebar">' . $tree_select . $PHPShopInterface->setSelect('status[]', $order_status_value, '100%', false, false, false, false, 1, true), false, "report_search", false, false, 'form-sidebar');


    $searchforma = $PHPShopInterface->setInputDate("date_from", $date_from, 'margin-bottom:10px', null, null);
    $searchforma .= $PHPShopInterface->setInputDate("date_to", $date_to, false, null, null);

    $sidebarright[] = array('title' => 'Интервал', 'content' => $searchforma, false, "order_search", false, false, 'form-sidebar');


    $searchforma = $PHPShopInterface->setInputText('', 'product', $filter_product, false, false, false, false, 'Товар содержит');
    $sidebarright[] = array('title' => 'Название товара', 'content' => $searchforma, false, "order_search", false, false, 'form-sidebar');

    $searchforma = $PHPShopInterface->setInputText('', 'min_sales', $min_sales, 100);
    $sidebarright[] = array('title' => 'Мин. кол-во продаж', 'content' => $searchforma, false, "order_search", false, false, 'form-sidebar');

    $searchforma = $PHPShopInterface->setInputText('', 'max_sales', $max_sales, 100);
    $sidebarright[] = array('title' => 'Макс. кол-во продаж', 'content' => $searchforma, false, "order_search", false, false, 'form-sidebar');

    $searchforma = $PHPShopInterface->setInputText('', 'min_gmv', number_format($min_gmv, 2, '.', ''), 100);

    $searchforma .= $PHPShopInterface->setInputArg(array('type' => 'hidden', 'name' => 'path', 'value' => $_GET['path']));
    $searchforma .= $PHPShopInterface->setButton('Показать', 'search', 'btn-report-search pull-right');


    if ($clean)
        $searchforma .= $PHPShopInterface->setButton('Сброс', 'remove', 'btn-report-cancel pull-left', $_GET['path']);

    $sidebarright[] = array('title' => 'Мин. GMV (оборот)', 'content' => $searchforma . '</form>', false, "order_search", false, false, 'form-sidebar');

    // Данные для графиков
    $quick_filters_html .= $PHPShopInterface->setInputArg(array('type' => 'hidden', 'name' => 'chart_date', 'value' => json_encode($chart_dates)));
    $quick_filters_html .= $PHPShopInterface->setInputArg(array('type' => 'hidden', 'name' => 'chart_gmv', 'value' => json_encode($chart_gmv)));
    $quick_filters_html .= $PHPShopInterface->setInputArg(array('type' => 'hidden', 'name' => 'chart_products', 'value' => json_encode($chart_products)));
    $quick_filters_html .= $PHPShopInterface->setInputArg(array('type' => 'hidden', 'name' => 'chart_sales', 'value' => json_encode($chart_sales)));
    $quick_filters_html .= $PHPShopInterface->setInputArg(array('type' => 'hidden', 'name' => 'chart_avgprice', 'value' => json_encode($chart_avgprice)));

    $sidebarright[] = array('title' => 'Быстрые фильтры', 'content' => $quick_filters_html, false, "order_search", false, false, 'form-sidebar');


    $PHPShopInterface->setSidebarRight($sidebarright, 2);

    // Рендеринг таблицы
    $PHPShopInterface->Compile();

    // Сохранение в XLSX
    if (is_array($CSV)) {
        require_once '../lib/simplexlsx/SimpleXLSXGen.php';

        $i = 0;
        foreach ($CSV as $data) {

            // Заголовок
            if ($i == 0) {
                foreach ($data as $val) {
                    $title[] = '<b><style bgcolor="#FFFF00">' . PHPShopString::win_utf8($val, true) . '</style></b>';
                }
                $tmp_content[] = $title;
            } else {
                foreach ($data as $val)
                    $content[] = PHPShopString::win_utf8($val, true);

                $tmp_content[] = $content;
                unset($content);
            }


            $i++;
        }
        SimpleXLSXGen::fromArray($tmp_content)->saveAs('./csv/report_goods.xlsx');
    }

    // Кеш для графиков и настроек
    if (empty($analytics_cache)) {

        $file = 'analytics-goods';
        $cache_key = md5(str_replace("www.", "", getenv('SERVER_NAME')) . $file);
        $PHPShopCache = new PHPShopCache($cache_key);
        $PHPShopFileCache = new PHPShopFileCache(0);
        $PHPShopFileCache->check_time = false;
        $PHPShopFileCache->dir = "/UserFiles/Cache/static/";

        foreach ($_GET as $k => $v)
            $analytics_cache['analytics_filter'][$k] = $v;

        $PHPShopFileCache->set($cache_key, gzcompress(serialize($analytics_cache), $PHPShopCache->level));
    }
}
