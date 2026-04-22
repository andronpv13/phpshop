<?php

function tab_analytics($analytics_cache) {
    global $PHPShopBase, $PHPShopInterface, $PHPShopSystem;


    if (!empty($analytics_cache['analytics_final_customers']))
        $GLOBALS['analytics_final_customers'] = $analytics_cache['analytics_final_customers'];

    if (!empty($analytics_cache['analytics_allowed_statuses']))
        $GLOBALS['analytics_allowed_statuses'] = $analytics_cache['analytics_allowed_statuses'];


    if (is_array($analytics_cache['analytics_filter'])) {
        foreach ($analytics_cache['analytics_filter'] as $k => $v)
            $_GET[$k] = $v;
    }

    // Знак рубля
    if ($PHPShopSystem->getDefaultValutaIso() == 'RUB' or $PHPShopSystem->getDefaultValutaIso() == 'RUR')
        $currency = ' <span class="rubznak hidden-xs">p</span>';
    else
        $currency = $PHPShopSystem->getDefaultValutaCode();

    // === ДИАГНОСТИКА ГРАФИКОВ ===
    debug("=== GRAPHS DEBUG START ===");
    // Получаем данные из основной таблицы
    $final_customers = isset($GLOBALS['analytics_final_customers']) ? $GLOBALS['analytics_final_customers'] : [];
    debug("Final customers count: " . count($final_customers));
    // Проверяем первый клиент (если есть)

    if (!empty($final_customers)) {
        $first_customer = $final_customers[0];

        debug("First customer ID: " . $first_customer['user_id']);
        debug("First customer LTV: " . $first_customer['ltv']);
        debug("First customer orders: " . $first_customer['total_orders']);

        // Смотрим даты заказов

        if (!empty($first_customer['order_dates'])) {
            debug("First customer order dates sample:");
            foreach (array_slice($first_customer['order_dates'], 0, 3) as $date) {
                debug(" - Raw: " . $date . ", As date: " . date('d-m-Y', $date));
            }
        }
    } else {
        debug("NO FINAL CUSTOMERS - EMPTY DATA!");
    }


    // === ПРОВЕРКА ДАТ ДО ГЕНЕРАЦИИ ГРАФИКОВ ===
    $date_from = isset($_GET['date_from']) ? $_GET['date_from'] : '';
    $date_to = isset($_GET['date_to']) ? $_GET['date_to'] : '';

    // Если дата "от" больше даты "до" - меняем местами
    if (!empty($date_from) && !empty($date_to) && strtotime($date_from) > strtotime($date_to)) {
        $temp = $date_from;
        $date_from = $date_to;
        $date_to = $temp;
    }

    // Ограничиваем будущие даты сегодняшним днем
    $today = date('d-m-Y');
    if (!empty($date_to) && strtotime($date_to) > strtotime($today)) {
        $date_to = $today;
    }

    // Получаем статусы из глобальной переменной
    $allowed_statuses = $GLOBALS['analytics_allowed_statuses'];

    // === ПАРАМЕТРЫ ФИЛЬТРАЦИИ ===
    $filter_product = isset($_GET['product']) ? trim($_GET['product']) : '';
    $min_orders = isset($_GET['min_orders']) ? (int) $_GET['min_orders'] : 0;
    $min_avg_check = isset($_GET['min_avg_check']) ? (float) $_GET['min_avg_check'] : 0;
    $date_from = isset($_GET['date_from']) ? $_GET['date_from'] : '';
    $date_to = isset($_GET['date_to']) ? $_GET['date_to'] : '';
    $sort = isset($_GET['sort']) ? $_GET['sort'] : 'ltv';
    $order = (isset($_GET['order']) && $_GET['order'] === 'asc') ? 'ASC' : 'DESC';
    if (!in_array($sort, ['ltv', 'avg_check', 'total_orders', 'last_order'])) {
        $sort = 'ltv';
    }

    // Период по умолчанию
    $default_date_from = date('d-m-Y', strtotime('-365 days'));
    $default_date_to = date('d-m-Y');
    $date_from = isset($_GET['date_from']) ? $_GET['date_from'] : $default_date_from;
    $date_to = isset($_GET['date_to']) ? $_GET['date_to'] : $default_date_to;

    $sort = isset($_GET['sort']) ? $_GET['sort'] : 'gmv';
    $order = (isset($_GET['order']) && $_GET['order'] === 'asc') ? 'ASC' : 'DESC';

    if (!in_array($sort, ['gmv', 'sales_count', 'avg_price', 'margin', 'name'])) {
        $sort = 'gmv';
    }
// === ДОБАВЬТЕ ЗДЕСЬ РАСЧЕТ ГРУППИРОВКИ ДЛЯ ТЕКСТА ===
    $start_temp = new DateTime($date_from);
    $end_temp = new DateTime($date_to);
    $interval_temp = $start_temp->diff($end_temp);
    $days_count_temp = $interval_temp->days;

// Определяем группировку для текста
    if ($days_count_temp > 180) {
        $group_by_text = ' (по месяцам)';
    } elseif ($days_count_temp > 30) {
        $group_by_text = ' (по неделям)';
    } else {
        $group_by_text = ' (по дням)';
    }

    // === СБОР ДАННЫХ ИЗ ЗАКАЗОВ ЧЕРЕЗ ORM ===
    $timestamp_from = strtotime($date_from);
    $timestamp_to = strtotime($date_to . ' 23:59:59');

// === ИСПОЛЬЗУЕМ ДАННЫЕ ИЗ ОСНОВНОЙ ТАБЛИЦЫ ===
// $final_customers уже содержит всех отфильтрованных клиентов
// НЕ нужно делать дополнительный SQL запрос!
// Просто используем глобальную переменную из основной таблицы
    $final_customers = isset($GLOBALS['analytics_final_customers']) ? $GLOBALS['analytics_final_customers'] : [];

    // Данные для метрик (остается без изменений)
    $total_customers = count($final_customers);
    $total_ltv = array_sum(array_column($final_customers, 'ltv'));
    $avg_check = $total_customers > 0 ? $total_ltv / array_sum(array_column($final_customers, 'total_orders')) : 0;

    $repeat_customers = count(array_filter($final_customers, function($c) {
                return $c['total_orders'] > 1;
            }));

    $repeat_rate = $total_customers > 0 ? ($repeat_customers / $total_customers) * 100 : 0;



    $metrics_html = '
    <div class="row intro-row">
        <div class="col-xs-6 col-sm-6 col-md-3 ">
            <div class="card card-hover-shadow">
                <div class="card-body" style="padding: 1rem;">
                    <h6 class="card-subtitle metrics-tooltip" data-toggle="tooltip" data-placement="top" title="' . __("Общее количество отфильтрованных клиентов по всем условиям за период") . '">
    ' . __('Клиентов' . $group_by_text) . '
					</h6>
                    <h2 class="card-title text-inherit" style="color: #377dff;">' . number_format($total_customers, 0, '', ' ') . ' <small>' . __("чел") . '</small></h2>
                    
                    <div class="chartjs-custom" style="height: 4.5rem;">
                            <canvas id="js-chart-customers"></canvas>
                    </div>
           
                </div>
            </div>
        </div>
        
        <div class="col-xs-6 col-sm-6 col-md-3 col-panel">
            <div class="card card-hover-shadow">
                <div class="card-body" style="padding: 1rem;">
                    <h6 class="card-subtitle metrics-tooltip" data-toggle="tooltip" data-placement="top" title="' . __("Сумма всех заказов отфильтрованных клиентов за выбранный период") . '">
                        ' . __('Общий LTV' . $group_by_text) . '
                    </h6>
                    <h2 class="card-title text-inherit" style="color: #28a745;">' . number_format($total_ltv, 0, '', ' ') . ' <small>' . $currency . '</small></h2>
                        
                    <div class="chartjs-custom" style="height: 4.5rem;">
                            <canvas id="js-chart-ltv"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xs-6 col-sm-6 col-md-3 col-panel">
            <div class="card card-hover-shadow" >
                <div class="card-body" style="padding: 1rem;">
                    <h6 class="card-subtitle metrics-tooltip" data-toggle="tooltip" data-placement="top" title="' . __("Средняя сумма одного заказа. Расчет: общий LTV / общее количество заказов") . '">
                        ' . __('Средний чек' . $group_by_text) . '
                    </h6>
                    <h2 class="card-title text-inherit" style="color: #b37cfc;">' . number_format($avg_check, 0, '', ' ') . ' <small>' . $currency . '</small></h2>
                    <div class="chartjs-custom" style="height: 4.5rem;">
                            <canvas id="js-chart-avg"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xs-6 col-sm-6 col-md-3 col-panel">
            <div class="card card-hover-shadow">
                <div class="card-body" style="padding: 1rem;">
                    <h6 class="card-subtitle metrics-tooltip"data-toggle="tooltip" data-placement="top" title="' . __("Процент клиентов, сделавших 2 и более заказов внутри каждого периода. Расчет: (клиенты с 2+ заказами в периоде / все клиенты периода) * 100%") . '">
                        ' . __('Лояльность' . $group_by_text) . '
                    </h6>
                    <h2 class="card-title text-inherit" style="color: #f0616e;">' . number_format($repeat_rate, 1) . ' <small>%</small></h2>
                        
                    <div class="chartjs-custom" style="height: 4.5rem;">
                            <canvas id="js-chart-repeat"></canvas>
                        </div>
                </div>
            </div>
        </div>
    </div>';

// === ДАННЫЕ ДЛЯ ГРАФИКОВ ===
    $pdo = $PHPShopBase->link_db;

// Определяем даты для графиков
    $graph_date_from = $date_from ?: date('d-m-Y', strtotime('-365 days'));
    $graph_date_to = $date_to ?: date('d-m-Y');

// === АВТОМАТИЧЕСКАЯ ГРУППИРОВКА ПО ПЕРИОДАМ ===
    $start = new DateTime($graph_date_from);
    $end = new DateTime($graph_date_to);
    $interval = $start->diff($end);
    $days_count = $interval->days;

// Выбираем период группировки
    if ($days_count > 180) {
        $group_by = 'MONTH';
    } elseif ($days_count > 30) {
        $group_by = 'WEEK';
    } else {
        $group_by = 'DAY';
    }

// === ФОРМИРУЕМ ПЕРИОДЫ ДЛЯ ГРАФИКОВ ===
    $all_dates = [];
    $chart_dates = [];

// ПРОВЕРЯЕМ ЧТО ДАТЫ НЕ БУДУЩИЕ
    $today = date('d-m-Y');
    $effective_date_to = min($graph_date_to, $today); // Не выходим за сегодня

    $start = new DateTime($graph_date_from);
    $end = new DateTime($effective_date_to); // Используем ограниченную дату
    $interval = $start->diff($end);
    $days_count = $interval->days;

// Выбираем период группировки
    if ($days_count > 180) {
        $group_by = 'MONTH';
    } elseif ($days_count > 30) {
        $group_by = 'WEEK';
    } else {
        $group_by = 'DAY';
    }

// ГЕНЕРАЦИЯ ПЕРИОДОВ С ЗАЩИТОЙ ОТ БУДУЩИХ ДАТ
    if ($group_by == 'MONTH') {
        $start = new DateTime($graph_date_from);
        $end = new DateTime($effective_date_to);
        $interval = new DateInterval('P1M');
        $period = new DatePeriod($start, $interval, $end);

        foreach ($period as $date) {
            $date_key = $date->format('m-Y');
            $all_dates[] = $date_key;
            $chart_dates[] = $date->format('m-Y');
        }
    } elseif ($group_by == 'WEEK') {
        $current = strtotime($graph_date_from);
        $end_time = strtotime($effective_date_to); // Ограничиваем сегодняшней датой

        while ($current <= $end_time) {
            $date_key = date('W-Y', $current);
            $all_dates[] = $date_key;
            $chart_dates[] = date('d.m', $current);
            $current = strtotime('+1 week', $current);
        }
    } else {
        // По дням с защитой от бесконечного цикла
        $current_date = $graph_date_from;
        $end_date = $effective_date_to;

        while ($current_date <= $end_date) {
            $all_dates[] = $current_date;
            $chart_dates[] = date('d.m', strtotime($current_date));
            $current_date = date('d-m-Y', strtotime($current_date . ' +1 day'));

            // ДОПОЛНИТЕЛЬНАЯ ЗАЩИТА: максимум 1000 итераций
            if (count($all_dates) > 1000) {
                break;
            }
        }
    }

// === ДИАГНОСТИКА ПЕРИОДОВ ===
    debug("=== PERIODS DEBUG ===");
    debug("Graph date range: $graph_date_from to $graph_date_to");
    debug("Group by: $group_by");
    debug("Generated periods count: " . count($all_dates));
    debug("First 5 periods: " . implode(", ", array_slice($all_dates, 0, 5)));
    debug("Last 5 periods: " . implode(", ", array_slice($all_dates, -5)));

// Проверяем соответствие дат заказов и периодов
    $matched_periods = [];
    foreach ($final_customers as $customer) {
        foreach ($customer['order_dates'] as $order_timestamp) {
            $order_date = date('d-m-Y', $order_timestamp);

            // Определяем период для этого заказа
            if ($group_by == 'MONTH') {
                $period_key = date('m-Y', $order_timestamp);
            } elseif ($group_by == 'WEEK') {
                $period_key = date('W-Y', $order_timestamp);
            } else {
                $period_key = date('d-m-Y', $order_timestamp);
            }

            if (in_array($period_key, $all_dates)) {
                $matched_periods[$period_key] = true;
            } else {
                debug("Order date $order_date -> period $period_key NOT FOUND in all_dates!");
            }
        }
    }
    debug("Matched periods count: " . count($matched_periods));
    debug("Matched periods: " . implode(", ", array_keys($matched_periods)));

// ? КОНЕЦ ДИАГНОСТИКИ 
// === SQL ЗАПРОСЫ С ГРУППИРОВКОЙ ===
// 1. График клиентов
// Инициализируем данные для графика
    $daily_customers_data = [];
    foreach ($final_customers as $customer) {
        foreach ($customer['order_dates'] as $order_timestamp) {
            // Определяем период
            if ($group_by == 'MONTH') {
                $period_key = date('m-Y', $order_timestamp);
            } elseif ($group_by == 'WEEK') {
                $period_key = date('W-Y', $order_timestamp);
            } else {
                $period_key = date('d-m-Y', $order_timestamp);
            }

            // СУММИРУЕМ КЛИЕНТОВ ПО ПЕРИОДАМ (как в LTV)
            if (!isset($daily_customers_data[$period_key])) {
                $daily_customers_data[$period_key] = 1;
            } else {
                $daily_customers_data[$period_key] += 1; // ? ИЗМЕНИТЬ: суммируем!
            }
        }
    }

// Затем формируем график (оставить как есть):
    $chart_customers = [];
    foreach ($all_dates as $date) {
        $chart_customers[] = isset($daily_customers_data[$date]) ? $daily_customers_data[$date] : 0;
    }

// ДИАГНОСТИКА
    debug("=== CUSTOMERS GRAPH FROM FINAL CUSTOMERS ===");
    debug("Final customers count: " . count($final_customers));
    debug("Chart data sample: " . implode(", ", array_slice($chart_customers, 0, 5)));

// 2. График LTV
// Инициализируем данные для графика
    $daily_ltv_data = [];

// Заполняем данными из final_customers
    foreach ($final_customers as $customer) {
        // Для каждого заказа клиента определяем период
        foreach ($customer['order_dates'] as $index => $order_timestamp) {

            // Определяем ключ периода в зависимости от группировки
            if ($group_by == 'MONTH') {
                $period_key = date('m-Y', $order_timestamp);
            } elseif ($group_by == 'WEEK') {
                $period_key = date('W-Y', $order_timestamp);
            } else {
                $period_key = date('d-m-Y', $order_timestamp);
            }

            // ИСПОЛЬЗУЕМ РЕАЛЬНЫЕ СУММЫ ЗАКАЗОВ
            if (isset($customer['order_amounts'][$index])) {
                $order_amount = $customer['order_amounts'][$index];
            } else {
                // Fallback: если нет отдельных сумм, используем средний чек
                $order_amount = $customer['ltv'] / count($customer['order_dates']);
            }

            // Суммируем LTV по периодам
            if (!isset($daily_ltv_data[$period_key])) {
                $daily_ltv_data[$period_key] = $order_amount;
            } else {
                $daily_ltv_data[$period_key] += $order_amount;
            }
        }
    }
// СФОРМИРУЙТЕ ФИНАЛЬНЫЕ МАССИВЫ ДЛЯ ГРАФИКА
    $chart_ltv = [];
    foreach ($all_dates as $date) {
        $chart_ltv[] = isset($daily_ltv_data[$date]) ? $daily_ltv_data[$date] : 0;
    }

// ДИАГНОСТИКА
    debug("=== LTV GRAPH FROM FINAL CUSTOMERS ===");
    debug("Final customers LTV total: " . array_sum(array_column($final_customers, 'ltv')));
    debug("Chart LTV data sample: " . implode(", ", array_slice($chart_ltv, 0, 5)));

// 3. График среднего чека
// === ДАННЫЕ ДЛЯ ГРАФИКА СРЕДНЕГО ЧЕКА ИЗ FINAL_CUSTOMERS ===
// Инициализируем данные для графика
    $daily_avg_data = [];

// Собираем статистику по периодам
    $period_stats = [];

    foreach ($final_customers as $customer) {
        // Для каждого заказа клиента определяем период
        foreach ($customer['order_dates'] as $order_timestamp) {

            // Определяем ключ периода в зависимости от группировки
            if ($group_by == 'MONTH') {
                $period_key = date('m-Y', $order_timestamp);
            } elseif ($group_by == 'WEEK') {
                $period_key = date('W-Y', $order_timestamp);
            } else {
                $period_key = date('d-m-Y', $order_timestamp);
            }

            // Распределяем LTV клиента по заказам
            $order_ltv = $customer['ltv'] / count($customer['order_dates']);

            // Собираем статистику по периодам
            if (!isset($period_stats[$period_key])) {
                $period_stats[$period_key] = [
                    'total_ltv' => $order_ltv,
                    'orders_count' => 1
                ];
            } else {
                $period_stats[$period_key]['total_ltv'] += $order_ltv;
                $period_stats[$period_key]['orders_count'] += 1;
            }
        }
    }

// Рассчитываем средний чек для каждого периода
    foreach ($period_stats as $period_key => $stats) {
        $daily_avg_data[$period_key] = round($stats['total_ltv'] / $stats['orders_count']);
    }

// СФОРМИРУЙТЕ ФИНАЛЬНЫЕ МАССИВЫ ДЛЯ ГРАФИКА
    $chart_avg = [];
    foreach ($all_dates as $date) {
        $chart_avg[] = isset($daily_avg_data[$date]) ? $daily_avg_data[$date] : 0;
    }

// ДИАГНОСТИКА
    debug("=== AVG CHECK GRAPH DEBUG ===");
    debug("Chart avg data sample: " . implode(", ", array_slice($chart_avg, 0, 5)));
    //debug("Chart avg average: " . (array_sum($chart_avg) / count(array_filter($chart_avg))));
    debug("Table avg check: " . $avg_check);

// 4. График лояльности 
// === ИСПРАВЛЕННЫЙ КОД ЛОЯЛЬНОСТИ ===
    $daily_repeat_data = [];
    $period_stats = [];

    foreach ($final_customers as $customer) {
        // Считаем заказы клиента ВНУТРИ каждого периода
        $customer_period_orders = [];

        foreach ($customer['order_dates'] as $order_timestamp) {
            // Определяем период заказа
            if ($group_by == 'MONTH') {
                $period_key = date('m-Y', $order_timestamp);
            } elseif ($group_by == 'WEEK') {
                $period_key = date('W-Y', $order_timestamp);
            } else {
                $period_key = date('d-m-Y', $order_timestamp);
            }

            // Считаем заказы клиента в каждом периоде
            if (!isset($customer_period_orders[$period_key])) {
                $customer_period_orders[$period_key] = 1;
            } else {
                $customer_period_orders[$period_key] ++;
            }
        }

        // Для каждого периода где был клиент
        foreach ($customer_period_orders as $period_key => $orders_in_period) {
            if (!isset($period_stats[$period_key])) {
                $period_stats[$period_key] = [
                    'total_customers' => 0,
                    'repeat_customers' => 0
                ];
            }

            $period_stats[$period_key]['total_customers'] ++;

            // Лояльный = 2+ заказа В ЭТОМ ПЕРИОДЕ
            if ($orders_in_period >= 2) {
                $period_stats[$period_key]['repeat_customers'] ++;
            }
        }
    }

// Расчет лояльности (оставить как есть)
    foreach ($period_stats as $period_key => $stats) {
        $repeat_rate = $stats['total_customers'] > 0 ?
                ($stats['repeat_customers'] / $stats['total_customers']) * 100 : 0;
        $daily_repeat_data[$period_key] = round($repeat_rate, 1);
    }

// СФОРМИРУЙТЕ ФИНАЛЬНЫЕ МАССИВЫ ДЛЯ ГРАФИКА
    $chart_repeat = [];
    foreach ($all_dates as $date) {
        $chart_repeat[] = isset($daily_repeat_data[$date]) ? $daily_repeat_data[$date] : 0;
    }

// ДИАГНОСТИКА
    debug("=== REPEAT RATE GRAPH FROM FINAL CUSTOMERS ===");
    debug("Final customers repeat rate: " . $repeat_rate . "%");
    debug("Chart repeat data sample: " . implode(", ", array_slice($chart_repeat, 0, 5)));


    // Данные для графиков
    $metrics_html .= $PHPShopInterface->setInputArg(array('type' => 'hidden', 'name' => 'chart_date', 'value' => json_encode($chart_dates)));
    $metrics_html .= $PHPShopInterface->setInputArg(array('type' => 'hidden', 'name' => 'chart_customers', 'value' => json_encode($chart_customers)));
    $metrics_html .= $PHPShopInterface->setInputArg(array('type' => 'hidden', 'name' => 'chart_ltv', 'value' => json_encode($chart_ltv)));
    $metrics_html .= $PHPShopInterface->setInputArg(array('type' => 'hidden', 'name' => 'chart_avg', 'value' => json_encode($chart_avg)));
    $metrics_html .= $PHPShopInterface->setInputArg(array('type' => 'hidden', 'name' => 'chart_repeat', 'value' => json_encode($chart_repeat)));


// === ОБЩАЯ ДИАГНОСТИКА ВСЕХ ГРАФИКОВ ===
    debug("=== ALL GRAPHS SUMMARY ===");
    debug("Customers graph: " . count(array_filter($chart_customers)) . " non-zero points");
    debug("LTV graph: " . count(array_filter($chart_ltv)) . " non-zero points, total: " . array_sum($chart_ltv));
    debug("Avg check graph: " . count(array_filter($chart_avg)) . " non-zero points");
    debug("Loyalty graph: " . count(array_filter($chart_repeat)) . " non-zero points");

    // Проверяем что данные есть
    if (array_sum($chart_customers) == 0) {
        debug("? CUSTOMERS GRAPH: ALL ZEROS!");
    }
    if (array_sum($chart_ltv) == 0) {
        debug("? LTV GRAPH: ALL ZEROS!");
    }
    if (array_sum($chart_avg) == 0) {
        debug("? AVG CHECK GRAPH: ALL ZEROS!");
    }
    if (array_sum($chart_repeat) == 0) {
        debug("? LOYALTY GRAPH: ALL ZEROS!");
    }

    return $metrics_html;
}

// Отладка
function debug($str) {
    //echo $str . '<br>';
}
