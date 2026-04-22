$().ready(function () {

    // Остановить автоматизацмю
    $(".success-notification .close").on('click', function (event) {
        event.preventDefault();

        $.MessageBox({
            buttonDone: "OK",
            buttonFail: locale.cancel,
            message: $('#locale_vk_stop_export').val()
        }).done(function () {
            
            var data = [];
            data.push({name: 'stop', value: 1});

            $.ajax({
                mimeType: 'text/html; charset=' + locale.charset,
                url: '../modules/vkseller/admpanel/ajax/export.ajax.php',
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


    // Автоматизация экcпорта в VK
    function auto_vk_export(start, end, count) {

        var data = [];
        data.push({name: 'start', value: start});
        data.push({name: 'end', value: end});
        data.push({name: 'count', value: count});

        var stop = $('#stop').val();

        if (stop != 1) {
            $.ajax({
                mimeType: 'text/html; charset=' + locale.charset,
                url: '../modules/vkseller/admpanel/ajax/export.ajax.php',
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
                            message: $('#locale_vk_export_done').val().split('%').join(json['count'])
                        }).done(function () {
                            $('.success-notification').delay(500).fadeOut(1000);
                        });

                    } else if (json['success']) {
                        start = json['start'];
                        end = json['end'];
                        count = json['count'];
                        $('.progress-bar').css('width', json['bar'] + '%').html(json['bar'] + '%');
                        auto_vk_export(start, end, count);
                    }

                }
            });
        }
    }

    // Запуск экcпорта в VK
    $("body").on('click', ".vk-export", function (event) {
        event.preventDefault();

        $.MessageBox({
            buttonDone: "OK",
            buttonFail: locale.cancel,
            message: $('#locale_vk_start_export').val()
        }).done(function () {

            $(window).bind("beforeunload", function () {
                return "Are you sure you want to exit? Please complete sign up or the app will get deleted.";
            });

            showProgressBar($('#locale_vk_export').val());
            $('.success-notification .close').removeClass('hide');
            auto_vk_export(0, 0, 0);
        })
    });

    // OAuth-токен
    $("#client_token").on('click', function (event) {
        event.preventDefault();
        if ($('#client_id_new').val() !== '') {
            window.open($(this).attr('href') + $('#client_id_new').val());
        } else {
            $.MessageBox({
                buttonDone: "OK",
                message: locale.select_no + ' ID Application?'
            });
        }
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
        window.location.replace('?path=modules.dir.vkseller.order');
    });

});