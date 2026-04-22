$().ready(function () {

    locale.icon_load = null;

    // Отправление сообщения
    $(".send-message").on('click', function (event) {

        var chat_id = $('input[name="chat_id"]').val();
        var user_id = $('input[name="user_id"]').val();
        var sender = $('input[name="sender"]').val();
        var $input = $("#uploadimage");

        var data = new FormData();
        data.append('file', $input.prop('files')[0]);
        data.append('selectID', 1);
        data.append('actionList[selectID]', 'actionReplies.shopusers.edit');
        data.append('message', $('textarea#message').val());
        data.append('user_id', user_id);
        data.append('chat_id', chat_id);
        data.append('sender', sender);
        data.append('attachment', $('[name="attachment"]').val());

        $.ajax({
            mimeType: 'text/html; charset=' + locale.charset,
            url: '?path=dialog&id=' + chat_id + '&sender=' + sender,
            processData: false,
            contentType: false,
            type: 'post',
            data: data,
            dataType: "json",
            async: false,
            success: function (json) {

                if (json['success'] == 1) {

                    // Очистка
                    $('textarea#message').val(null);
                    $('#uploadimage').val(null);
                    $('input[name="attachment"]').val(null);
                    $('.remove').click();
                    $(".link-thumbnail .img-thumbnail").attr('src', 'images/no_photo.gif');

                    var data = [];
                    data.push({name: 'selectID', value: 1});
                    data.push({name: 'actionList[selectID]', value: 'actionGetNew'});

                    $.ajax({
                        mimeType: 'text/html; charset=' + locale.charset,
                        url: '?path=dialog&id=' + chat_id + '&sender=' + sender,
                        type: 'post',
                        data: data,
                        dataType: "json",
                        async: false,
                        success: function (json) {
                            if (json['num'] > 0 && json['message'] !== "") {
                                $('#message-list').append(json['message']);
                                $('html').animate({scrollTop: $('#m').offset().top}, 2000);
                                $('#badge-' + chat_id).text(0);
                            }
                        }
                    });
                }
            }
        });
    });

    // Быстрый ответ
    $(".dialog-answer").on('click', function (event) {
        event.preventDefault();
        var message = $("#message").val();
        $("#message").val(message + $(this).attr('data-content'));
        $(".send-message").removeClass('disabled');
    });

    $("textarea[name=message],input[name=file]").on('click', function (event) {
        $(".send-message").removeClass('disabled');
    });

    $("#attachment-disp").on('click', function (event) {
        event.preventDefault();
        $(this).toggleClass('hide');
        $("#attachment").toggleClass('hide');
        $(".send-message").removeClass('disabled');
        $('html').animate({scrollTop: $('#f').offset().top});
    });

    // закрепление навигации
    if ($('#fix-check').length && typeof (WAYPOINT_LOAD) != 'undefined')
        var waypoint = new Waypoint({
            element: document.getElementById('fix-check'),
            handler: function (direction) {
                $('.navbar-action').toggleClass('navbar-fixed-top');
                $('.up').toggleClass('hide');
            },
        });

    // Поиск
    $('#show-dialog-search').on('click', function () {
        $('#dialog-search').slideToggle('slow');
    });

    // Поиск
    $('#input-dialog-search').keyup(function (event) {
        if (event.keyCode == '13') {
            event.preventDefault();
            $('#btn-search').click();
        }
        return false;
    });

    $('#btn-search').on('click', function () {
        var search = $('#input-dialog-search').val();
        if (search !== "")
            window.location.href = '?path=dialog&search=' + $('#input-dialog-search').val();
        else
            window.location.href = '?path=dialog';
    });

    // Прокрутка вниз
    if ($('#m').length)
        $('html').animate({scrollTop: $('#m').offset().top});

    // Прокрутка вниз
    $('a[href="#tabs-dialog"]').on('click', function (event) {
        $('html').animate({scrollTop: $('#m').offset().top});
    });

    // Наверх
    $('.up').on('click', function (event) {
        $('html').animate({scrollTop: 0});
    });

    // Проверка ответа
    setInterval(function () {

        var chat_id = $('input[name="chat_id"]').val();
        var sender = $('input[name="sender"]').val();

        var data = [];
        data.push({name: 'selectID', value: 1});
        data.push({name: 'actionList[selectID]', value: 'actionGetNew'});

        if( typeof chat_id != 'undefined')
        $.ajax({
            mimeType: 'text/html; charset=' + locale.charset,
            url: '?path=dialog&id=' + chat_id + '&sender=' + sender,
            type: 'post',
            data: data,
            dataType: "json",
            async: false,
            success: function (json) {
                if (json['num'] > 0) {
                    $('#play-chat').trigger("play");
                    $('#message-list').append(json['message']);
                    $('#badge-' + chat_id).text(json['num']);
                }

                $('#message-preloader').css('visibility', 'hidden');
            }
        });

    }, 3000);

    // Shift+Ввод
    $('#message').on('keydown', function (e) {
        if (e.keyCode === 13 && !e.shiftKey) {
            $('.send-message').click();
            return false;
        }
    });


});