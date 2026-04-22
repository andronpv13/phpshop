// Переопределение функции
var TABLE_EVENT = true;
var ajax_path = "./catalog/ajax/";

$().ready(function () {

    // Id каталога
    cat = $.getUrlVar('cat');

    // Блокировка ссылки на товары
    if ($('.viewproduct').hasClass('disabled'))
        $('.viewproduct').addClass('disabled');

    // Создать из списка Modal
    $("button[name=addNewModal]").on('click', function () {

        var href = '?path=product&return=catalog&action=new&frame=true&admin=true';
        if (cat > 0)
            href += '&cat=' + cat;

        $('.product-modal-content').attr('height', $(window).height() - 120);
        $('.product-modal-content').attr('src', href);
        $('#adminModal .modal-title').text(locale.creature);
        $('#adminModal').modal('toggle');
    });

    // Создать из модальной карточки карточки
    $("button[name=actionInsert]").on('click', function (event) {
        event.preventDefault();

        $(window).unbind("beforeunload");

        var data = [];
        data.push({name: 'saveID', value: 1});
        $('#product_edit .form-control, #product_edit .hidden-edit, #product_edit input:radio:checked, #product_edit input:checkbox:checked').each(function () {
            if ($(this).attr('name') !== undefined) {
                data.push({name: $(this).attr('name'), value: escape($(this).val())});
            }
        });

        $('#product_edit').ajaxSubmit({
            data: data,
            dataType: "html",
            contentType: false,
            processData: false,
            success: function () {
                parent.window.$('#adminModal').modal('hide');
                var cat = $('[name=category_new]').selectpicker('val');
                if (cat > 0)
                    parent.window.location.href = '?path=catalog&cat=' + cat;
                else
                    parent.window.location.reload();
            }

        });
    });

    // Editor в отдельное окно
    $('#adminModal #filemanagerwindow').on('click', function () {
        var url = $('.product-modal-content').attr('src');
        parent.window.location.href = url.split('&frame=true&admin=true').join('');
        $('#openAdminModal').modal('hide');
    });

    // Модальное окно открытие переход prev
    $("body").on('click', '.modal-prev', function (event) {
        event.preventDefault();

        var id = $(this).attr('data-id');
        var next = parent.window.$('[data-id="' + id + '"]').closest('.data-row').prev();
        var href = next.find('.adminModal').attr('href');

        if (typeof (href) != 'undefined') {
            parent.window.$('.product-modal-content').attr('src', href + '&frame=true&admin=true');
            parent.window.$('#adminModal #productlink').attr('href', href);

            parent.window.$('.modal-next').attr('data-id', next.find('.adminModal').attr('data-id'));
            parent.window.$('#adminModal #productlink').attr('href', next.find('a').attr('href'));
        } else
            $(this).addClass('disabled');
    });

    // Модальное окно открытие переход next
    $("body").on('click', '.modal-next', function (event) {
        event.preventDefault();

        var id = $(this).attr('data-id');
        var next = parent.window.$('[data-id="' + id + '"]').closest('.data-row').next();
        var href = next.find('.adminModal').attr('href');

        if (typeof (href) != 'undefined') {
            parent.window.$('.product-modal-content').attr('src', href + '&frame=true&admin=true');
            parent.window.$('#adminModal #productlink').attr('href', href);

            parent.window.$('.modal-next').attr('data-id', next.find('.adminModal').attr('data-id'));
            parent.window.$('#adminModal #productlink').attr('href', next.find('a').attr('href'));
        } else
            $(this).addClass('disabled');
    });

    // Модальное окно открытие
    $("body").on('click', '.adminModal', function (event) {
        event.preventDefault();

        $('.modal-next').attr('data-id', $(this).attr('data-id'));
        $('#adminModal .modal-title').text($('#catname').text());
        $('.product-modal-content').attr('height', $(window).height() - 120);
        $('.product-modal-content').attr('src', $(this).attr('href') + '&frame=true&admin=true');

        $('#adminModal #productlink').attr('href', $(this).closest('.data-row').find('a').attr('href'));
        $('#adminModal').modal('toggle');

    });

    // Модальное окно закрытие
    $('#adminModal').on('hidden.bs.modal', function (event) {

        var cat = $.cookie('cat');
        $('.product-modal-content').attr('src', null);
        $.cookie('cat', cat);

        if (adminModal.is_change) {
            var cat = adminModal.window.$('[name=category_new]').selectpicker('val');
            if (cat > 0)
                parent.window.location.href = '?path=catalog&cat=' + cat;
            else
                parent.window.location.reload();
        }
    });

    // Предпросмотр
    $(".cat-view").on('click', function (event) {
        event.preventDefault();
        window.open('../../shop/CID_' + $.cookie('cat') + '.html');
    });

    // Назад к категории из товаров
    $("#btnBackProduct, .cat-select").on('click', function (event) {
        var path = $.getUrlVar('path');

        if ($.cookie('cat') != 'undefined' && path == 'catalog' && typeof $.getUrlVar('cat') == 'undefined') {
            event.preventDefault();
            window.location.href = '?path=catalog&id=' + $.cookie('cat');
        }

    });

    // Дерево категорий
    $('#tree [role="progressbar"]').css('width', '90%');
    if ($('#tree').length) {

        $.ajax({
            type: "GET",
            url: ajax_path + "tree.ajax.php",
            data: "id=" + $.getUrlVar('id') + '&cat=' + $.getUrlVar('cat') + '&action=' + $.getUrlVar('action') + '&path=' + $.getUrlVar('path'),
            dataType: "html",
            async: false,
            success: function (json)
            {
                $('#tree').treeview({
                    data: json,
                    enableLinks: false,
                    showIcon: true,
                    color: $('#temp-color').css('color'),
                    showBorder: false,
                    selectedBackColor: $('#temp-color-selected').css('color'),
                    onhoverColor: $('.navbar-action').css('background-color'),
                    backColor: "transparent",
                    expandIcon: 'glyphicon glyphicon-triangle-right',
                    collapseIcon: 'glyphicon glyphicon-triangle-bottom',
                    onNodeSelected: function (event, data) {

                        $("#data").DataTable().search("");

                        if (typeof (table) != 'undefined') {

                            $('.main').addClass('transition');
                            cat = data['tags'];
                            table.api().ajax.url(ajax_path + "product.ajax.php?cat=" + cat).load();

                            $('#select_all').prop('checked', false);

                            if (data['editable'] != 0) {

                                $('[name="addNew"]').attr('data-cat', cat);
                                $.cookie('cat', cat);
                                $('.cat-select, .cat-view').removeClass('hide');

                                $('#btnBackProduct').removeClass('disabled');
                            } else {
                                $('.cat-select, .cat-view').addClass('hide');
                            }
                        } else
                            window.location.href = '?path=catalog&id=' + data['tags'];
                    }
                });

                // Путь node
                $('#tree').treeview('getExpanded').forEach(function (entry) {
                    $('#tree').treeview('revealNode', [entry['nodeId'], {silent: true}]);
                });
            }
        });
    }

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
                url: '?path=catalog.select',
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
        data.push({name: 'cat', value: cat});
        data.push({name: 'actionList[selectID]', value: 'actionOption'});

        $.ajax({
            mimeType: 'text/html; charset=' + locale.charset,
            url: '?path=catalog.select',
            type: 'post',
            data: data,
            dataType: "html",
            async: false,
            success: function (data) {
                $('#selectModal .modal-dialog').removeClass('modal-lg');
                $('#selectModal .modal-title').html(locale.option_title);
                $('#selectModal .modal-footer .btn-primary').addClass('option-send');
                $('#selectModal .modal-footer .btn-delete').addClass('hidden');
                $('#selectModal .modal-footer .btn-primary').html(locale.ok);
                $('#selectModal .modal-body').html(data);
                $('#selectModal').modal('show');
            }
        });
    });

    // Расширенный поиск товара, выбор категории 
    $('body').on('change', 'select[name="where[category]"]', function () {

        var cat = $(this).val();
        var data = [];
        data.push({name: 'selectID', value: 1});
        data.push({name: 'ajax', value: 1});
        data.push({name: 'cat', value: cat});
        data.push({name: 'actionList[selectID]', value: 'actionAdvanceSearch'});

        $.ajax({
            mimeType: 'text/html; charset=' + locale.charset,
            url: '?path=catalog.search',
            type: 'post',
            data: data,
            dataType: "html",
            async: false,
            success: function (data) {
                $('#selectModal .modal-dialog').removeClass('modal-lg');
                $('#selectModal .modal-title').html(locale.search_advance_title);
                $('#selectModal .modal-footer .btn-primary').html(locale.search_advance_but);
                $('#selectModal .modal-footer .btn-primary').addClass('search-send');
                $('#selectModal .modal-footer .btn-delete').addClass('hidden');
                $('#selectModal .modal-body').html(data);
                $('#selectModal').modal('show');
                $('#modal-form').attr('method', 'get');
                $("#data").DataTable().search("");
            }
        });

    });

    // Расширенный поиск товара - 1 шаг
    $(".search").on('click', function (event) {
        event.preventDefault();

        var data = [];
        data.push({name: 'selectID', value: 1});
        data.push({name: 'ajax', value: 1});
        data.push({name: 'cat', value: $.cookie('cat')});
        data.push({name: 'actionList[selectID]', value: 'actionAdvanceSearch'});

        $.ajax({
            mimeType: 'text/html; charset=' + locale.charset,
            url: '?path=catalog.search',
            type: 'post',
            data: data,
            dataType: "html",
            async: false,
            success: function (data) {
                $('#selectModal .modal-dialog').removeClass('modal-lg');
                $('#selectModal .modal-title').html(locale.search_advance_title);
                $('#selectModal .modal-footer .btn-primary').html(locale.search_advance_but);
                $('#selectModal .modal-footer .btn-primary').addClass('search-send');
                $('#selectModal .modal-footer .btn-delete').addClass('hidden');
                $('#selectModal .modal-body').html(data);
                $('#selectModal').modal('show');
                $('#modal-form').attr('method', 'get');
                $("#data").DataTable().search("");
            }
        });
    });

    // Расширенный поиск товара - 2 шаг
    $("body").on('click', ".search-send", function (event) {
        event.preventDefault();
        var push = '?from=search&';
        $('#modal-form .form-control,  #modal-form input:radio:checked, #modal-form input:checkbox:checked').each(function () {
            if ($(this).attr('name') !== undefined) {
                push += $(this).attr('name') + '=' + escape($(this).val()) + '&';

            }
        });
        table.api().ajax.url(ajax_path + 'product.ajax.php' + push).load();
        $('#selectModal').modal('hide');

    });

    // Переход на страницу из списка
    $("body").on('click', '#dropdown_action .url', function (event) {
        event.preventDefault();
        window.open('../../shop/UID_' + $(this).attr('data-id') + '.html');
    });

    // Редактировать с выбранными - 2 шаг
    $("body").on('click', "#selectModal .modal-footer .edit-select-send", function (event) {
        event.preventDefault();

        if (typeof cat != 'undefined')
            var current_cat = '&cat=' + cat;
        else
            var current_cat = '';

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
                url: '?path=catalog.select',
                type: 'post',
                data: data,
                dataType: "json",
                async: false,
                success: function () {
                    window.location.href = '?path=catalog.select' + current_cat;
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

    // Копировать ID с выбранными
    $(".select-action .copy-id-select").on('click', function (event) {
        event.preventDefault();

        if ($('#data input[name="items"]:checkbox:checked').length) {
            var data = '';
            $('#data input[name="items"]:checkbox:checked').each(function () {
                if (this.value != 'all')
                    data += $(this).attr('data-id') + ',';
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

    // Удалить отложенные
    $(".select-action .id-select-delete").on('click', function (event) {
        event.preventDefault();

        if ($('#data input[name="items"]:checkbox:checked').length) {

            if ($.cookie('idselect') !== undefined) {
                var data = $.cookie('idselect');
                var idselect = eval(data);
            } else
                var idselect = [];

            var cur = 0;
            var count = 0;

            $('#data input[name="items"]:checkbox:checked').each(function () {
                if (this.value != 'all') {

                    cur = $.inArray($(this).attr('data-id'), idselect);

                    if (cur > 0) {
                        idselect.splice(cur, 1);
                        count++;
                    }
                }

            });

            $.MessageBox({
                buttonDone: "OK",
                buttonFail: locale.cancel,
                message: locale.confirm_wishlist_delete
            }).done(function () {

                showAlertMessage(locale.wishlist_delete_done + ' ' + count);

                $.cookie('idselect', JSON.stringify(idselect), {
                    path: '/',
                    expires: 365
                });
            })

        } else
            alert(locale.select_no);
    });

    // Отложить выбранные
    $(".select-action .id-select").on('click', function (event) {
        event.preventDefault();

        if ($('#data input[name="items"]:checkbox:checked').length) {

            if ($.cookie('idselect') !== undefined) {
                var data = $.cookie('idselect');
                var idselect = eval(data);
            } else
                var idselect = [];

            var count = 0;
            $('#data input[name="items"]:checkbox:checked').each(function () {
                if (this.value != 'all') {

                    if ($.inArray($(this).attr('data-id'), idselect) < 0) {
                        idselect.push($(this).attr('data-id'));
                    }

                    count++;
                }

            });

            $.MessageBox({
                buttonDone: "OK",
                buttonFail: locale.cancel,
                message: locale.confirm_wishlist
            }).done(function () {

                showAlertMessage(locale.wishlist_done + ' ' + count);

                $.cookie('idselect', JSON.stringify(idselect), {
                    path: '/',
                    expires: 365
                });
            })

        } else
            alert(locale.select_no);
    });

    // Редактировать с выбранными - 1 шаг
    $(".select-action .edit-select").on('click', function (event) {
        event.preventDefault();

        if ($('#data input[name="items"]:checkbox:checked').length) {
            var data = [];
            $('#data input[name="items"]:checkbox:checked').each(function () {
                if (this.value != 'all')
                    data.push({name: 'select[' + $(this).attr('data-id') + ']', value: $(this).attr('data-id')});

            });

            data.push({name: 'selectID', value: 1});
            data.push({name: 'ajax', value: 1});
            data.push({name: 'actionList[selectID]', value: 'actionSelect'});

            $.ajax({
                mimeType: 'text/html; charset=' + locale.charset,
                url: '?path=catalog.select',
                type: 'post',
                data: data,
                dataType: "html",
                async: false,
                success: function (data) {
                    $('#selectModal .modal-title').html(locale.select_title);
                    $('#selectModal .modal-footer .btn-primary').html(locale.select_edit);
                    $('#selectModal .modal-footer .btn-primary').addClass('edit-select-send');
                    $('#selectModal .modal-dialog').addClass('modal-lg');
                    $('#selectModal .modal-body').html(data);
                    $('#selectModal').modal('show');
                }
            });
        } else
            alert(locale.select_no);
    });

    // Экспортировать с выбранными
    $(".select-action .export-select").on('click', function (event) {
        event.preventDefault();

        if ($('#data input[name="items"]:checkbox:checked').length) {
            var data = [];
            $('#data input[name="items"]:checkbox:checked').each(function () {
                if (this.value != 'all')
                    data.push({name: 'select[' + $(this).attr('data-id') + ']', value: $(this).attr('data-id')});
            });

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
                    window.location.href = '?path=exchange.export';
                }

            });
        } else
            alert(locale.select_no);
    });


    // Управление деревом категорий
    $('.title-icon .glyphicon-chevron-down').on('click', function () {
        $('#tree').treeview('expandAll', {silent: true});
    });

    $('.title-icon .glyphicon-chevron-up').on('click', function () {
        $('#tree').treeview('collapseAll', {silent: true});
    });

    $('body').on('focus', '.editable', function () {
        if (
                $(this).attr('data-edit') === 'price_new' ||
                $(this).attr('data-edit') === 'price2_new' ||
                $(this).attr('data-edit') === 'price3_new' ||
                $(this).attr('data-edit') === 'price4_new' ||
                $(this).attr('data-edit') === 'price5_new' ||
                $(this).attr('data-edit') === 'price_megamarket_new' ||
                $(this).attr('data-edit') === 'price_ozon_new' ||
                $(this).attr('data-edit') === 'price_wb_new' ||
                $(this).attr('data-edit') === 'price_yandex_new' ||
                $(this).attr('data-edit') === 'price_yandex_2_new' ||
                $(this).attr('data-edit') === 'price_yandex_3_new' ||
                $(this).attr('data-edit') === 'price_avito_new' ||
                $(this).attr('data-edit') === 'price_google_new' ||
                $(this).attr('data-edit') === 'price_cdek_new' ||
                $(this).attr('data-edit') === 'price_aliexpress_new' ||
                $(this).attr('data-edit') === 'price_sbermarket_new'
                ) {
            $(this).attr('old-value', $(this).val());
        }
    });

    // Изменение данных из списка (цена, склад)
    $('body').on('change', '.editable', function () {
        var elem = $(this);
        if (
                elem.attr('data-edit') === 'price_new' && Number(elem.attr('old-value')) > 0 ||
                elem.attr('data-edit') === 'price2_new' && Number(elem.attr('old-value')) > 0 ||
                elem.attr('data-edit') === 'price3_new' && Number(elem.attr('old-value')) > 0 ||
                elem.attr('data-edit') === 'price4_new' && Number(elem.attr('old-value')) > 0 ||
                elem.attr('data-edit') === 'price5_new' && Number(elem.attr('old-value')) > 0 ||
                elem.attr('data-edit') === 'price_megamarket_new' && Number(elem.attr('old-value')) > 0 ||
                elem.attr('data-edit') === 'price_ozon_new' && Number(elem.attr('old-value')) > 0 ||
                elem.attr('data-edit') === 'price_wb_new' && Number(elem.attr('old-value')) > 0 ||
                elem.attr('data-edit') === 'price_yandex_new' && Number(elem.attr('old-value')) > 0 ||
                elem.attr('data-edit') === 'price_yandex_2_new' && Number(elem.attr('old-value')) > 0 ||
                elem.attr('data-edit') === 'price_yandex_3_new' && Number(elem.attr('old-value')) > 0 ||
                elem.attr('data-edit') === 'price_avito_new' && Number(elem.attr('old-value')) > 0 ||
                elem.attr('data-edit') === 'price_google_new' && Number(elem.attr('old-value')) > 0 ||
                elem.attr('data-edit') === 'price_cdek_new' && Number(elem.attr('old-value')) > 0 ||
                elem.attr('data-edit') === 'price_aliexpress_new' && Number(elem.attr('old-value')) > 0 ||
                elem.attr('data-edit') === 'price_sbermarket_new' && Number(elem.attr('old-value')) > 0
                ) {
            // Если новая цена на 20% меньше - выводим предупреждение, иначе - редактируем сразу
            if (Number(elem.val()) * 100 / Number(elem.attr('old-value')) < 80) {
                $.MessageBox({
                    buttonDone: "OK",
                    buttonFail: locale.cancel,
                    message: locale.confirm_change_price
                }).done(function () {
                    fastDatatableEdit(elem);
                }).fail(function () {
                    elem.val(elem.attr('old-value'));
                });
            } else {
                fastDatatableEdit(elem);
            }
        } else {
            fastDatatableEdit(elem);
        }
    });

    // Ссылка в Node
    $('#tree').on('nodeSelected', function (event, data) {
        if (data['href'])
            window.location.href = './admin.php' + data['href'];
    });

    // Поиск категорий
    var search = function (e) {
        var pattern = $('#input-category-search').val();
        var options = {
            ignoreCase: true, // case insensitive
            exactMatch: false, // like or equals
            revealResults: true // reveal matching nodes
        };
        var results = $('#tree').treeview('search', [pattern, options]);
    };
    $('#btn-search').on('click', search);

    $('#show-category-search').on('click', function () {
        $('#category-search').slideToggle('slow');
    });

    $('#input-category-search').keyup(function (event) {
        if (event.keyCode == '13') {
            event.preventDefault();
            search();
        }
        return false;
    });

    // Создать новый товар из списка
    $("button[name=addNew]").on('click', function () {
        //var cat = $(this).attr('data-cat');
        var href = '?path=product&return=catalog&action=new';
        if (cat > 0)
            href += '&cat=' + cat;
        window.location.href = href;
        action = true;
    });


    // Создать новый каталог из списка
    $("button[name=addNewCat], .addNewCat").on('click', function () {
        var href = '?path=catalog&action=new';
        window.location.href = href;
        action = true;
    });


    // Создать копию из списка
    $(".select-action .copy").on('click', function (event) {
        event.preventDefault();
        window.location.href = '?path=product&return=catalog&action=new&id=' + $('input[name=rowID]').val();
    });

    // Создать копию из списка dropdown
    $("body").on('click', ".data-row .copy", function (event) {
        event.preventDefault();
        window.location.href = '?path=product&return=catalog&action=new&id=' + $(this).attr('data-id');
    });


    // Активация из списка dropdown
    $("body").on('mouseenter', '.data-row', function () {
        $(this).find('#dropdown_action').show();
        $(this).find('.editable').removeClass('input-hidden');
        $(this).find('.media-object').addClass('image-shadow');
    });
    $("body").on('mouseleave', '.data-row', function () {
        $(this).find('#dropdown_action').hide();
        $(this).find('.editable').addClass('input-hidden');
        $(this).find('.media-object').removeClass('image-shadow');
    });

    // Таблица данных
    if (typeof ($.cookie('data_length')) == 'undefined')
        var data_length = [10, 25, 50, 75, 100, 500, 1000];
    else
        var data_length = [parseInt($.cookie('data_length')), 10, 25, 50, 75, 100, 500, 1000];

    // Мобильная навигация
    if (typeof is_mobile !== 'undefined') {
        locale.dataTable.paginate.next = "»";
        locale.dataTable.paginate.previous = "«";
    }

    if ($('#data').html()) {
        var table = $('#data').dataTable({
            "ajax": {
                "type": "GET",
                "url": ajax_path + 'product.ajax.php' + window.location.search,
                "dataSrc": function (json) {
                    $('#catname').text(json.catname);
                    $('#select_all').prop('checked', false);
                    return json.data;
                }
            },
            "processing": true,
            "serverSide": true,
            "paging": true,
            "ordering": true,
            "info": false,
            "searching": true,
            "lengthMenu": data_length,
            "language": locale.dataTable,
            "stripeClasses": ['data-row', 'data-row'],
            "fnDrawCallback": function () {

                $('.toggle-event').bootstrapToggle();
                $('.main').removeClass('transition');

            },
            "aoColumnDefs": [{
                    'bSortable': false,
                    'aTargets': ['sorting-hide']
                }]
        });
    }

    $('.fix-products').on('click', function () {
        $.MessageBox({
            buttonDone: "OK",
            buttonFail: locale.cancel,
            message: locale.create_db_dump
        }).done(function () {
            $.ajax({
                mimeType: 'text/html; charset=' + locale.charset,
                url: '?path=catalog.list',
                type: 'post',
                data: {
                    'actionList[rowID]': 'actionDeleteProducts',
                    'rowID': 1,
                    'mode': $('select[name="fix_products"]').val(),
                    'ajax': 1
                },
                dataType: "json",
                async: false,
                success: function (json) {
                    if (json['success'] == 1) {
                        showAlertMessage(locale.done + '. ' + locale.products_completed + ' ' + json['count']);
                    } else
                        showAlertMessage(locale.save_false, true);
                }
            });
        });
    });

    $('.fix-category').on('click', function () {

        $.MessageBox({
            buttonDone: "OK",
            buttonFail: locale.cancel,
            message: locale.create_db_dump
        }).done(function () {
            $.ajax({
                mimeType: 'text/html; charset=' + locale.charset,
                url: '?path=catalog.list',
                type: 'post',
                data: {
                    'actionList[rowID]': 'actionDeleteCategory',
                    'rowID': 1,
                    'mode': $('select[name="fix_category"]').val(),
                    'ajax': 1
                },
                dataType: "json",
                async: false,
                success: function (json) {
                    if (json['success'] == 1) {
                        showAlertMessage(locale.products_completed + ' ' + json['count']);
                    } else
                        showAlertMessage(locale.save_false, true);
                }
            });
        });
    });

    $(".delete-category").on('click', function (event) {
        event.preventDefault();

        $.MessageBox({
            message: locale.product_operations,
            buttonDone: {
                delete: {
                    text: locale.delete
                },
                move: {
                    text: locale.move_to_temporary_folder
                }
            },
        }).done(function (data, button) {
            $('#product_edit').append('<input type="hidden" name="delID" value="1">');
            $('#product_edit').append('<input type="hidden" name="products_operation" value="' + button + '">');
            $('#product_edit').append('<input type="hidden" name="ajax" value="1">');
            $('#product_edit').ajaxSubmit({
                dataType: "json",
                success: function (json) {
                    if (json['success'] == 1) {
                        if (json['count'] > 0) {
                            showAlertMessage(locale.products_completed + ' ' + json['count']);
                            setTimeout(function () {
                                window.location.href = '?path=' + $('#path').val();
                            }, 1500);
                        } else {
                            window.location.href = '?path=' + $('#path').val();
                        }
                    } else
                        showAlertMessage(locale.save_false, true);
                }
            });
        });
    });

    // Вывод подтипов в общем списке
    $("body").on('click', ".view-parent", function (event) {
        event.preventDefault();
        var el = $(this);
        var id = $(this).attr('data-id');

        if (el.attr('data-action') != 'off') {

            $.ajax({
                mimeType: 'text/html; charset=' + locale.charset,
                url: ajax_path + "product.ajax.php?&parents=" + id,
                type: 'get',
                async: false,
                success: function (html) {

                    el.closest('tr').after(html);
                    el.attr('data-action', 'off');
                    $('[data-parent="' + id + '"]').addClass('success').addClass('parent-list');
                    $('[data-parent="' + id + '"] img.media-object').addClass('media-object-parent');

                    // Toggle
                    $('.toggle-event').bootstrapToggle();
                }

            });
        } else {
            el.removeAttr('data-action');
            $('[data-parent="' + id + '"]').remove();
        }

    });

    // Редактировать значение подтипа в списке - 1 шаг 
    $("body").on('click', '.parent-list a[target="_self"]', function (event) {
        event.preventDefault();

        var data = [];
        var id = $(this).closest('.data-row').attr('data-id');
        var parent = $(this).closest('.data-row').attr('data-parent');
        data.push({name: 'selectID', value: id});
        data.push({name: 'parentID', value: parent});
        data.push({name: 'ajax', value: 1});
        data.push({name: 'actionList[selectID]', value: 'actionOptionEdit'});

        $.ajax({
            mimeType: 'text/html; charset=' + locale.charset,
            url: '?path=product&id=' + id + '&parent_name=' + escape($('input[name="name_new"]').val()),
            data: data,
            dataType: "html",
            async: false,
            success: function (data) {

                $('#selectModal .modal-title').html(locale.edit_option_value);
                $('#selectModal .modal-footer .btn-primary').removeClass('edit-select-send');
                $('#selectModal .modal-footer .btn-primary').addClass('value-edit-send');
                $('#selectModal .modal-footer .btn-delete').removeClass('hidden');
                $('#selectModal .modal-footer .btn-delete').addClass('value-delete');
                $('#selectModal .modal-body').html(data).css('height', $(window).height() - 180).css('overflow-y', 'scroll').css('padding', '15px');

                $('.color').colorpicker({
                    format: 'hex',
                });

                $('.elfinder-modal-content').attr('data-option', 'return=lfile');
                $('#selectModal').modal('show');
            }

        });
    });

    // Редактировать значение подтипа в списке - 2 шаг
    $("body").on('click', "#selectModal .modal-footer .value-edit-send", function (event) {
        event.preventDefault();

        var id = $('input[name=rowID]').val();
        var parent = $('input[name=parentID]').val();

        var data = [];
        data.push({name: 'editID', value: '1'});
        data.push({name: 'editParent', value: '1'});
        data.push({name: 'actionList[rowID]', value: 'actionUpdate.catalog.edit'});
        $('#modal-form .form-control, #modal-form .hidden-edit, #modal-form input:radio:checked, #modal-form input:checkbox:checked').each(function () {
            if ($(this).attr('name') !== undefined) {
                data.push({name: $(this).attr('name'), value: escape($(this).val())});
            }
        });

        $('#modal-form').attr('action', '?path=product&id=' + id);
        $('#modal-form').ajaxSubmit({
            data: data,
            dataType: "json",
            success: function (json) {
                $('#selectModal').modal('hide');
                if (json['success'] == 1) {

                    showAlertMessage(locale.save_done);
                } else
                    showAlertMessage(locale.save_false, true);
            }
        });
    });

    // Удаление подтипа из карточки
    $("body").on('click', "#selectModal .modal-footer .value-delete", function (event) {
        event.preventDefault();
        var id = $('input[name=rowID].hidden-edit').val();

        $.MessageBox({
            buttonDone: "OK",
            buttonFail: locale.cancel,
            message: locale.confirm_delete
        }).done(function () {
            var data = [];
            data.push({name: 'delID', value: '1'});
            data.push({name: 'actionList[delID]', value: 'actionDelete.catalog.edit'});
            data.push({name: 'parent_enabled', value: '1'});
            data.push({name: 'parent', value: $.getUrlVar('id')});

            $('#modal-form').attr('action', '?path=product&id=' + id);
            $('#modal-form').ajaxSubmit({
                data: data,
                dataType: "json",
                success: function (json) {
                    $('#selectModal').modal('hide');

                    if (json['success'] == 1) {
                        $('[data-id="' + id + '"]').remove();
                        showAlertMessage(locale.save_done);
                    } else
                        showAlertMessage(locale.save_false, true);
                }

            });

        })
    });

    // Объединить выбранные
    $(".select-action .join-select").on('click', function (event) {
        event.preventDefault();

        if ($('#data input[name="items"]:checkbox:checked').length) {
            var data = [];
            $('#data input[name="items"]:checkbox:checked').each(function () {
                if (this.value != 'all')
                    data.push({name: 'select[' + $(this).attr('data-id') + ']', value: $(this).attr('data-id')});

            });

            $.MessageBox({
                buttonDone: "OK",
                buttonFail: locale.cancel,
                message: locale.confirm_join
            }).done(function () {


                data.push({name: 'selectID', value: 1});
                data.push({name: 'ajax', value: 1});
                data.push({name: 'actionList[selectID]', value: 'actionJoin'});

                $.ajax({
                    mimeType: 'text/html; charset=' + locale.charset,
                    url: '?path=catalog.select',
                    type: 'post',
                    data: data,
                    dataType: "json",
                    async: false,
                    success: function (json) {
                        if (json['success'] == 1) {
                            showAlertMessage(locale.save_done);
                        } else
                            showAlertMessage(locale.save_false, true);
                    }
                });

            })

        } else
            alert(locale.select_no);
    });
});

function fastDatatableEdit(element)
{
    var data = [];

    data.push({name: element.attr('data-edit'), value: escape(element.val())});
    data.push({name: 'rowID', value: element.attr('data-id')});
    data.push({name: 'editID', value: 1});
    data.push({name: 'ajax', value: 1});
    data.push({name: 'actionList[editID]', value: 'actionUpdate.catalog.edit'});
    $.ajax({
        mimeType: 'text/html; charset=' + locale.charset,
        url: '?path=product&id=' + element.attr('data-id'),
        type: 'post',
        data: data,
        dataType: "json",
        async: false,
        success: function (json) {
            if (json['success'] == 1) {
                showAlertMessage(locale.save_done);
            } else
                showAlertMessage(locale.save_false, true);
        }
    });
}