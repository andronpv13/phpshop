<?php

use Shuchkin\SimpleXLSXGen;

$TitlePage = __("Аналитика покупателей");

PHPShopObj::loadClass("array");
PHPShopObj::loadClass("category");

// Добавьте функцию здесь ?
function formatFrequency($frequency) {
    $width = min($frequency * 25, 100);
    if ($frequency >= 2) {
        return '<div style="background: #e9ecef; height: 4px; width: 40px; border-radius: 2px; display: inline-block;" data-toggle="tooltip" data-placement="top" title="' . __("Высокая частота") . ': ' . number_format($frequency, 1, ',', '') . ' ' . __("заказов в месяц") . '"><div style="background: #28a745; height: 100%; width: ' . $width . '%; border-radius: 2px;"></div></div>';
    } elseif ($frequency >= 1) {
        return '<div style="background: #e9ecef; height: 4px; width: 40px; border-radius: 2px; display: inline-block;" data-toggle="tooltip" data-placement="top" title="' . __("Средняя частота") . ': ' . number_format($frequency, 1, ',', '') . ' ' . __("заказов в месяц") . '"><div style="background: #ffc107; height: 100%; width: ' . $width . '%; border-radius: 2px;"></div></div>';
    } else {
        return '<div style="background: #e9ecef; height: 4px; width: 40px; border-radius: 2px; display: inline-block;" data-toggle="tooltip" data-placement="top" title="' . __("Низкая частота") . ': ' . number_format($frequency, 1, ',', '') . ' ' . __("заказов в месяц") . '"><div style="background: #dc3545; height: 100%; width: ' . $width . '%; border-radius: 2px;"></div></div>';
    }
}

function actionStart() {
    global $PHPShopInterface, $PHPShopSystem, $TitlePage, $PHPShopBase;

    $PHPShopInterface->addJSFiles('./report/gui/analytics.gui.js', './js/bootstrap-datetimepicker.min.js', './report/gui/chart.min.js');
    $PHPShopInterface->addCSSFiles('./css/bootstrap-datetimepicker.min.css');

    $PHPShopInterface->action_button['Экспортировать данные'] = [
        'name' => __('Экспортировать данные'),
        'class' => 'btn  btn-default btn-sm navbar-btn btn-action-panel-blank',
        'action' => './csv/report_customers.xlsx',
        'type' => 'button',
        'icon' => 'glyphicon glyphicon-export'
    ];
    
    $PHPShopInterface->action_select['Скопировать e-mail выбранных'] = array(
        'name' => 'Скопировать e-mail выбранных',
        'action' => 'copy-mail-select',
        'class' => 'disabled'
    );
    
    $PHPShopInterface->action_select['Учебник'] = array(
        'name' => 'Инструкция',
        'url' => 'https://wiki.phpshop.ru/marketing/analitika',
        'target' => '_blank'
    );

    $PHPShopInterface->setActionPanel($TitlePage, ['Скопировать e-mail выбранных','|','Учебник'], ['Экспортировать данные']);

    // Восстанавление настроек фильтрации из кеша
    if (empty($_GET['status'])) {

        $file = 'analytics';
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
    if (!empty($_GET['date_from']) or ! empty($_GET['date_to']) or ! empty($_GET['min_avg_check']) or ! empty($_GET['min_orders'])) {
        $clean = true;
    }

// === ПАРАМЕТРЫ ФИЛЬТРАЦИИ ===
// Статусы заказов
    PHPShopObj::loadClass('order');
    $PHPShopOrderStatusArray = new PHPShopOrderStatusArray();

    $status_array = $PHPShopOrderStatusArray->getArray();
    $status_array[0] = ['name' => __('Новый заказ'), 'sklad_action' => false, 'id' => 'null', 'description' => __('Системный')];

// ИНИЦИАЛИЗАЦИЯ МАССИВА СТАТУСОВ ПО УМОЛЧАНИЮ
    $default_statuses = [];
    foreach ($status_array as $status_val) {
        if ($status_val['sklad_action'] == 1) {
            $default_statuses[] = $status_val['id'];
        }
    }

// ОПРЕДЕЛЯЕМ ВЫБРАННЫЕ СТАТУСЫ
    $allowed_statuses = $default_statuses; // по умолчанию
    if (isset($_GET['status']) && is_array($_GET['status']) && !empty($_GET['status'])) {
        // Если статусы переданы в запросе - используем их
        $allowed_statuses = [];
        foreach ($_GET['status'] as $status_val) {
            if ($status_val == 'null') {
                $allowed_statuses[] = 0;
            } else {
                $allowed_statuses[] = $status_val;
            }
        }
    }

// ФОРМИРУЕМ ДАННЫЕ ДЛЯ SELECT
    $order_status_value = [];
    if (is_array($status_array)) {
        foreach ($status_array as $status_val) {
            $sel = in_array($status_val['id'], $allowed_statuses);

            // ДОБАВЛЯЕМ ПОДПИСЬ "СИСТЕМНЫЙ" ДЛЯ НОВОГО ЗАКАЗА
            $status_name = substr($status_val['name'], 0, 22);
            if ($status_val['id'] === 'null' || $status_val['id'] === 0) {
                $status_name = $status_name;
            }

            $order_status_value[] = array($status_name, $status_val['id'], $sel, 'data-subtext="' . $status_val['description'] . '"');
        }
    }    // Делаем глобальной
    $GLOBALS['analytics_allowed_statuses'] = $allowed_statuses;

// === ПАРАМЕТРЫ ФИЛЬТРАЦИИ ===
    $filter_product = isset($_GET['product']) ? trim($_GET['product']) : '';
    $min_orders = isset($_GET['min_orders']) ? (int) $_GET['min_orders'] : 0;
    $min_avg_check = isset($_GET['min_avg_check']) ? (float) $_GET['min_avg_check'] : 0;
    $date_from = isset($_GET['date_from']) ? $_GET['date_from'] : '';
    $date_to = isset($_GET['date_to']) ? $_GET['date_to'] : '';
    $sort = isset($_GET['sort']) ? $_GET['sort'] : 'ltv';
    $order = (isset($_GET['order']) && $_GET['order'] === 'asc') ? 'ASC' : 'DESC';

// === НОВЫЕ ПАРАМЕТРЫ ДЛЯ БЫСТРЫХ ФИЛЬТРОВ ===
    $abc_group = isset($_GET['abc_group']) ? $_GET['abc_group'] : null;
    $max_orders = isset($_GET['max_orders']) ? (int) $_GET['max_orders'] : null;
    $min_frequency = isset($_GET['min_frequency']) ? (float) $_GET['min_frequency'] : null;
    $first_order_from = isset($_GET['first_order_from']) ? $_GET['first_order_from'] : null;
    $last_order_to = isset($_GET['last_order_to']) ? $_GET['last_order_to'] : null;

    if (!in_array($sort, ['ltv', 'avg_check', 'total_orders', 'last_order'])) {
        $sort = 'ltv';
    }

// Период по умолчанию
    $default_date_from = date('d-m-Y', strtotime('-365 days'));
    $default_date_to = date('d-m-Y');
    $date_from = isset($_GET['date_from']) ? $_GET['date_from'] : $default_date_from;
    $date_to = isset($_GET['date_to']) ? $_GET['date_to'] : $default_date_to;

// === ПРОСТАЯ КОНВЕРТАЦИЯ ДАТ ===
    $timestamp_from = strtotime($date_from);
    $timestamp_to = strtotime($date_to . ' 23:59:59');

// === РАСЧЕТ ABC ГРУПП ПО ВСЕМ КЛИЕНТАМ ЗА ВСЕ ВРЕМЯ (ДОБАВИТЬ ЗДЕСЬ) ===
    $pdo = $PHPShopBase->link_db;
    $sql_abc = "
    SELECT u.id AS user_id, SUM(o.sum) as total_ltv
    FROM phpshop_shopusers u
    JOIN phpshop_orders o ON u.id = o.user
    WHERE o.user > 0 AND o.sum > 0 AND o.statusi IN (" . implode(',', $allowed_statuses) . ")
    GROUP BY u.id
    HAVING total_ltv > 0
    ORDER BY total_ltv DESC
";

    $result_abc = $pdo->query($sql_abc);
    $all_customers_ltv = [];
    if ($result_abc)
        while ($row = $result_abc->fetch_assoc()) {
            $all_customers_ltv[$row['user_id']] = $row['total_ltv'];
        }

// Рассчитываем глобальные ABC группы
    $global_abc_groups = [];
    $ltv_values = array_values($all_customers_ltv);
    rsort($ltv_values);
    $total_global = count($ltv_values);

    foreach ($all_customers_ltv as $user_id => $ltv) {
        $rank = array_search($ltv, $ltv_values) + 1;
        $percentile = ($rank / $total_global) * 100;

        if ($percentile <= 20) {
            $global_abc_groups[$user_id] = 'A';
        } elseif ($percentile <= 50) {
            $global_abc_groups[$user_id] = 'B';
        } else {
            $global_abc_groups[$user_id] = 'C';
        }
    }
// === КОНЕЦ РАСЧЕТА ABC ГРУПП ===
// === СБОР ДАННЫХ ===
    $pdo = $PHPShopBase->link_db;

    $sql = "
    SELECT u.id AS user_id, u.mail, u.name, o.sum AS order_sum, 
           o.datas AS order_date,
           o.orders AS orders_blob
    FROM phpshop_shopusers u
    JOIN phpshop_orders o ON u.id = o.user
    WHERE o.user > 0 
      AND CAST(o.datas AS UNSIGNED) > 0
      AND CAST(o.datas AS UNSIGNED) BETWEEN $timestamp_from AND $timestamp_to
      AND o.statusi IN (" . implode(',', $allowed_statuses) . ")
";

    $sql .= " ORDER BY u.id, o.date";

    $result = $pdo->query($sql);
    $orders = [];
    if ($result)
        while ($row = $result->fetch_assoc()) {
            $orders[] = $row;
        }

    foreach ($orders as $order) {
        if ($order['user_id'] == 254) {
            debug("User 254 order: date=" . $order['order_date'] . " (" . date('d-m-Y', $order['order_date']) . "), sum=" . $order['order_sum']);
        }
    }

//Таблица
    $customers = [];
    foreach ($orders as $row) {
        $uid = $row['user_id'];
        if (!isset($customers[$uid])) {
            $customers[$uid] = [
                'user_id' => $uid,
                'mail' => $row['mail'],
                'name' => $row['name'],
                'total_orders' => 0,
                'ltv' => 0,
                'order_dates' => [],
                'order_amounts' => [], //  ДОБАВЛЕНО: хранит суммы каждого заказа
                'products' => [],
                'product_names_raw' => []
            ];
        }

// ПЕРЕНЕСИТЕ весь парсинг blob СЮДА, перед увеличением счетчиков
        $blob = $row['orders_blob'];
        $blob_sum = 0;
        $calculated_sum = 0;

        if ($blob) {
            $data = @unserialize($blob);
            if (is_array($data)) {
                // Извлекаем товары
                if (isset($data['Cart']['cart']) && is_array($data['Cart']['cart'])) {
                    foreach ($data['Cart']['cart'] as $item) {
                        if (!empty($item['name'])) {
                            $name_clean = trim($item['name']);
                            $customers[$uid]['products'][] = $name_clean;
                            $customers[$uid]['product_names_raw'][] = $name_clean;
                        }
                        // РАССЧИТЫВАЕМ СУММУ ИЗ ТОВАРОВ
                        $price = isset($item['price']) ? floatval($item['price']) : 0;
                        $quantity = isset($item['quantity']) ? intval($item['quantity']) : 1;
                        $calculated_sum += $price * $quantity;
                    }
                }

                // Извлекаем сумму из blob
                if (isset($data['Cart']['total_sum'])) {
                    $blob_sum = $data['Cart']['total_sum'];
                } elseif (isset($data['total_sum'])) {
                    $blob_sum = $data['total_sum'];
                }
            }
        }

// ИСПРАВЛЕННАЯ ЛОГИКА СУММ
        if (!empty($row['order_sum']) && $row['order_sum'] > 0) {
            $effective_sum = $row['order_sum'];
        } elseif ($blob_sum > 0) {
            $effective_sum = $blob_sum;
        } else {
            $effective_sum = $calculated_sum;
        }

// ТОЛЬКО ОДИН РАЗ увеличиваем счетчики!
        $customers[$uid]['total_orders'] ++;
        $customers[$uid]['ltv'] += $effective_sum;
        $customers[$uid]['order_dates'][] = $row['order_date'];
        $customers[$uid]['order_amounts'][] = $effective_sum;
    }

// === ФИЛЬТРАЦИЯ КЛИЕНТОВ С УЧЕТОМ НОВЫХ ПАРАМЕТРОВ ===
    $final_customers = [];

    foreach ($customers as $c) {
        $c['products'] = array_unique($c['products']);
        sort($c['products']);
        $c['first_order'] = min($c['order_dates']);
        $c['last_order'] = max($c['order_dates']);
        $c['avg_check'] = $c['total_orders'] > 0 ? $c['ltv'] / $c['total_orders'] : 0;
        $c['max_order'] = !empty($c['order_amounts']) ? max($c['order_amounts']) : 0;


        // === ИСПОЛЬЗУЕМ ГЛОБАЛЬНЫЕ ABC ГРУППЫ ===
        $c['customer_group'] = isset($global_abc_groups[$c['user_id']]) ? $global_abc_groups[$c['user_id']] : 'C';

        // === ИСПРАВЛЕННЫЙ РАСЧЕТ ЧАСТОТЫ ===
        $period_end = !empty($timestamp_to) ? $timestamp_to : time();
        $months_active = max(1, ($period_end - $c['first_order']) / 86400 / 30);
        $c['purchase_frequency'] = $c['total_orders'] / $months_active;

        if ($c['total_orders'] > 1) {
            $c['avg_order_interval'] = ($c['last_order'] - $c['first_order']) / ($c['total_orders'] - 1) / 86400;
        } else {
            $c['avg_order_interval'] = 0;
        }
        $c['customer_age_days'] = (time() - $c['first_order']) / 86400;

        // === ОСНОВНЫЕ ФИЛЬТРЫ ===
        if ($filter_product !== '' && !array_filter($c['product_names_raw'], function($p) use ($filter_product) {
                    return stripos($p, $filter_product) !== false;
                }))
            continue;

        if ($min_orders > 0 && $c['total_orders'] < $min_orders)
            continue;
        if ($min_avg_check > 0 && $c['avg_check'] < $min_avg_check)
            continue;

        // === НОВЫЕ ФИЛЬТРЫ ДЛЯ БЫСТРЫХ ФИЛЬТРОВ ===
        // Фильтр по ABC группе
        if (isset($_GET['abc_group']) && $c['customer_group'] != $_GET['abc_group'])
            continue;

        // Фильтр по максимальному количеству заказов
        if (isset($_GET['max_orders']) && $c['total_orders'] > $_GET['max_orders'])
            continue;

        // Фильтр по частоте покупок
        if (isset($_GET['min_frequency']) && $c['purchase_frequency'] < $_GET['min_frequency'])
            continue;

        // Фильтр по дате первого заказа
        if (isset($_GET['first_order_from'])) {
            $first_order_from_ts = strtotime($_GET['first_order_from']);
            if ($c['first_order'] < $first_order_from_ts)
                continue;
        }

        // Фильтр по дате последнего заказа
        if (isset($_GET['last_order_to'])) {
            $last_order_to_ts = strtotime($_GET['last_order_to'] . ' 23:59:59');
            if ($c['last_order'] > $last_order_to_ts)
                continue;
        }
// Фильтр по максимальному заказу
        if (isset($_GET['min_max_order']) && $c['max_order'] < $_GET['min_max_order'])
            continue;
        $final_customers[] = $c;
    }
// === СОХРАНЯЕМ ДАННЫЕ ДЛЯ ГРАФИКОВ ===
    $GLOBALS['analytics_final_customers'] = $final_customers;

// === БЫСТРЫЕ ФИЛЬТРЫ - ОСНОВНЫЕ СЕГМЕНТЫ ===
    $quick_filters = [
        'main_segments' => [
            'title' => 'Основные сегменты',
            'filters' => [
                'vip_clients' => [
                    'name' => 'VIP-клиенты',
                    'tooltip' => 'Топ 20% клиентов по LTV (группа A) за выбранный период',
                    'params' => [
                        'abc_group' => 'A'
                    ]
                ],
                'loyal_customers' => [
                    'name' => 'Постоянные клиенты',
                    'tooltip' => '3 и более заказов за выбранный период',
                    'params' => [
                        'min_orders' => 3
                    ]
                ],
                'new_customers' => [
                    'name' => 'Новые клиенты',
                    'tooltip' => 'Первый заказ за последние 30 дней',
                    'params' => [
                        'first_order_from' => date('d-m-Y', strtotime('-30 days'))
                    ]
                ],
                'sleeping_customers' => [
                    'name' => 'Спящие клиенты',
                    'tooltip' => 'Не покупали более 90 дней (из клиентов выбранного периода)',
                    'params' => [
                        'last_order_to' => date('d-m-Y', strtotime('-90 days'))
                    ]
                ],
                'one_time_buyers' => [
                    'name' => 'Однократные',
                    'tooltip' => 'Только 1 заказ за выбранный период',
                    'params' => [
                        'min_orders' => 1,
                        'max_orders' => 1
                    ]
                ],
                'frequent_customers' => [
                    'name' => 'Частые покупатели',
                    'tooltip' => 'Более 2 заказов в месяц в среднем за выбранный период',
                    'params' => [
                        'min_frequency' => 2.0
                    ]
                ]
            ]
        ],
        'financial' => [
            'title' => 'Финансовые категории',
            'filters' => [
                'high_ltv' => [
                    'name' => 'Высокий LTV',
                    'tooltip' => 'LTV более 50 000 рублей за выбранный период',
                    'params' => [
                        'min_avg_check' => 50000
                    ]
                ],
                'whales' => [
                    'name' => 'Крупные покупатели',
                    'tooltip' => 'Клиенты с максимальным чеком более 100 000 рублей',
                    'params' => [
                        'min_max_order' => 100000
                    ]
                ],
                'premium_customers' => [
                    'name' => 'Премиальные',
                    'tooltip' => 'Средний чек выше 15000 рублей за выбранный период',
                    'params' => [
                        'min_avg_check' => 15000
                    ]
                ]
            ]
        ],
        'risks' => [
            'title' => 'Проблемные сегменты',
            'filters' => [
                'at_risk' => [
                    'name' => 'В зоне риска',
                    'tooltip' => 'Не покупали более 60 дней (из клиентов выбранного периода)',
                    'params' => [
                        'last_order_to' => date('d-m-Y', strtotime('-60 days'))
                    ]
                ],
                'rare_buyers' => [
                    'name' => 'Редкие покупатели',
                    'tooltip' => 'Менее 3 заказов за весь период активности клиента',
                    'params' => [
                        'max_orders' => 2
                    ]
                ],
                'inactive_high_value' => [
                    'name' => 'Неактивные с высоким LTV',
                    'tooltip' => 'LTV > 30 000 руб и не покупали более 60 дней',
                    'params' => [
                        'min_avg_check' => 30000,
                        'last_order_to' => date('d-m-Y', strtotime('-60 days'))
                    ]
                ],
                'sleeping_vip' => [
                    'name' => 'Спящие VIP',
                    'tooltip' => 'VIP клиенты (группа A), которые не покупали более 90 дней',
                    'params' => [
                        'abc_group' => 'A',
                        'last_order_to' => date('d-m-Y', strtotime('-90 days'))
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
    // Знак рубля
    if ($PHPShopSystem->getDefaultValutaIso() == 'RUB' or $PHPShopSystem->getDefaultValutaIso() == 'RUR')
        $currency = ' <span class="rubznak hidden-xs">p</span>';
    else
        $currency = $PHPShopSystem->getDefaultValutaCode();


    $PHPShopInterface->_CODE .= '<div style="padding-bottom:10px">' . $PHPShopInterface->loadLib('tab_analytics', false, './report/') . '</div>';
    //$PHPShopInterface->checkbox_action = false;

    if (is_array($final_customers)) {

        $CSV[0] = ['Группа', 'Email', 'Имя', 'Кол-во заказов', 'LTV', 'Средник чек', 'Интервал', 'Частота', 'Первый заказ', 'Последний заказ'];

        $PHPShopInterface->setCaption(
                [null, "1%"],['ABC', '4%', ['tooltip' => 'ABC-группа клиента по LTV за всю историю магазина. Расчет глобальный, не зависит от выбранного периода. A - топ 20% по LTV, B - следующие 30%, C - остальные 50%']], ['Email', '6%', ['tooltip' => '']], ['Имя', '6%', ['tooltip' => '']], ['Заказы', '6%', ['tooltip' => 'Общее количество заказов клиента за всю историю']], ['LTV', '6%', ['tooltip' => 'Пожизненная ценность (Lifetime Value). Расчет: общая сумма всех заказов клиента за всю историю']], ['Ср. чек', '6%', ['tooltip' => 'Средний чек за ВСЕ заказы клиента. Расчет: LTV / количество заказов']], ['Частота', '6%', ['tooltip' => 'Расчет: количество заказов / количество месяцев активности. Показывает сколько заказов в среднем делает клиент за 1 месяц. Пример: 0,5 = 1 заказ в 2 месяца, 1 = 1 заказ в месяц, 2 = 2 заказа в месяц']], ['Инт.', '6%', ['tooltip' => 'Средний интервал между заказами за всю историю. Расчет: (последний заказ - первый заказ) / (количество заказов - 1) в днях']], ['Пер. зак.', '6%', ['tooltip' => 'Дата первого заказа клиента за всю историю']], ['Пос. зак.', '6%', ['tooltip' => 'Дата последнего заказа клиента за всю историю']], ['Товары', '15%', ['tooltip' => 'Список уникальных товаров, купленных клиентом за всю историю']]
        );
// Данные таблицы
        foreach ($final_customers as $k => $c) {

            $CSV[$k + 1] = [$c['customer_group'], $c['mail'], $c['name'], $c['total_orders'], number_format($c['ltv'], 0, ',', ' '), number_format($c['avg_check'], 0, ',', ' '), number_format($c['purchase_frequency'], 1, ',', ' '), number_format($c['avg_order_interval'], 0, ',', ' '), date('d.m.Y', $c['first_order']), date('d.m.Y', $c['last_order'])];

            if (is_array($c['products']))
                foreach ($c['products'] as $n => $products) {
                    array_push($CSV[0], 'Товар ' . ($n + 1));
                    array_push($CSV[$k + 1], $products);
                }

            // Обрезаем длинные email
            $display_email = $c['mail'];
            if (strlen($display_email) > 25) {
                $display_email = substr($display_email, 0, 22) . '...';
            }

            $PHPShopInterface->setRow(
                    ['id'=>$row['id'],'select'=>$row['mail']],['name' => '<span class="label label-' . ($c['customer_group'] == 'A' ? 'success' : ($c['customer_group'] == 'B' ? 'warning' : 'default')) . '">' . $c['customer_group'] . '</span>', 'order' => $c['customer_group']], ['name' => '<span title="' . htmlspecialchars($c['mail']) . '">' . $display_email . '</span>', 'order' => $c['mail'], 'link' => '?path=shopusers&id=' . $c['user_id'], 'target' => '_blank'], ['name' => $c['name'], 'order' => $c['name'], 'link' => '?path=shopusers&id=' . $c['user_id'], 'target' => '_blank'], ['name' => $c['total_orders'], 'order' => $c['total_orders']], ['name' => '<span class="number-format">' . number_format($c['ltv'], 0, ',', '') . '</span>' . $currency, 'order' => $c['ltv']], ['name' => '<span class="number-format">' . number_format($c['avg_check'], 0, ',', '') . '</span>' . $currency, 'order' => $c['avg_check']], ['name' => formatFrequency($c['purchase_frequency']), 'order' => $c['purchase_frequency']], ['name' => $c['avg_order_interval'] > 0 ? number_format($c['avg_order_interval'], 0, ',', '') . __(' дн.') : __('—'), 'order' => $c['avg_order_interval']], ['name' => date('d.m.Y', $c['first_order']), 'order' => $c['first_order']], ['name' => date('d.m.Y', $c['last_order']), 'order' => $c['last_order']], ['name' => implode(', ', array_map(function($product) {
                                    return '<small>' . htmlspecialchars($product) . '</small>';
                                }, array_slice($c['products'], 0, 1))) . (count($c['products']) > 1 ? ', <br><small>+еще ' . (count($c['products']) - 1) . '</small>' : ''), 'order' => implode(', ', $c['products'])]
            );
        }
    }

    $searchforma .= '<form method="get" target="" enctype="multipart/form-data" action="" name="report_search" id="report_search" class="form-sidebar">';
    $searchforma .= $PHPShopInterface->setSelect('status[]', $order_status_value, '100%', false, false, false, false, 1, true);
    $sidebarright[] = array('title' => 'Статус заказа', 'content' => $searchforma, false, "order_search", false, false, 'form-sidebar');

    $display_date_from = date('d-m-Y', strtotime($date_from));
    $display_date_to = date('d-m-Y', strtotime($date_to));

    $searchforma = $PHPShopInterface->setInputDate("date_from", $display_date_from, 'margin-bottom:10px', null, null);
    $searchforma .= $PHPShopInterface->setInputDate("date_to", $display_date_to, false, null, null);
    $sidebarright[] = array(
        'title' => 'Интервал <span class="glyphicon glyphicon-info-sign" style="font-size:12px; color:#677788;" data-toggle="tooltip" data-placement="top" title="Показывает клиентов, активных в выбранном периоде. Данные клиентов - за всю историю"></span>',
        'content' => $searchforma,
        false,
        "order_search",
        false,
        false,
        'form-sidebar'
    );

    $searchforma = $PHPShopInterface->setInputText('', 'product', $filter_product, false, false, false, false, 'Товар содержит');
    $sidebarright[] = array(
        'title' => 'Название товара <span class="glyphicon glyphicon-info-sign" style="font-size:12px; color:#677788;" data-toggle="tooltip" data-placement="top" title="Поиск клиентов, которые покупали товары с указанным названием"></span>',
        'content' => $searchforma,
        false,
        "order_search",
        false,
        false,
        'form-sidebar'
    );

    $searchforma = $PHPShopInterface->setInputText('', 'min_orders', $min_orders, 100);
    $sidebarright[] = array(
        'title' => 'Мин. заказов <span class="glyphicon glyphicon-info-sign" style="font-size:12px; color:#677788;" data-toggle="tooltip" data-placement="top" title="Минимальное количество заказов за всю историю клиента"></span>',
        'content' => $searchforma,
        false,
        "order_search",
        false,
        false,
        'form-sidebar'
    );

    $searchforma = $PHPShopInterface->setInputText('', 'min_avg_check', $min_avg_check, 100);

    $searchforma .= $PHPShopInterface->setInputArg(array('type' => 'hidden', 'name' => 'path', 'value' => $_GET['path']));
    $searchforma .= $PHPShopInterface->setButton('Показать', 'search', 'btn-report-search pull-right', '', 'data-toggle="tooltip" data-placement="top" title="Применить все выбранные фильтры"');

    if ($clean) {
        $searchforma .= $PHPShopInterface->setButton('Сброс', 'remove', 'btn-report-cancel pull-left', $_GET['path'], 'data-toggle="tooltip" data-placement="top" title="Сбросить все фильтры и показать данные за последний год"');
    }

    $sidebarright[] = array(
        'title' => 'Мин. средний чек <span class="glyphicon glyphicon-info-sign" style="font-size:12px; color:#677788;" data-toggle="tooltip" data-placement="top" title="Минимальный средний чек за все заказы клиента"></span>',
        'content' => $searchforma . '</form>',
        false,
        "order_search",
        false,
        false,
        'form-sidebar'
    );

    $sidebarright[] = array(
        'title' => 'Быстрые фильтры <span class="glyphicon glyphicon-info-sign" style="font-size:12px; color:#677788;" data-toggle="tooltip" data-placement="top" title="Фильтры добавляются к выбранному периоду. Показывают клиентов, которые активны в выбранные даты и соответствуют условию фильтра"></span>',
        'content' => $quick_filters_html,
        false,
        "order_search",
        false,
        false,
        'form-sidebar'
    );

    $PHPShopInterface->setSidebarRight($sidebarright, 2);

    // Рендеринг таблицы
    $PHPShopInterface->Compile('table-customers-analytics');

    // Сохранение в XLSX
    if (is_array($CSV)) {
        require_once '../lib/simplexlsx/SimpleXLSXGen.php';

        $i = 0;
        foreach ($CSV as $data) {

            // Заголовок
            if ($i == 0) {
                foreach ($data as $val) {
                    $title[] = '<b><style bgcolor="#FFFF00">' . PHPShopString::win_utf8($val,true) . '</style></b>';
                }
                $tmp_content[] = $title;
            } else {
                foreach ($data as $val)
                    $content[] = PHPShopString::win_utf8($val,true);

                $tmp_content[] = $content;
                unset($content);
            }


            $i++;
        }
        SimpleXLSXGen::fromArray($tmp_content)->saveAs('./csv/report_customers.xlsx');
    }

    // Кеш для графиков и настроек
    if (empty($analytics_cache)) {

        $file = 'analytics';
        $cache_key = md5(str_replace("www.", "", getenv('SERVER_NAME')) . $file);
        $PHPShopCache = new PHPShopCache($cache_key);
        $PHPShopFileCache = new PHPShopFileCache(0);
        $PHPShopFileCache->check_time = false;
        $PHPShopFileCache->dir = "/UserFiles/Cache/static/";

        foreach ($_GET as $k => $v)
            $analytics_cache['analytics_filter'][$k] = $v;

        $analytics_cache['analytics_final_customers'] = $GLOBALS['analytics_final_customers'];
        $analytics_cache['analytics_allowed_statuses'] = $GLOBALS['analytics_allowed_statuses'];

        $PHPShopFileCache->set($cache_key, gzcompress(serialize($analytics_cache), $PHPShopCache->level));
    }
}
