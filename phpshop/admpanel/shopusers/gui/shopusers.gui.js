$().ready(function () {

    // Копировать E-mail с выбранными
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
        $('[name="user_id_new"]').val($(this).attr('data-id'));
        $('[name="name_new"]').val($(this).attr('data-name'));

        $('[data-toggle="popover"]').popover('hide');
    });

    $('[data-toggle="popover"]').popover({
        "html": true,
        "placement": "bottom",
        "template": '<div class="popover" role="tooltip" style="max-width:600px"><div class="arrow"></div><div class="popover-content"></div></div>'

    });

    // Поиск товара  -  2 шаг
    $("body").on('click', "#selectModal .modal-footer .id-add-send", function (event) {
        event.preventDefault();


        $('.search-list input:checkbox:checked').each(function () {
            var id = $(this).attr('data-id');
            var name = $(this).parents(".data-row").find('.product-name').text();

            $(selectTarget).val(name);
            $('[name="parent_id_new"]').val(id);
        });

        $('#selectModal').modal('hide');
    });

    // Выбор элемента по клику в модальном окне подбора товара
    $('body').on('click', ".search-list  td", function () {
        $(this).parent('tr').find('input:checkbox[name=items]').each(function () {
            this.checked = !this.checked && !this.disabled;
        });
    });

    // Поиск товара - Поиск
    $("body").on('click', "#selectModal .search-action", function (event) {
        event.preventDefault();

        var data = [];
        data.push({name: 'selectID', value: 2});
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

    // Поиск товара  - 1 шаг
    $(".product-search").on('click', function (event) {
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

    // Расширенный поиск сообщений
    $(".search").on('click', function (event) {
        event.preventDefault();

        var data = [];
        data.push({name: 'selectID', value: 1});
        data.push({name: 'ajax', value: 1});
        data.push({name: 'cat', value: $.cookie('cat')});
        data.push({name: 'actionList[selectID]', value: 'actionAdvanceSearch'});

        $.ajax({
            mimeType: 'text/html; charset=' + locale.charset,
            url: '?path=shopusers.messages',
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

    // Разослать уведомления автоматически
    $("body").on('click', ".select-action .send-user-all", function (event) {
        event.preventDefault();

        if (confirm(locale.confirm_notice)) {

            var data = [];
            data.push({name: 'saveID', value: 1});
            data.push({name: 'actionList[saveID]', value: 'actionUpdateAuto'});

            $.ajax({
                mimeType: 'text/html; charset=' + locale.charset,
                url: '?path=shopusers.notice&id=1',
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
    });

    // Разослать уведомления с выбранными
    $("body").on('click', ".select-action .send-user-select", function (event) {
        event.preventDefault();
        var result = 1;
        if ($('#data input:checkbox:checked').length) {
            if (confirm(locale.confirm_notice)) {
                $('#data input[name="items"]:checkbox:checked').each(function () {

                    var data = [];
                    var id = $(this).val();
                    data.push({name: 'saveID', value: 1});
                    data.push({name: 'rowID', value: id});
                    data.push({name: 'email', value: $(this).closest('.data-row').find('td:nth-child(5)>a').html()});
                    data.push({name: 'productID', value: $(this).closest('.data-row').find('td:nth-child(4)').html()});

                    data.push({name: 'actionList[saveID]', value: 'actionUpdate'});

                    $.ajax({
                        mimeType: 'text/html; charset=' + locale.charset,
                        url: '?path=shopusers.notice&id=' + id,
                        type: 'post',
                        data: data,
                        dataType: "json",
                        async: false,
                        success: function (json) {
                            if (json['success'] != 1) {
                                result = 0;
                                showAlertMessage(locale.save_false, true);
                            }
                        }
                    });
                });

                if (result == 1)
                    showAlertMessage(locale.save_done);
            }
        } else
            alert(locale.select_no);

    });

    $("body").on('click', ".send-user", function (event) {
        event.preventDefault();
        var result = 1;
        var data = [];
        var id = $(this).attr('data-id');

        data.push({name: 'saveID', value: 1});
        data.push({name: 'rowID', value: id});
        data.push({name: 'email', value: $(this).closest('.data-row').find('td:nth-child(5)>a').html()});
        data.push({name: 'productID', value: $(this).closest('.data-row').find('td:nth-child(4)').html()});
        data.push({name: 'actionList[saveID]', value: 'actionUpdate'});

        $.ajax({
            mimeType: 'text/html; charset=' + locale.charset,
            url: '?path=shopusers.notice&id=' + id,
            type: 'post',
            data: data,
            dataType: "json",
            async: false,
            success: function (json) {
                if (json['success'] != 1) {
                    result = 0;
                    showAlertMessage(locale.save_false, true);
                }
            }
        });
        if (result == 1)
            showAlertMessage(locale.save_done);

    });

    // Сделать новый заказ из списка пользователей
    $(".dropdown-menu .order").on('click', function () {
        $(this).attr('href', '?path=order&action=new&user=' + $(this).attr('data-id'));
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
                url: '?path=exchange.export.user',
                type: 'post',
                data: data,
                dataType: "json",
                async: false,
                success: function () {
                    window.location.href = '?path=exchange.export.user';
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
    $(".comment-url").on('click', function (event) {
        event.preventDefault();
        var table = $('#data').DataTable();
        var id = $(this).closest('.data-row').find('td:nth-child(3)>a').html();
        table.search(id).draw();
    });


    // Карта доставки
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
            myMap.controls
                    .add('mapTools', {left: 5, top: 5});
            firstGeoObject.options.set('preset', 'twirl#buildingsIcon');
            myMap.geoObjects.add(firstGeoObject);
        });
    }

    // Мобильная навигация
    if (typeof is_mobile !== 'undefined') {
        locale.dataTable.paginate.next = "»";
        locale.dataTable.paginate.previous = "«";
    }

    // Таблица данных
    if ($.getUrlVar('path') == 'shopusers') {
        if (typeof ($.cookie('data_length')) == 'undefined')
            var data_length = [10, 25, 50, 75, 100, 500];
        else
            var data_length = [parseInt($.cookie('data_length')), 10, 25, 50, 75, 100, 500];

        if ($('#data').html()) {
            var table = $('#data').dataTable({
                "ajax": {
                    "type": "GET",
                    "url": ajax_path + 'shopusers.ajax.php' + window.location.search,
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
                "info": false,
                "searching": true,
                "lengthMenu": data_length,
                "language": locale.dataTable,
                "stripeClasses": ['data-row', 'data-row'],
                "fnDrawCallback": function () {

                    // Активация из списка dropdown
                    $('.data-row').hover(
                            function () {
                                $(this).find('#dropdown_action').show();
                            },
                            function () {
                                $(this).find('#dropdown_action').hide();
                            });

                    $('.toggle-event').bootstrapToggle();
                },
                "aoColumnDefs": [{
                        'bSortable': false,
                        'aTargets': ['sorting-hide']
                    }]
            });
        }
    }

});