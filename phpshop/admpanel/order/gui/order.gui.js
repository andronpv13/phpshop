// Переопределение функции
var TABLE_EVENT = true;
var ajax_path = "./order/ajax/";

$().ready(function () {
    
    // Копировать IE-mail с выбранными
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

    // Поиск пользователя
    $(".search_user").on('input', function () {

        var words = $(this).val();
        var s = $(this);
        var set = s.attr('data-set');
        if (words.length > 3) {
            $.ajax({
                type: "POST",
                url: "?path=shopusers",
                data: {
                    words: escape(words),
                    set: set,
                    ajax: 1,
                    selectID: 1,
                    'actionList[selectID]': 'actionOrderSearch'
                },
                success: function (data)

                {
                    // Результат поиска
                    if (data != '') {
                        s.attr('data-content', data);
                        s.popover('show');

                        // Отключение DADATA
                        if ($('#body').attr('data-token') != "")
                            $("[name='fio_new']").suggestions().disable();
                    } else {
                        s.popover('hide');

                        // Включение DADATA
                        if ($('#body').attr('data-token') != "")
                            $("[name='fio_new']").suggestions().enable();
                    }
                }
            });

        } else {
            s.attr('data-content', '');
            s.popover('hide');
        }
    });

    // Закрыть поиск пользователя
    $('body').on('click', '.close', function (event) {
        event.preventDefault();
        $('[data-toggle="popover"]').popover('hide');
    });

    // Выбор в поиске пользователя
    $('body').on('click', '.select-search', function (event) {
        event.preventDefault();

        $('[name="user_search"]').val($(this).text());
        $('[name="user_new"]').val($(this).attr('data-id'));
        $('[name="fio_new"]').val($(this).attr('data-name'));
        $('[name="tel_new"]').val($(this).attr('data-tel'));
        $('[name="person[mail]"]').val($(this).attr('data-mail'));
        $('[data-toggle="popover"]').popover('hide');
    });

    $('[data-toggle="popover"]').popover({
        "html": true,
        "placement": "bottom",
        "template": '<div class="popover" role="tooltip" style="max-width:600px"><div class="arrow"></div><div class="popover-content"></div></div>'

    });

    // Напоминание об оплате
    $(".order-reminder").on('click', function (event) {
        event.preventDefault();

        $.MessageBox({
            buttonDone: "OK",
            buttonFail: locale.cancel,
            message: locale.confirm_reminder
        }).done(function () {

            var data = [];
            var order_id = $('#footer input[name=rowID]').val();
            data.push({name: 'selectID', value: 1});
            data.push({name: 'ajax', value: 1});
            data.push({name: 'actionList[selectID]', value: 'actionReminder'});
            $.ajax({
                mimeType: 'text/html; charset=' + locale.charset,
                url: '?path=order&id=' + order_id,
                type: 'post',
                data: data,
                dataType: "json",
                async: false,
                success: function () {
                    showAlertMessage(locale.save_done);
                }

            });
        })
    });

    // Добавить файл товара - 2 шаг
    $("body").on('click', "#selectModal .modal-footer .file-add-send", function (event) {
        event.preventDefault();
        var id = parseInt($('input[name=fileCount]').val());
        $('.file-list').append('<tr class="data-row" data-row="' + id + '"><td class="file-edit"><a href="' + $('input[name=lfile]').val() + '" class="file-edit"></a></td><td><input class="hidden-edit " value="" name="files_new[' + id + '][path]" type="hidden"><input class="hidden-edit" value="" name="files_new[' + id + '][name]" type="hidden"></td><td style="text-align:right" class="file-edit-path"><a href="' + $('input[name=lfile]').val() + '" class="file-edit-path" target="_blank"></a></td></tr>');
        $('.file-list [data-row="' + id + '"] .file-edit > a').html($('input[name=modal_file_name]').val());
        $('.file-list [data-row="' + id + '"] input[name="files_new[' + id + '][name]"]').val($('input[name=modal_file_name]').val());
        $('.file-list [data-row="' + id + '"] .file-edit-path > a').html('<span class="glyphicon glyphicon-floppy-disk"></span>' + $('input[name=lfile]').val());
        $('.file-list [data-row="' + id + '"] input[name="files_new[' + id + '][path]"]').val($('input[name=lfile]').val());
        $('.file-add').attr('data-count', id);
        $('#selectModal .modal-footer .btn-primary').removeClass('file-add-send');
        $('#selectModal').modal('hide');
    });

    // Добавить файл товара - 1 шаг
    $(".file-add").on('click', function (event) {
        event.preventDefault();

        var data = [];
        var id = $(this).closest('.data-row').attr('data-row');
        data.push({name: 'fileID', value: id});
        data.push({name: 'ajax', value: 1});
        data.push({name: 'fileCount', value: $(this).attr('data-count')});
        data.push({name: 'actionList[fileID]', value: 'actionFileEdit'});

        $.ajax({
            mimeType: 'text/html; charset=' + locale.charset,
            url: '?path=product&id=file' + '&name=' + escape('File'),
            type: 'post',
            data: data,
            dataType: "html",
            async: false,
            success: function (data) {
                $('#selectModal .modal-dialog').removeClass('modal-lg');
                $('#selectModal .modal-title').html(locale.file_add);
                $('#selectModal .modal-footer .btn-primary').removeClass('edit-select-send');
                $('#selectModal .modal-footer .btn-primary').addClass('file-add-send');
                $('#selectModal .modal-footer .btn-delete').addClass('hidden');
                $('#selectModal .modal-footer .btn-delete').addClass('file-delete');
                $('#selectModal .modal-body').html(data);

                $('.elfinder-modal-content').attr('data-option', 'return=lfile');
                $('#selectModal').modal('show');
            }
        });
    });

    // Удаление файла товара
    $("body").on('click', "#selectModal .modal-footer .file-delete", function (event) {
        event.preventDefault();
        var id = $('input[name=fileID]').val();

        $.MessageBox({
            buttonDone: "OK",
            buttonFail: locale.cancel,
            message: locale.confirm_delete
        }).done(function () {
            $('.file-list [data-row="' + id + '"]').remove();
            $('#selectModal').modal('hide');
        });

    });

    // Редактировать файл товара - 2 шаг
    $("body").on('click', "#selectModal .modal-footer .file-edit-send", function (event) {
        event.preventDefault();
        var id = $('input[name=fileID]').val();


        var name = $('input[name=modal_file_name]').val();
        $('.file-list [data-row="' + id + '"] .file-edit > a').html(name);
        $('.file-list [data-row="' + id + '"] input[name="files_new[' + id + '][name]"]').val(name);
        $('.file-list [data-row="' + id + '"] .file-edit-path > a').html('<span class="glyphicon glyphicon-floppy-disk"></span>' + $('input[name=lfile]').val());
        $('.file-list [data-row="' + id + '"] input[name="files_new[' + id + '][path]"]').val($('input[name=lfile]').val());
        $('#selectModal').modal('hide');

    });

    // Редактировать файл товара
    $("body").on('click', ".data-row .file-edit > a", function (event) {
        event.preventDefault();

        var data = [];
        var id = $(this).closest('.data-row').attr('data-row');
        data.push({name: 'fileID', value: id});
        data.push({name: 'ajax', value: 1});
        data.push({name: 'actionList[fileID]', value: 'actionFileEdit'});
        var name = $(this).html();

        $.ajax({
            mimeType: 'text/html; charset=' + locale.charset,
            url: '?path=product&id=file&file=' + $(this).attr('href') + '&name=' + escape(name),
            type: 'post',
            data: data,
            dataType: "html",
            async: false,
            success: function (data) {
                $('#selectModal .modal-dialog').removeClass('modal-lg');
                $('#selectModal .modal-title').html(locale.file_edit + ': ' + name);
                $('#selectModal .modal-footer .btn-primary').removeClass('edit-select-send');
                $('#selectModal .modal-footer .btn-primary').addClass('file-edit-send');
                $('#selectModal .modal-footer .btn-delete').removeClass('hidden');
                $('#selectModal .modal-footer .btn-delete').addClass('file-delete');
                $('#selectModal .modal-body').html(data);

                $('.elfinder-modal-content').attr('data-option', 'return=lfile');
                $('#selectModal').modal('show');
            }
        });
    });

    // Настройка полей - 2 шаг
    $("body").on('click', "#selectModal .modal-footer .option-send", function (event) {
        event.preventDefault();

        if ($('#selectModal input:checkbox:checked').length) {
            var data = [];
            $('#selectModal input:checkbox:checked').each(function () {
                data.push({name: 'option[' + $(this).attr('name') + ']', value: $(this).val()});

            });

            data.push({name: 'selectID', value: 1});
            data.push({name: 'ajax', value: 1});
            data.push({name: 'actionList[selectID]', value: 'actionOptionSave'});
            $.ajax({
                mimeType: 'text/html; charset=' + locale.charset,
                url: '?path=order.select',
                type: 'post',
                data: data,
                dataType: "json",
                async: false,
                success: function () {
                    window.location.reload();
                }

            });
        } else
            alert(locale.select_no);
    });

    // Настройка полей - 1 шаг
    $(".option").on('click', function (event) {
        event.preventDefault();

        var data = [];
        data.push({name: 'selectID', value: 1});
        data.push({name: 'ajax', value: 1});
        data.push({name: 'actionList[selectID]', value: 'actionOption'});

        $.ajax({
            mimeType: 'text/html; charset=' + locale.charset,
            url: '?path=order.select',
            type: 'post',
            data: data,
            dataType: "html",
            async: false,
            success: function (data) {
                $('#selectModal .modal-dialog').removeClass('modal-lg');
                $('#selectModal .modal-title').html(locale.option_title);
                $('#selectModal .modal-footer .btn-primary').addClass('option-send');
                $('#selectModal .modal-footer .btn-delete').addClass('hidden');
                $('#selectModal .modal-body').html(data);
                $('#selectModal').modal('show');
            }
        });
    });

    // Обзор товара в корзине
    $('body').on('click', ".media-heading > a", function (event) {
        if ($('.bar-tab').is(":visible")) {
            event.preventDefault();
            $(this).closest(".data-row").find(".cart-value-edit").click();

        }
    });

    // Редактировать с выбранными - 2 шаг
    $("body").on('click', "#selectModal .modal-footer .edit-select-send", function (event) {
        event.preventDefault();

        if ($('#selectModal input:checkbox:checked').length) {
            var data = [];
            $('#selectModal input:checkbox:checked').each(function () {
                data.push({name: 'select_col[' + $(this).attr('name') + ']', value: $(this).attr('name')});

            });

            data.push({name: 'selectID', value: 1});
            data.push({name: 'ajax', value: 1});
            data.push({name: 'actionList[selectID]', value: 'actionSelectEdit'});
            $.ajax({
                mimeType: 'text/html; charset=' + locale.charset,
                url: '?path=order.select',
                type: 'post',
                data: data,
                dataType: "json",
                async: false,
                success: function () {
                    window.location.href = '?path=order.select';
                }

            });
        } else
            alert(locale.select_no);
    });

    // Cнять выделения чекбоксов
    $('#selectModal').on('click', "#select-none", function (event) {
        event.preventDefault();
        $('#selectModal input:checkbox:checked').each(function () {
            this.checked = false;
        });
    });

    // Выделить все чекбоксы
    $('#selectModal').on('click', "#select-all", function (event) {
        event.preventDefault();
        $('#selectModal input:checkbox').each(function () {
            this.checked = true;
        });
    });

    // Редактировать с выбранными - 1 шаг
    $(".select-action .edit-select").on('click', function (event) {
        event.preventDefault();

        if ($('#data input:checkbox:checked').length) {
            var data = [];
            $('#data input:checkbox:checked').each(function () {
                if (this.value != 'all')
                    data.push({name: 'select[' + $(this).attr('data-id') + ']', value: $(this).attr('data-id')});

            });

            data.push({name: 'selectID', value: 1});
            data.push({name: 'ajax', value: 1});
            data.push({name: 'actionList[selectID]', value: 'actionSelect'});

            $.ajax({
                mimeType: 'text/html; charset=' + locale.charset,
                url: '?path=order.select',
                type: 'post',
                data: data,
                dataType: "html",
                async: false,
                success: function (data) {
                    $('#selectModal .modal-title').html(locale.select_title);
                    $('#selectModal .modal-footer .btn-primary').html(locale.select_edit);
                    $('#selectModal .modal-footer .btn-primary').addClass('edit-select-send');
                    $('#selectModal .modal-body').html(data);
                    $('#selectModal').modal('show');
                }
            });
        } else
            alert(locale.select_no);
    });


    // datetimepicker
    if ($(".date").length) {
        $.fn.datetimepicker.dates['ru'] = locale;
        $(".date").datetimepicker({
            format: 'dd-mm-yyyy',
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

    // Поиск заказа - очистка
    $(".btn-order-cancel").on('click', function () {
        table.api().ajax.url(ajax_path + 'order.ajax.php').load();
        $(this).addClass('hide');
    });

    // Поиск заказа
    $(".btn-order-search").on('click', function () {
        var push = '?';
        $('#order_search .form-control, #order_search .selectpicker').each(function () {
            if ($(this).attr('name') !== undefined) {
                push += $(this).attr('name') + '=' + escape($(this).val()) + '&';
            }
        });
        table.api().ajax.url(ajax_path + 'order.ajax.php' + push).load();
        $('.btn-order-cancel').removeClass('hide');
    });

    // Сделать копию из списка заказов
    $("body").on('click', ".dropdown-menu .copy", function () {
        $(this).attr('href', '?path=order&action=new&id=' + $(this).attr('data-id'));
    });

    // Связь e-mail из списка заказов
    $("body").on('click', ".dropdown-menu .email", function () {
        $(this).attr('href', 'mailto:' + $('#order-' + $(this).attr('data-id') + '-email').html());
    });

    // Печатные бланки
    $(".btn-print-order").on('click', function () {
        window.open($(this).attr('data-option'));
    });

    // Изменить скидку заказа
    $(".discount").on('click', function () {
        
        $(window).unbind("beforeunload");

        var order_id = $('#footer input[name=rowID]').val();
        var data = [];
        data.push({name: 'selectID', value: $('.discount-value').val()});
        data.push({name: 'selectAction', value: 'discount'});
        data.push({name: 'actionList[selectID]', value: 'actionCartUpdate.order.edit'});

        $.ajax({
            mimeType: 'text/html; charset=' + locale.charset,
            url: '?path=order&id=' + order_id,
            type: 'post',
            data: data,
            dataType: "html",
            async: false,
            success: function () {
                window.location.reload();
            }

        });
    });

    // Добавить в корзину - Поиск
    $("body").on('click', "#selectModal .search-action", function (event) {
        event.preventDefault();

        var data = [];
        data.push({name: 'selectID', value: 1});
        data.push({name: 'ajax', value: 1});
        data.push({name: 'actionList[selectID]', value: 'actionSearch'});

        $.ajax({
            mimeType: 'text/html; charset=' + locale.charset,
            url: '?path=catalog.search&words=' + escape($('input:text[name=search_name]').val()) + '&cat=' + $('select[name=search_category]').val() + '&price_start=' + $('input:text[name=search_price_start]').val() + '&price_end=' + $('input:text[name=search_price_end]').val(),
            type: 'post',
            data: data,
            dataType: "html",
            async: false,
            success: function (data) {
                $('#selectModal .modal-body').html(data);
            }

        });
    });

    // Добавить в корзину товар -  2 шаг
    $("body").on('click', "#selectModal .modal-footer .cart-add-send", function (event) {
        event.preventDefault();
        var count = 0;
        var order_id = $('#footer input[name=rowID]').val();

        // Progress 
        $('#selectModal .modal-footer .btn').addClass('hidden');
        $('.progress').removeClass('hidden');

        // Всего элементов
        $('.cart-list input:text').each(function () {
            if (this.value > 0 || $(this).attr('data-cart') == "true")
                count++;
        });
        var total = $('.progress').width() / count;

        $('.cart-list input:text').each(function () {
            if (this.value > 0 || $(this).attr('data-cart') == "true") {
                var data = [];
                data.push({name: 'selectID', value: $(this).attr('data-id')});
                data.push({name: 'selectNum', value: this.value});
                data.push({name: 'ajax', value: 1});
                data.push({name: 'selectAction', value: 'add'});
                data.push({name: 'actionList[selectID]', value: 'actionCartUpdate.order.edit'});

                $.ajax({
                    mimeType: 'text/html; charset=' + locale.charset,
                    url: '?path=order&id=' + order_id,
                    type: 'post',
                    data: data,
                    dataType: "html",
                    async: false,
                    success: function () {

                        // Progress 
                        var progress = parseInt($('.progress-bar').css('width').split('px').join(''));
                        $('.progress-bar').css('width', (progress + total) + 'px');

                    }
                });
            }
        });

        $('#selectModal').modal('show');
        $('.progress-bar').css('width', '100%');
        window.location.reload();
    });

    // Управление полем корзины в поиске товара
    $("body").on('click', ".item-minus", function () {
        var id = $(this).attr('data-id');
        var current = $('#select_id_' + id).val();
        current--;
        if (current < 0)
            current = 0;
        else if (isNaN(current))
            current = 0;
        $('#select_id_' + id).val(parseInt(current));
    });

    $("body").on('click', ".item-plus", function () {
        var id = $(this).attr('data-id');
        var current = $('#select_id_' + id).val();
        current++;
        if (isNaN(current))
            current = 1;
        $('#select_id_' + id).val(parseInt(current));
    });


    // Добавить в корзину товар - 1 шаг
    $(".cart-add").on('click', function (event) {
        event.preventDefault();

        var data = [];
        data.push({name: 'selectID', value: 1});
        data.push({name: 'ajax', value: 1});
        data.push({name: 'actionList[selectID]', value: 'actionSearch'});

        $.ajax({
            mimeType: 'text/html; charset=' + locale.charset,
            url: '?path=catalog.search',
            type: 'post',
            data: data,
            dataType: "html",
            async: false,
            success: function (data) {
                $('#selectModal .modal-dialog').addClass('modal-lg');
                $('#selectModal .modal-title').html(locale.add_cart_value);
                $('#selectModal .modal-footer .btn-primary').removeClass('edit-select-send');
                $('#selectModal .modal-footer .btn-primary').addClass('cart-add-send');
                $('#selectModal .modal-footer .btn-delete').addClass('hidden');
                $('#selectModal .modal-body').css('max-height', ($(window).height() - 200) + 'px');
                $('#selectModal .modal-body').css('overflow-y', 'auto');
                $('#selectModal .modal-body').html(data);
                $('#selectModal').modal('show');
            }
        });
    });

    // Удаление из списка товара заказа корзины
    $(".data-row .cart-value-remove").on('click', function (event) {
        event.preventDefault();

        var order_id = $('#footer input[name=rowID]').val();
        var product_id = $(this).attr('data-id');

        if (confirm(locale.confirm_delete)) {

            var data = [];
            data.push({name: 'selectID', value: product_id});
            data.push({name: 'selectAction', value: 'delete'});
            data.push({name: 'actionList[selectID]', value: 'actionCartUpdate.order.edit'});

            $('#modal-form').attr('action', '?path=order&id=' + order_id);
            $('#modal-form').ajaxSubmit({
                data: data,
                dataType: "json",
                success: function (json) {
                    $('#selectModal').modal('hide');
                    window.location.reload();
                }
            });
        }
    });

    // Удаление товара из заказа модальное окно
    $("body").on('click', "#selectModal .modal-footer .value-delete", function (event) {
        event.preventDefault();

        var product_id = $('.modal-body input[name=rowID]').val();
        var order_id = $('.modal-body input[name=orderID]').val();

        if (confirm(locale.confirm_delete)) {

            var data = [];
            data.push({name: 'selectID', value: product_id});
            data.push({name: 'selectAction', value: 'delete'});
            data.push({name: 'actionList[selectID]', value: 'actionCartUpdate.order.edit'});

            $('#modal-form').attr('action', '?path=order&id=' + order_id);
            $('#modal-form').ajaxSubmit({
                data: data,
                dataType: "json",
                success: function (json) {
                    $('#selectModal').modal('hide');
                    window.location.reload();
                }
            });
        }
    });

    // Редактировать корзину - 2 шаг
    $("body").on('click', "#selectModal .modal-footer .value-edit-send", function (event) {
        event.preventDefault();

        var product_id = $('#modal-form input[name=rowID]').val();
        var order_id = $('#modal-form input[name=orderID]').val();

        var data = [];
        data.push({name: 'selectID', value: product_id});
        data.push({name: 'actionList[selectID]', value: 'actionCartUpdate.order.edit'});
        $('#modal-form .form-control, #modal-form .hidden-edit, #modal-form input:radio:checked, #modal-form input:checkbox:checked').each(function () {
            if ($(this).attr('name') !== undefined) {
                data.push({name: $(this).attr('name'), value: escape($(this).val())});
            }
        });

        $('#modal-form').attr('action', '?path=order&id=' + order_id);
        $('#modal-form').ajaxSubmit({
            data: data,
            dataType: "json",
            success: function (json) {
                $('#selectModal').modal('hide');
                if (json['success'] == 1) {
                    window.location.reload();
                } else
                    showAlertMessage(locale.save_false, true);
            }

        });

    });

    // Редактировать корзину - 1 шаг
    $("body").on('click', ".data-row .cart-value-edit", function (event) {
        event.preventDefault();

        var data = [];
        var id = $(this).attr('data-id');
        var order_id = $('#footer input[name=rowID]').val();
        var parent = $(this).closest('.data-row').attr('data-row');
        data.push({name: 'selectID', value: escape(id)});
        data.push({name: 'ajax', value: 1});
        data.push({name: 'actionList[selectID]', value: 'actionValueEdit'});
        data.push({name: 'parentID', value: parent});

        $.ajax({
            mimeType: 'text/html; charset=' + locale.charset,
            url: '?path=order&id=' + order_id,
            data: data,
            dataType: "html",
            async: false,
            success: function (data) {
                $('#selectModal .modal-dialog').removeClass('modal-lg');
                $('#selectModal .modal-title').html(locale.edit_cart_value);
                $('#selectModal .modal-footer .btn-primary').removeClass('edit-select-send');
                $('#selectModal .modal-footer .btn-primary').addClass('value-edit-send');
                $('#selectModal .modal-footer .btn-delete').removeClass('hidden');
                $('#selectModal .modal-footer .btn-delete').addClass('value-delete');
                $('#selectModal .modal-body').html(data);
                $('#selectModal').modal('show');
            }

        });
    });

    // Экспортировать с выбранными
    $(".select-action .export-select").on('click', function (event) {
        event.preventDefault();

        if ($('input:checkbox:checked').length) {
            var data = [];
            $('input:checkbox:checked').each(function () {
                if (this.value != 'all')
                    data.push({name: 'select[' + $(this).attr('data-id') + ']', value: $(this).attr('data-id')});
            });

            data.push({name: 'selectID', value: 1});
            data.push({name: 'ajax', value: 1});
            data.push({name: 'actionList[selectID]', value: 'actionSelect'});
            $.ajax({
                mimeType: 'text/html; charset=' + locale.charset,
                url: '?path=exchange.export.order',
                type: 'post',
                data: data,
                dataType: "json",
                async: false,
                success: function () {
                    window.location.href = '?path=exchange.export.order';
                }

            });
        } else
            alert(locale.select_no);
    });

    // Настройка bootstrap-select
    $('.selectpicker').selectpicker({
        dropdownAlignRight: true
    });

    // Обновление данных
    $("button[name=editID]").on('click', function () {

        if ($('#product_edit input[name=fio_new]').val() != "") {
            $('#user-data-1 .sidebar-data-0').text($('#product_edit input[name=fio_new]').val());
            $('#user-data-2 .sidebar-data-0').text($('#product_edit input[name=fio_new]').val());
        }

        if ($('#product_edit input[name=tel_new]').val() != "") {
            $('#user-data-1 .sidebar-data-2').text($('#product_edit input[name=tel_new]').val());
            $('#user-data-2 .sidebar-data-1').text($('#product_edit input[name=tel_new]').val());
        }
    });

    // Карта
    if ($('#map').length) {
        ymaps.ready(init);
    }
    function init() {
        ymaps.geocode($('#map').attr('data-geocode'), {results: 1}).then(function (res) {
            var firstGeoObject = res.geoObjects.get(0);
            //res.geoObjects.get(0).properties.set('balloonContentHeader', 'Доставка');
            res.geoObjects.get(0).properties.set('balloonContentBody', $('#map').attr('data-title'));
            window.myMap = new ymaps.Map("map", {
                center: firstGeoObject.geometry.getCoordinates(),
                zoom: 10
            });
            myMap.controls.add('mapTools', {left: 5, top: 5});
            firstGeoObject.options.set('preset', 'twirl#buildingsIcon');
            myMap.geoObjects.add(firstGeoObject);
        });
    }

    // Активация из списка dropdown
    $("body").on('mouseenter', '.data-row', function () {
        $(this).find('#dropdown_action').show();
    });
    $("body").on('mouseleave', '.data-row', function () {
        $(this).find('#dropdown_action').hide();
    });

    // Мобильная навигация
    if (typeof is_mobile !== 'undefined') {
        locale.dataTable.paginate.next = "»";
        locale.dataTable.paginate.previous = "«";
    }

    // Таблица данных
    if (typeof ($.cookie('data_length')) == 'undefined')
        var data_length = [10, 25, 50, 75, 100, 500];
    else
        var data_length = [parseInt($.cookie('data_length')), 10, 25, 50, 75, 100, 500];

    if ($('#data').html()) {
        var table = $('#data').dataTable({
            "ajax": {
                "type": "GET",
                "url": ajax_path + 'order.ajax.php' + window.location.search,
                "dataSrc": function (json) {
                    $('#stat_sum').text(json.sum);
                    $('#stat_num').text(json.num);
                    $('#select_all').prop('checked', false);
                    return json.data;
                }
            },
            "processing": true,
            "serverSide": true,
            "paging": true,
            "ordering": true,
            "order": [[3, "desc"]],
            "info": false,
            "searching": true,
            "lengthMenu": data_length,
            "language": locale.dataTable,
            "stripeClasses": ['data-row', 'data-row'],
            "aoColumnDefs": [{
                    'bSortable': false,
                    'aTargets': ['sorting-hide']
                }]
        });
    }


    // Изменить стоимость доставки
    $('select[name="person[dostavka_metod]"]').on('change', function () {

        if ($(this).val() == 0) {

            $.MessageBox({
                buttonDone: locale.ok,
                buttonFail: locale.close,
                input: true,
                message: locale.delivery
            }).done(function (cost) {
                if ($.trim(cost)) {

                    var order_id = $('#footer input[name=rowID]').val();
                    var data = [];
                    data.push({name: 'selectID', value: cost});
                    data.push({name: 'selectAction', value: 'changeDeliveryCost'});
                    data.push({name: 'actionList[selectID]', value: 'actionCartUpdate.order.edit'});

                    $.ajax({
                        mimeType: 'text/html; charset=' + locale.charset,
                        url: '?path=order&id=' + order_id,
                        type: 'post',
                        data: data,
                        dataType: "html",
                        async: false,
                        success: function () {
                            $(window).unbind("beforeunload");
                            window.location.reload();
                        }
                    });
                } else {
                }
            });

        }
    });
});