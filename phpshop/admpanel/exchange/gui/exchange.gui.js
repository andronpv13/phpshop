// Переопределение функции
var TABLE_EVENT = true;
locale.icon_load = locale.file_load;

$().ready(function () {

    // Блокировка помощника для URL
    $('body').on('click', '#promtUrl', function () {
        //$('#bot').bootstrapToggle('off').bootstrapToggle('disable');
    });


    // Рассчет скорости 
    $('body').on('change', '#export_imgload', function () {

        var line_limit = Number($('#line_limit').val());

        if ($(this).val() != 1) {
            line_limit = line_limit + 200;
        } else {
            line_limit = 1;
        }

        $('#line_limit').val(line_limit);
    });

    // Рассчет скорости 
    $('body').on('change', '#export_imgproc', function () {

        var line_limit = Number($('#line_limit').val());

        if ($(this).prop('checked') === true) {
            line_limit = line_limit - 100;
        } else {
            line_limit = line_limit + 100;
        }

        $('#line_limit').val(line_limit);
    });


    // Таблица заголовков полей
    $('[name="import-col-name"]').on('click', function (event) {
        event.preventDefault();
        $('#import-col-name').slideToggle('slow');
    });

    // Выбор сохраненной настройки
    $('body').on('change', '#exchanges', function () {
        if (this.value != "new")
            window.location.href += '&exchanges=' + this.value;
    });

    // Остановить автоматизацмю
    $(".load-result .close, .load-info .close").on('click', function (event) {
        event.preventDefault();

        $.MessageBox({
            buttonDone: "OK",
            buttonFail: locale.cancel,
            message: locale.import_stop
        }).done(function () {
            $('#stop').val(1);
            $(".load-info").hide();
            $(".load-result").hide();
            $('[name="saveID"]').hide();
            $(window).unbind("beforeunload");
        });
    });


    // Автоматизация импорта
    function auto_import(start, end) {

        var count = Number($('#total-update').html());
        var stop = $('#stop').val();
        var img_load = 0;

        if (stop != 1) {

            var data = [];
            data.push({name: 'selectID', value: 1});
            data.push({name: 'actionList[selectID]', value: 'actionSave'});
            data.push({name: 'start', value: start});
            data.push({name: 'end', value: end});
            data.push({name: 'ajax', value: true});
            data.push({name: 'img_load', value: img_load});

            //console.log(data);

            $('#product_edit').ajaxSubmit({
                data: data,
                dataType: "json",
                contentType: false,
                processData: false,
                success: function (json) {
                    $('#bot_result').html(json['result']);
                    count += json['count'];
                    img_load += json['img_load'];
                    $('#total-update').html(count);

                    if (json['success'] == 'done') {
                        $('.progress-bar').css('width', '100%');
                        $('.progress-bar').removeClass('active').html('100%');
                        $('#play').trigger("play");
                        $(window).unbind("beforeunload");
                        $('#total-min').html(0);

                        $.MessageBox({
                            buttonDone: "OK",
                            message: locale.import_done + count + ' ' + json['action']
                        }).done(function () {
                            window.location.href = '?path=exchange.import';
                        });

                    } else if (json['success']) {
                        start += limit;
                        end += limit;
                        $('.progress-bar').css('width', json['bar'] + '%').html(json['bar'] + '%');
                        auto_import(start, end);
                    }

                }


            });
        }
    }

    // Автоматизация загрузки файла
    if ($('.bot-progress .progress-bar').hasClass('active')) {

        $(window).bind("beforeunload", function () {
            return "Are you sure you want to exit? Please complete sign up or the app will get deleted.";
        });

        var limit = Number($('[name="line_limit"]').val());
        auto_import(0, limit);
    }

    // Модальное окно таблиц
    $('#selectModal').on('show.bs.modal', function (event) {
        $('#selectModal .modal-title').html($('[data-target="#selectModal"]').attr('data-title'));
        $('#selectModal .modal-footer .btn-primary').addClass('hidden');
        $('#selectModal .modal-footer [data-dismiss="modal"]').html(locale.close);
        $('#selectModal .modal-body').css('max-height', ($(window).height() - 200) + 'px');
        $('#selectModal .modal-body').css('overflow-y', 'auto');
    });

    // Сохранить Ace
    $(".ace-save").on('click', function (event) {
        event.preventDefault();
        $('#editor_src').val(editor.getValue());
        $('#product_edit').submit();
    });


    // Ace
    if ($('#editor_src').length) {
        var editor = ace.edit("editor");
        var mod = $('#editor_src').attr('data-mod');
        var theme = $('#editor_src').attr('data-theme');
        editor.setTheme("ace/theme/" + theme);
        editor.session.setMode("ace/mode/" + mod);
        editor.setValue($('#editor_src').val(), 1);
        editor.getSession().setUseWrapMode(true);
        editor.setShowPrintMargin(false);
        editor.setAutoScrollEditorIntoView(true);
        $('#editor').height(300);
        editor.resize();
    }

    // Корректировка обязательных полей update/insert
    $('#export_action').on('changed.bs.select', function () {
        $('kbd.enabled').toggle();
        if ($('#export_action').val() == 'update') {
            $('#export_uniq').attr('disabled', 'disabled');
            $('#export_key').attr('disabled', null);
        } else {
            $('#export_uniq').attr('disabled', null);
            $('#export_key').attr('disabled', 'disabled');
        }
    });

    if ($('#export_action').val() == 'update') {
        //$('#export_uniq').attr('disabled', 'disabled');
    }

    // Удалить диапазон
    $(".select-remove").on('click', function (event) {
        event.preventDefault();

        var data = [];
        data.push({name: 'selectID', value: 1});
        data.push({name: 'ajax', value: 1});
        data.push({name: 'actionList[selectID]', value: 'actionSelect'});
        $.ajax({
            mimeType: 'text/html; charset=' + locale.charset,
            url: '?path=exchange.export.product',
            type: 'post',
            data: data,
            dataType: "json",
            async: false,
            success: function () {
                window.location.reload();
            }
        });
    });

    // Очистить сервисную таблицу из списка
    $(".data-row .clean-base").on('click', function (event) {
        event.preventDefault();
        var table = $(this).closest('.data-row').find('td:nth-child(2)').html();

        $.MessageBox({
            buttonDone: "OK",
            buttonFail: locale.cancel,
            message: locale.confirm_clean + ': ' + table + '?'
        }).done(function () {

            var data = [];
            data.push({name: 'table', value: table});
            data.push({name: 'saveID', value: 1});
            data.push({name: 'ajax', value: 1});
            data.push({name: 'actionList[saveID]', value: 'actionSave'});
            $.ajax({
                mimeType: 'text/html; charset=' + locale.charset,
                url: '?path=exchange.service',
                type: 'get',
                data: data,
                dataType: "json",
                async: false,
                success: function (json) {
                    if (json['success'] == 1) {
                        window.location.reload();
                    } else
                        showAlertMessage(locale.save_false, true, true);
                }
            });
        })

    });

    // Очистить сервисную таблицу с отмеченными
    $('.select-action .sql-clean').on('click', function (event) {
        event.preventDefault();

        var chk = $('input:checkbox:checked').length;
        var i = 0;

        if (chk > 0) {

            $.MessageBox({
                buttonDone: "OK",
                buttonFail: locale.cancel,
                message: locale.confirm_clean
            }).done(function () {

                $('input:checkbox:checked').each(function () {
                    var table = $(this).closest('.data-row').find('td:nth-child(2)').html();
                    var data = [];

                    data.push({name: 'table', value: table});
                    data.push({name: 'saveID', value: 1});
                    data.push({name: 'ajax', value: 1});
                    data.push({name: 'actionList[saveID]', value: 'actionSave'});
                    $.ajax({
                        mimeType: 'text/html; charset=' + locale.charset,
                        url: '?path=exchange.service',
                        type: 'get',
                        data: data,
                        dataType: "json",
                        async: false
                    });

                    i++;
                    if (chk == i)
                        window.location.reload();
                });
            })

        } else
            alert(locale.select_no);
    });

    // Очистить битые изображения с отмеченными
    $("body").on('click', ".select-action .image-clean", function (event) {
        event.preventDefault();

        var chk = $('input[name="items"]:checkbox:checked').length;
        var i = 0;

        if (chk > 0) {

            $.MessageBox({
                buttonDone: "OK",
                buttonFail: locale.cancel,
                message: locale.confirm_delete
            }).done(function () {

                $('input[name="items"]:checkbox:checked').each(function () {
                    var id = $(this).closest('.data-row');
                    $('.list_edit_' + $(this).attr('data-id')).ajaxSubmit({
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

    // Автоматизация восстановления бекапа
    function auto_restore(file, option) {

        var data = [];
        data.push({name: 'file', value: '/phpshop/admpanel/dumper/backup/' + file});
        data.push({name: 'restoreID', value: 1});
        data.push({name: 'ajax', value: 1});
        data.push({name: 'actionList[restoreID]', value: 'actionRestore'});
        data.push({name: 'option', value: option});

        //console.log(data);
        $.ajax({
            mimeType: 'text/html; charset=' + locale.charset,
            url: './dumper/ajax/restore.ajax.php',
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
                        message: locale.restore_backup + ' ' + file + ' ' + locale.done.toLowerCase()
                    }).done(function () {
                        $('.success-notification').delay(500).fadeOut(1000);
                    });

                } else if (json['success']) {
                    option = json['option'];
                    $('.progress-bar').css('width', json['bar'] + '%').html(json['bar'] + '%');
                    auto_restore(file, option);
                }

            }
        });
    }

    // Восстановить бекап из списка
    $("body").on('click', ".data-row .restore", function (event) {
        event.preventDefault();
        var file = $(this).closest('.data-row').find('td:nth-child(2)>a').html();

        $.MessageBox({
            buttonDone: "OK",
            buttonFail: locale.cancel,
            message: locale.confirm_restore + ': ' + file + '?'
        }).done(function () {

            $(window).bind("beforeunload", function () {
                return "Are you sure you want to exit? Please complete sign up or the app will get deleted.";
            });

            showProgressBar(locale.restore_backup);
            auto_restore(file, 0);
        })
    });

    // Удаление из списка
    $("body").on('click', ".data-row .delete", function (event) {
        event.preventDefault();
        if ($.getUrlVar('path') === 'exchange.backup')
            $('.list_edit_' + $(this).attr('data-id')).append('<input type="hidden" name="file" value="' + $(this).closest('.data-row').find('td:nth-child(2)>a').html() + '">');
    });

    // Удалить с выбранными
    $(".select-action .select").on('click', function (event) {
        event.preventDefault();
        if ($('input:checkbox:checked').length) {

            $('input:checkbox:checked').each(function () {
                var id = $(this).closest('.data-row');
                $('.list_edit_' + $(this).attr('data-id')).append('<input type="hidden" name="file" value="' + $(this).closest('.data-row').find('td:nth-child(2)>a').html() + '">');
            });

        } else
            alert(locale.select_no);
    });

    // Скачать бекап с отмеченными
    $('.select-action .load').on('click', function (event) {
        event.preventDefault();
        if ($('input:checkbox:checked').length) {

            $('input:checkbox:checked').each(function () {
                var add = $(this).closest('.data-row').find('td:nth-child(2)>a').html();
                window.open('?path=exchange.backup&file=' + add);
            });
        } else
            alert(locale.select_no);
    });

    // Оптимизировать базу
    $(".select-action .sql-optim").on('click', function (event) {
        event.preventDefault();
        window.location.href = '?path=exchange.sql&query=optimize';
    });

    // Скачать бекап из списка
    $(".data-row .load").on('click', function (event) {
        event.preventDefault();
        window.location.href = $(this).closest('.data-row').find('td:nth-child(2)>a').attr('href');
    });

    // SQL команда
    $('#sql_query').on('change', function () {
        if ($(this).val() != 0)
            editor.setValue($(this).val());
        //$('#sql_text').html($(this).val());
    });

    // Cнять выделения таблиц
    $("#select-none").on('click', function (event) {
        event.preventDefault();
        $('#pattern_table option:selected').each(function () {
            this.selected = false;
        });
    });

    // Поставить выделения всех таблиц
    $("#select-all").on('click', function (event) {
        event.preventDefault();
        $('#pattern_table option').each(function () {
            this.selected = true;
        });
    });

    // Удаление всех полей
    $("#remove-all").on('click', function (event) {
        event.preventDefault();
        $('#pattern_default option').each(function () {
            this.selected = false;
            $('#pattern_more').append('<option value="' + this.value + '" selected>' + $(this).html() + '</option>');
            $(this).remove();
        });
    });

    // Добавление все поля в выгрузку
    $("#send-all").on('click', function (event) {
        event.preventDefault();
        $('#pattern_more option').each(function () {
            this.selected = true;
            $('#pattern_default').append('<option value="' + this.value + '" selected>' + $(this).html() + '</option>');
            $(this).remove();
        });
    });

    // Добавление выделенные поля в выгрузку
    $("#send-default").on('click', function (event) {
        event.preventDefault();
        $('#pattern_more option:selected').each(function () {
            if (typeof this.value != 'undefined') {
                $('#pattern_default').append('<option value="' + this.value + '" selected>' + $(this).html() + '</option>');
                $(this).remove();
            }
        });
    });

    // Удаление выделенные поля из выгрузки
    $("#send-more").on('click', function (event) {
        event.preventDefault();
        if (typeof $('#pattern_default :selected').html() != 'undefined') {
            $('#pattern_more').append('<option value="' + $('#pattern_default :selected').val() + '">' + $('#pattern_default :selected').html() + '</option>');
            $('#pattern_default option:selected').remove();
        }
    });

    // Лимит
    $(".btn-file-search").on('click', function () {
        $('#file_search').submit();
    });


    // Лимит - очистка
    $(".btn-file-cancel").on('click', function () {
        window.location.replace('?path=exchange.file');
    });


    // Таблица сортировки
    var table = $('#data').dataTable({
        "paging": true,
        "ordering": true,
        "info": false,
        "language": locale.dataTable,
        "fnDrawCallback": function () {

            // Активация из списка dropdown
            $('.data-row').hover(
                    function () {
                        $(this).find('#dropdown_action').show();
                    },
                    function () {
                        $(this).find('#dropdown_action').hide();
                    });
        }
    });

    // Конструктор SQL запроса
    $('body').on('click', '.query_generation', function () {

        var action = $('#query_action').val();
        var table = $('#query_table').val();
        var vars = $('#query_var').val();
        var condition = $('#query_condition').val();
        var val = $('#query_val').val();
        var query = '';

        if (action == 'select')
            query = action + ' * from ' + table + ' where ';
        else if (action == 'update')
            query = action + ' ' + table + ' set ';
        else if (action == 'delete')
            query = action + ' from ' + table + ' where ';

        if (val == 'prompt') {
            $.MessageBox({
                buttonDone: locale.ok,
                buttonFail: locale.close,
                input: true,
                message: locale.option_title
            }).done(function (data) {
                if ($.trim(data)) {

                    if (query != '') {
                        query += vars + ' ' + condition + " '" + data + "'";
                        editor.setValue(query);
                    }
                }
            });
        } else if (query != '') {
            query += ' `' + vars + '` ' + condition + ' ' + val;
            editor.setValue(query);
        }
    });


});