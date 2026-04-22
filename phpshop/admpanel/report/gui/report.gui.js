

var lineChartData = {
    datasets: [
        {
            label: "Dataset",
            fillColor: "rgba(151,187,205,0.2)",
            strokeColor: "rgba(151,187,205,1)",
            pointColor: "rgba(151,187,205,1)",
            pointStrokeColor: "#fff",
            pointHighlightFill: "#fff",
            pointHighlightStroke: "rgba(151,187,205,1)"
        }
    ]
};

$().ready(function() {


    // Экспорт данных
    $(".select-action .export").on('click', function(event) {
        event.preventDefault();

        var data = [];

        if ($("#export").length) {
            $(JSON.parse($("#export").attr('data-export'))).each(function(i, val) {
                data.push({name: 'select[' + val + ']', value: val});
            });
        }

        data.push({name: 'selectID', value: 1});
        data.push({name: 'ajax', value: 1});
        data.push({name: 'actionList[selectID]', value: 'actionSelect'});
        $.ajax({
            mimeType: 'text/html; charset='+locale.charset,
            url: '?path=' + $("#export").attr('data-path'),
            type: 'post',
            data: data,
            dataType: "json",
            async: false,
            success: function() {
                window.location.href = '?path=' + $("#export").attr('data-path');
            }

        });

    });

    // Поиск заказа
    $(".btn-order-search").on('click', function() {
        $('#order_search').submit();
    });

    // Поиск заказа - очистка
    $(".btn-order-cancel").on('click', function() {
        window.location.replace('?path=report.statorder');
    });
    
    // Поиск товаров - очистка
    $(".btn-product-cancel").on('click', function() {
        window.location.replace('?path=report.statproduct');
    });

    // datetimepicker
    if ($(".date").length) {
        $.fn.datetimepicker.dates['ru'] = locale;
        $(".date").datetimepicker({
            format: 'dd-mm-yyyy',
            pickerPosition:'bottom-left',
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


    // Круговая диаграмма
    if ($('#chart-area').length) {

        var pieData = [];

        $('#data-value > span').each(function() {
            eval($(this).attr('data-value'));
        });

        eval($("#test").attr('data-value'));
        var ctx = $("#chart-area").get(0).getContext("2d");
        pieChart = new Chart(ctx).Pie(pieData, {
            animation: false,
            responsive: true
        });

        $('.progress').toggle();
    }

    // Линейный график
    if ($('#canvas').length) {
        lineChartData.datasets[0].data = JSON.parse($("#canvas").attr('data-value'));
        lineChartData.labels = JSON.parse($("#canvas").attr('data-label'));
        var currency = $("#canvas").attr('data-currency');

        var ctx = $("#canvas").get(0).getContext("2d");
        lineChart = new Chart(ctx).Line(lineChartData, {
            animation: false,
            responsive: true,
            tooltipTemplate: "<%if (label){%><%=label%>: <%}%><%= value %> " + currency
        });
        $('.progress').toggle();
    }

    $('.canvas-bar').on('click', function(event) {
        event.preventDefault();
        lineChart.destroy();
        $('.progress').toggle();

        lineChart = new Chart(ctx).Bar(lineChartData, {
            animation: false,
            responsive: true,
            tooltipTemplate: "<%if (label){%><%=label%>: <%}%><%= value %> " + currency
        });

        $('ul.select-action > li').removeClass('disabled');
        $(this).parent('li').addClass('disabled');
        $('.progress').toggle();
    });


    $('.canvas-line').on('click', function(event) {
        event.preventDefault();
        lineChart.destroy();
        $('.progress').toggle();

        lineChart = new Chart(ctx).Line(lineChartData, {
            animation: false,
            responsive: true,
            tooltipTemplate: "<%if (label){%><%=label%>: <%}%><%= value %> " + currency
        });

        $('ul.select-action > li').removeClass('disabled');
        $(this).parent('li').addClass('disabled');
        $('.progress').toggle();
    });

    $('.canvas-radar').on('click', function(event) {
        event.preventDefault();
        lineChart.destroy();
        $('.progress').toggle();

        lineChart = new Chart(ctx).Radar(lineChartData, {
            animation: false,
            responsive: true,
            tooltipTemplate: "<%if (label){%><%=label%>: <%}%><%= value %> " + currency
        });

        $('ul.select-action > li').removeClass('disabled');
        $(this).parent('li').addClass('disabled');
        $('.progress').toggle();
    });

    // Добавление в переадресацию с отмеченными
    $('.select-action .add-search-base').on('click', function(event) {
        event.preventDefault();
        var name = new String;
        if ($('input:checkbox:checked').length) {

            $('input:checkbox:checked').each(function() {
                var add = $(this).closest('.data-row').find('td:nth-child(2)>a').html();
                name += add + ',';
            });
            window.location.href = '?path=report.searchreplace&action=new&data[name]=' + name.substring(0, (name.length - 1));
        }
        else
            alert(locale.select_no);
    });

    // Добавление в переадресацию из списка
    $(".data-row .add-search-base").on('click', function(event) {
        event.preventDefault();
        window.location.href = '?path=report.searchreplace&action=new&data[name]=' + $(this).closest('.data-row').find('td:nth-child(2)>a').html();
    });


    // Указать ID товара в виде тега - Поиск
    $("body").on('click', "#selectModal .search-action", function(event) {
        event.preventDefault();

        var data = [];
        data.push({name: 'selectID', value: 2});
        data.push({name: 'ajax', value: 1});
        data.push({name: 'actionList[selectID]', value: 'actionSearch'});

        $.ajax({
            mimeType: 'text/html; charset='+locale.charset,
            url: '?path=catalog.search&words=' + escape($('input:text[name=search_name]').val()) + '&cat=' + $('select[name=search_category]').val() + '&price_start=' + $('input:text[name=search_price_start]').val() + '&price_end=' + $('input:text[name=search_price_end]').val(),
            type: 'post',
            data: data,
            dataType: "html",
            async: false,
            success: function(data) {
                $('#selectModal .modal-body').html(data);

            }

        });
    });

    // Указать ID товара в виде тега  -  2 шаг
    $("body").on('click', "#selectModal .modal-footer .id-add-send", function(event) {
        event.preventDefault();

        $('.search-list input:checkbox:checked').each(function() {
            var id = $(this).attr('data-id');
            if ($('#uid_new').tagExist(id))
            {
                this.disabled;
            }
            else
                $(selectTarget).addTag(id);
        });

        $('#selectModal').modal('hide');
    });


    // Выбор элемента по клику в модальном окне подбора товара
    $('body').on('click', ".search-list  td", function() {
        $(this).parent('tr').find('input:checkbox[name=items]').each(function() {
            this.checked = !this.checked && !this.disabled;
        });
    });



    // Указать ID товара в виде тега  - 1 шаг
    $(".tag-search").on('click', function(event) {
        event.preventDefault();

        selectTarget = $(this).attr('data-target');

        var data = [];
        data.push({name: 'selectID', value: 2});
        data.push({name: 'ajax', value: 1});
        data.push({name: 'currentID', value: $(selectTarget).val()});
        data.push({name: 'actionList[selectID]', value: 'actionSearch'});

        $.ajax({
            mimeType: 'text/html; charset='+locale.charset,
            url: '?path=catalog.search',
            type: 'post',
            data: data,
            dataType: "html",
            async: false,
            success: function(data) {
                //$('#selectModal .modal-dialog').removeClass('modal-lg');
                $('#selectModal .modal-title').html(locale.add_cart_value);
                $('#selectModal .modal-footer .btn-primary').removeClass('edit-select-send');
                $('#selectModal .modal-footer .btn-primary').addClass('id-add-send');
                $('#selectModal .modal-footer .btn-delete').addClass('hidden');
                $('#selectModal .modal-body').css('max-height', ($(window).height() - 200) + 'px');
                $('#selectModal .modal-body').css('overflow-y', 'auto');
                $('#selectModal .modal-body').html(data);

                $(".search-list td input:checkbox").each(function() {
                    this.checked = true;
                });

                $('#selectModal').modal('show');

            }

        });
    });

    if($('#uid_new').length)
    $('#uid_new').tagsInput({
        'height': '100px',
        'width': '100%',
        'interactive': true,
        'defaultText': locale.enter,
        'removeWithBackspace': true,
        'minChars': 0,
        'maxChars': 0, // if not provided there is no limit
        'placeholderColor': '#666666'
    });


});