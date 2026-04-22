// Переопределение функции
var STATUS_EVENT = true;
var TABLE_EVENT = true;

$().ready(function () {

    if (typeof ($.cookie('data_length')) == 'undefined')
        var data_length = [10, 25, 50];
    else
        var data_length = [parseInt($.cookie('data_length')), 10, 25, 50];

    var table = $('#data').dataTable({
        "lengthMenu": data_length,
        "paging": true,
        "ordering": true,
        "info": false,
        "language": locale.dataTable,
        "aaSorting": [],
        "columnDefs": [
            {"orderable": false, "targets": 0}
        ],
        "fnDrawCallback": function () {

            // Активация из списка dropdown
            $('.data-row').hover(
                    function () {
                        $(this).find('#dropdown_action').show();
                    },
                    function () {
                        $(this).find('#dropdown_action').hide();
                    });

            // Toggle
            $('.toggle-event').bootstrapToggle();

            // Инструкция из списка
            $(".data-row .manual").on('click', function (event) {
                event.preventDefault();
                window.open($(this).closest('.data-row').find('.modules-list > a').attr('data-wiki'));
            });

            // Установленные модули
            $('table#data tr').each(function (key, value) {
                if (key > 0) {
                    if ($(value).find('.install-date').html() != '') {
                        $(value).addClass('success');
                    }
                }
            });
        },

    });

    // Выключение из настроек модуля
    $(".select-action .off").on('click', function (event) {
        event.preventDefault();

        $.MessageBox({
            buttonDone: "OK",
            buttonFail: locale.cancel,
            message: locale.confirm_off
        }).done(function () {

            var data = [];
            data.push({action: 0, ajax: 1, id: 'button'});
            $.ajax({
                mimeType: 'text/html; charset=' + locale.charset,
                url: '?path=modules.action&id=' + $.getUrlVar('id'),
                type: 'post',
                data: data,
                dataType: "html",
                async: false,
                success: function () {
                    window.location.href = '?path=modules';
                }
            });
        })
    });

    // Выключение с выбранными модулями
    $(".select-action .module-off-select").on('click', function (event) {
        event.preventDefault();

        if ($('input:checkbox:checked').length) {

            $.MessageBox({
                buttonDone: "OK",
                buttonFail: locale.cancel,
                message: locale.confirm_off
            }).done(function () {

                $('input:checkbox:checked').each(function () {
                    var id = $(this).attr('data-id');
                    var parent = $(this).closest('.data-row');

                    // Снятие флага выделения
                    this.checked = !this.checked && !this.disabled;

                    $('.list_edit_' + id).append('<input type="hidden" name="action" value="0">');
                    $('.list_edit_' + id).ajaxSubmit({
                        success: function (json) {
                            var data = $.parseJSON(json);

                            parent.removeClass('success');
                            parent.find('.install-date').html(data['date']);

                            // Счетчик
                            var count = $('#mod-install-count').html();
                            if (count > 0)
                                count--;
                            $('#mod-install-count').html(count);

                            // Главное меню
                            $('#modules-menu').find('a[href="' + parent.find('.modules-list>a').attr('href') + '"]').parent('li').empty();

                            // Статус меню
                            parent.find('#dropdown_status_' + id).html(locale.off);
                            parent.find('.toggle-event').bootstrapToggle('back');

                            // Экшен меню
                            parent.find('.dropdown-menu .off').html('Включить <span class="glyphicon glyphicon-play"></span>');
                            parent.find('.dropdown-menu .off').attr('class', 'on');

                            // Меню настройка удаление
                            parent.find('.dropdown-menu .option').empty();

                            showAlertMessage(locale.module_done);
                        }
                    });
                });
            })
        } else
            alert(locale.select_no);
    });

    // Включение с выбранными модулями
    $(".select-action .module-on-select").on('click', function (event) {
        event.preventDefault();

        if ($('input:checkbox:checked').length) {

            $.MessageBox({
                buttonDone: "OK",
                buttonFail: locale.cancel,
                message: locale.confirm_on
            }).done(function () {

                $('input:checkbox:checked').each(function () {
                    var id = $(this).attr('data-id');
                    var parent = $(this).closest('.data-row');

                    // Снятие флага выделения
                    this.checked = !this.checked && !this.disabled;

                    $('.list_edit_' + id).append('<input type="hidden" name="action" value="1">');
                    $('.list_edit_' + id).ajaxSubmit({
                        success: function (json) {
                            var data = $.parseJSON(json);

                            parent.addClass('success');
                            parent.find('.install-date').html(data['date']);

                            // Счетчик
                            var count = $('#mod-install-count').html();
                            count++;
                            $('#mod-install-count').html(count);

                            // Главное меню
                            $('#modules-menu').append('<li><a href="' + parent.find('.modules-list>a').attr('href') + '">' + parent.find('.modules-list>a').html().split(/.\d+/).join('') + '</a></li>');

                            // Статус меню
                            parent.find('#dropdown_status_' + id).html(locale.on);
                            parent.find('.toggle-event').bootstrapToggle('back');

                            // Акшион меню
                            parent.find('.dropdown-menu .on').html('Выключить <span class="glyphicon glyphicon-stop"></span>');
                            parent.find('.dropdown-menu .on').attr('class', 'off');

                            // Меню настройка добавление
                            parent.find('.dropdown-menu .divider').before('<li><a href="' + parent.find('.modules-list>a').attr('href') + '">Настройки</a></li>');
                            showAlertMessage(locale.module_done);
                        }
                    });
                });
            })

        } else
            alert(locale.select_no);
    });

    // Быстрое изменение статуса модуля
    $('body').on('change', '.toggle-event', function () {

        var id = $(this).attr('data-id');
        var element = $(this);

        if ($(this).prop('checked') === true)
            var val = 1;
        else
            var val = 0;

        var caption = $(this).html();
        var parent = $(this).closest('.data-row');
        var message;

        if (val == 0)
            message = locale.confirm_off;
        else
            message = locale.confirm_on;

        $.MessageBox({
            buttonDone: "OK",
            buttonFail: locale.cancel,
            message: message
        }).fail(function () {
            element.bootstrapToggle('back');
        }).done(function () {

            $('.status_edit_' + id).append('<input type="hidden" name="action" value="' + val + '">');
            $('.status_edit_' + id).ajaxSubmit({
                success: function (json) {
                    var data = $.parseJSON(json);

                    $("#dropdown_status_" + id).html(caption);
                    showAlertMessage(locale.module_done);

                    var count = $('#mod-install-count').html();
                    if (val > 0)
                        count++;
                    else
                        count--;

                    $('#mod-install-count').html(count);

                    parent.toggleClass('success');
                    parent.find('.install-date').html(data['date']);

                    // Добавление/удаление меню модулю в основное меню
                    if (val == 1) {
                        $('#modules-menu').append('<li><a href="' + parent.find('.modules-list>a').attr('href') + '">' + parent.find('.modules-list>a').html().split(/.\d+/).join('') + ' <span class="text-muted glyphicon glyphicon-star"></span></a></li>');

                        parent.find('.dropdown-menu .on').html('Выключить <span class="glyphicon glyphicon-stop"></span>');
                        parent.find('.dropdown-menu .on').attr('class', 'off');

                        // Меню настройка добавление
                        parent.find('.dropdown-menu .divider').before('<li><a href="' + parent.find('.modules-list>a').attr('href') + '">Настройки</a></li>');
                    } else {
                        $('#modules-menu').find('a[href="' + parent.find('.modules-list>a').attr('href') + '"]').parent('li').empty();

                        parent.find('.dropdown-menu .off').html('Включить <span class="glyphicon glyphicon-play"></span>');
                        parent.find('.dropdown-menu .off').attr('class', 'on');

                        // Меню настройка удаление
                        parent.find('.dropdown-menu .option').empty();
                    }

                }
            });
        });
    });

    // Управление модулями из списка dropmenu
    $("body").on('click', ".data-row #dropdown_action .on", function (event) {
        event.preventDefault();
        var parent = $(this).closest('.data-row');
        var id = $(this);
        var data_id = $(this).attr('data-id');

        $.MessageBox({
            buttonDone: "OK",
            buttonFail: locale.cancel,
            message: locale.confirm_on
        }).done(function () {

            $('.list_edit_' + data_id).append('<input type="hidden" name="action" value="1">');
            $('.list_edit_' + data_id).ajaxSubmit({
                success: function (json) {

                    var data = $.parseJSON(json);

                    if (data['success'] == 1) {

                        parent.toggleClass('success');

                        id.html('Выключить <span class="glyphicon glyphicon-stop"></span>');
                        id.attr('class', 'off');

                        var count = $('#mod-install-count').html();

                        count++;
                        $('#mod-install-count').html(count);

                        parent.find('.install-date').html(data['date']);

                        // Меню настройка добавление
                        id.closest('ul').find('.divider').before('<li><a href="' + parent.find('.modules-list>a').attr('href') + '">Настройки</a></li>');
                        // Главное меню
                        $('#modules-menu').append('<li><a href="' + parent.find('.modules-list>a').attr('href') + '">' + parent.find('.modules-list>a').html().split(/.\d+/).join('') + ' <span class="text-muted glyphicon glyphicon-star"></span></a></li>');

                        // Статус меню
                        parent.find('#dropdown_status_' + id.attr('data-id')).html('Вкл.');
                        parent.find('.toggle-event').bootstrapToggle('back');

                        showAlertMessage(locale.module_done);
                    } else
                        showAlertMessage(locale.save_false, true);
                }
            });
        })

    });

    $("body").on('click', ".data-row #dropdown_action .off", function (event) {
        event.preventDefault();
        var parent = $(this).closest('.data-row');
        var id = $(this);
        var data_id = $(this).attr('data-id');

        $.MessageBox({
            buttonDone: "OK",
            buttonFail: locale.cancel,
            message: locale.confirm_on
        }).done(function () {

            $('.list_edit_' + data_id).append('<input type="hidden" name="action" value="0">');
            $('.list_edit_' + data_id).ajaxSubmit({
                success: function (json) {
                    parent.toggleClass('success');

                    id.html('Включить <span class="glyphicon glyphicon-play"></span>');
                    id.attr('class', 'on');

                    var count = $('#mod-install-count').html();
                    var data = $.parseJSON(json);
                    count--;
                    $('#mod-install-count').html(count);

                    parent.find('.install-date').html(data['date']);

                    // Меню настройка удаление
                    id.closest('ul').find('.option').empty();

                    // Главное меню
                    $('#modules-menu').find('a[href="' + parent.find('.modules-list>a').attr('href') + '"]').parent('li').empty();

                    // Статус меню
                    parent.find('#dropdown_status_' + id.attr('data-id')).html('<span class="text-muted">Выкл</span>');
                    parent.find('.toggle-event').bootstrapToggle('back');

                    showAlertMessage(locale.module_done);
                }
            });
        })


    });

    // Иконки оформления меню
    $(".data-row .dropdown-menu .off, .select-action .off").append(' <span class="glyphicon glyphicon-stop"></span>');
    $(".data-row .dropdown-menu .on").append(' <span class="glyphicon glyphicon-play"></span>');

    // Установленные модули
    $('table#data tr').each(function (key, value) {
        if (key > 0) {
            if ($(value).find('.install-date').html() != '') {
                $(value).addClass('success');
            }
        }
    });

    // Ссылка на модуль или инструкцию
    $("body").on('click', ".modules-list > a", function (event) {
        event.preventDefault();
        if ($(this).closest('.data-row').find('.install-date').html() == '') {
            if ($(this).attr('data-wiki') != "")
                window.open($(this).attr('data-wiki'));
        } else
            window.location.href = $(this).attr('href');
    });

    // Настройка из списка
    $(".data-row .option").on('click', function (event) {
        event.preventDefault();
        window.location.href = $(this).closest('.data-row').find('.modules-list > a').attr('href');
    });

    // Выделение текущей категории
    if (typeof modcat != 'undefined')
        $('.treegrid-' + modcat).addClass('treegrid-active');

    // Дерево категорий
    if (typeof (TREEGRID_LOAD) != 'undefined')
        $('.tree').treegrid({
            saveState: true,
            expanderExpandedClass: 'glyphicon glyphicon-triangle-bottom',
            expanderCollapsedClass: 'glyphicon glyphicon-triangle-right'
        });
});