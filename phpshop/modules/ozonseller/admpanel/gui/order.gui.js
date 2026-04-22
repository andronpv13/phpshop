$().ready(function () {
    
    // Удалить с выбранными
    $("body").on('click', ".select-action .select-deactivation", function (event) {
        event.preventDefault();

        var chk = $('input[name="items"]:checkbox:checked').length;
        var i = 0;

        if (chk > 0) {

            $.MessageBox({
                buttonDone: "OK",
                buttonFail: locale.cancel,
                message: locale.confirm_wishlist_delete
            }).done(function () {

                $('input[name="items"]:checkbox:checked').each(function () {
                    var id = $(this).closest('.data-row');
                    $('.status_edit_' + $(this).attr('data-id')).ajaxSubmit({
                        dataType: "json",
                        success: function (json) {
                            if (json['success'] == 1) {
                                id.remove();
                                showAlertMessage(locale.save_done);
                                i++;
                            } else
                                showAlertMessage(locale.save_false, true);
                        }
                    });
                });
            })
        } else
            alert(locale.select_no);
    });

    // Остановить автоматизацмю
    $(".success-notification .close").on('click', function (event) {
        event.preventDefault();

        $.MessageBox({
            buttonDone: "OK",
            buttonFail: locale.cancel,
            message: $('#locale_ozon_stop_export').val()
        }).done(function () {

            var data = [];
            data.push({name: 'stop', value: 1});

            $.ajax({
                mimeType: 'text/html; charset=' + locale.charset,
                url: '../modules/ozonseller/admpanel/ajax/export.ajax.php',
                type: 'post',
                data: data,
                dataType: "json",
                success: function () {
                    $(window).unbind("beforeunload");
                    $('#stop').val(1);
                }
            });

        });
    });


    // Автоматизация экcпорта в OZON
    function auto_ozon_export(start, end, count) {

        var data = [];
        data.push({name: 'start', value: start});
        data.push({name: 'end', value: end});
        data.push({name: 'count', value: count});

        var stop = $('#stop').val();

        if (stop != 1) {
            $.ajax({
                mimeType: 'text/html; charset=' + locale.charset,
                url: '../modules/ozonseller/admpanel/ajax/export.ajax.php',
                type: 'post',
                data: data,
                dataType: "json",
                success: function (json) {
                    if (json['success'] == 'done') {
                        $('#play').trigger("play");
                        $(window).unbind("beforeunload");
                        $('.progress-bar').css('width', json['bar'] + '%').html(json['bar'] + '%').removeClass('active');

                        $.MessageBox({
                            buttonDone: "OK",
                            message: $('#locale_ozon_export_done').val().split('%').join(json['count'])
                        }).done(function () {
                            $('.success-notification').delay(500).fadeOut(1000);
                        });

                    } else if (json['success']) {
                        start = json['start'];
                        end = json['end'];
                        count = json['count'];
                        $('.progress-bar').css('width', json['bar'] + '%').html(json['bar'] + '%');
                        auto_ozon_export(start, end, count);
                    }

                }
            });
        }
    }

    // Запуск экcпорта в OZON
    $("body").on('click', ".ozon-export", function (event) {
        event.preventDefault();

        $.MessageBox({
            buttonDone: "OK",
            buttonFail: locale.cancel,
            message: $('#locale_ozon_start_export').val()
        }).done(function () {

            $(window).bind("beforeunload", function () {
                return "Are you sure you want to exit? Please complete sign up or the app will get deleted.";
            });

            showProgressBar($('#locale_ozon_export').val());
            $('.success-notification .close').removeClass('hide');
            auto_ozon_export(0, 0, 0);
        })
    });

    // Поиск категории
    $(".search_ozoncategory").on('input', function () {

        var words = $(this).val();
        var s = $(this);
        var set = s.attr('data-set');
        if (words.length > 2) {
            $.ajax({
                type: "POST",
                url: "?path=modules&id=ozonseller",
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
    $('body').on('click', '.select-search-ozon', function (event) {
        event.preventDefault();

        $('[name="category_ozonseller"]').val($(this).attr('data-name'));
        $('[name="category_ozonseller_new"]').val($(this).attr('data-id'));
        $('[data-toggle="popover"]').popover('hide');
    });

    $('[data-toggle="popover"]').popover({
        "html": true,
        "placement": "bottom",
        "template": '<div class="popover" role="tooltip" style="max-width:600px"><div class="arrow"></div><div class="popover-content"></div></div>'

    });

    // Выбрать все категории
    $("body").on('change', "#categories_all", function () {
        if (this.checked)
            $('[name="categories[]"]').selectpicker('selectAll');
        else
            $('[name="categories[]"]').selectpicker('deselectAll');
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
        window.location.replace('?path=modules.dir.ozonseller.order');
    });

});