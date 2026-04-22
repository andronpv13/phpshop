$().ready(function () {

    // Выбрать все категории
    $("body").on('change', "#categories_all", function () {
        if (this.checked)
            $('[name="categories[]"]').selectpicker('selectAll');
        else
            $('[name="categories[]"]').selectpicker('deselectAll');
    });

    // Блокировка
    $('body').on('change', '#filtr_new', function () {
        if ($(this).prop('checked') === true) {
            $('#virtual_new').bootstrapToggle('off');
        }
    });

    // Блокировка
    $('body').on('change', '#virtual_new', function () {
        if ($(this).prop('checked') === true) {
            $('#filtr_new').bootstrapToggle('off');
        }
    });

    // Быстрое изменение checkbox
    $("body").on('click', ".data-row .checkbox", function (event) {
        var data = [];
        var id = $(this).attr('data-id');
        var name = $(this).attr('name');

        data.push({name: name + '_new', value: this.checked ? 1 : 0});
        data.push({name: 'rowID', value: id});
        data.push({name: 'selectID', value: 1});
        data.push({name: 'ajax', value: 1});
        data.push({name: 'actionList[selectID]', value: 'actionUpdate.sort.edit'});

        $.ajax({
            mimeType: 'text/html; charset=' + locale.charset,
            url: '?path=sort&id=' + id,
            data: data,
            dataType: "json",
            type: 'post',
            async: false,
            success: function (json) {
                if (json['success'] != '') {
                    showAlertMessage(locale.save_done);

                } else
                    showAlertMessage(locale.save_false, true);
            }

        });
    });

    // Удаление характеристики
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
            data.push({name: 'actionList[delID]', value: 'actionDelete.sort.edit'});

            $('#modal-form').attr('action', '?path=sort.value&id=' + id);
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

    // Редактировать значение характеристики - 2 шаг
    $("body").on('click', "#selectModal .modal-footer .value-edit-send", function (event) {
        event.preventDefault();

        var id = $('input[name=rowID]').val();
        var parent = $('input[name=parentID]').val();


        var data = [];
        data.push({name: 'rowID', value: '1'});
        data.push({name: 'actionList[rowID]', value: 'actionUpdate.sort.edit'});

        $('#modal-form .form-control, #modal-form .hidden-edit, #modal-form input:radio:checked, #modal-form input:checkbox:checked').each(function () {
            if ($(this).attr('name') !== undefined) {
                data.push({name: $(this).attr('name'), value: escape($(this).val())});
            }
        });

        $('#modal-form').attr('action', '?path=sort.value&id=' + id);
        $('#modal-form').ajaxSubmit({
            data: data,
            dataType: "json",
            success: function (json) {

                $('#selectModal').modal('hide');

                if (json['success'] == 1) {
                    $('[data-row="' + parent + '"] :nth-child(1) input:text').val($('#modal-form input[name="num_value"]').val());
                    $('[data-row="' + parent + '"] :nth-child(2) input:text').val($('#modal-form input[name="name_value"]').val());
                    showAlertMessage(locale.save_done);
                } else
                    showAlertMessage(locale.save_false, true);
            }

        });

    });

    // Редактировать значение характеристики - 1 шаг 
    $("body").on('click', ".data-row .value-edit", function (event) {
        event.preventDefault();

        var data = [];
        var id = $(this).attr('data-id');
        var parent = $(this).closest('.data-row').attr('data-row');
        data.push({name: 'selectID', value: id});
        data.push({name: 'parentID', value: parent});
        data.push({name: 'ajax', value: 1});
        data.push({name: 'actionList[selectID]', value: 'actionValueEdit.sort.view'});
        data.push({name: 'brand', value: $('#brand_new').prop('checked')});
        data.push({name: 'virtual', value: $('#virtual_new').prop('checked')});

        $.ajax({
            mimeType: 'text/html; charset=' + locale.charset,
            url: '?path=sort.value&id=' + id,
            data: data,
            dataType: "html",
            async: false,
            success: function (data) {
                //$('#selectModal .modal-dialog').removeClass('modal-lg');
                $('#selectModal .modal-title').html(locale.edit_sort_value);
                $('#selectModal .modal-footer .btn-primary').removeClass('edit-select-send');
                $('#selectModal .modal-footer .btn-primary').addClass('value-edit-send');
                $('#selectModal .modal-footer .btn-delete').removeClass('hidden');
                $('#selectModal .modal-footer .btn-delete').addClass('value-delete');
                $('#selectModal .modal-body').html(data);

                $('.elfinder-modal-content').attr('data-option', 'return=lfile');
                $('#selectModal').modal('show');
            }

        });
    });


    // Добавить значение характеристики
    $("body").on('click', 'button[name=addValue]', function () {
        var parent = $(this).closest('.data-row');
        var name = $(this).closest('.data-row').find('input[name=name_value]').val();
        var num = $(this).closest('.data-row').find('input[name=num_value]').val();

        var data = [];
        data.push({name: 'actionList[saveID]', value: 'actionInsert.sort.create'});
        data.push({name: 'saveID', value: 1});
        data.push({name: 'name_value', value: escape(name)});
        data.push({name: 'num_value', value: num});
        data.push({name: 'category_value', value: $('#footer input[name=rowID]').val()});
        $.ajax({
            mimeType: 'text/html; charset=' + locale.charset,
            url: '?path=sort.value&action=new',
            type: 'post',
            data: data,
            dataType: "json",
            async: false,
            success: function (json) {
                if (json['success'] != '') {
                    parent.before('<tr class="data-row" data-row="' + json['success'] + '"><td style="text-align:left"><input style="width:100%" class="form-control input-sm" name="num_value" value="' + parseInt(0 + num) + '"></td><td style="text-align:left"><input style="width:100%" data-id="' + json['success'] + '" data-edit="name_value" class="form-control input-sm editable" value="' + name + '"></td><td style="text-align:center"><div class="dropdown" id="dropdown_action"><a href="#" class="dropdown-toggle btn btn-default btn-sm" data-toggle="dropdown" role="button" aria-expanded="false"><span class="glyphicon glyphicon-cog"></span> <span class="caret"></span></a><ul class="dropdown-menu" role="menu" ><li><a href="#" data-id="' + json['success'] + '" class="value-edit">Редактировать</a></li><li class="divider"></li><li><a href="#" data-id="' + json['success'] + '" class="remove">Удалить <span class="glyphicon glyphicon-trash"></span></a></li></ul></div></td><td><span data-original-title="Удалить" class="glyphicon glyphicon-remove remove" data-id="' + json['success'] + '" data-toggle="tooltip" data-placement="top" title="Удалить"></span></td></tr>');
                    $('.editable-add').val(null);
                    showAlertMessage(locale.save_done);

                } else
                    showAlertMessage(locale.save_false, true);
            }
        });
        $(this).val('');
        $(this).closest('.data-row').find('input[name=num_value]').val('');
    });

    // Удалить значение характеристики
    $("body").on('click', '.data-row .remove', function (event) {
        event.preventDefault();
        var id = $(this).closest('.data-row');
        var data_id = $(this).attr('data-id');

        $.MessageBox({
            buttonDone: "OK",
            buttonFail: locale.cancel,
            message: locale.confirm_delete
        }).done(function () {

            var data = [];
            data.push({name: 'rowID', value: data_id});
            data.push({name: 'deleteID', value: 1});
            data.push({name: 'actionList[deleteID]', value: 'actionDelete.sort.edit'});
            $.ajax({
                mimeType: 'text/html; charset=' + locale.charset,
                url: '?path=sort.value&id=' + $(this).attr('data-id'),
                type: 'post',
                data: data,
                dataType: "json",
                async: false,
                success: function (json) {
                    if (json['success'] == 1) {
                        showAlertMessage(locale.save_done);
                        id.empty();
                    } else
                        showAlertMessage(locale.save_false, true);
                }
            });
        })

    });

    // Изменение данных из списка (имя, сортировка)
    $('.editable').on('change', function () {
        var data = [];
        data.push({name: $(this).attr('data-edit'), value: escape($(this).val())});
        data.push({name: 'rowID', value: $(this).attr('data-id')});
        data.push({name: 'editID', value: 1});
        data.push({name: 'actionList[editID]', value: 'actionUpdate.sort.edit'});

        $(this).css('text-decoration', 'underline').css('text-decoration-style', 'dashed');

        $.ajax({
            mimeType: 'text/html; charset=' + locale.charset,
            url: '?path=sort.value&id=' + $(this).attr('data-id'),
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
    });

    // Дерево категорий
    $('.data-tree .dropdown-toggle').addClass('btn-xs');

    // Редактировать категорию в дереве
    $(".tree .edit").on('click', function (event) {
        event.preventDefault();
        window.location.href += '&id=' + $(this).attr('data-id') + '&type=sub';
    });

    // Удалить категорию в дереве
    $(".tree .delete").on('click', function (event) {
        event.preventDefault();
        var id = $(this).closest('.data-tree');
        var data_id = $(this).attr('data-id');

        $.MessageBox({
            buttonDone: "OK",
            buttonFail: locale.cancel,
            message: locale.confirm_delete
        }).done(function () {

            $('.list_edit_' + data_id).ajaxSubmit({
                success: function () {
                    id.empty();
                    showAlertMessage(locale.save_done);
                }
            });
        })

    });

    // Создать новый из карточки
    $(".newsub").on('click', function (event) {
        event.preventDefault();
        window.location.href += '&action=new&type=sub';
    });

    // Создать новый из списка
    $("button[name=addNew]").on('click', function () {
        var cat = $(this).attr('data-cat');
        var href = '?path=sort&action=new';
        if (cat > 0)
            href += '&cat=' + cat;
        window.location.href = href;
        action = true;
    });

    // Выделение текущей категории
    if (typeof cat != 'undefined') {
        $('.treegrid-' + cat).addClass('treegrid-active');
    }

    // Создать копию из списка dropdown
    $(".data-row .copy").on('click', function (event) {
        event.preventDefault();
        window.location.href = '?path=sort&action=new&id=' + $(this).attr('data-id');
    });


    // Активация из списка dropdown
    $('.data-row, .data-tree').hover(
            function () {
                $(this).find('#dropdown_action').show();
                $(this).find('.editable').removeClass('input-hidden');
                $(this).find('.remove, .add').removeClass('hide');
            },
            function () {
                $(this).find('#dropdown_action').hide();
                $(this).find('.editable').addClass('input-hidden');
                $(this).find('.remove, .add').addClass('hide');
            });

    // Очистка кэша всех каталогов
    $(".ResetCache").on('click', function (event) {
        event.preventDefault();

        var data = [];
        data.push({name: 'actionList[resetCache]', value: 'actionResetCache.sort.edit'});
        data.push({name: 'resetCache', value: '1'});
        data.push({name: 'ajax', value: 1});

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
    });

    // Удалить неиспользуемые
    $(".CleanSort").on('click', function (event) {
        event.preventDefault();

        var data = [];
        data.push({name: 'actionList[CleanSort]', value: 'actionCleanSort.sort.edit'});
        data.push({name: 'CleanSort', value: '1'});
        data.push({name: 'ajax', value: 1});

        $.MessageBox({
            buttonDone: "OK",
            buttonFail: locale.cancel,
            message: locale.create_db_dump
        }).done(function () {

            $.ajax({
                mimeType: 'text/html; charset=' + locale.charset,
                url: '?path=catalog.select',
                type: 'post',
                data: data,
                dataType: "json",
                async: false,
                success: function (json) {
                    if (json['success'] == 1) {
                        showAlertMessage(locale.done+'. '+locale.products_completed + ' ' + json['count']);
                    } else
                        showAlertMessage(locale.save_false, true);
                }
            });

        });

    });
});