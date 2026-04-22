$().ready(function () {

    // Экспорт данных
    $(".select-action .export").on('click', function (event) {
        event.preventDefault();

        var data = [];

        if ($("#export").length) {
            $(JSON.parse($("#export").attr('data-export'))).each(function (i, val) {
                data.push({name: 'select[' + val + ']', value: val});
            });
        }

        data.push({name: 'selectID', value: 1});
        data.push({name: 'ajax', value: 1});
        data.push({name: 'actionList[selectID]', value: 'actionSelect'});
        $.ajax({
            mimeType: 'text/html; charset=' + locale.charset,
            url: '?path=' + $("#export").attr('data-path'),
            type: 'post',
            data: data,
            dataType: "json",
            async: false,
            success: function () {
                window.location.href = '?path=' + $("#export").attr('data-path');
            }

        });

    });
    
    // Поиск заказа
    $(".btn-order-search").on('click', function() {
        $('#order_search').submit();
    });

    // Поиск заказа - очистка
    $(".btn-order-cancel").on('click', function () {
        window.location.replace('?path=modules.dir.adanalyzer.stat');
    });

    // datetimepicker
    if ($(".date").length) {
        $.fn.datetimepicker.dates['ru'] = locale;
        $(".date").datetimepicker({
            format: 'dd-mm-yyyy',
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

});