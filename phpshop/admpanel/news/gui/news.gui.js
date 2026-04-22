
$().ready(function() {
    
    $.fn.datetimepicker.dates['ru'] = locale;

    // Автоматизация рассылки
    if ($('#bot_result').length) {
        
        $(window).bind("beforeunload", function() {
            return "Are you sure you want to exit? Please complete sign up or the app will get deleted.";
        });

        var time = performance.now();

        var min = $('[name="time_limit"]').val();
        var limit = Number($('[name="message_limit"]').val());
        var start = limit;
        var end = limit;
        var refreshId = setInterval(function() {

            var data = [];
            data.push({name: 'selectID', value: 1});
            data.push({name: 'actionList[selectID]', value: 'actionBot'});
            data.push({name: 'start', value: start});
            data.push({name: 'end', value: end});
            data.push({name: 'time', value: min});
            data.push({name: 'performance', value: performance.now() - time});

            $.ajax({
                mimeType: 'text/html; charset='+locale.charset,
                url: '?path=news.sendmail&id=' + $('[name="rowID"]').val(),
                type: 'post',
                data: data,
                dataType: "json",
                async: false,
                success: function(data) {
                    $('#bot_result').html(data['result']);
                    if (data['success'] == 'done') {
                        clearInterval(refreshId);
                        $('.progress-bar').css('width', '100%');
                        $('.progress-bar').removeClass('active').html('100%');
                        $('#play').trigger("play");
                        $(window).unbind("beforeunload");
                    }
                    else if (data['success']) {
                        start += limit;
                        end += limit;
                        $('.progress-bar').css('width', data['bar'] + '%').html(data['bar'] + '%');

                    }

                }

            });

        }, min * 60000);

    }


    $(".tree a[data-view]").on('click', function(event) {
        event.preventDefault();

        $('html, body').animate({scrollTop: $("a[name=" + $(this).attr('data-view') + "]").offset().top - 100}, 500);
    });

    // datetimepicker
    $(".date").datetimepicker({
        format: 'dd-mm-yyyy',
        language: 'ru',
        weekStart: 1,
        todayBtn: 1,
        autoclose: 1,
        todayHighlight: 1,
        startView: 2,
        minView: 2,
        forceParse: 0
    });

    // Указать ID товара в виде тега - Поиск
    $("body").on('click', "#selectModal .search-action", function(event) {
        event.preventDefault();

        var data = [];
        data.push({name: 'selectID', value: 2});
        data.push({name: 'ajax', value: 1});
        data.push({name: 'actionList[selectID]', value: 'actionSearch'});

        $.ajax({
            mimeType: 'text/html; charset='+locale.charset,
            url: '?path=catalog.search&words=' + escape($('input:text[name=search_name]').val()) + '&cat=' + $('select[name=search_category]').val() + '&price_start=' + $('input:text[name=search_price_start]').val() + '&price_end=' + $('input:text[name=search_price_end]').val(),
            type: 'post',
            data: data,
            dataType: "html",
            async: false,
            success: function(data) {
                $('#selectModal .modal-body').html(data);

            }

        });
    });

    // Указать ID товара в виде тега  -  2 шаг
    $("body").on('click', "#selectModal .modal-footer .id-add-send", function(event) {
        event.preventDefault();
        
        $('.search-list input:checkbox').each(function() {
            var id = $(this).attr('data-id');
            $(selectTarget).removeTag(id);
        });
        

        $('.search-list input:checkbox:checked').each(function() {
            var id = $(this).attr('data-id');
            $(selectTarget).addTag(id);
        });

        $('#selectModal').modal('hide');
    });

    // Выбор элемента по клику в модальном окне подбора товара
    $('body').on('click', ".search-list  td", function() {
        $(this).parent('tr').find('input:checkbox[name=items]').each(function() {
            this.checked = !this.checked && !this.disabled;
        });
    });


    // Указать ID товара в виде тега  - 1 шаг
    $(".tag-search").on('click', function(event) {
        event.preventDefault();

        selectTarget = $(this).attr('data-target');

        var data = [];
        data.push({name: 'selectID', value: 2});
        data.push({name: 'ajax', value: 1});
        data.push({name: 'currentID', value: $(selectTarget).val()});
        data.push({name: 'actionList[selectID]', value: 'actionSearch'});

        $.ajax({
            mimeType: 'text/html; charset='+locale.charset,
            url: '?path=catalog.search',
            type: 'post',
            data: data,
            dataType: "html",
            async: false,
            success: function(data) {
                //$('#selectModal .modal-dialog').removeClass('modal-lg');
                $('#selectModal .modal-title').html(locale.add_cart_value);
                $('#selectModal .modal-footer .btn-primary').removeClass('edit-select-send');
                $('#selectModal .modal-footer .btn-primary').addClass('id-add-send');
                $('#selectModal .modal-footer .btn-delete').addClass('hidden');
                $('#selectModal .modal-body').css('max-height', ($(window).height() - 200) + 'px');
                $('#selectModal .modal-body').css('overflow-y', 'auto');
                $('#selectModal .modal-body').html(data);

                $(".search-list td input:checkbox").each(function() {
                    this.checked = true;
                });

                $('#selectModal').modal('show');

            }

        });
    });


    if ($('#odnotip_new').length)
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

});