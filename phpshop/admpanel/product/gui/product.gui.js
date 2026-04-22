$().ready(function () {

    // Создание видео
    $("body").on('click', "#videoCreate", function (event) {
        event.preventDefault();
        var id = $(this).attr('data-id');

        showProgressBar(locale.create_video);
        $('.progress-bar').css('width', '0%').html('0%');

        $.MessageBox({
            buttonDone: "OK",
            buttonFail: locale.cancel,
            message: locale.confirm_video
        }).done(function () {

            var data = [];
            data.push({name: 'productID', value: id});

            $.ajax({
                mimeType: 'text/html; charset=' + locale.charset,
                url: './editors/default/video/ajax/ffmpeg.ajax.php',
                data: data,
                type: 'post',
                dataType: "json",
                async: false,
                success: function (json) {
                    if (json['success'] == 1) {
                        $('.progress-bar').css('width', '100%').html('100%').removeClass('active');
                        $('.success-notification').delay(500).fadeOut(1000);
                        setTimeout(function () {
                            window.location.href = window.location.href.split('&tab=1').join('') + '&tab=1';
                        }, 5000);
                    } else
                        showAlertMessage(locale.save_false, true);
                }
            });
        }).fail(function () {
            $('.success-notification').delay(100).fadeOut(100);
        });
    });

    $("body").on('click', ".set-image-tab", function (e) {
        e.preventDefault();
        $('#selectModal').modal('hide');
        $('#myTabs a[data-id="Изображение"]').tab('show');
    });

    // Смена кода валюты
    $("body").on('change', '#baseinputvaluta_new', function () {
        $('[data-type="price"] .input-group-addon').html($(this).attr('data-code'));
    });

    // Копировать подтип
    $("body").on('click', '.data-row .value-copy', function () {

        var parent = $(this).closest('.data-row');
        var name = $(this).closest('.data-row').find('input[data-edit=parent_new]').val();
        var items = $(this).closest('.data-row').find('input[data-edit=items_new]').val();
        var price = $(this).closest('.data-row').find('input[data-edit=price_new]').val();
        var parent2 = $(this).closest('.data-row').find('input[data-edit=parent2_new]').val();
        var pic_small = $(this).closest('.data-row').find('img').attr('src');
        var pic_big = $(this).closest('.data-row').find('img').attr('data-big');

        if (name != '' || parent2 != '') {

            var data = [];

            data.push({name: 'actionList[saveID]', value: 'actionInsert.catalog.create'});
            data.push({name: 'saveID', value: 1});
            data.push({name: 'parent_new', value: escape(name)});
            data.push({name: 'items_new', value: items});
            data.push({name: 'price_new', value: price});
            data.push({name: 'parent2_new', value: escape(parent2)});
            data.push({name: 'name_new', value: escape($('[name="name_new"]').val() + ' ' + name + ' ' + parent2)});
            data.push({name: 'parent_enabled_new', value: 1});
            data.push({name: 'enabled_new', value: 1});
            data.push({name: 'ajax', value: 1});
            data.push({name: 'parent', value: $('input[name="rowID"]').val()});
            data.push({name: 'category_new', value: $('select[name="category_new"]').val()});
            data.push({name: 'baseinputvaluta_new', value: $('input[name="baseinputvaluta_new"]:checked').val()});
            data.push({name: 'pic_small_new', value: pic_small});
            data.push({name: 'pic_big_new', value: pic_big});

            $.ajax({
                mimeType: 'text/html; charset=' + locale.charset,
                url: '?path=product&action=new',
                type: 'post',
                data: data,
                dataType: "json",
                async: false,
                success: function (json) {
                    if (json['success'] != '') {
                        parent.after('<tr class="data-row" data-row="' + json['success'] + '"><td><img src="' + pic_small + '" data-big="' + pic_big + '" onerror="this.onerror = null;this.src = \'./images/no_photo.gif\'" class="media-object"></td><td style="text-align:left"><input style="width:100%" data-id="' + json['success'] + '" data-edit="parent_new" class="editable form-control input-sm"  value="' + name + '"></td><td style="text-align:left"><input style="width:100%" data-id="' + json['success'] + '" data-edit="parent2_new" class="editable form-control input-sm"  value="' + parent2 + '"></td><td style="text-align:left"><input style="width:100%" class="editable form-control input-sm" data-edit="items_new" data-id="' + json['success'] + '" value="' + parseInt(0 + items) + '"></td><td style="text-align:left"><input style="width:100%" class="editable form-control input-sm" data-edit="price_new" data-id="' + json['success'] + '" value="' + parseInt(0 + price) + '"></td><td style="text-align:center"><div class="dropdown" id="dropdown_action"><a href="#" class="dropdown-toggle btn btn-default btn-sm" data-toggle="dropdown" role="button" aria-expanded="false"><span class="glyphicon glyphicon-cog"></span> <span class="caret"></span></a><ul class="dropdown-menu" role="menu" ><li><a href="#" data-id="' + json['success'] + '" class="value-edit">Редактировать</a></li><li class="divider"></li><li><a href="#" data-id="' + json['success'] + '" class="value-delete">Удалить <span class="glyphicon glyphicon-trash"></span></a></li></ul></div></td><td></td></tr>');

                        // Цена главного товара
                        if ($('input[name="price_new"]').val() == 0)
                            $('input[name="price_new"]').val(price);

                        // Добавление в список изображений
                        $('.img-parent .selectpicker').prepend('<option value="' + json['success'] + '">' + escape(name) + '</option>');
                        $('.img-parent').selectpicker('refresh');

                        showAlertMessage(locale.save_done);

                    } else
                        showAlertMessage(locale.save_false, true);
                }
            });
            $(this).closest('.data-row').find('input[name=name2_option_new]').val('');
            $(this).closest('.data-row').find('input[name=name_option_new]').val('');
            $(this).closest('.data-row').find('input[name=items_value]').val('');
        }
    });


    // Добавить подтип
    $("body").on('click', 'button[name=addOption]', function () {

        var parent = $(this).closest('.data-row');
        var name = $(this).closest('.data-row').find('input[name=name_option_new]').val();
        var items = $(this).closest('.data-row').find('input[name=items_option_new]').val();
        var price = $(this).closest('.data-row').find('input[name=price_option_new]').val();
        var parent2 = $(this).closest('.data-row').find('input[name=name2_option_new]').val();

        if (name != '' || parent2 != '') {

            var data = [];

            data.push({name: 'actionList[saveID]', value: 'actionInsert.catalog.create'});
            data.push({name: 'saveID', value: 1});
            data.push({name: 'parent_new', value: escape(name)});
            data.push({name: 'items_new', value: items});
            data.push({name: 'price_new', value: price});
            data.push({name: 'parent2_new', value: escape(parent2)});
            data.push({name: 'name_new', value: escape($('[name="name_new"]').val() + ' ' + name + ' ' + parent2)});
            data.push({name: 'parent_enabled_new', value: 1});
            data.push({name: 'enabled_new', value: 1});
            data.push({name: 'ajax', value: 1});
            data.push({name: 'parent', value: $('input[name="rowID"]').val()});
            data.push({name: 'category_new', value: $('select[name="category_new"]').val()});
            data.push({name: 'baseinputvaluta_new', value: $('input[name="baseinputvaluta_new"]:checked').val()});

            $.ajax({
                mimeType: 'text/html; charset=' + locale.charset,
                url: '?path=product&action=new',
                type: 'post',
                data: data,
                dataType: "json",
                async: false,
                success: function (json) {
                    if (json['success'] != '') {
                        parent.before('<tr class="data-row" data-row="' + json['success'] + '"><td></td><td style="text-align:left"><input style="width:100%" data-id="' + json['success'] + '" data-edit="parent_new" class="editable form-control input-sm"  value="' + name + '"></td><td style="text-align:left"><input style="width:100%" data-id="' + json['success'] + '" data-edit="parent2_new" class="editable form-control input-sm"  value="' + parent2 + '"></td><td style="text-align:left"><input style="width:100%" class="editable form-control input-sm" data-edit="items_new" data-id="' + json['success'] + '" value="' + parseInt(0 + items) + '"></td><td style="text-align:left"><input style="width:100%" class="editable form-control input-sm" data-edit="price_new" data-id="' + json['success'] + '" value="' + parseInt(0 + price) + '"></td><td style="text-align:center"><div class="dropdown" id="dropdown_action"><a href="#" class="dropdown-toggle btn btn-default btn-sm" data-toggle="dropdown" role="button" aria-expanded="false"><span class="glyphicon glyphicon-cog"></span> <span class="caret"></span></a><ul class="dropdown-menu" role="menu" ><li><a href="#" data-id="' + json['success'] + '" class="value-edit">Редактировать</a></li><li class="divider"></li><li><a href="#" data-id="' + json['success'] + '" class="value-delete">Удалить <span class="glyphicon glyphicon-trash"></span></a></li></ul></div></td><td></td></tr>');

                        // Цена главного товара
                        if ($('input[name="price_new"]').val() == 0)
                            $('input[name="price_new"]').val(price);

                        // Добавление в список изображений
                        $('.img-parent .selectpicker').prepend('<option value="' + json['success'] + '">' + escape(name) + '</option>');
                        $('.img-parent').selectpicker('refresh');

                        showAlertMessage(locale.save_done);

                    } else
                        showAlertMessage(locale.save_false, true);
                }
            });
            $(this).closest('.data-row').find('input[name=name2_option_new]').val('');
            $(this).closest('.data-row').find('input[name=name_option_new]').val('');
            $(this).closest('.data-row').find('input[name=items_value]').val('');
        }
    });


    // Удаление подтипа из списка
    $("body").on('click', ".data-row .value-delete", function (event) {
        event.preventDefault();
        var id = $(this).attr('data-id');
        var parent = $(this).closest('.data-row');

        $.MessageBox({
            buttonDone: "OK",
            buttonFail: locale.cancel,
            message: locale.confirm_delete
        }).done(function () {

            var data = [];
            data.push({name: 'delID', value: '1'});
            data.push({name: 'rowID', value: id});
            data.push({name: 'actionList[delID]', value: 'actionDelete.catalog.edit'});
            data.push({name: 'parent_enabled', value: '1'});
            data.push({name: 'parent', value: $.getUrlVar('id')});

            $.ajax({
                mimeType: 'text/html; charset=' + locale.charset,
                url: '?path=product&id=' + id,
                data: data,
                type: 'post',
                dataType: "json",
                async: false,
                success: function (json) {
                    if (json['success'] == 1) {
                        parent.remove();
                        showAlertMessage(locale.save_done);
                    } else
                        showAlertMessage(locale.save_false, true);
                }
            });
        })
    });

    // Удаление подтипа из карточки
    $("body").on('click', "#selectModal .modal-footer .value-delete", function (event) {
        event.preventDefault();
        var id = $('input[name=rowID]').val();
        var parent = $('input[name=parentID]').val();

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
                        $('[data-row="' + parent + '"]').empty();
                        showAlertMessage(locale.save_done);
                    } else
                        showAlertMessage(locale.save_false, true);
                }

            });

        })
    });

    // Редактировать значение подтипа - 2 шаг
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

                    $('[data-row="' + parent + '"] :nth-child(2) input:text').val($('#modal-form input[name="parent_new"]').val());
                    $('[data-row="' + parent + '"] :nth-child(3) input:text').val($('#modal-form input[name="parent2_new"]').val());

                    if ($('#modal-form input[name="color_new"]').val() != '#ffffff')
                        $('[data-row="' + parent + '"] :nth-child(3) input:text').css('color', $('#modal-form input[name="color_new"]').val());

                    $('[data-row="' + parent + '"] :nth-child(4) input:text').val($('#modal-form input[name="items_new"]').val());
                    $('[data-row="' + parent + '"] :nth-child(5) input:text').val($('#modal-form input[name="price_new"]').val());

                    // Вывод
                    if (json['enabled'] == 1 && json['sklad'] == 0)
                        $('[data-row="' + parent + '"] :nth-child(7)').html('');
                    else
                        $('[data-row="' + parent + '"] :nth-child(7)').html('<span class="pull-right text-muted glyphicon glyphicon-eye-close" data-toggle="tooltip" data-placement="top" title="Скрыто"></span>');

                    // Цена главного товара
                    if ($('input[name="price_new"]').val() == 0)
                        $('input[name="price_new"]').val($('#modal-form input[name="price_new"]').val());

                    showAlertMessage(locale.save_done);
                } else
                    showAlertMessage(locale.save_false, true);
            }
        });
    });

    // Редактировать значение подтипа - 1 шаг 
    $("body").on('click', ".data-row .value-edit", function (event) {
        event.preventDefault();

        var data = [];
        var id = $(this).attr('data-id');
        var parent = $(this).closest('.data-row').attr('data-row');
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
                //$('#selectModal .modal-body').html(data).css('min-height', '620px');
                $('#selectModal .modal-body').html(data).css('height', $(window).height() - 200).css('overflow-y', 'scroll').css('padding', '15px');

                $('.color').colorpicker({
                    format: 'hex',
                });

                $('.elfinder-modal-content').attr('data-option', 'return=lfile');
                $('#selectModal').modal('show');
            }

        });
    });

    // Загрузка файлов на сервер
    $("body").on('click', '.btn-upload', function (event) {
        event.preventDefault();
        $("#uploader").contents().find('#send-btn').click();
    });

    // Пакетный загрузчик
    $("body").on('click', "#uploaderModal", function (event) {
        event.preventDefault();
        var id = $('input[name="rowID"]').val();
        var cat = $('[name="category_new"]').selectpicker('val');
        $('#selectModal .modal-body').html($('#elfinderModal .modal-body').html());
        $('#selectModal .elfinder-modal-content').attr('src', './product/gui/uploader.gui.php?id=' + id + '&cat=' + cat);
        $('#selectModal .elfinder-modal-content').attr('id', 'uploader');
        $('#selectModal .modal-title').html(locale.select_file + 'ы');
        $('#selectModal .modal-footer .btn-primary').addClass('btn-upload');
        $('#selectModal .modal-footer .btn-primary').prop("type", "button");

        $('#selectModal').modal('show');
    });

    // Память закладки для изображений
    $('a[data-toggle="tab"]').on('shown.bs.tab', function () {
        $('input[name="tabName"]').val($(this).attr('data-id'));
    });

    // Перезагрузка страницы при добавлении изображения
    $("button[name=editID]").on('click', function (event) {
        event.preventDefault();

        if ($('input[name="img_new"]').val()) {
            setTimeout(function () {
                window.location.href = window.location.href.split('&tab=1').join('') + '&tab=1';
            }, 5000);
        }
        // Мобильная версия с перезагрузкой
        else if ($('.navbar-right  button[name="saveID"]').is(":hidden") && $.getUrlVar('frame') === undefined) {
            $('#product_edit').append('<input type="hidden" name="saveID" value="1">');
            $('#product_edit').submit();
        }

        // Проверка характеристики
        $('.vendor_add').each(function () {
            if (this.value != '') {
                setTimeout(function () {
                    window.location.href = window.location.href.split('&tab=1').join('') + '&tab=5';
                }, 5000);
            }
        });
    });

    // закрепление навигации
    if ($('#fix-check:visible').length && typeof (WAYPOINT_LOAD) != 'undefined')
        var waypoint = new Waypoint({
            element: document.getElementById('fix-check'),
            handler: function (direction) {
                $('.navbar-action').toggleClass('navbar-fixed-top');
            }
        });

    // Указать ID товара в виде тега - Поиск
    $("body").on('click', "#selectModal .search-action", function (event) {
        event.preventDefault();

        var data = [];
        data.push({name: 'selectID', value: 2});
        data.push({name: 'ajax', value: 1});
        data.push({name: 'actionList[selectID]', value: 'actionSearch'});
        data.push({name: 'frame', value: $('#frame').val()});

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

    // Указать ID товара в виде тега  -  2 шаг
    $("body").on('click', "#selectModal .modal-footer .id-add-send", function (event) {
        event.preventDefault();

        $('.search-list input:checkbox').each(function () {
            var id = $(this).attr('data-id');
            $(selectTarget).removeTag(id);
        });


        $('.search-list input:checkbox:checked').each(function () {
            var id = $(this).attr('data-id');

            if ($(selectTarget).attr('data-tagsinput-init') == 'true')
                $(selectTarget).addTag(id);
            else
                $(selectTarget).val(id);
        });

        $('#selectModal').modal('hide');
    });

    // Выбор элемента по клику в модальном окне подбора товара
    $('body').on('click', ".search-list .product-name", function () {
        $(this).parent('tr').find('input:checkbox[name=items]').each(function () {
            this.checked = !this.checked && !this.disabled;
        });
    });

    // Указать ID товара в виде тега  - 1 шаг
    $(".tag-search").on('click', function (event) {
        event.preventDefault();

        selectTarget = $(this).attr('data-target');

        var data = [];
        data.push({name: 'selectID', value: 2});
        data.push({name: 'ajax', value: 1});
        data.push({name: 'currentID', value: $(selectTarget).val()});
        data.push({name: 'actionList[selectID]', value: 'actionSearch'});
        data.push({name: 'frame', value: $.getUrlVar('frame')});

        $.ajax({
            mimeType: 'text/html; charset=' + locale.charset,
            url: '?path=catalog.search',
            type: 'post',
            data: data,
            dataType: "html",
            async: false,
            success: function (data) {
                //$('#selectModal .modal-dialog').removeClass('modal-lg');
                $('#selectModal .modal-title').html(locale.add_cart_value);
                $('#selectModal .modal-footer .btn-primary').removeClass('edit-select-send');
                $('#selectModal .modal-footer .btn-primary').addClass('id-add-send');
                $('#selectModal .modal-footer .btn-delete').addClass('hidden');
                $('#selectModal .modal-body').css('max-height', ($(window).height() - 200) + 'px');
                $('#selectModal .modal-body').css('overflow-y', 'auto');
                $('#selectModal .modal-body').html(data);

                $(".search-list td input:checkbox").each(function () {
                    this.checked = true;
                });

                $('#selectModal').modal('show');
            }
        });
    });

    $('#odnotip_new').tagsInput({
        'height': '100px',
        'width': '100%',
        'interactive': true,
        'defaultText': locale.enter,
        'removeWithBackspace': true,
        'minChars': 0,
        'maxChars': 0,
        'placeholderColor': '#666666'
    });

    // Редактирование изображения товара
    $(".img-main").on('click', function (event) {
        event.preventDefault();

        $('input[name="pic_big_new"]').val($(this).attr('data-path'));
        $('input[name="pic_small_new"]').val($(this).attr('data-path-s'));

        $('[data-icon="pic_big_new"]').html($(this).attr('data-path'));
        $('[data-icon="pic_small_new"]').html($(this).attr('data-path-s'));
        $('[data-thumbnail="pic_big_new"]').attr('src', $(this).attr('data-path'));

        $('.img-main').removeClass('btn-success');
        $(this).removeClass('btn-default');
        $(this).addClass('btn-success');
    });

    // Удаление изображения товара
    $(".img-delete").on('click', function (event) {
        event.preventDefault();
        var data = [];
        var id = $(this).attr('data-id');
        var parent = $(this).closest('.data-row');
        var main = $(this).attr('data-main');
        data.push({name: 'ajax', value: 1});
        data.push({name: 'rowID', value: id});
        data.push({name: 'actionList[rowID]', value: 'actionImgDelete.catalog.edit'});

        $.MessageBox({
            buttonDone: "OK",
            buttonFail: locale.cancel,
            message: locale.confirm_delete
        }).done(function () {

            $.ajax({
                mimeType: 'text/html; charset=' + locale.charset,
                url: '?path=product&id=' + id,
                type: 'post',
                data: data,
                dataType: "json",
                async: false,
                success: function (json) {
                    if (json['success'] == 1) {
                        showAlertMessage(locale.save_done);
                        parent.fadeOut();

                        // Если удалено главное
                        if (main != "") {
                            $('input[name="pic_small_new"]').val('');
                            $('input[name="pic_big_new"]').val('');
                            $('[data-thumbnail="pic_big_new"]').attr('src', '');
                        }

                    } else
                        showAlertMessage(locale.save_false, true, true);
                }
            });
        })

    });


    // Установка изображения товара для подтипа
    $('.img-parent').on('changed.bs.select', function (e) {

        var data = [];
        var id = $(this).selectpicker('val');

        if (id == null || id == 0)
            return true;

        var text = $(this).find('option:selected').text();
        var img = $(this).attr('id');
        data.push({name: 'ajax', value: 1});
        data.push({name: 'rowID', value: id});
        data.push({name: 'pic_small_new', value: img.replace('.', 's.')});
        data.push({name: 'pic_big_new', value: img});
        data.push({name: 'actionList[rowID]', value: 'actionUpdate.catalog.edit'});

        $.ajax({
            mimeType: 'text/html; charset=' + locale.charset,
            url: '?path=product&id=' + id,
            type: 'post',
            data: data,
            dataType: "json",
            async: false,
            success: function (json) {
                if (json['success'] == 1) {
                    showAlertMessage(locale.save_done);

                } else
                    showAlertMessage(locale.save_false, true, true);
            }
        });

        // Сброс значений
        $('.img-parent').each(function () {
            if ($(this).find('option:selected').text() == text && $(this).attr('id') != img) {
                $(this).selectpicker('val', 0);
            }
        });
    });

    // Ввод ALT иконки
    $('body').on('click', '.setAlt', function (event) {
        event.preventDefault();
        var img = $(this);
        var id = img.attr('data-id');

        $.MessageBox({
            buttonDone: locale.ok,
            buttonFail: locale.close,
            input: img.attr('data-alt'),
            message: locale.alt
        }).done(function (alt) {
            if ($.trim(alt)) {

                var data = [];
                data.push({name: 'ajax', value: 1});
                data.push({name: 'rowID', value: id});
                data.push({name: 'info_new', value: escape(alt)});
                data.push({name: 'actionList[rowID]', value: 'actionImgEdit.catalog.edit'});

                $.ajax({
                    mimeType: 'text/html; charset=' + locale.charset,
                    url: '?path=product&id=' + id,
                    type: 'post',
                    data: data,
                    dataType: "json",
                    async: false,
                    success: function (json) {
                        if (json['success'] == 1) {
                            showAlertMessage(locale.save_done);
                            $('img[data-id="' + id + '"]').attr('title', alt);
                            img.attr('data-alt', alt);

                        } else
                            showAlertMessage(locale.save_false, true, true);
                    }
                });

            } else {

            }
        });
    });

    // Сортировка изображения товара
    $('.img-num').on('changed.bs.select', function (e) {

        var data = [];
        var id = $(this).attr('id');
        data.push({name: 'ajax', value: 1});
        data.push({name: 'rowID', value: id});
        data.push({name: 'num_new', value: $(this).selectpicker('val')});
        data.push({name: 'actionList[rowID]', value: 'actionImgEdit.catalog.edit'});

        $.ajax({
            mimeType: 'text/html; charset=' + locale.charset,
            url: '?path=product&id=' + id,
            type: 'post',
            data: data,
            dataType: "json",
            async: false,
            success: function (json) {
                if (json['success'] == 1) {
                    showAlertMessage(locale.save_done);
                } else
                    showAlertMessage(locale.save_false, true, true);
            }
        });
    });

    // Добавить файл товара - 2 шаг
    $("body").on('click', "#selectModal .modal-footer .file-add-send", function (event) {
        event.preventDefault();
        var id = parseInt($('.file-add').attr('data-count'));
        $('.file-list').append('<tr class="data-row" data-row="' + id + '"><td class="file-edit"><a href="' + $('input[name=lfile]').val() + '" class="file-edit"></a></td><td><input class="hidden-edit " value="" name="files_new[' + id + '][path]" type="hidden"><input class="hidden-edit" value="" name="files_new[' + id + '][name]" type="hidden"></td><td style="text-align:right" class="file-edit-path"><a href="' + $('input[name=lfile]').val() + '" class="file-edit-path" target="_blank"></a></td></tr>');
        $('.file-list [data-row="' + id + '"] .file-edit > a').html($('input[name=modal_file_name]').val());
        $('.file-list [data-row="' + id + '"] input[name="files_new[' + id + '][name]"]').val($('input[name=modal_file_name]').val());
        $('.file-list [data-row="' + id + '"] .file-edit-path > a').html('<span class="glyphicon glyphicon-floppy-disk"></span>' + $('input[name=lfile]').val());
        $('.file-list [data-row="' + id + '"] input[name="files_new[' + id + '][path]"]').val($('input[name=lfile]').val());
        $('.file-add').attr('data-count', id + 1);

        $('#selectModal .modal-footer .btn-primary').removeClass('file-add-send');
        $('#selectModal').modal('hide');
    });

    // Добавить файл товара - 1 шаг
    $(".file-add").on('click', function (event) {
        event.preventDefault();

        var data = [];
        var id = $(this).closest('.data-row').attr('data-row');
        data.push({name: 'selectID', value: id});
        data.push({name: 'ajax', value: 1});
        data.push({name: 'actionList[selectID]', value: 'actionFileEdit'});

        $.ajax({
            mimeType: 'text/html; charset=' + locale.charset,
            url: '?path=product&id=file' + '&name=' + escape(''),
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
        var id = $('input[name=selectID]').val();

        $.MessageBox({
            buttonDone: "OK",
            buttonFail: locale.cancel,
            message: locale.confirm_delete
        }).done(function () {

            $('.file-list [data-row="' + id + '"]').remove();
            $('#selectModal').modal('hide');
        })

    });

    // Редактировать файл товара - 2 шаг
    $("body").on('click', "#selectModal .modal-footer .file-edit-send", function (event) {
        event.preventDefault();
        var id = $('input[name=selectID]').val();

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
        data.push({name: 'selectID', value: id});
        data.push({name: 'ajax', value: 1});
        data.push({name: 'actionList[selectID]', value: 'actionFileEdit'});
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
});