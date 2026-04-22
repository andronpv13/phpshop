// Загрузка файла
$(document).on('change', '.btn-file :file', function () {
    var input = $(this),
            numFiles = input.get(0).files ? input.get(0).files.length : 1,
            label = input.val().replace(/\\/g, '/').replace(/.*\//, '');
    input.trigger('fileselect', [numFiles, label]);
});


/**
 * Notification
 * @param {string} message текст сообщения
 * @@param {string} danger режим показа ошибки
 * @param {bool} hide отключить закрытие сообщения
 */
function showAlertMessage(message, danger, hide) {

    if (typeof danger != 'undefined') {
        if (danger === true)
            danger = 'danger';
        $('.success-notification').find('.alert').addClass('alert-' + danger);
    } else {
        $('.success-notification').find('.alert').removeClass('alert-danger');
        $('.success-notification').find('.alert').removeClass('alert-info');
    }

    var messageBox = '.success-notification';
    var innerBox = '#notification .notification-alert';

    if ($(messageBox).length > 0 && typeof is_mobile == 'undefined') {
        $(messageBox).removeClass('hide');
        $(innerBox).html(message);
        $(messageBox).fadeIn('slow');

        if (typeof hide == 'undefined') {
            setTimeout(function () {
                $(messageBox).delay(500).fadeOut(1000);
            }, 5000);
        }
    }
}

/**
 * Прогресс бар
 * @param {string} message текст сообщения
 */
function showProgressBar(message) {

    $('.success-notification').find('.alert').removeClass('alert-danger');
    $('.success-notification').find('.alert').removeClass('alert-info');
    $('.success-notification .close').addClass('hide');

    var messageBox = '.success-notification';
    var innerBox = '#notification .notification-alert';

    message += '<div class="progress bot-progress" style="width:250px"><div class="progress-bar progress-bar-striped  progress-bar-success active" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">0%</div></div>';

    if ($(messageBox).length > 0) {
        $(messageBox).removeClass('hide');
        $(innerBox).html(message);
        $(messageBox).fadeIn('slow');
    }
}


// Инициализируем таблицу перевода https://wm-school.ru/html/html_win-1251.html
var trans = [];
for (var i = 0x410; i <= 0x44F; i++)
    trans[i] = i - 0x350; // А-Яа-я
trans[0x401] = 0xA8;    // Ё
trans[0x451] = 0xB8;    // ё

// Таблица перевода на украинский/белоруский
trans[0x457] = 0xBF;    // ї
trans[0x407] = 0xAF;    // Ї
trans[0x456] = 0xB3;    // і
trans[0x406] = 0xB2;    // І
trans[0x404] = 0xAA;    // Є
trans[0x454] = 0xBA;    // є

// Сохраняем стандартную функцию escape()
var escapeOrig = window.escape;

// Переопределяем функцию escape()
window.escape = function (str)
{

    if (locale.charset == 'utf-8')
        return str;

    else {
        var str = String(str);
        var ret = [];
        // Составляем массив кодов символов, попутно переводим кириллицу
        for (var i = 0; i < str.length; i++)
        {
            var n = str.charCodeAt(i);
            if (typeof trans[n] != 'undefined')
                n = trans[n];
            if (n <= 0xFF)
                ret.push(n);
        }
        return escapeOrig(String.fromCharCode.apply(null, ret));
    }
};

// Очистка категории при перезагурзки
window.onbeforeunload = function ()
{
    $.cookie('cat', null);
};

$().ready(function () {

    // Отключение обучающих уроков
    $('body').on('change', '#presentation-check', function () {
        $.cookie('presentation', this.checked, {
            path: '/',
            expires: 365
        });
    });

    // Размер экрана
    $('.setscreen').on('click', function (event) {
        event.preventDefault();

        if ($.cookie('fullscreen') == undefined || $.cookie('fullscreen') == 0)
            var fullscreen = 1;

        else
            var fullscreen = 0;

        $.cookie('fullscreen', fullscreen, {
            path: '/',
            expires: 365
        });

        location.reload();
    });

    // Выбор обучающих уроков
    $('#presentation-select').on('click', function (event) {
        event.preventDefault();
        $('#selectModal .modal-dialog').removeClass('modal-lg');
        $('#selectModal .modal-title').html(locale.presentation_title);
        $('#selectModal .modal-footer .btn-delete').addClass('hidden');
        $('#selectModal .modal-footer .btn-primary').addClass('hidden');
        $('#selectModal .modal-footer [data-dismiss="modal"]').text(locale.close);
        $('#selectModal .modal-body').html($('#presentation').html());
        $('#selectModal').modal('show');
    });

    // Поиск избражений в Яндекс
    $('#yandexsearchModal').on('click', function (event) {
        event.preventDefault();
        var id = $(this).attr('data-target');
        $('#adminModal .modal-title').html(locale.select_file);
        $('#adminModal .glyphicon-fullscreen, #adminModal .glyphicon-eye-open').addClass('hidden');
        $('#adminModal .product-modal-content').attr('height', $(window).height() - 120);
        $('#adminModal .product-modal-content').attr('src', './system/ajax/yandexsearch.ajax.php?text=' + encodeURIComponent($('#name_new').val())+'&target='+id);
        $('#adminModal').modal('show');
    });

    // Назад
    $('.back').on('click', function (event) {
        event.preventDefault();

        if ($.getUrlVar('frame') !== undefined) {
            parent.window.$('#adminModal').modal('hide');
        } else
            history.back(1);
    });

    // Загрузка иконки
    $('.btn-file :file').on('fileselect', function (event, numFiles, label) {
        var input = $(this).parents('.input-group').find(':text'),
                log = numFiles > 1 ? numFiles + ' files selected' : label;

        if (input.length) {
            input.val(log);
        }

        var id = $(this).attr('data-target');

        $('[data-icon="' + id + '"]').html(log);
        //$("input[name='" + id + "']").val('/UserFiles/Image/' + log);
        $("input[name='" + id + "']").val(log);
        $('[data-icon="' + id + '"]').prev('.glyphicon').removeClass('hide');

        if (locale.icon_load != null)
            showAlertMessage(locale.icon_load, 'info');
    });

    // Ввод URL иконки
    $('body').on('click', '#promtUrl', function () {

        var id = $(this).attr('data-target');

        $.MessageBox({
            input: true,
            message: "URL"
        }).done(function (data) {
            if ($.trim(data)) {
                var file = data;
                $('[data-icon="' + id + '"]').html(file);
                $('[data-icon="' + id + '"]').prev('.glyphicon').removeClass('hide');
                $("input[name='" + id + "']").val(file);
                $('[data-thumbnail="' + id + '"]').attr('src', file);
                $("input[name=img_new]").val(file);
                $("input[name=furl]").val(file);

            }
        });
    });

    // Удаление иконки
    $('body').on('click', '.remove', function () {
        $(this).next('span').html(locale.select_file);
        $(this).toggleClass('hide');
        $(this).closest('.form-group').find(".img-thumbnail").attr('src', './images/no_photo.gif');
        $("input[name=" + $(this).attr('data-return') + "]").val('');
    });

    // Ссылка на иконке
    $(".link-thumbnail").on('click', function (event) {
        event.preventDefault();
        var src = $(this).find('.img-thumbnail').attr('src');
        if (src != 'images/no_photo.gif')
            window.open(src);
    });

    // Файл-менеджер elfinder
    $('#elfinderModal').on('show.bs.modal', function (event) {
        $('.elfinder-modal-content').attr('data-option', $(event.relatedTarget).attr('data-return'));
        var path = $(event.relatedTarget).attr('data-path');

        if (typeof path == 'undefined')
            path = $('.elfinder-modal-content').attr('data-path');

        var option = $('.elfinder-modal-content').attr('data-option');
        $('.elfinder-modal-content').attr('src', './editors/default/elfinder/elfinder.php?path=' + path + '&' + option);
    });

    // Сворачиваемый блок описания
    $('.collapse').on('hidden.bs.collapse', function () {
        $(this).prev('h4').find('span').removeClass('glyphicon-triangle-bottom');
        $(this).prev('h4').find('span').addClass('glyphicon-triangle-right');
    });
    $('.collapse').on('show.bs.collapse', function () {
        $(this).prev('h4').find('span').removeClass('glyphicon-triangle-right');
        $(this).prev('h4').find('span').addClass('glyphicon-triangle-bottom');
    });

    $('#rules-message>a[href="#"]').on('click', function (event) {
        event.preventDefault();
        history.back(1);
    });

    // Удалить с выбранными
    $("body").on('click', ".select-action .select", function (event) {
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
                                //table.fnDeleteRow(id.attr('data-row'));
                                id.remove();
                                showAlertMessage(locale.save_done);
                                i++;
                                //if (chk == i)
                                //window.location.reload();

                            } else
                                showAlertMessage(locale.save_false, true);
                        }
                    });
                });
            })
        } else
            alert(locale.select_no);
    });

    // Создать из списка
    $("button[name=addNew]").on('click', function () {
        if (typeof action == 'undefined')
            window.location.href += '&action=new';
    });

    // Создать новый из карточки
    $(".addNewElement").on('click', function (event) {
        event.preventDefault();

        cat = $('[name="addNew"]').attr('data-cat') || $.getUrlVar('id');
        if (cat > 0)
            window.location.href += '&action=new&cat=' + cat;
        else
            window.location.href += '&action=new';
    });

    // Быстрое изменение статуса
    $("body").on('click', ".data-row .status", function (event) {
        event.preventDefault();

        // Проверка на переопределение функции
        if (typeof (STATUS_EVENT) == 'undefined') {

            var id = $(this).attr('data-id');
            var caption = $(this).html();

            // Выделение выбранного элемента
            $(this).closest('ul').find('li').removeClass('disabled');
            $(this).closest('.dropdown').find('a.dropdown-toggle').toggleClass('text-muted');
            $(this).parent('li').addClass('disabled');

            // Возможные варианты переключателей
            $('.status_edit_' + id + ' input[name=enabled_new]').val($(this).attr('data-val'));
            $('.status_edit_' + id + ' input[name=flag_new]').val($(this).attr('data-val'));
            $('.status_edit_' + id + ' input[name=statusi_new]').val($(this).attr('data-val'));
            $('.status_edit_' + id).ajaxSubmit({
                dataType: "json",
                success: function (json) {
                    if (json['success'] == 1) {
                        $("#dropdown_status_" + id).html(caption);
                        showAlertMessage(locale.save_done);
                    } else
                        showAlertMessage(locale.save_false, true);
                }
            });
        }
    });

    // Быстрое изменение статуса Toogle
    $('body').on('change', '.toggle-event', function () {

        // Проверка на переопределение функции
        if (typeof (STATUS_EVENT) == 'undefined') {

            var id = $(this).attr('data-id');

            if ($(this).prop('checked') === true) {
                var val = 1;
                $(this).closest('.data-row').find('a').removeClass('text-muted');
            } else {
                var val = 0;
                $(this).closest('.data-row').find('a').addClass('text-muted');
            }

            // Возможные варианты переключателей
            $('.status_edit_' + id + ' input[name=enabled_new]').val(val);
            $('.status_edit_' + id + ' input[name=flag_new]').val(val);
            $('.status_edit_' + id + ' input[name=statusi_new]').val(val);
            $('.status_edit_' + id).ajaxSubmit({
                dataType: "json",
                success: function (json) {
                    if (json['success'] == 1) {
                        showAlertMessage(locale.save_done);
                    } else
                        showAlertMessage(locale.save_false, true);
                }
            });
        }
    });

    // Сообщение валидатора
    if (typeof (VALIDATOR_LOAD) != 'undefined')
        $('#product_edit').validator().on('submit', function (event) {
            if (event.isDefaultPrevented()) {
                showAlertMessage(locale.validator_false);
            }
        });

    // Сохранить из карточки
    $("button[name=editID]").on('click', function (event) {
        event.preventDefault();

        var data = [];
        data.push({name: 'editID', value: 1});
        $('#product_edit .form-control, #product_edit .hidden-edit, #product_edit input:radio:checked, #product_edit input:checkbox:checked').each(function () {
            if ($(this).attr('name') !== undefined) {
                data.push({name: $(this).attr('name'), value: escape($(this).val())});
            }
        });

        $('#product_edit').ajaxSubmit({
            data: data,
            dataType: "json",
            contentType: false,
            processData: false,
            success: function (json) {

                if (json['success'] == 1) {
                    showAlertMessage(locale.save_done);
                    is_change = false;

                } else
                    showAlertMessage(locale.save_false, true);
            }

        });
    });

    // Сохранить и закрыть из карточки
    $("button[name=saveID").on('click', function () {
        $(window).unbind("beforeunload");
    });

    // Иконки оформления меню
    $(".deleteone, .delete, .value-delete").append(' <span class="glyphicon glyphicon-trash"></span>');

    // Удаление из карточки
    $(".deleteone").on('click', function (event) {
        event.preventDefault();

        $.MessageBox({
            buttonDone: "OK",
            buttonFail: locale.cancel,
            message: locale.confirm_delete
        }).done(function () {

            $('#product_edit').append('<input type="hidden" name="delID" value="1">');
            $('#product_edit').append('<input type="hidden" name="ajax" value="1">');
            $('#product_edit').ajaxSubmit({
                dataType: "json",
                success: function (json) {

                    if (json['success'] == 1) {

                        if ($.getUrlVar('frame') !== undefined) {
                            parent.window.$('#adminModal').modal('hide');
                            parent.window.location.reload();
                        } else
                            window.location.href = '?path=' + $('#path').val();
                    } else
                        showAlertMessage(locale.save_false, true);
                }
            });
        })
    });

    // Удаление из списка
    $("body").on('click', ".data-row .delete", function (event) {
        event.preventDefault();
        var id = $(this).closest('.data-row');
        var data_id = $(this).attr('data-id');

        $.MessageBox({
            buttonDone: "OK",
            buttonFail: locale.cancel,
            message: locale.confirm_delete
        }).done(function () {

            $('.list_edit_' + data_id).ajaxSubmit({
                dataType: "json",
                success: function (json) {
                    if (json['success'] == 1) {
                        if (typeof (table) != 'undefined')
                            table.fnDeleteRow(id.attr('data-row'));
                        else
                            id.remove();
                        showAlertMessage(locale.save_done);
                    } else
                        showAlertMessage(locale.save_false, true);
                }
            });
        })
    });

    // Редактировать из списка
    $("body").on('click', ".data-row .edit", function (event) {
        event.preventDefault();
        window.location.href = $(this).closest('.data-row').find('.list_edit_' + $(this).attr('data-id')).attr('action');
    });

    // Редактировать из списка dropdown
    $("body").on('mouse', "#dropdown_action", function () {
        $("input:checkbox[name=items]").each(function () {
            this.checked = !this.checked && !this.disabled;
        });
    });

    // Активация из списка dropdown
    $('.data-row').hover(
            function () {
                $(this).find('#dropdown_action').show();
            },
            function () {
                $(this).find('#dropdown_action').hide();
            });

    // Выбор всех элементов через checkbox
    $('body').on('click', "#select_all", function () {
        $('ul.select-action > li').toggleClass('disabled');

        // Постоянное меню
        $('ul.select-action > li > a.enabled').parent('li').removeClass('disabled');

        $("body input:checkbox[name=items]").each(function () {
            this.checked = !this.checked && !this.disabled;
        });
    });

    // Выбор элемента через checkbox
    $("body").on('click', "input[name=items]", function () {
        $('ul.select-action > li').removeClass('disabled');
    });

    // Кнопки в Action Panel
    $(".btn-action-panel").on('click', function () {
        window.location.href = '?path=' + $(this).attr('name');
    });

    // Закрыть в Action Panel
    $(".btn-action-back").on('click', function () {
        history.back(1);
    });

    // Предпросмотр из главного меню
    $(".go2front").on('click', function () {
        if ($('.front').length) {
            $(this).attr('href', $('.front').attr('href'));
        } else if ($.cookie('cat') !== 'null' && $.cookie('cat') !== 'undefined') {
            $(this).attr('href', '../../shop/CID_' + $.cookie('cat') + '.html');
        } else
            $(this).attr('href', '../../');
    });

    // Открытие страницы в Action Panel
    $(".btn-action-panel-blank").on('click', function (event) {
        event.preventDefault();
        window.open($(this).attr('name'));
    });

    if (typeof is_mobile !== 'undefined') {
        locale.dataTable.paginate.next = "»";
        locale.dataTable.paginate.previous = "«";
    }

    // Таблица сортировки
    if (typeof (TABLE_EVENT) == 'undefined') {

        if (typeof ($.cookie('data_length')) == 'undefined')
            var data_length = [10, 25, 50];
        else
            var data_length = [parseInt($.cookie('data_length')), 10, 25, 50];

        var table = $('#data').dataTable({
            "lengthMenu": data_length,
            "paging": true,
            "ordering": true,
            "info": false,
            "searching": true,
            "language": locale.dataTable,
            "aaSorting": [],
            "columnDefs": [
                {"orderable": false, "targets": 0}
            ],
            "fnDrawCallback": function () {
                $('.toggle-event').bootstrapToggle();
            },

        });

        // Проверка checked в пагинации
        $('#data').on('draw.dt', function () {
            if ($('#select_all').prop("checked")) {
                $("input:checkbox[name=items]").each(function () {
                    this.checked = 'checked';
                });
            }
        });

    }

    // Сохранение настройки пагинатора
    $('select[name="data_length"]').on('change', function () {
        if (this.value > 10)
            $.cookie('data_length', this.value, {
                path: '/phpshop/admpanel/',
                expires: 365
            });
        else
            $.removeCookie('data_length', {path: '/phpshop/admpanel/'});
    });

    // Подсказки 
    $('[data-toggle="tooltip"]').tooltip({container: 'body'});

    // Сolorpicker
    if ($('.color').length)
        $('.color').colorpicker({format: 'hex'});

    // Новые заказы
    if ($('#orders-check').html() > 0) {
        $('#orders-check').parent('.navbar-btn').removeClass('hide');
        $('#orders-mobile-check').removeClass('hide');
    }

    // Новые диалоги
    if ($('#dialog-check').html() > 0) {
        $('#dialog-check').parent('.navbar-btn').removeClass('hide');
    }
    
    // Новые лиды
    if ($('#notes-check').html() > 0) {
        $('#notes-check').parent('.navbar-btn').removeClass('hide');
    }


    // Уроки
    if (typeof presentation_start != 'undefined') {
        $('#presentation-select').click();
    }

    // Переход к элементу по хешу
    if (window.location.hash != '') {
        var el = $("a[name='set" + window.location.hash.split('#').join('') + "']");
        if (typeof el.offset() != 'undefined') {
            $('html, body').animate({scrollTop: el.offset().top - 50}, 500);
        }
    }

    // Переход к элементу закладки по ?tab
    if ($.getUrlVar('tab') !== undefined) {
        $('#myTabs a[href="#tabs-"' + $.getUrlVar('tab') + '"]').tab('show');
    }

    // Filemanager в отдельное окно
    $('#filemanagerwindow').on('click', function () {
        var w = '1240';
        var h = '550';
        var url = $('.elfinder-modal-content').attr('src');
        filemanager = window.open(url + '&resizable=1', "chat", "dependent=1,left=100,top=100,width=" + w + ",height=" + h + ",location=0,menubar=0,resizable=1,scrollbars=0,status=0,titlebar=0,toolbar=0");
        filemanager.focus();
        $('#elfinderModal').modal('hide');
    });

    // Progress
    if (parent.window.$('#adminModal') && $.getUrlVar('frame') !== undefined) {
        parent.window.$('.progress-bar').css('width', '90%');
        setTimeout(function () {
            parent.window.$('.progress').hide();
        }, 500);
    }

    // Новые диалоги
    setInterval(function () {
        var data = [];
        data.push({name: 'selectID', value: 1});
        data.push({name: 'actionList[selectID]', value: 'actionGetNew'});

        $.ajax({
            mimeType: 'text/html; charset=' + locale.charset,
            url: '?path=dialog',
            type: 'post',
            data: data,
            dataType: "json",
            async: false,
            success: function (json) {
                var old_num = (Number($('#dialog-check').text()) || 0);
                $('#dialog-check').text(json['num']);
                $('#dialog-mobile-check').text(json['num']);

                if (json['num'] > 0) {
                    $('#dialog-mobile-check').removeClass('hide');
                } else {
                    $('#dialog-mobile-check').addClass('hide');
                }

                if (old_num < json['num']) {
                    $('#play-chat').trigger("play");
                    //$('#dialog-check').parent('.navbar-btn').removeClass('hide');
                }
            }
        });

    }, 30000);

    // Новые заказы
    setInterval(function () {
        var data = [];
        data.push({name: 'selectID', value: 1});
        data.push({name: 'actionList[selectID]', value: 'actionGetNew'});

        $.ajax({
            mimeType: 'text/html; charset=' + locale.charset,
            url: '?path=order',
            type: 'post',
            data: data,
            dataType: "json",
            async: false,
            success: function (json) {
                var old_num = (Number($('#orders-check').text()) || 0);
                $('#orders-check').text(json['num']);
                $('#orders-mobile-check').text(json['num']);
                if (old_num < json['num']) {
                    $('#play').trigger("play");
                    $('#orders-check').parent('.navbar-btn').removeClass('hide');
                }
            }
        });

    }, 30000);

    // Сгенерировать пароль
    $(".password-gen").on('click', function (event) {
        event.preventDefault();
        $('input[name=password_new],input[name=password2_new]').val($(this).attr('data-password'));
        $('input:password').attr("type", "text");
        $.MessageBox({
            buttonDone: "OK",
            message: $(this).attr('data-text') + $(this).attr('data-password')
        });
    });

    // Отображение пароля
    $(".password-view").on('click', function (event) {
        event.preventDefault();
        $('input:password').attr("type", "text");
    });

    // Preloader
    $('.main').removeClass('transition');

    // Состояние измеений форм
    var is_change = false;

    // Форма редактировалась
    $('#product_edit .form-control, #product_edit .hidden-edit, #product_edit input:radio, #product_edit input:checkbox').change(function () {
        is_change = true;
    });

    // Форма редактировалась редактором
    $('#product_edit .redactor-editor').focusout(function () {
        is_change = true;
    });

    // Сообщение сохранить 
    $(window).bind("beforeunload", function () {
        if (is_change)
            return "Are you sure you want to exit? Please complete sign up or the app will get deleted.";
    });


    // AI
    $("body").on('click', ".ai-help", function () {

        var obj = $(this).attr('data-value');
        var user = $(this).attr('data-user');
        var length = $(this).attr('data-length');
        var role = $(this).attr('data-role');

        if ($('[name="' + user + '"]').val() !== "" && $('[name="' + user + '"]').val() !== undefined)
            var text = $('[name="' + user + '"]').val();
        else
            var text = $('[name="' + obj + '"]').val();

        if (text == "") {

            if ($('[name="name_new"]').val() !== undefined) {
                text = $('[name="name_new"]').val();
            }

            if ($('[name="zag_new"]').val() !== undefined)
                text = $('[name="zag_new"]').val();

            if ($('[name="tema_new"]').val() !== undefined)
                text = $('[name="tema_new"]').val();
        }

        $.MessageBox({
            buttonDone: "OK",
            buttonFail: locale.cancel,
            message: locale.confirm_ai_help
        }).done(function () {

            var data = [];
            data.push({name: 'text', value: text});
            data.push({name: 'length', value: length});
            data.push({name: 'role', value: role});

            $.ajax({
                mimeType: 'text/html; charset=' + locale.charset,
                url: './system/ajax/gpt.ajax.php',
                data: data,
                type: 'post',
                dataType: "json",
                async: false,
                success: function (json) {
                    if (json['success'] == 1) {

                        $.MessageBox({
                            buttonDone: locale.ai_done,
                            buttonFail: locale.cancel,
                            message: json['text'],
                            width: "50%"
                        }).done(function () {


                            if (typeof tinymce !== "undefined" && tinymce.get(obj) !== null) {
                                tinymce.get(obj).setContent(json['text']);
                            }

                            $('[name="' + obj + '"]').val(json['text']);
                        })

                    } else {
                        $.MessageBox({
                            buttonDone: "OK",
                            buttonFail: locale.cancel,
                            message: locale.ai_false
                        })
                    }
                }
            });
        })
    });

});

// GET переменные из URL страницы
$.extend({
    getUrlVars: function () {
        var vars = [], hash;
        var hashes = window.location.href.slice(window.location.href.indexOf('?') + 1).split('&');
        for (var i = 0; i < hashes.length; i++)
        {
            hash = hashes[i].split('=');
            vars.push(hash[0]);
            vars[hash[0]] = hash[1];
        }
        return vars;
    },
    getUrlVar: function (name) {
        return $.getUrlVars()[name];
    }
});

function imgerror(obj) {
    obj.src = './images/no_photo.gif';
}