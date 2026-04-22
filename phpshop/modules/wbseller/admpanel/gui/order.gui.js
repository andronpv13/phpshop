$().ready(function () {

    // Поиск категории
    $(".search_wbcategory").on('input', function () {

        var words = $(this).val();
        var s = $(this);
        var set = s.attr('data-set');
        if (words.length > 3) {
            $.ajax({
                type: "POST",
                url: "?path=modules&id=wbseller",
                data: {
                    words: escape(words),
                    set: set,
                    ajax: 1,
                    selectID: 1,
                    'actionList[selectID]': 'actionCategorySearch'
                },
                success: function (data)

                {
                    // Результат поиска
                    if (data != '') {
                        s.attr('data-content', data);
                        s.popover('show');

                    } else {
                        s.popover('hide');

                    }
                }
            });

        } else {
            s.attr('data-content', '');
            s.popover('hide');
        }
    });

    // Закрыть поиск категории
    $('body').on('click', '.close', function (event) {
        event.preventDefault();
        $('[data-toggle="popover"]').popover('hide');
    });

    // Выбор в поиске категорию
    $('body').on('click', '.select-search-wb', function (event) {
        event.preventDefault();

        $('[name="category_wbseller_new"]').val($(this).attr('data-name'));
        $('[name="category_wbseller_id_new"]').val($(this).attr('data-id'));
        $('[data-toggle="popover"]').popover('hide');
    });

    $('[data-toggle="popover"]').popover({
        "html": true,
        "placement": "bottom",
        "template": '<div class="popover" role="tooltip" style="max-width:600px"><div class="arrow"></div><div class="popover-content"></div></div>'

    });

    // datetimepicker
    if ($(".date").length) {
        $.fn.datetimepicker.dates['ru'] = locale;
        $(".date").datetimepicker({
            format: 'yyyy-mm-dd',
            weekStart: 1,
            language: 'ru',
            todayBtn: 1,
            autoclose: 1,
            todayHighlight: 1,
            startView: 2,
            minView: 2,
            forceParse: 0
        });
    }

    // Поиск заказа
    $(".btn-order-search").on('click', function () {
        $('#order_search').submit();
    });

    // Поиск заказа - очистка
    $(".btn-order-cancel").on('click', function () {
        window.location.replace('?path=modules.dir.wbseller.order');
    });

});