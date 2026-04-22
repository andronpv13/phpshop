// Переопределение функции
var TABLE_EVENT = true;

function initChart(canvasId, data, color, title, postfix) {
    var canvas = document.getElementById(canvasId);
    if (!canvas)
        return;

    var ctx = canvas.getContext('2d');
    new Chart2(ctx, {
        type: "line",
        data: {
            labels: JSON.parse($("#chart_date").val()),
            datasets: [{
                    data: data,
                    borderColor: color,
                    borderWidth: 2,
                    pointRadius: 2,
                    pointHoverRadius: 4,
                    pointBackgroundColor: "transparent",
                    pointHoverBackgroundColor: color,
                    pointBorderColor: "transparent",
                    pointHoverBorderColor: "#fff",
                    pointBorderWidth: 2,
                    tension: 0.4,
                    fill: false
                }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {display: false},
                x: {display: false}
            },
            plugins: {
                legend: {display: false},
                tooltip: {
                    enabled: true,
                    mode: "index",
                    intersect: false,
                    backgroundColor: "rgba(0, 0, 0, 0.8)",
                    titleColor: "#fff",
                    bodyColor: "#fff",
                    borderColor: color,
                    borderWidth: 1,
                    cornerRadius: 4,
                    padding: 8,
                    displayColors: false,
                    callbacks: {
                        title: function (tooltipItems) {
                            return tooltipItems[0].label;
                        },
                        label: function (context) {
                            var value = context.parsed.y;
                            var formattedValue = new Intl.NumberFormat("ru-RU").format(value);
                            return title + ": " + formattedValue + postfix;
                        }
                    }
                }
            },
            hover: {
                mode: "nearest",
                intersect: false
            }
        }
    });
}

$().ready(function () {

    // Копировать E-mail с выбранными
    $(".select-action .copy-mail-select").on('click', function (event) {
        event.preventDefault();

        if ($('#data input[name="items"]:checkbox:checked').length) {
            var data = '';
            $('#data input[name="items"]:checkbox:checked').each(function () {
                if (this.value != 'all')
                    data += $(this).attr('data-select') + ',';
            });

            var $tmp = $("<textarea>");
            $("body").append($tmp);
            $tmp.val(data.substring(0, data.length - 1)).select();
            document.execCommand("copy");
            $tmp.remove();

            $.MessageBox({
                buttonDone: locale.close,
                message: locale.copy
            });


        } else
            alert(locale.select_no);
    });

    // datetimepicker
    if ($(".date").length) {
        $.fn.datetimepicker.dates['ru'] = locale;
        $(".date").datetimepicker({
            format: 'dd-mm-yyyy', // ? ИЗМЕНИТЬ ЗДЕСЬ
            pickerPosition: 'bottom-left',
            language: 'ru',
            weekStart: 1,
            todayBtn: 1,
            autoclose: 1,
            todayHighlight: 1,
            startView: 2,
            minView: 2,
            forceParse: 0
        });
    }
    // Поиск заказа
    $(".btn-report-search").on('click', function () {
        $('#report_search').submit();
    });

    // Поиск - очистка
    $(".btn-report-cancel").on('click', function () {
        window.location.replace('?reset=true&path=' + $(this).attr('data-option'));
    });

    // Графики товары
    if ($("#chart_gmv").length) {
        initChart("js-chart-gmv", JSON.parse($("#chart_gmv").val()), "#377dff", "GMV", locale.analytics.currency);
        initChart("js-chart-products", JSON.parse($("#chart_products").val()), "#28a745", locale.analytics.products, "");
        initChart("js-chart-sales", JSON.parse($("#chart_sales").val()), "#b37cfc", locale.analytics.sales, locale.analytics.unit);
        initChart("js-chart-avgprice", JSON.parse($("#chart_avgprice").val()), "#f0616e", locale.analytics.avgprice, locale.analytics.currency);
    }

// Графики клиенты
    if ($("#chart_customers").length) {
        initChart("js-chart-customers", JSON.parse($("#chart_customers").val()), "#377dff", locale.analytics.customers, locale.analytics.customers_unit);
        initChart("js-chart-ltv", JSON.parse($("#chart_ltv").val()), "#28a745", "LTV", " \u20BD");
        initChart("js-chart-avg", JSON.parse($("#chart_avg").val()), "#b37cfc", locale.analytics.avg, " \u20BD");
        initChart("js-chart-repeat", JSON.parse($("#chart_repeat").val()), "#f0616e", locale.analytics.repeat, "%");
    }



    // Находим таблицу аналитики
    var table = $("#data").addClass('table-responsive');

 /*
    // Обертываем таблицу в контейнер со скроллом
    table.wrap("<div class=\"table-analytics-scroll-container\" style=\"overflow-x: auto; width: 100%; \"></div>");

    // Принудительно задаем минимальную ширину таблице
    table.css({
        "min-width": "1400px",
        "width": "auto",
        "table-layout": "fixed"
    });

    // Применяем ширины колонок
   
     table.find("th:nth-child(1)").css("width", "1%");
     table.find("th:nth-child(2)").css("width", "4%");
     table.find("th:nth-child(3)").css("width", "4%");
     table.find("th:nth-child(4)").css("width", "6%");
     table.find("th:nth-child(5)").css("width", "6%");
     table.find("th:nth-child(6)").css("width", "6%");
     table.find("th:nth-child(7)").css("width", "6%");
     table.find("th:nth-child(8)").css("width", "6%");
     table.find("th:nth-child(9)").css("width", "6%");
     table.find("th:nth-child(10)").css("width", "6%");
     table.find("th:nth-child(11)").css("width", "6%");
     table.find("th:nth-child(11)").css("width", "25%");
     table.find("td:nth-child(5)").css("padding-left", "27px");
     table.find("td:nth-child(8)").css("padding-left", "15px");*/

// Отмена выравнивания для последней колонки (Товары)
    table.find("td:last-child, th:last-child").css("text-align", "left");

    $('.metrics-tooltip').tooltip();

    // Умная маска для дат
    $('input[name="date_from"], input[name="date_to"]').on('input', function () {
        var value = $(this).val().replace(/[^0-9]/g, '');

        if (value.length === 4) {
            var currentYear = new Date().getFullYear().toString();
            value = value + currentYear;
        }

        if (value.length >= 2) {
            value = value.substring(0, 2) + '-' + value.substring(2);
        }
        if (value.length >= 5) {
            value = value.substring(0, 5) + '-' + value.substring(5);
        }
        if (value.length > 10) {
            value = value.substring(0, 10);
        }

        $(this).val(value);
    });

    // Добавляем НЕРАЗРЫВНЫЕ пробелы для разделения тысяч
    $(".number-format").each(function () {
        var text = $(this).text();
        var formatted = text.replace(/\B(?=(\d{3})+(?!\d))/g, "&nbsp;");
        $(this).html(formatted);
    });

// === ПРОСТОЙ СКРОЛЛ ДЛЯ ПОКАЗА ===
    setTimeout(function () {
        var container = table.parent();
        if (container[0].scrollWidth > container[0].clientWidth) {
            container[0].scrollLeft = 100;
            setTimeout(function () {
                container[0].scrollLeft = 0;
            }, 800);
        }
    }, 1000);


// Таблица сортировки
    var table = $('#data').dataTable({
        "lengthMenu": [50, 100, 150],
        "paging": true,
        "ordering": true,
        "info": false,
        "searching": false,
        "language": locale.dataTable,
    });
});